<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, drop the existing check constraint
        DB::statement('ALTER TABLE stock_movements DROP CONSTRAINT IF EXISTS stock_movements_type_check');
        
        // Update the type column to support more detailed movement types
        DB::statement("ALTER TABLE stock_movements 
            ADD CONSTRAINT stock_movements_type_check 
            CHECK (type IN (
                'purchase', 
                'purchase_receipt',
                'sale',
                'production_consumption',
                'production_output',
                'production_return',
                'return',
                'adjustment_in',
                'adjustment_out',
                'transfer_in',
                'transfer_out',
                'waste',
                'damage',
                'initial',
                'in',
                'out',
                'adjustment',
                'transfer',
                'production'
            ))");
    }

    public function down(): void
    {
        // Revert to original constraint
        DB::statement('ALTER TABLE stock_movements DROP CONSTRAINT IF EXISTS stock_movements_type_check');
        
        DB::statement("ALTER TABLE stock_movements 
            ADD CONSTRAINT stock_movements_type_check 
            CHECK (type IN ('in', 'out', 'adjustment', 'transfer', 'production', 'damage', 'return'))");
    }
};