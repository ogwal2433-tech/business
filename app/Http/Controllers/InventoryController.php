<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\InventoryUploadLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function fullInventoryDetails()
    {
        // Get products belonging to the logged-in admin
        $products = Inventory::where('admin_id', auth()->id())
            ->with(['inventoryHistories' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }])
            ->orderBy('name')
            ->paginate(15);

        $products->getCollection()->transform(function ($product) {
            $validHistories = $product->inventoryHistories->filter(function ($history) {
                return !is_null($history->previous_quantity);
            });

            if ($validHistories->isNotEmpty()) {
                $product->initial_quantity = $validHistories->max('previous_quantity');
            } elseif ($product->inventoryHistories->isNotEmpty()) {
                $product->initial_quantity = $product->inventoryHistories->first()->new_quantity;
            } else {
                $product->initial_quantity = $product->quantity;
            }

            return $product;
        });

        return view('products.fullInventory', compact('products'));
    }

    public function lookup($sku)
    {
        $item = Inventory::where('sku', $sku)
            ->where('admin_id', auth()->id())
            ->first();

        if (!$item) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'name' => $item->name,
            'quantity' => $item->quantity,
            'unit' => $item->unit,
            'purchase_price_bulk' => $item->purchase_price_bulk,
            'selling_price_bulk' => $item->selling_price_bulk,
            'purchase_price' => $item->purchase_price,
            'price' => $item->price,
        ]);
    }

    public function uploadLogs()
    {
        $adminId = auth()->id();

        $batches = InventoryUploadLog::where('admin_id', $adminId)
            ->selectRaw('batch_id, MIN(created_at) as uploaded_at, COUNT(*) as total, SUM(CASE WHEN status = "skipped" THEN 1 ELSE 0 END) as skipped_count')
            ->groupBy('batch_id')
            ->orderBy('uploaded_at', 'desc')
            ->paginate(20);

        return view('inventory.upload_logs', compact('batches'));
    }

    public function showUploadForm()
    {
        return view('inventory.upload');
    }


    public function list()
{
    $products = Inventory::where('admin_id', auth()->id())
        ->orderBy('name')
        ->paginate(10);

    $totalPurchaseValue = 0;
    $totalSellValue = 0;

    foreach ($products as $product) {
        // Calculate total purchase value
        $product->total_purchase_value = $this->calculateTotalValue(
            $product->quantity,
            $product->unit,
            $product->purchase_price_bulk,
            $product->purchase_price
        );

        // Calculate total sell value
        $product->total_sell_value = $this->calculateTotalValue(
            $product->quantity,
            $product->unit,
            $product->selling_price_bulk,
            $product->price
        );

        // Calculate expected profit
        $product->expected_profit = $product->total_sell_value - $product->total_purchase_value;

        $totalPurchaseValue += $product->total_purchase_value;
        $totalSellValue += $product->total_sell_value;
    }

    $expectedProfit = $totalSellValue - $totalPurchaseValue;

    return view('inventory.list', compact(
        'products',
        'totalPurchaseValue',
        'totalSellValue',
        'expectedProfit'
    ));
}

/**
 * Calculate total value based on quantity, unit, bulk price and unit price.
 *
 * @param int|float $quantity
 * @param string $unit
 * @param float|null $bulkPrice
 * @param float|null $unitPrice
 * @return float
 */
