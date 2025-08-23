<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['food', 'beverage', 'other'])->default('other');
            $table->string('unit'); // pcs, kg, liter, pack, etc
            $table->string('image')->nullable();
            $table->decimal('base_price', 15, 2)->default(0); // Cost price
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('min_stock', 10, 3)->default(0);
            $table->decimal('current_stock', 10, 3)->default(0);
            $table->decimal('reserved_stock', 10, 3)->default(0); // Stock reserved for orders
            $table->integer('production_time_hours')->default(0);
            $table->date('expiry_date')->nullable();
            $table->boolean('is_manufactured')->default(true); // true if produced, false if purchased
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['sku', 'is_active']);
            $table->index(['type', 'category_id']);
            $table->index('is_manufactured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};