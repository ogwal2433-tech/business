<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('default_password')->nullable()->change();
            $table->string('shop_name')->nullable()->change();
            $table->string('shop_address')->nullable()->change();
            $table->string('shop_phone')->nullable()->change();
            $table->string('shop_email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('default_password')->nullable(false)->change();
            $table->string('shop_name')->nullable(false)->change();
            $table->string('shop_address')->nullable(false)->change();
            $table->string('shop_phone')->nullable(false)->change();
            $table->string('shop_email')->nullable(false)->change();
        });
    }
};
