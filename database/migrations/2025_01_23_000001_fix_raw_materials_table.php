<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            // Remove the duplicate category text field if it exists
            if (Schema::hasColumn('raw_materials', 'category')) {
                $table->dropColumn('category');
            }
            
            // Add reorder_point and reorder_quantity if they don't exist
            if (!Schema::hasColumn('raw_materials', 'reorder_point')) {
                $table->decimal('reorder_point', 10, 3)->default(0)->after('maximum_stock');
            }
            
            if (!Schema::hasColumn('raw_materials', 'reorder_quantity')) {
                $table->decimal('reorder_quantity', 10, 3)->default(0)->after('reorder_point');
            }
            
            // Ensure category_id has proper foreign key
            if (!Schema::hasColumn('raw_materials', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('description')->constrained()->nullOnDelete();
            }
            
            // Add indexes for better performance
            if (!Schema::hasIndex('raw_materials', 'raw_materials_status_index')) {
                $table->index('status');
            }
            
            if (!Schema::hasIndex('raw_materials', 'raw_materials_supplier_id_index')) {
                $table->index('supplier_id');
            }
            
            if (!Schema::hasIndex('raw_materials', 'raw_materials_is_active_index')) {
                $table->index('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            // Re-add category column
            if (!Schema::hasColumn('raw_materials', 'category')) {
                $table->string('category')->nullable()->after('description');
            }
            
            // Drop indexes if they exist
            $table->dropIndex(['status']);
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['is_active']);
        });
    }
};