protected function calculateTotalValue($quantity, $unit, $bulkPrice, $unitPrice)
{
    // Normalize prices (treat null as zero)
    $bulkPrice = $bulkPrice ?? 0;
    $unitPrice = $unitPrice ?? 0;

    // Decide price based on unit type
    if (in_array(strtolower($unit), ['dozen', 'carton'])) {
        // For bulk units, multiply quantity by bulk price
        return $quantity * $bulkPrice;
    } else {
        // For individual units or unknown units, multiply quantity by unit price
        return $quantity * $unitPrice;
    }
}


    public function showAdjustmentForm()
    {
        // Only allow selecting owned products
        $products = Inventory::where('admin_id', auth()->id())
            ->orderBy('name')
            ->get();

        return view('inventory.adjust', compact('products'));
    }

   public function processAdjustment(Request $request)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:inventories,id',
        'type' => 'required|in:increase,decrease',
        'quantity' => 'required|integer|min:1',
        'note' => 'nullable|string|max:255',
    ]);

    $adminId = auth()->id();

    // Ensure inventory belongs to current admin
    $product = Inventory::where('id', $validated['product_id'])
                      ->where('admin_id', $adminId)
                      ->firstOrFail();

    $original = $product->quantity;
    $newQuantity = $original;

    if ($validated['type'] === 'increase') {
        $newQuantity += $validated['quantity'];
    } else {
        $newQuantity -= $validated['quantity'];

        if ($newQuantity < 0) {
            return back()->withErrors(['quantity' => 'Quantity cannot go below 0.']);
        }
    }

    $product->quantity = $newQuantity;
    $product->save();

    $unit = $product->unit;
    $unitQuantities = ['piece' => 1, 'dozen' => 12, 'carton' => 24];
    $unitSize = $unitQuantities[$unit] ?? 1;
    $piecesChanged = $validated['quantity'] * $unitSize;

    $note = $validated['note'] ? $validated['note'] . ' | ' : '';
    $note .= $validated['quantity'] . ' ' . $unit . ($validated['quantity'] > 1 ? 's' : '');
    if ($unit !== 'piece') {
        $note .= ' → ' . $piecesChanged . ' pieces';
    }

    // Log history (with current prices)
    InventoryHistory::create([
        'product_id' => $product->id,
        'user_id' => $adminId,
        'type' => $validated['type'],
        'quantity' => $piecesChanged,
        'previous_quantity' => $original,
        'new_quantity' => $newQuantity,
        'purchase_price' => $product->purchase_price,
        'purchase_price_bulk' => $product->purchase_price_bulk,
        'selling_price' => $product->price,
        'selling_price_bulk' => $product->selling_price_bulk,
        'unit' => $product->unit,
        'note' => $note,
    ]);

    return redirect()->route('inventory.history')->with('success', __('Stock adjusted and logged.'));
}


    public function history()
    {
        // Only show history related to products owned by this admin
        $logs = InventoryHistory::whereHas('product', function ($q) {
                $q->where('admin_id', auth()->id());
            })
            ->with(['product', 'user'])
            ->latest()
            ->paginate(15);

        return view('inventory.history', compact('logs'));
    }

    public function index(Request $request)
    {
        $query = Inventory::where('admin_id', auth()->id());

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->where('quantity', '<=', 10)->where('quantity', '>', 0);
            } elseif ($request->stock_status === 'out') {
                $query->where('quantity', 0);
            } elseif ($request->stock_status === 'in') {
                $query->where('quantity', '>', 10);
            }
        }

        $products = $query->paginate(15);

        return view('inventory.index', compact('products'));
    }

  public function priceLookup()
{
    $user = auth()->user();

    // Show products created by the admin  to their respective employees
    $adminId = $user->isAdmin() ? $user->id : $user->admin_id;

    $products = Inventory::where('admin_id', $adminId)->get();

    return view('employee.products.lookup', compact('products'));
}


