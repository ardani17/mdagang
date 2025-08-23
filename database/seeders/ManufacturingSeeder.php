<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RawMaterial;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Category;
use App\Models\Supplier;

class ManufacturingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $tepungCategory = Category::where('slug', 'tepung')->first();
        $gulaCategory = Category::where('slug', 'gula-pemanis')->first();
        $minyakCategory = Category::where('slug', 'minyak-lemak')->first();
        $rotiCategory = Category::where('slug', 'roti-kue')->first();
        $snackCategory = Category::where('slug', 'snack')->first();

        // Get suppliers
        $supplier1 = Supplier::first();
        $supplier2 = Supplier::skip(1)->first();
        $supplier3 = Supplier::skip(2)->first();

        // Create Raw Materials
        $rawMaterials = [
            [
                'category_id' => $tepungCategory->id ?? 1,
                'supplier_id' => $supplier1->id ?? 1,
                'name' => 'Tepung Terigu Premium',
                'sku' => 'RM-TEP-001',
                'description' => 'Tepung terigu protein tinggi untuk roti',
                'unit' => 'kg',
                'cost_price' => 12000,
                'last_purchase_price' => 11500,
                'current_stock' => 500,
                'minimum_stock' => 100,
                'maximum_stock' => 1000,
                'location' => 'Gudang A-01',
                'is_active' => true,
            ],
            [
                'category_id' => $gulaCategory->id ?? 1,
                'supplier_id' => $supplier1->id ?? 1,
                'name' => 'Gula Pasir Halus',
                'sku' => 'RM-GUL-001',
                'description' => 'Gula pasir halus kualitas premium',
                'unit' => 'kg',
                'cost_price' => 14000,
                'last_purchase_price' => 13500,
                'current_stock' => 300,
                'minimum_stock' => 50,
                'maximum_stock' => 500,
                'location' => 'Gudang A-02',
                'is_active' => true,
            ],
            [
                'category_id' => $minyakCategory->id ?? 1,
                'supplier_id' => $supplier2->id ?? 2,
                'name' => 'Mentega Premium',
                'sku' => 'RM-MNT-001',
                'description' => 'Mentega berkualitas untuk produksi roti',
                'unit' => 'kg',
                'cost_price' => 45000,
                'last_purchase_price' => 44000,
                'current_stock' => 100,
                'minimum_stock' => 20,
                'maximum_stock' => 200,
                'location' => 'Gudang B-01',
                'is_active' => true,
            ],
            [
                'category_id' => $tepungCategory->id ?? 1,
                'supplier_id' => $supplier1->id ?? 1,
                'name' => 'Tepung Maizena',
                'sku' => 'RM-TEP-002',
                'description' => 'Tepung maizena untuk kue',
                'unit' => 'kg',
                'cost_price' => 15000,
                'last_purchase_price' => 14500,
                'current_stock' => 150,
                'minimum_stock' => 30,
                'maximum_stock' => 300,
                'location' => 'Gudang A-03',
                'is_active' => true,
            ],
            [
                'category_id' => null,
                'supplier_id' => $supplier3->id ?? 3,
                'name' => 'Telur Ayam',
                'sku' => 'RM-TLR-001',
                'description' => 'Telur ayam segar',
                'unit' => 'butir',
                'cost_price' => 2000,
                'last_purchase_price' => 1900,
                'current_stock' => 1000,
                'minimum_stock' => 200,
                'maximum_stock' => 2000,
                'location' => 'Gudang C-01',
                'is_active' => true,
            ],
            [
                'category_id' => null,
                'supplier_id' => $supplier2->id ?? 2,
                'name' => 'Susu Bubuk',
                'sku' => 'RM-SSU-001',
                'description' => 'Susu bubuk full cream',
                'unit' => 'kg',
                'cost_price' => 85000,
                'last_purchase_price' => 83000,
                'current_stock' => 50,
                'minimum_stock' => 10,
                'maximum_stock' => 100,
                'location' => 'Gudang B-02',
                'is_active' => true,
            ],
            [
                'category_id' => null,
                'supplier_id' => $supplier1->id ?? 1,
                'name' => 'Coklat Bubuk',
                'sku' => 'RM-CKL-001',
                'description' => 'Coklat bubuk premium',
                'unit' => 'kg',
                'cost_price' => 120000,
                'last_purchase_price' => 118000,
                'current_stock' => 30,
                'minimum_stock' => 5,
                'maximum_stock' => 50,
                'location' => 'Gudang A-04',
                'is_active' => true,
            ],
            [
                'category_id' => null,
                'supplier_id' => $supplier3->id ?? 3,
                'name' => 'Ragi Instant',
                'sku' => 'RM-RGI-001',
                'description' => 'Ragi instant untuk roti',
                'unit' => 'gram',
                'cost_price' => 500,
                'last_purchase_price' => 480,
                'current_stock' => 5000,
                'minimum_stock' => 1000,
                'maximum_stock' => 10000,
                'location' => 'Gudang C-02',
                'is_active' => true,
            ],
        ];

        foreach ($rawMaterials as $material) {
            RawMaterial::create($material);
        }

        // Create Products
        $products = [
            [
                'category_id' => $rotiCategory->id ?? 1,
                'name' => 'Roti Tawar Premium',
                'sku' => 'PRD-RTI-001',
                'description' => 'Roti tawar lembut dan bergizi',
                'unit' => 'loaf',
                'cost_price' => 15000,
                'selling_price' => 25000,
                'current_stock' => 100,
                'minimum_stock' => 20,
                'maximum_stock' => 200,
                'is_active' => true,
                'notes' => 'Best seller product',
            ],
            [
                'category_id' => $rotiCategory->id ?? 1,
                'name' => 'Roti Coklat',
                'sku' => 'PRD-RTI-002',
                'description' => 'Roti dengan isian coklat premium',
                'unit' => 'pcs',
                'cost_price' => 8000,
                'selling_price' => 15000,
                'current_stock' => 150,
                'minimum_stock' => 30,
                'maximum_stock' => 300,
                'is_active' => true,
                'notes' => 'Favorit anak-anak',
            ],
            [
                'category_id' => $snackCategory->id ?? 2,
                'name' => 'Donat Gula',
                'sku' => 'PRD-DNT-001',
                'description' => 'Donat lembut dengan taburan gula',
                'unit' => 'pcs',
                'cost_price' => 5000,
                'selling_price' => 10000,
                'current_stock' => 80,
                'minimum_stock' => 20,
                'maximum_stock' => 150,
                'is_active' => true,
                'notes' => 'Fresh daily',
            ],
            [
                'category_id' => $rotiCategory->id ?? 1,
                'name' => 'Croissant Butter',
                'sku' => 'PRD-CRS-001',
                'description' => 'Croissant dengan butter premium',
                'unit' => 'pcs',
                'cost_price' => 12000,
                'selling_price' => 22000,
                'current_stock' => 50,
                'minimum_stock' => 10,
                'maximum_stock' => 100,
                'is_active' => true,
                'notes' => 'Premium product',
            ],
            [
                'category_id' => $snackCategory->id ?? 2,
                'name' => 'Brownies Coklat',
                'sku' => 'PRD-BRW-001',
                'description' => 'Brownies coklat lembut dan lezat',
                'unit' => 'box',
                'cost_price' => 25000,
                'selling_price' => 45000,
                'current_stock' => 40,
                'minimum_stock' => 10,
                'maximum_stock' => 80,
                'is_active' => true,
                'notes' => 'Cocok untuk gift',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Create Recipes
        $rotiTawar = Product::where('sku', 'PRD-RTI-001')->first();
        $rotiCoklat = Product::where('sku', 'PRD-RTI-002')->first();
        $donat = Product::where('sku', 'PRD-DNT-001')->first();

        $tepungTerigu = RawMaterial::where('sku', 'RM-TEP-001')->first();
        $gulaPasir = RawMaterial::where('sku', 'RM-GUL-001')->first();
        $mentega = RawMaterial::where('sku', 'RM-MNT-001')->first();
        $telur = RawMaterial::where('sku', 'RM-TLR-001')->first();
        $susu = RawMaterial::where('sku', 'RM-SSU-001')->first();
        $coklat = RawMaterial::where('sku', 'RM-CKL-001')->first();
        $ragi = RawMaterial::where('sku', 'RM-RGI-001')->first();

        // Recipe for Roti Tawar
        if ($rotiTawar) {
            $recipe1 = Recipe::create([
                'product_id' => $rotiTawar->id,
                'name' => 'Resep Roti Tawar Premium',
                'code' => 'RCP-001',
                'description' => 'Resep standar untuk roti tawar premium',
                'yield_quantity' => 1,
                'production_time' => 180, // 3 hours
                'instructions' => '1. Campur bahan kering\n2. Tambahkan bahan basah\n3. Uleni hingga kalis\n4. Fermentasi 1 jam\n5. Panggang 30 menit',
                'is_active' => true,
            ]);

            // Add ingredients
            if ($tepungTerigu) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe1->id,
                    'raw_material_id' => $tepungTerigu->id,
                    'quantity' => 0.5,
                    'unit' => 'kg',
                ]);
            }
            if ($gulaPasir) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe1->id,
                    'raw_material_id' => $gulaPasir->id,
                    'quantity' => 0.05,
                    'unit' => 'kg',
                ]);
            }
            if ($mentega) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe1->id,
                    'raw_material_id' => $mentega->id,
                    'quantity' => 0.03,
                    'unit' => 'kg',
                ]);
            }
            if ($ragi) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe1->id,
                    'raw_material_id' => $ragi->id,
                    'quantity' => 10,
                    'unit' => 'gram',
                ]);
            }
        }

        // Recipe for Roti Coklat
        if ($rotiCoklat) {
            $recipe2 = Recipe::create([
                'product_id' => $rotiCoklat->id,
                'name' => 'Resep Roti Coklat',
                'code' => 'RCP-002',
                'description' => 'Resep roti dengan isian coklat',
                'yield_quantity' => 1,
                'production_time' => 120, // 2 hours
                'instructions' => '1. Buat adonan roti\n2. Buat isian coklat\n3. Bentuk dan isi dengan coklat\n4. Fermentasi\n5. Panggang',
                'is_active' => true,
            ]);

            // Add ingredients
            if ($tepungTerigu) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe2->id,
                    'raw_material_id' => $tepungTerigu->id,
                    'quantity' => 0.2,
                    'unit' => 'kg',
                ]);
            }
            if ($coklat) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe2->id,
                    'raw_material_id' => $coklat->id,
                    'quantity' => 0.05,
                    'unit' => 'kg',
                ]);
            }
            if ($gulaPasir) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe2->id,
                    'raw_material_id' => $gulaPasir->id,
                    'quantity' => 0.03,
                    'unit' => 'kg',
                ]);
            }
        }

        // Recipe for Donat
        if ($donat) {
            $recipe3 = Recipe::create([
                'product_id' => $donat->id,
                'name' => 'Resep Donat Gula',
                'code' => 'RCP-003',
                'description' => 'Resep donat empuk dengan taburan gula',
                'yield_quantity' => 1,
                'production_time' => 90, // 1.5 hours
                'instructions' => '1. Buat adonan donat\n2. Fermentasi\n3. Bentuk donat\n4. Goreng hingga keemasan\n5. Taburi gula',
                'is_active' => true,
            ]);

            // Add ingredients
            if ($tepungTerigu) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe3->id,
                    'raw_material_id' => $tepungTerigu->id,
                    'quantity' => 0.15,
                    'unit' => 'kg',
                ]);
            }
            if ($gulaPasir) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe3->id,
                    'raw_material_id' => $gulaPasir->id,
                    'quantity' => 0.05,
                    'unit' => 'kg',
                ]);
            }
            if ($telur) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe3->id,
                    'raw_material_id' => $telur->id,
                    'quantity' => 1,
                    'unit' => 'butir',
                ]);
            }
        }

        $this->command->info('Manufacturing data (Raw Materials, Products, Recipes) seeded successfully!');
    }
}