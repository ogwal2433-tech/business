<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('download_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('platform'); // android, ios, windows, mac
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('type')->default('public'); // public, secure
            $table->timestamps();

            $table->index(['platform', 'created_at']);
            $table->index('ip_address');
        });
    }

    public function down()
    {
        Schema::dropIfExists('download_logs');
    }
};
