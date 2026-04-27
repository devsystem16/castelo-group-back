<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('cedula', 20)->unique();
            $table->string('whatsapp', 20);
            $table->string('email')->unique();
            $table->string('bank_name');
            $table->string('account_number', 30);
            $table->enum('account_type', ['ahorros', 'corriente'])->default('ahorros');
            $table->text('description')->nullable();
            $table->string('referral_code', 20)->unique()->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->decimal('commission_rate', 5, 2)->default(8.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