public function uploadInventory(Request $request)
{
    $validated = $request->validate([
        'products' => ['required', 'array', 'min:1'],
        'products.*.sku' => ['required', 'string'],
        'products.*.name' => ['required', 'string'],
        'products.*.quantity' => ['required', 'integer', 'min:1'],
        'products.*.unit' => ['required', 'in:piece,dozen,carton'],
        'products.*.purchase_price_bulk' => ['nullable', 'string'],
        'products.*.selling_price_bulk' => ['nullable', 'string'],
        'products.*.purchase_price' => ['nullable', 'string'],
        'products.*.price' => ['nullable', 'string'],
    ]);
 $adminId = auth()->id();
    $unitQuantities = [
        'piece' => 1,
        'dozen' => 12,
        'carton' => 24,
    ];

    $adminId = auth()->id();
    $batchId = (string) Str::uuid();
    $created = 0;
    $skipped = [];

    DB::beginTransaction();

    try {
        foreach ($validated['products'] as $product) {
            $unit = $product['unit'];
            $unitSize = $unitQuantities[$unit] ?? 1;

            $originalQuantity = (int) $product['quantity'];
            $quantityInPieces = $unit === 'piece'
                ? $originalQuantity
                : $originalQuantity * $unitSize;

            $isBulk = $unit !== 'piece';

            $purchasePriceBulk = $isBulk
                ? $this->parseCurrency($product['purchase_price_bulk'] ?? null)
                : null;

            $sellingPriceBulk = $isBulk
                ? $this->parseCurrency($product['selling_price_bulk'] ?? null)
                : null;

            $purchasePrice = $isBulk
                ? ($purchasePriceBulk > 0 ? $purchasePriceBulk / $quantityInPieces : 0)
                : $this->parseCurrency($product['purchase_price'] ?? null);

            $price = $isBulk
                ? ($sellingPriceBulk > 0 ? $sellingPriceBulk / $quantityInPieces : 0)
                : $this->parseCurrency($product['price'] ?? null);

            // Check for duplicate SKU
            $existing = Inventory::where('sku', $product['sku'])
                ->where('admin_id', $adminId)
                ->first();

            if ($existing) {
                $skipped[] = $product['sku'];
                InventoryUploadLog::create([
                    'admin_id' => $adminId,
                    'batch_id' => $batchId,
                    'sku' => $product['sku'],
                    'name' => $product['name'],
                    'status' => 'skipped',
                    'reason' => 'SKU already exists in inventory',
                ]);
                continue;
            }

            // 1. Save inventory
            $productModel = Inventory::create([
                'sku' => $product['sku'],
                'name' => $product['name'],
                'quantity' => $quantityInPieces,
                'original_quantity' => $originalQuantity,
                'unit' => $unit,
                'purchase_price_bulk' => $purchasePriceBulk,
                'selling_price_bulk' => $sellingPriceBulk,
                'purchase_price' => $purchasePrice,
                'price' => $price,
                'admin_id' => $adminId,
            ]);

            // 2. Inventory history (with prices)
            InventoryHistory::create([
                'product_id' => $productModel->id,
                'user_id' => $adminId,
                'type' => 'increase',
                'quantity' => $quantityInPieces,
                'previous_quantity' => 0,
                'new_quantity' => $quantityInPieces,
                'purchase_price' => $purchasePrice,
                'purchase_price_bulk' => $purchasePriceBulk,
                'selling_price' => $price,
                'selling_price_bulk' => $sellingPriceBulk,
                'unit' => $unit,
                'note' => __('Uploaded: :original :unit :arrow :pieces pieces of :name', [
                    'original' => $originalQuantity,
                    'unit' => $unit,
                    'arrow' => '→',
                    'pieces' => $quantityInPieces,
                    'name' => $product['name'],
                ]),
            ]);

            $created++;
        }

        DB::commit();

        $message = __('Inventory uploaded successfully. :created product(s) created.', ['created' => $created]);
        if (count($skipped) > 0) {
            $message .= " " . __(':count product(s) skipped (already exist).', ['count' => count($skipped)]);
            \Log::info('Inventory upload skipped duplicates', [
                'batch_id' => $batchId,
                'admin_id' => $adminId,
                'skipped_skus' => $skipped,
            ]);
            return redirect()->back()
                ->with('success', $message)
                ->with('inventory_upload', true)
                ->with('warning', __(':count product(s) were skipped because they already exist.', ['count' => count($skipped)]) . ' <a href="' . route('inventory.upload.logs') . '" class="underline">' . __('View upload logs') . '</a> ' . __('for details.'));
        }

        return redirect()->back()->with('success', $message)->with('inventory_upload', true);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Inventory upload failed', ['error' => $e->getMessage()]);
        return back()->with('error', __('Failed to upload inventory: :error', ['error' => $e->getMessage()]));
    }
}

/**
 * Remove commas and convert to float.
 */
private function parseCurrency(?string $value): float
{
    return $value ? floatval(str_replace(',', '', $value)) : 0;
}


