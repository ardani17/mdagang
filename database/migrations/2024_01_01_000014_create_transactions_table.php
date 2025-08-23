<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->date('transaction_date');
            $table->enum('type', ['income', 'expense', 'transfer']);
            $table->string('category');
            $table->string('subcategory')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->nullable(); // cash, bank_transfer, credit_card, etc
            $table->string('reference')->nullable(); // Reference to invoice, order, etc
            $table->string('reference_type')->nullable(); // order, invoice, purchase_order, etc
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable(); // Path to receipt/document
            $table->timestamps();
            
            $table->index(['transaction_number', 'status']);
            $table->index(['transaction_date', 'type']);
            $table->index(['category', 'subcategory']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};