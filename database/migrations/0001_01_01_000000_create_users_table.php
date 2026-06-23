<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // This column references another user as the admin/manager
            $table->unsignedBigInteger('admin_id')->nullable();

            $table->string('name');
            $table->string('username')->unique();
                        $table->string('business_name')->nullable();

            $table->string('email')->nullable();
            $table->string('password');

            $table->enum('role', ['admin', 'employee']);
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();

            // Add foreign key constraint referencing the same users table
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