public function downloadTemplate(): StreamedResponse
{
    $headers = [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=inventory_template.csv",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];

    $columns = ['SKU', 'Name', 'Quantity', 'Unit', 'Purchase Price', 'Selling Price'];

    $callback = function () use ($columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);
        // Optional: add a sample row
        fputcsv($file, ['123456', 'Sample Product (per piece)', '10', 'piece', '100.00', '150.00']);
        fputcsv($file, ['123457', 'Sample Product (per carton)', '5', 'carton', '120000.00', '180000.00']);
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
public function bulkUpload(Request $request)
{
    $user = Auth::user();

    if (!$user || $user->role !== 'admin') {
        abort(403, 'Unauthorized action.');
    }

    $request->validate([
        'inventory_file' => 'required|file|mimes:csv,txt|max:2048',
    ]);

    $file = $request->file('inventory_file');
    $path = $file->getRealPath();
    $rows = array_map('str_getcsv', file($path));

    if (count($rows) < 2) {
        return back()->withErrors(['inventory_file' => 'The uploaded file is empty or invalid.']);
    }

    $unitQuantities = [
        'piece' => 1,
        'dozen' => 12,
        'carton' => 24,
    ];

    $batchId = (string) Str::uuid();
    $created = 0;
    $skipped = [];

    foreach (array_slice($rows, 1) as $index => $row) {
        if (count($row) < 6) continue;

        [$sku, $name, $quantity, $unit, $purchasePrice, $sellingPrice] = array_map('trim', $row);
        $unit = strtolower($unit);

        $existing = Inventory::where('sku', $sku)
            ->where('admin_id', $user->id)
            ->first();

        if ($existing) {
            $skipped[] = $sku;
            InventoryUploadLog::create([
                'admin_id' => $user->id,
                'batch_id' => $batchId,
                'sku' => $sku,
                'name' => $name,
                'status' => 'skipped',
                'reason' => 'SKU already exists in inventory',
            ]);
            continue;
        }

        $unitSize = $unitQuantities[$unit] ?? 1;
        $originalQuantity = (int) $quantity;
        $quantityInPieces = $originalQuantity * $unitSize;

        $isBulk = $unit !== 'piece';
        $purchasePriceFloat = (float) $purchasePrice;
        $sellingPriceFloat = (float) $sellingPrice;

        $purchasePriceBulk = $isBulk ? $purchasePriceFloat : null;
        $sellingPriceBulk = $isBulk ? $sellingPriceFloat : null;
        $perPiecePurchase = $isBulk ? $purchasePriceFloat / $unitSize : $purchasePriceFloat;
        $perPieceSell = $isBulk ? $sellingPriceFloat / $unitSize : $sellingPriceFloat;

        $inventory = Inventory::create([
            'sku' => $sku,
            'name' => $name,
            'quantity' => $quantityInPieces,
            'original_quantity' => $originalQuantity,
            'unit' => $unit,
            'purchase_price_bulk' => $purchasePriceBulk,
            'selling_price_bulk' => $sellingPriceBulk,
            'purchase_price' => $perPiecePurchase,
            'price' => $perPieceSell,
            'admin_id' => $user->id,
        ]);

        InventoryHistory::create([
            'product_id' => $inventory->id,
            'user_id' => $user->id,
            'type' => 'increase',
            'quantity' => $quantityInPieces,
            'previous_quantity' => 0,
            'new_quantity' => $quantityInPieces,
            'purchase_price' => $perPiecePurchase,
            'purchase_price_bulk' => $purchasePriceBulk,
            'selling_price' => $perPieceSell,
            'selling_price_bulk' => $sellingPriceBulk,
            'unit' => $unit,
            'note' => 'Uploaded: ' . $originalQuantity . ' ' . $unit . (($originalQuantity > 1 && $unit !== 'piece') ? 's' : '') . ' → ' . $quantityInPieces . ' pieces of ' . $name,
        ]);

        InventoryUploadLog::create([
            'admin_id' => $user->id,
            'batch_id' => $batchId,
            'sku' => $sku,
            'name' => $name,
            'status' => 'created',
            'reason' => null,
        ]);

        $created++;
    }

    $message = __('Bulk inventory uploaded successfully. :created product(s) created.', ['created' => $created]);
    if (count($skipped) > 0) {
        $message .= " " . __(':count product(s) skipped (already exist).', ['count' => count($skipped)]);
        \Log::info('Bulk inventory upload skipped duplicates', [
            'batch_id' => $batchId,
            'admin_id' => $user->id,
            'skipped_skus' => $skipped,
        ]);
        return back()
            ->with('success', $message)
            ->with('inventory_upload', true)
            ->with('warning', __(':count product(s) were skipped because they already exist.', ['count' => count($skipped)]) . ' <a href="' . route('inventory.upload.logs') . '" class="underline">' . __('View upload logs') . '</a> ' . __('for details.'));
    }

    return back()->with('success', $message)->with('inventory_upload', true);
}

public function editPrice($id)
{
    $product = Inventory::where('id', $id)->where('admin_id', auth()->id())->firstOrFail();
    return view('inventory.edit-price', compact('product'));
}

public function updatePrice(Request $request, $id)
{
    $product = Inventory::where('id', $id)->where('admin_id', auth()->id())->firstOrFail();

    $validated = $request->validate([
        'purchase_price' => 'nullable|numeric|min:0',
        'purchase_price_bulk' => 'nullable|numeric|min:0',
        'price' => 'nullable|numeric|min:0',
        'selling_price_bulk' => 'nullable|numeric|min:0',
    ]);

    // Strip commas from submitted values before use
    $submittedPP = $request->has('purchase_price') ? (float) str_replace(',', '', $request->purchase_price) : null;
    $submittedPPB = $request->has('purchase_price_bulk') ? (float) str_replace(',', '', $request->purchase_price_bulk) : null;
    $submittedSP = $request->has('price') ? (float) str_replace(',', '', $request->price) : null;
    $submittedSPB = $request->has('selling_price_bulk') ? (float) str_replace(',', '', $request->selling_price_bulk) : null;

    $oldPP = $product->purchase_price;
    $oldPPB = $product->purchase_price_bulk;
    $oldSP = $product->price;
    $oldSPB = $product->selling_price_bulk;

    $product->purchase_price = $submittedPP ?? $product->purchase_price;
    $product->purchase_price_bulk = $submittedPPB ?? $product->purchase_price_bulk;
    $product->price = $submittedSP ?? $product->price;
    $product->selling_price_bulk = $submittedSPB ?? $product->selling_price_bulk;

    // Recalculate per-piece prices for bulk units
    if (in_array(strtolower($product->unit), ['dozen', 'carton'])) {
        $unitSize = $product->unit === 'dozen' ? 12 : 24;
        if ($product->purchase_price_bulk > 0) {
            $product->purchase_price = $product->purchase_price_bulk / $unitSize;
        }
        if ($product->selling_price_bulk > 0) {
            $product->price = $product->selling_price_bulk / $unitSize;
        }
    }

    $product->save();

    InventoryHistory::create([
        'product_id' => $product->id,
        'user_id' => auth()->id(),
        'type' => 'price_update',
        'quantity' => 0,
        'previous_quantity' => $product->quantity,
        'new_quantity' => $product->quantity,
        'purchase_price' => $product->purchase_price,
        'purchase_price_bulk' => $product->purchase_price_bulk,
        'selling_price' => $product->price,
        'selling_price_bulk' => $product->selling_price_bulk,
        'note' => __('Price updated: purchase :oldPP → :newPP, sell :oldSP → :newSP', [
            'oldPP' => number_format($oldPP, 0),
            'newPP' => number_format($product->purchase_price, 0),
            'oldSP' => number_format($oldSP, 0),
            'newSP' => number_format($product->price, 0),
        ]),
    ]);

    return redirect()->back()->with('success', __('Prices updated for :name', ['name' => $product->name]));
}

public function destroy($id)
{
    $product = Inventory::where('id', $id)->where('admin_id', auth()->id())->firstOrFail();
    $name = $product->name;
    $product->delete();

    return redirect()->back()->with('success', __(':name deleted from inventory.', ['name' => $name]));
}


}
