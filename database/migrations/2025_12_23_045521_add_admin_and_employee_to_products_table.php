<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('admin_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('employee_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropForeign(['employee_id']);
            $table->dropColumn(['admin_id', 'employee_id']);
        });
    }
};
