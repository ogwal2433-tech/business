<?php

namespace App\Console\Commands;

use App\Models\BusinessSnapshot;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\AdminExpense;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BuildBusinessSnapshots extends Command
{
    protected $signature = 'business:build-snapshots {--months=24 : Number of past months to rebuild}';
    protected $description = 'Build historical business snapshots for net worth chart';

    public function handle()
    {
        $months = (int) $this->option('months');
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $this->info("Building snapshots for admin: {$admin->name} (ID: {$admin->id})");

            for ($i = $months - 1; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $snapshotDate = $date->copy()->endOfMonth()->toDateString();

                // Skip if snapshot already exists
                if (BusinessSnapshot::where('admin_id', $admin->id)
                    ->where('snapshot_date', $snapshotDate)->exists()) {
                    continue;
                }

                $this->line("  {$date->format('M Y')}...");

                // ── Inventory value at that month-end ──
                $inventoryValue = $this->reconstructInventoryValue($admin->id, $snapshotDate);

                // ── Cumulative sales up to that month-end ──
                $cumulativeSales = Sale::where('admin_id', $admin->id)
                    ->whereDate('created_at', '<=', $snapshotDate)
                    ->sum('total_amount');

                // ── Cumulative expenses up to that month-end ──
                $empExp = Expense::whereHas('employee', fn($q) => $q->where('admin_id', $admin->id))
                    ->whereDate('date', '<=', $snapshotDate)
                    ->sum('amount');
                $admExp = AdminExpense::where('admin_id', $admin->id)
                    ->whereDate('date', '<=', $snapshotDate)
                    ->sum('amount');
                $cumulativeExpenses = $empExp + $admExp;

                $netWorth = $inventoryValue + $cumulativeSales - $cumulativeExpenses;

                BusinessSnapshot::create([
                    'admin_id' => $admin->id,
                    'snapshot_date' => $snapshotDate,
                    'inventory_value' => round($inventoryValue, 2),
                    'cumulative_sales' => round($cumulativeSales, 2),
                    'cumulative_expenses' => round($cumulativeExpenses, 2),
                    'net_worth' => round($netWorth, 2),
                ]);
            }

            $this->info("  Done for {$admin->name}");
        }

        $this->info('All snapshots built successfully.');
    }

    private function reconstructInventoryValue($adminId, $snapshotDate)
    {
        $products = Inventory::where('admin_id', $adminId)->get();
        $totalValue = 0;

        foreach ($products as $product) {
            // Find the most recent inventory history before the snapshot date
            $history = InventoryHistory::where('product_id', $product->id)
                ->whereDate('created_at', '<=', $snapshotDate)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($history) {
                $quantity = $history->new_quantity;
                // Use historical prices if available, fall back to current
                $purchasePrice = $history->purchase_price ?? $product->purchase_price;
                $purchasePriceBulk = $history->purchase_price_bulk ?? $product->purchase_price_bulk;
            } else {
                // No history before snapshot date — use current values (product was created later)
                // Only include if the product was created before the snapshot date
                if ($product->created_at->toDateString() > $snapshotDate) {
                    continue;
                }
                $quantity = $product->quantity;
                $purchasePrice = $product->purchase_price;
                $purchasePriceBulk = $product->purchase_price_bulk;
            }

            $value = in_array(strtolower($product->unit), ['dozen', 'carton'])
                ? $quantity * ($purchasePriceBulk ?? 0)
                : $quantity * ($purchasePrice ?? 0);

            $totalValue += $value;
        }

        return $totalValue;
    }
}
