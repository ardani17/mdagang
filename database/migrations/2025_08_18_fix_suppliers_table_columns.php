<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Drop the old payment_terms column if it exists as string
            if (Schema::hasColumn('suppliers', 'payment_terms')) {
                $table->dropColumn('payment_terms');
            }
            
            // Add the missing columns
            $table->integer('payment_terms')->nullable()->after('tax_id');
            $table->integer('lead_time_days')->nullable()->after('payment_terms');
            $table->decimal('minimum_order_value', 15, 2)->nullable()->after('lead_time_days');
            
            // Drop products column as it's not used
            if (Schema::hasColumn('suppliers', 'products')) {
                $table->dropColumn('products');
            }
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Revert changes
            $table->dropColumn(['payment_terms', 'lead_time_days', 'minimum_order_value']);
            $table->string('payment_terms')->nullable()->after('tax_id');
            $table->json('products')->nullable()->after('rating');
        });
    }
};