<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_inspections', function (Blueprint $table) {
            $table->id();
            $table->string('inspection_id')->unique();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number');
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('batch_size');
            $table->string('inspector_name');
            $table->date('inspection_date');
            $table->enum('status', ['pending', 'passed', 'failed', 'conditional'])->default('pending');
            $table->integer('score')->default(0); // 0-100
            $table->json('checklist')->nullable(); // JSON array of inspection items
            $table->text('defects')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['inspection_id', 'status']);
            $table->index('production_order_id');
            $table->index('inspection_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_inspections');
    }
};