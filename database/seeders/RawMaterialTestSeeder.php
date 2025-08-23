<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RawMaterial;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\StockMovement;

class RawMaterialTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have categories
        $categories = Category::whereIn('name', ['Pemanis', 'Herbal', 'Kemasan', 'Bahan Utama'])->get();
        
        if ($categories->isEmpty()) {
            // Create categories if they don't exist
            $categoryNames = ['Pemanis', 'Herbal', 'Kemasan', 'Bahan Utama', 'Pengawet', 'Pewarna'];
            foreach ($categoryNames as $index => $name) {
                Category::create([
                    'name' => $name,
                    'slug' => strtolower(str_replace(' ', '-', $name)),
                    'description' => 'Kategori untuk ' . $name,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]);
            }
            $categories = Category::whereIn('name', ['Pemanis', 'Herbal', 'Kemasan', 'Bahan Utama'])->get();
        }

        // Get suppliers (use existing or create one)
        $supplier = Supplier::first();
        if (!$supplier) {
            $supplier = Supplier::create([
                'name' => 'PT Supplier Test',
                'code' => 'SUP001',
                'contact_person' => 'John Doe',
                'phone' => '081234567890',
                'email' => 'supplier@test.com',
                'address' => 'Jl. Test No. 123',
                'city' => 'Jakarta',
                'rating' => 4.5,
                'lead_time_days' => 7,
                'is_active' => true,
            ]);
        }

        // Raw materials data
        $rawMaterials = [
            [
                'code' => 'RM000001',
                'name' => 'Gula Cair',
                'description' => 'Gula cair untuk pemanis minuman',
                'category_id' => $categories->where('name', 'Pemanis')->first()->id,
                'supplier_id' => $supplier->id,
                'unit' => 'liter',
                'current_stock' => 150,
                'minimum_stock' => 50,
                'maximum_stock' => 500,
                'reorder_point' => 75,
                'reorder_quantity' => 200,
                'average_price' => 15000,
                'last_purchase_price' => 15000,
                'last_purchase_date' => now()->subDays(7),
                'storage_location' => 'Gudang A - Rak 1',
                'lead_time_days' => 7,
                'is_active' => true,
            ],
            [
                'code' => 'RM000002',
                'name' => 'Jahe Merah',
                'description' => 'Jahe merah segar untuk bahan herbal',
                'category_id' => $categories->where('name', 'Herbal')->first()->id,
                'supplier_id' => $supplier->id,
                'unit' => 'kg',
                'current_stock' => 25,
                'minimum_stock' => 20,
                'maximum_stock' => 100,
                'reorder_point' => 30,
                'reorder_quantity' => 50,
                'average_price' => 35000,
                'last_purchase_price' => 35000,
                'last_purchase_date' => now()->subDays(14),
                'storage_location' => 'Gudang B - Rak 2',
                'lead_time_days' => 5,
                'is_active' => true,
            ],
            [
                'code' => 'RM000003',
                'name' => 'Botol Plastik 250ml',
                'description' => 'Botol plastik untuk kemasan minuman',
                'category_id' => $categories->where('name', 'Kemasan')->first()->id,
                'supplier_id' => $supplier->id,
                'unit' => 'pcs',
                'current_stock' => 5000,
                'minimum_stock' => 1000,
                'maximum_stock' => 10000,
                'reorder_point' => 2000,
                'reorder_quantity' => 5000,
                'average_price' => 1500,
                'last_purchase_price' => 1500,
                'last_purchase_date' => now()->subDays(10),
                'storage_location' => 'Gudang C - Area 1',
                'lead_time_days' => 14,
                'is_active' => true,
            ],
            [
                'code' => 'RM000004',
                'name' => 'Air Mineral',
                'description' => 'Air mineral untuk bahan utama',
                'category_id' => $categories->where('name', 'Bahan Utama')->first()->id,
                'supplier_id' => $supplier->id,
                'unit' => 'liter',
                'current_stock' => 1000,
                'minimum_stock' => 500,
                'maximum_stock' => 5000,
                'reorder_point' => 750,
                'reorder_quantity' => 2000,
                'average_price' => 3000,
                'last_purchase_price' => 3000,
                'last_purchase_date' => now()->subDays(3),
                'storage_location' => 'Gudang A - Tank 1',
                'lead_time_days' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'RM000005',
                'name' => 'Madu Murni',
                'description' => 'Madu murni sebagai pemanis alami',
                'category_id' => $categories->where('name', 'Pemanis')->first()->id,
                'supplier_id' => $supplier->id,
                'unit' => 'kg',
                'current_stock' => 10, // Low stock
                'minimum_stock' => 20,
                'maximum_stock' => 100,
                'reorder_point' => 30,
                'reorder_quantity' => 50,
                'average_price' => 75000,
                'last_purchase_price' => 75000,
                'last_purchase_date' => now()->subDays(30),
                'storage_location' => 'Gudang A - Rak 3',
                'lead_time_days' => 10,
                'is_active' => true,
            ],
            [
                'code' => 'RM000006',
                'name' => 'Kunyit Bubuk',
                'description' => 'Kunyit bubuk untuk bahan herbal',
                'category_id' => $categories->where('name', 'Herbal')->first()->id,
                'supplier_id' => $supplier->id,
                'unit' => 'kg',
                'current_stock' => 5, // Critical stock
                'minimum_stock' => 15,
                'maximum_stock' => 50,
                'reorder_point' => 20,
                'reorder_quantity' => 30,
                'average_price' => 45000,
                'last_purchase_price' => 45000,
                'last_purchase_date' => now()->subDays(45),
                'storage_location' => 'Gudang B - Rak 4',
                'lead_time_days' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($rawMaterials as $materialData) {
            // Check if material already exists
            $existingMaterial = RawMaterial::where('code', $materialData['code'])->first();
            
            if (!$existingMaterial) {
                $material = RawMaterial::create($materialData);
                
                // Create initial stock movement
                if ($material->current_stock > 0) {
                    StockMovement::create([
                        'item_type' => 'raw_material',
                        'item_id' => $material->id,
                        'type' => 'in',
                        'quantity' => $material->current_stock,
                        'unit_cost' => $material->last_purchase_price,
                        'total_cost' => $material->current_stock * $material->last_purchase_price,
                        'before_stock' => 0,
                        'after_stock' => $material->current_stock,
                        'reason' => 'initial_stock',
                        'notes' => 'Initial stock from seeder',
                        'created_by' => 1, // Admin user
                    ]);
                }
                
                echo "Created raw material: {$material->name}\n";
            } else {
                echo "Raw material already exists: {$materialData['name']}\n";
            }
        }
    }
}