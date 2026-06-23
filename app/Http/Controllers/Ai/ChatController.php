<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Models\AdminExpense;
use App\Models\Expense;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\User;
use App\Services\GroqService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function sendMessage(Request $request, GroqService $groq)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'reply' => __('Unauthorized'),
                'status' => 'error',
            ]);
        }

        if (!$user->planHasFeature('ai_assistant')) {
            return response()->json([
                'reply' => __('AI Assistant is not available on your current plan.'),
                'status' => 'error',
            ]);
        }

        $context = $this->buildContext($user);
        $toolDefs = $this->getToolDefinitions();

        $systemPrompt = $groq->buildSystemPrompt($context);
        $systemPrompt .= "\n\nYou have access to these functions. When you need data, output a function call on its own line like this:\n<function=function_name={\"arg\":\"value\"}</function>\n\nAvailable functions:\n" . $toolDefs;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $request->message],
        ];

        $turnCount = 0;

        while ($turnCount < 10) {
            $turnCount++;

            $result = $groq->continue($messages);

            if (!$result) {
                return response()->json([
                    'reply' => __("I'm sorry, I'm not available right now. Please try again later."),
                    'status' => 'fallback',
                ]);
            }

            $content = $result['content'] ?? '';

            $fnCalls = $this->parseFunctionCalls($content);

            if (empty($fnCalls)) {
                return response()->json([
                    'reply' => $content,
                    'status' => 'success',
                ]);
            }

            $cleaned = preg_replace('/<function=.*?<\/function>/s', '', $content);
            $cleaned = trim($cleaned);

            $results = [];
            foreach ($fnCalls as $fn) {
                $output = $this->executeTool($fn['name'], $fn['args'], $user);
                $results[] = "Result of {$fn['name']}:\n$output";
            }

            $messages[] = ['role' => 'assistant', 'content' => $cleaned ?: null];

            $messages[] = [
                'role' => 'user',
                'content' => "Function results:\n\n" . implode("\n\n", $results) . "\n\nUse these results to answer the user's question. If you need more data, call another function.",
            ];
        }

        return response()->json([
            'reply' => __('I took too long to process. Please try again.'),
            'status' => 'fallback',
        ]);
    }

    protected function parseFunctionCalls(string $content): array
    {
        $calls = [];
        $pattern = '/<function=(\w+)=({.*?})<\/function>/s';
        if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $args = json_decode($m[2], true);
                $calls[] = [
                    'name' => $m[1],
                    'args' => $args ?: [],
                ];
            }
        }
        return $calls;
    }

    protected function buildContext($user): array
    {
        return [
            'Business Name' => $user->business_name ?? $user->name,
            'Admin Name' => $user->name,
            'Date' => now()->format('l, F j, Y'),
        ];
    }

    protected function getToolDefinitions(): string
    {
        return implode("\n", [
            "- get_summary(): Get a quick business overview — today/month/all sales totals, product count, employee count, expense totals, outstanding credit.",
            "- query_sales(date_from, date_to, employee_name, product_name, is_credit, limit): Query sales records. All parameters optional. Returns date, product, qty, amount, who sold it.",
            "- query_products(search, low_stock_only, out_of_stock_only, sort_by, sort_dir, limit): Query inventory/products. Returns name, stock, buy/sell price.",
            "- query_employees(status, search): Query employees. Returns name, status, email, and their total sales for current month.",
            "- query_expenses(date_from, date_to, employee_name, category, limit): Query employee expenses.",
            "- query_admin_expenses(date_from, date_to, category, limit): Query operational/business expenses.",
        ]);
    }

    protected function executeTool(string $name, array $args, $user): string
    {
        $adminId = $user->id;

        return match ($name) {
            'get_summary' => $this->toolGetSummary($adminId),
            'query_sales' => $this->toolQuerySales($adminId, $args),
            'query_products' => $this->toolQueryProducts($adminId, $args),
            'query_employees' => $this->toolQueryEmployees($adminId, $args),
            'query_expenses' => $this->toolQueryExpenses($adminId, $args),
            'query_admin_expenses' => $this->toolQueryAdminExpenses($adminId, $args),
            default => "Unknown function: $name. Available: get_summary, query_sales, query_products, query_employees, query_expenses, query_admin_expenses",
        };
    }

    protected function toolGetSummary(int $adminId): string
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        $todaySales = Sale::where('admin_id', $adminId)->whereDate('created_at', $today);
        $monthSales = Sale::where('admin_id', $adminId)->whereDate('created_at', '>=', $monthStart);
        $allSales = Sale::where('admin_id', $adminId);

        $totalProducts = Inventory::where('admin_id', $adminId)->count();
        $lowStock = Inventory::where('admin_id', $adminId)->where('quantity', '<', 10)->where('quantity', '>', 0)->count();
        $outOfStock = Inventory::where('admin_id', $adminId)->where('quantity', 0)->count();

        $employees = User::where('admin_id', $adminId)->where('role', 'employee');
        $totalEmp = (clone $employees)->count();
        $activeEmp = (clone $employees)->where('status', 'active')->count();

        $monthExpenses = Expense::whereHas('employee', fn($q) => $q->where('admin_id', $adminId))
            ->whereDate('date', '>=', $monthStart)->sum('amount');
        $monthAdminExp = AdminExpense::where('admin_id', $adminId)
            ->whereDate('date', '>=', $monthStart)->sum('amount');

        $creditTotal = Sale::where('admin_id', $adminId)
            ->whereColumn('amount_paid', '<', 'total_amount')
            ->sum(DB::raw('total_amount - amount_paid'));

        return sprintf(
            "Business Summary as of %s:\n"
            . "- Today Sales: UGX %s (%d transactions)\n"
            . "- This Month Sales: UGX %s (%d transactions)\n"
            . "- All-Time Sales: UGX %s (%d transactions)\n"
            . "- Products: %d total (%d low stock, %d out of stock)\n"
            . "- Employees: %d total (%d active)\n"
            . "- This Month Expenses: UGX %s (employee + operational)\n"
            . "- Outstanding Credit: UGX %s",
            now()->format('d M Y'),
            number_format($todaySales->sum('total_amount')), $todaySales->count(),
            number_format($monthSales->sum('total_amount')), $monthSales->count(),
            number_format($allSales->sum('total_amount')), $allSales->count(),
            $totalProducts, $lowStock, $outOfStock,
            $totalEmp, $activeEmp,
            number_format($monthExpenses + $monthAdminExp),
            number_format($creditTotal)
        );
    }

    protected function toolQuerySales(int $adminId, array $args): string
    {
        $query = Sale::with('product', 'employee')->where('admin_id', $adminId);

        if (!empty($args['date_from'])) {
            $query->whereDate('created_at', '>=', $args['date_from']);
        }
        if (!empty($args['date_to'])) {
            $query->whereDate('created_at', '<=', $args['date_to']);
        }
        if (!empty($args['employee_name'])) {
            $query->whereHas('employee', fn($q) => $q->where('name', 'like', '%' . $args['employee_name'] . '%'));
        }
        if (!empty($args['product_name'])) {
            $query->whereHas('product', fn($q) => $q->where('name', 'like', '%' . $args['product_name'] . '%'));
        }
        if (!empty($args['is_credit'])) {
            $query->whereColumn('amount_paid', '<', 'total_amount');
        }

        $limit = min($args['limit'] ?? 20, 100);
        $sales = $query->latest()->take($limit)->get();

        if ($sales->isEmpty()) {
            return 'No sales found matching the criteria.';
        }

        $lines = $sales->map(fn($s) => sprintf(
            '%s | %s | Qty: %d | UGX %s | By: %s%s',
            $s->created_at->format('d M Y H:i'),
            $s->product?->name ?? 'Unknown',
            $s->quantity,
            number_format($s->total_amount),
            $s->employee?->name ?? 'Admin',
            $s->amount_paid < $s->total_amount ? ' (Credit: UGX ' . number_format($s->total_amount - $s->amount_paid) . ' remaining)' : ''
        ));

        $lines->prepend('Date | Product | Qty | Amount | Sold By');
        $lines->prepend('---');
        $lines->push('---');
        $lines->push('Total: UGX ' . number_format($sales->sum('total_amount')) . ' (' . $sales->count() . ' records)');

        return $lines->implode("\n");
    }

    protected function toolQueryProducts(int $adminId, array $args): string
    {
        $query = Inventory::where('admin_id', $adminId);

        if (!empty($args['search'])) {
            $query->where('name', 'like', '%' . $args['search'] . '%');
        }
        if (!empty($args['low_stock_only'])) {
            $query->where('quantity', '<', 10)->where('quantity', '>', 0);
        }
        if (!empty($args['out_of_stock_only'])) {
            $query->where('quantity', 0);
        }

        if (!empty($args['sort_by'])) {
            $dir = ($args['sort_dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
            $col = match ($args['sort_by']) {
                'name' => 'name',
                'stock' => 'quantity',
                'price' => 'price',
                default => 'name',
            };
            $query->orderBy($col, $dir);
        } else {
            $query->orderBy('name');
        }

        $limit = min($args['limit'] ?? 50, 200);
        $products = $query->take($limit)->get();

        if ($products->isEmpty()) {
            return 'No products found matching the criteria.';
        }

        $lines = $products->map(fn($p) => sprintf(
            '%s | Stock: %d %s | Buy: UGX %s | Sell: UGX %s%s',
            $p->name,
            $p->quantity,
            $p->unit ?? 'pcs',
            number_format($p->purchase_price ?? 0),
            number_format($p->price),
            $p->quantity == 0 ? ' [OUT OF STOCK]' : ($p->quantity < 10 ? ' [LOW STOCK]' : '')
        ));

        $lines->prepend('Product | Stock | Buy Price | Sell Price');
        $lines->prepend('---');

        return $lines->implode("\n");
    }

    protected function toolQueryEmployees(int $adminId, array $args): string
    {
        $query = User::where('admin_id', $adminId)->where('role', 'employee');

        if (!empty($args['status'])) {
            $query->where('status', $args['status']);
        }
        if (!empty($args['search'])) {
            $query->where('name', 'like', '%' . $args['search'] . '%');
        }

        $employees = $query->orderBy('name')->get();

        if ($employees->isEmpty()) {
            return 'No employees found matching the criteria.';
        }

        $monthStart = now()->startOfMonth()->toDateString();

        $lines = $employees->map(function ($emp) use ($adminId, $monthStart) {
            $monthSales = Sale::where('admin_id', $adminId)
                ->where('employee_id', $emp->id)
                ->whereDate('created_at', '>=', $monthStart)
                ->select(DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(*) as txns'))
                ->first();

            return sprintf(
                '%s | Status: %s | Email: %s | This Month: UGX %s (%d sales)',
                $emp->name,
                $emp->status,
                $emp->email ?? 'N/A',
                number_format($monthSales->total ?? 0),
                $monthSales->txns ?? 0
            );
        });

        $lines->prepend('Employee | Status | Email | This Month Sales');
        $lines->prepend('---');

        return $lines->implode("\n");
    }

    protected function toolQueryExpenses(int $adminId, array $args): string
    {
        $query = Expense::with('employee')
            ->whereHas('employee', fn($q) => $q->where('admin_id', $adminId));

        if (!empty($args['date_from'])) {
            $query->whereDate('date', '>=', $args['date_from']);
        }
        if (!empty($args['date_to'])) {
            $query->whereDate('date', '<=', $args['date_to']);
        }
        if (!empty($args['employee_name'])) {
            $query->whereHas('employee', fn($q) => $q->where('name', 'like', '%' . $args['employee_name'] . '%'));
        }
        if (!empty($args['category'])) {
            $query->where('category', $args['category']);
        }

        $limit = min($args['limit'] ?? 20, 100);
        $expenses = $query->latest()->take($limit)->get();

        if ($expenses->isEmpty()) {
            return 'No expenses found matching the criteria.';
        }

        $lines = $expenses->map(fn($e) => sprintf(
            '%s | %s | UGX %s | %s | By: %s',
            $e->date,
            $e->title,
            number_format($e->amount),
            $e->category,
            $e->employee?->name ?? 'Unknown'
        ));

        $lines->prepend('Date | Title | Amount | Category | Employee');
        $lines->prepend('---');
        $lines->push('---');
        $lines->push('Total: UGX ' . number_format($expenses->sum('amount')) . ' (' . $expenses->count() . ' records)');

        return $lines->implode("\n");
    }

    protected function toolQueryAdminExpenses(int $adminId, array $args): string
    {
        $query = AdminExpense::where('admin_id', $adminId);

        if (!empty($args['date_from'])) {
            $query->whereDate('date', '>=', $args['date_from']);
        }
        if (!empty($args['date_to'])) {
            $query->whereDate('date', '<=', $args['date_to']);
        }
        if (!empty($args['category'])) {
            $query->where('category', $args['category']);
        }

        $limit = min($args['limit'] ?? 20, 100);
        $expenses = $query->latest()->take($limit)->get();

        if ($expenses->isEmpty()) {
            return 'No admin expenses found matching the criteria.';
        }

        $lines = $expenses->map(fn($e) => sprintf(
            '%s | %s | UGX %s | %s',
            $e->date,
            $e->title,
            number_format($e->amount),
            $e->category
        ));

        $lines->prepend('Date | Title | Amount | Category');
        $lines->prepend('---');
        $lines->push('---');
        $lines->push('Total: UGX ' . number_format($expenses->sum('amount')) . ' (' . $expenses->count() . ' records)');

        return $lines->implode("\n");
    }
}
