<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['urbano', 'rural', 'agricola', 'comercial', 'industrial'])->default('urbano');
            $table->string('province', 100);
            $table->string('canton', 100);
            $table->decimal('price', 12, 2);
            $table->decimal('area_m2', 12, 2);
            $table->enum('status', ['disponible', 'reservado', 'vendido'])->default('disponible');
            $table->string('soil_type', 100)->nullable();
            $table->string('access_services')->nullable();
            $table->string('legal_documents')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('published')->default(true);
            $table->unsignedBigInteger('views')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
