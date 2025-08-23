<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('recipe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('batch_count')->default(1);
            $table->decimal('quantity_planned', 10, 3);
            $table->decimal('quantity_produced', 10, 3)->default(0);
            $table->date('start_date');
            $table->date('target_date');
            $table->date('completed_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled', 'on_hold'])->default('pending');
            $table->integer('progress')->default(0); // Percentage
            $table->string('operator_name')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('estimated_cost', 15, 2)->default(0);
            $table->decimal('actual_cost', 15, 2)->default(0);
            $table->integer('estimated_duration_hours')->default(0);
            $table->decimal('actual_duration_hours', 8, 2)->default(0);
            $table->integer('efficiency_percentage')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['order_number', 'status']);
            $table->index(['start_date', 'target_date']);
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }
};