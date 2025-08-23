<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Changed from sku to code
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('unit'); // kg, liter, pcs, etc
            $table->decimal('current_stock', 10, 3)->default(0);
            $table->decimal('minimum_stock', 10, 3)->default(0);
            $table->decimal('maximum_stock', 10, 3)->default(0);
            $table->decimal('reorder_point', 10, 3)->default(0);
            $table->decimal('reorder_quantity', 10, 3)->default(0);
            $table->decimal('average_price', 15, 2)->default(0);
            $table->decimal('last_purchase_price', 15, 2)->default(0);
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->date('last_purchase_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['good', 'low_stock', 'critical', 'out_of_stock'])->default('good');
            $table->string('storage_location')->nullable();
            $table->integer('lead_time_days')->default(0); // Days to receive after ordering
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['code', 'status']);
            $table->index(['category', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_materials');
    }
};