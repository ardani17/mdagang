<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Main categories
            [
                'name' => 'Makanan',
                'slug' => 'makanan',
                'description' => 'Kategori untuk produk makanan',
                'parent_id' => null,
            ],
            [
                'name' => 'Minuman',
                'slug' => 'minuman',
                'description' => 'Kategori untuk produk minuman',
                'parent_id' => null,
            ],
            [
                'name' => 'Bahan Baku',
                'slug' => 'bahan-baku',
                'description' => 'Kategori untuk bahan baku produksi',
                'parent_id' => null,
            ],
        ];

        foreach ($categories as $category) {
            $parent = Category::create($category);

            // Add subcategories
            if ($category['slug'] === 'makanan') {
                Category::create([
                    'name' => 'Roti & Kue',
                    'slug' => 'roti-kue',
                    'description' => 'Produk roti dan kue',
                    'parent_id' => $parent->id,
                ]);
                Category::create([
                    'name' => 'Snack',
                    'slug' => 'snack',
                    'description' => 'Makanan ringan',
                    'parent_id' => $parent->id,
                ]);
                Category::create([
                    'name' => 'Makanan Berat',
                    'slug' => 'makanan-berat',
                    'description' => 'Makanan utama',
                    'parent_id' => $parent->id,
                ]);
            }

            if ($category['slug'] === 'minuman') {
                Category::create([
                    'name' => 'Jus',
                    'slug' => 'jus',
                    'description' => 'Jus buah dan sayur',
                    'parent_id' => $parent->id,
                ]);
                Category::create([
                    'name' => 'Kopi & Teh',
                    'slug' => 'kopi-teh',
                    'description' => 'Minuman kopi dan teh',
                    'parent_id' => $parent->id,
                ]);
                Category::create([
                    'name' => 'Minuman Kemasan',
                    'slug' => 'minuman-kemasan',
                    'description' => 'Minuman dalam kemasan',
                    'parent_id' => $parent->id,
                ]);
            }

            if ($category['slug'] === 'bahan-baku') {
                Category::create([
                    'name' => 'Tepung',
                    'slug' => 'tepung',
                    'description' => 'Berbagai jenis tepung',
                    'parent_id' => $parent->id,
                ]);
                Category::create([
                    'name' => 'Gula & Pemanis',
                    'slug' => 'gula-pemanis',
                    'description' => 'Gula dan pemanis lainnya',
                    'parent_id' => $parent->id,
                ]);
                Category::create([
                    'name' => 'Minyak & Lemak',
                    'slug' => 'minyak-lemak',
                    'description' => 'Minyak goreng dan lemak',
                    'parent_id' => $parent->id,
                ]);
                Category::create([
                    'name' => 'Bumbu & Rempah',
                    'slug' => 'bumbu-rempah',
                    'description' => 'Bumbu dan rempah-rempah',
                    'parent_id' => $parent->id,
                ]);
            }
        }

        $this->command->info('Categories seeded successfully!');
    }
}