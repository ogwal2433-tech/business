<?php

$users = App\Models\User::where('role', 'admin')->get(['id', 'name', 'email']);
if ($users->isEmpty()) {
    echo "No admin users found.\n";
    exit;
}

echo "Select an admin user:\n";
foreach ($users as $i => $u) {
    echo "  [$i] {$u->name} ({$u->email})\n";
}
echo "Enter number: ";
$handle = fopen('php://stdin', 'r');
$line = fgets($handle);
$index = (int) trim($line);
$admin = $users[$index] ?? $users[0];
$adminId = $admin->id;
echo "Using: {$admin->name} (ID: $adminId)\n";

$products = [
    ['sku' => 'BARCODE001', 'name' => 'Coca Cola 500ml', 'quantity' => 240, 'original_quantity' => 10, 'unit' => 'carton', 'purchase_price_bulk' => 120000, 'selling_price_bulk' => 180000, 'purchase_price' => 5000, 'price' => 7500],
    ['sku' => 'BARCODE002', 'name' => 'Pepsi 500ml', 'quantity' => 120, 'original_quantity' => 10, 'unit' => 'dozen', 'purchase_price_bulk' => 60000, 'selling_price_bulk' => 90000, 'purchase_price' => 5000, 'price' => 7500],
    ['sku' => 'BARCODE003', 'name' => 'Bottled Water 1L', 'quantity' => 500, 'original_quantity' => 500, 'unit' => 'piece', 'purchase_price_bulk' => null, 'selling_price_bulk' => null, 'purchase_price' => 1000, 'price' => 2000],
    ['sku' => 'BARCODE004', 'name' => 'Cooking Oil 2L', 'quantity' => 60, 'original_quantity' => 5, 'unit' => 'carton', 'purchase_price_bulk' => 300000, 'selling_price_bulk' => 420000, 'purchase_price' => 12500, 'price' => 17500],
    ['sku' => 'BARCODE005', 'name' => 'Sugar 1kg', 'quantity' => 100, 'original_quantity' => 100, 'unit' => 'piece', 'purchase_price_bulk' => null, 'selling_price_bulk' => null, 'purchase_price' => 4000, 'price' => 5500],
];

foreach ($products as $p) {
    $existing = App\Models\Inventory::where('sku', $p['sku'])->where('admin_id', $adminId)->first();
    if ($existing) {
        echo "SKIP: {$p['sku']} already exists\n";
        continue;
    }
    $p['admin_id'] = $adminId;
    App\Models\Inventory::create($p);
    echo "CREATED: {$p['sku']} - {$p['name']}\n";
}

echo "\nDone! 5 sample products added for {$admin->name}.\n";
