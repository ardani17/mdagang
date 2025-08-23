<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('category');
            $table->decimal('batch_size', 10, 3)->default(1); // Quantity produced per batch
            $table->string('batch_unit'); // Unit of the batch (pcs, kg, liter, etc)
            $table->decimal('total_cost', 15, 2)->default(0); // Total cost per batch
            $table->decimal('cost_per_unit', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('profit_margin', 5, 2)->default(0); // Percentage
            $table->integer('production_time_minutes')->default(0);
            $table->text('instructions')->nullable();
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            $table->timestamps();
            
            $table->index(['code', 'status']);
            $table->index('product_id');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};