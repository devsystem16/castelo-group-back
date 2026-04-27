<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('affiliates')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->enum('status', ['pending', 'converted'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
