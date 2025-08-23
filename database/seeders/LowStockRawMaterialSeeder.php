<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RawMaterial;
use App\Models\Supplier;

class LowStockRawMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing suppliers
        $suppliers = Supplier::all();
        
        if ($suppliers->isEmpty()) {
            $this->command->warn('No suppliers found. Please run SupplierSeeder first.');
            return;
        }

        $lowStockMaterials = [
            [
                'code' => 'RM-LOW-001',
                'name' => 'Garam Himalaya',
                'description' => 'Garam himalaya premium untuk produksi jamu',
                'category' => 'Bahan Utama',
                'unit' => 'kg',
                'current_stock' => 5.0, // Low stock
                'minimum_stock' => 20.0,
                'maximum_stock' => 100.0,
                'reorder_point' => 15.0,
                'reorder_quantity' => 50.0,
                'average_price' => 45000.00,
                'last_purchase_price' => 47000.00,
                'supplier_id' => $suppliers->random()->id,
                'status' => 'low_stock',
                'storage_location' => 'Gudang A - Rak 5',
                'lead_time_days' => 3,
                'notes' => 'Stok rendah - perlu segera dipesan',
                'is_active' => true,
            ],
            [
                'code' => 'RM-CRIT-001',
                'name' => 'Madu Murni',
                'description' => 'Madu murni untuk campuran jamu',
                'category' => 'Pemanis',
                'unit' => 'liter',
                'current_stock' => 2.0, // Critical stock
                'minimum_stock' => 15.0,
                'maximum_stock' => 80.0,
                'reorder_point' => 10.0,
                'reorder_quantity' => 30.0,
                'average_price' => 85000.00,
                'last_purchase_price' => 88000.00,
                'supplier_id' => $suppliers->random()->id,
                'status' => 'critical',
                'storage_location' => 'Gudang B - Rak 1',
                'lead_time_days' => 5,
                'notes' => 'Stok kritis - segera pesan!',
                'is_active' => true,
            ],
            [
                'code' => 'RM-LOW-002',
                'name' => 'Daun Sirih Merah',
                'description' => 'Daun sirih merah kering untuk jamu',
                'category' => 'Herbal',
                'unit' => 'kg',
                'current_stock' => 8.0, // Low stock
                'minimum_stock' => 25.0,
                'maximum_stock' => 120.0,
                'reorder_point' => 20.0,
                'reorder_quantity' => 60.0,
                'average_price' => 35000.00,
                'last_purchase_price' => 36000.00,
                'supplier_id' => $suppliers->random()->id,
                'status' => 'low_stock',
                'storage_location' => 'Gudang B - Rak 3',
                'lead_time_days' => 2,
                'notes' => 'Stok rendah - monitoring ketat',
                'is_active' => true,
            ],
            [
                'code' => 'RM-CRIT-002',
                'name' => 'Kemasan Sachet 10ml',
                'description' => 'Kemasan sachet untuk jamu instan',
                'category' => 'Kemasan',
                'unit' => 'pcs',
                'current_stock' => 150.0, // Critical stock
                'minimum_stock' => 1000.0,
                'maximum_stock' => 10000.0,
                'reorder_point' => 800.0,
                'reorder_quantity' => 5000.0,
                'average_price' => 250.00,
                'last_purchase_price' => 260.00,
                'supplier_id' => $suppliers->random()->id,
                'status' => 'critical',
                'storage_location' => 'Gudang C - Rak 2',
                'lead_time_days' => 7,
                'notes' => 'Kemasan hampir habis - urgent!',
                'is_active' => true,
            ],
            [
                'code' => 'RM-LOW-003',
                'name' => 'Ekstrak Mengkudu',
                'description' => 'Ekstrak mengkudu untuk jamu kesehatan',
                'category' => 'Herbal',
                'unit' => 'liter',
                'current_stock' => 3.5, // Low stock
                'minimum_stock' => 12.0,
                'maximum_stock' => 60.0,
                'reorder_point' => 8.0,
                'reorder_quantity' => 25.0,
                'average_price' => 125000.00,
                'last_purchase_price' => 130000.00,
                'supplier_id' => $suppliers->random()->id,
                'status' => 'low_stock',
                'storage_location' => 'Gudang A - Rak 2',
                'lead_time_days' => 4,
                'notes' => 'Ekstrak premium - stok terbatas',
                'is_active' => true,
            ],
        ];

        foreach ($lowStockMaterials as $material) {
            RawMaterial::create($material);
        }

        $this->command->info('Low stock and critical stock raw materials seeded successfully!');
        $this->command->info('Added:');
        $this->command->info('- 3 Low stock materials');
        $this->command->info('- 2 Critical stock materials');
    }
}