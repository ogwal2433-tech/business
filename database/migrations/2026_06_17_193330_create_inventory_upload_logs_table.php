<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_upload_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->uuid('batch_id');
            $table->string('sku');
            $table->string('name')->nullable();
            $table->enum('status', ['created', 'skipped'])->default('created');
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index('batch_id');
            $table->index('admin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_upload_logs');
    }
};
