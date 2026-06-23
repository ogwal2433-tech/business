<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('inventory_histories', function (Blueprint $table) {
            $table->decimal('purchase_price', 15, 2)->nullable()->after('new_quantity');
            $table->decimal('purchase_price_bulk', 15, 2)->nullable()->after('purchase_price');
            $table->decimal('selling_price', 15, 2)->nullable()->after('purchase_price_bulk');
            $table->decimal('selling_price_bulk', 15, 2)->nullable()->after('selling_price');
        });

        DB::statement("ALTER TABLE inventory_histories MODIFY COLUMN type ENUM('increase', 'decrease', 'price_update') NOT NULL DEFAULT 'increase'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE inventory_histories MODIFY COLUMN type ENUM('increase', 'decrease') NOT NULL DEFAULT 'increase'");

        Schema::table('inventory_histories', function (Blueprint $table) {
            $table->dropColumn(['purchase_price', 'purchase_price_bulk', 'selling_price', 'selling_price_bulk']);
        });
    }
};
