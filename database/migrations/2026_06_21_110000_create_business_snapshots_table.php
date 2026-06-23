<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('business_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->date('snapshot_date');
            $table->decimal('inventory_value', 18, 2)->default(0);
            $table->decimal('cumulative_sales', 18, 2)->default(0);
            $table->decimal('cumulative_expenses', 18, 2)->default(0);
            $table->decimal('net_worth', 18, 2)->default(0);
            $table->timestamps();

            $table->unique(['admin_id', 'snapshot_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('business_snapshots');
    }
};
