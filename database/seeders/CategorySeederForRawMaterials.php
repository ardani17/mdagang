<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\RawMaterial;

class CategorySeederForRawMaterials extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Bahan Utama',
                'slug' => 'bahan-utama',
                'description' => 'Bahan utama untuk produksi',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pemanis',
                'slug' => 'pemanis',
                'description' => 'Bahan pemanis',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Herbal',
                'slug' => 'herbal',
                'description' => 'Bahan herbal',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Kemasan',
                'slug' => 'kemasan',
                'description' => 'Bahan kemasan',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Pengawet',
                'slug' => 'pengawet',
                'description' => 'Bahan pengawet',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Pewarna',
                'slug' => 'pewarna',
                'description' => 'Bahan pewarna',
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }

        // Update existing raw materials to use category_id
        $this->updateRawMaterialCategories();
    }

    /**
     * Update existing raw materials to use category_id instead of category text
     */
    private function updateRawMaterialCategories(): void
    {
        $rawMaterials = RawMaterial::whereNotNull('category')->get();
        
        foreach ($rawMaterials as $material) {
            $category = Category::where('name', $material->category)->first();
            if ($category) {
                $material->update(['category_id' => $category->id]);
            }
        }
    }
}