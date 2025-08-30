@extends('layouts.dashboard')

@section('title', 'Buat Resep Produk')
@section('page-title', 'Buat Resep Produk Baru')

@section('content')
<div x-data="recipeCreate()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Buat Resep Produk</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Buat Bill of Materials (BOM) untuk produk baru</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.recipes.index') }}"
               class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Resep
            </a>
            <button @click="saveAsDraft" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Simpan Draft
            </button>
            <button @click="saveRecipe" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Simpan Resep
            </button>
        </div>
    </div>

    <form @submit.prevent="saveRecipe" class="space-y-4 md:space-y-6">
        <!-- Basic Information -->
        <div class="card p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Dasar</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Nama Resep *</label>
                    <input type="text" x-model="recipe.name" class="input w-full h-12 text-base" required placeholder="Contoh: Resep Minuman Temulawak">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Kode Resep *</label>
                    <input type="text" x-model="recipe.code" class="input w-full h-12 text-base" required placeholder="Contoh: RES-TMW-001">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Produk *</label>
                    <select x-model="recipe.product_id" class="input w-full h-12 text-base" required>
                        <option value="">Pilih Produk</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Batch Size *</label>
                    <div class="flex gap-2">
                        <input type="number" x-model="recipe.batch_size" class="input flex-1 h-12 text-base" required placeholder="100" min="1">
                        <select x-model="recipe.unit" class="input w-20 md:w-24 h-12 text-base">
                            <option value="pcs">pcs</option>
                            <option value="liter">liter</option>
                            <option value="kg">kg</option>
                            <option value="gram">gram</option>
                            <option value="ml">ml</option>
                        </select>
                    </div>
                    <p class="text-xs text-muted mt-1">Jumlah produk yang dihasilkan per batch</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Waktu Produksi (menit) *</label>
                    <input type="number" x-model="recipe.production_time" class="input w-full h-12 text-base" required placeholder="60" min="1">
                </div>
            </div>
            
            <div class="mt-4 md:mt-6">
                <label class="block text-sm font-medium text-foreground mb-2">Deskripsi</label>
                <textarea x-model="recipe.description" class="input w-full text-base min-h-[80px]" rows="3" placeholder="Deskripsi resep dan catatan khusus"></textarea>
            </div>

            <div class="mt-4 md:mt-6">
                <label class="block text-sm font-medium text-foreground mb-2">Instruksi</label>
                <textarea x-model="recipe.instructions" class="input w-full text-base min-h-[80px]" rows="3" placeholder="Instruksi pembuatan produk"></textarea>
            </div>

            <div class="mt-4 md:mt-6">
                <label class="block text-sm font-medium text-foreground mb-2">Catatan</label>
                <textarea x-model="recipe.notes" class="input w-full text-base min-h-[80px]" rows="3" placeholder="Catatan tambahan"></textarea>
            </div>
        </div>

        <!-- Recipe Ingredients -->
        <div class="card p-4 md:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight">Bahan Baku</h3>
                <button type="button" @click="addIngredient" class="btn btn-outline btn-sm h-10 touch-manipulation">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Bahan
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(ingredient, index) in recipe.ingredients" :key="index">
                    <div class="grid grid-cols-1 gap-4 p-4 border border-border rounded-lg md:grid-cols-6 md:gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-foreground mb-2">Bahan Baku *</label>
                            <select x-model="ingredient.raw_material_id" @change="updateIngredientCost(index)" class="input w-full h-12 text-base" required>
                                <option value="">Pilih Bahan Baku</option>
                                @foreach($rawMaterials as $material)
                                <option value="{{ $material->id }}">{{ $material->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Jumlah *</label>
                            <input type="number"
                                   x-model="ingredient.quantity"
                                   @input="updateIngredientCost(index)"
                                   class="input w-full h-12 text-base"
                                   step="0.01"
                                   min="0"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Unit *</label>
                            <select x-model="ingredient.unit" class="input w-full h-12 text-base" required>
                                <option value="gram">gram</option>
                                <option value="kg">kg</option>
                                <option value="ml">ml</option>
                                <option value="liter">liter</option>
                                <option value="pcs">pcs</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Biaya per Unit</label>
                            <input type="number"
                                   x-model="ingredient.unit_cost"
                                   class="input w-full h-12 text-base bg-border/30"
                                   step="0.01"
                                   readonly>
                        </div>
                        <div class="flex flex-col justify-between">
                            <label class="block text-sm font-medium text-foreground mb-2">Total Biaya</label>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-sm font-semibold text-foreground flex-1" x-text="formatCurrency(ingredient.total_cost)"></span>
                                <button type="button" @click="removeIngredient(index)" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg touch-manipulation">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <div x-show="recipe.ingredients.length === 0" class="text-center py-8 border-2 border-dashed border-border rounded-lg">
                    <svg class="mx-auto h-12 w-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mt-2 text-sm md:text-base font-medium text-foreground leading-tight">Belum ada bahan baku</h3>
                    <p class="mt-1 text-xs md:text-sm text-muted leading-relaxed">Tambahkan bahan baku untuk membuat resep</p>
                    <button type="button" @click="addIngredient" class="mt-4 btn btn-primary btn-sm h-10 touch-manipulation">
                        Tambah Bahan Pertama
                    </button>
                </div>
            </div>
        </div>

        <!-- Cost Summary -->
        <div class="card p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Ringkasan Biaya</h3>
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 lg:gap-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                    <p class="text-xs md:text-sm text-muted leading-tight">Total Biaya Bahan</p>
                    <p class="text-lg md:text-xl font-bold text-foreground leading-none" x-text="formatCurrency(totalMaterialCost)"></p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                    <p class="text-xs md:text-sm text-muted leading-tight">Biaya per Unit</p>
                    <p class="text-lg md:text-xl font-bold text-foreground leading-none" x-text="formatCurrency(costPerUnit)"></p>
                </div>
                <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg">
                    <p class="text-xs md:text-sm text-muted leading-tight">Estimasi Harga Jual</p>
                    <input type="number"
                           x-model="recipe.estimated_selling_price"
                           class="input w-full h-10 text-base mt-1"
                           placeholder="0"
                           @input="calculateMargin">
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                    <p class="text-xs md:text-sm text-muted leading-tight">Margin Keuntungan</p>
                    <p class="text-lg md:text-xl font-bold text-foreground leading-none" x-text="profitMargin + '%'"></p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function recipeCreate() {
    return {
        recipe: {
            name: '',
            code: '',
            description: '',
            product_id: '',
            batch_size: 1,
            unit: 'pcs',
            production_time: 60,
            instructions: '',
            notes: '',
            ingredients: [],
            estimated_selling_price: 0
        },
        rawMaterials: @json($rawMaterials),

        addIngredient() {
            this.recipe.ingredients.push({
                raw_material_id: '',
                quantity: 0,
                unit: 'gram',
                unit_cost: 0,
                total_cost: 0
            });
        },

        removeIngredient(index) {
            this.recipe.ingredients.splice(index, 1);
        },

        updateIngredientCost(index) {
            const ingredient = this.recipe.ingredients[index];
            const material = this.rawMaterials.find(m => m.id == ingredient.raw_material_id);
            
            if (material) {
                ingredient.unit_cost = material.unit_cost;
                ingredient.total_cost = ingredient.quantity * ingredient.unit_cost;
            }
        },

        get totalMaterialCost() {
            return this.recipe.ingredients.reduce((sum, ingredient) => sum + ingredient.total_cost, 0);
        },

        get costPerUnit() {
            if (this.recipe.batch_size <= 0) return 0;
            return this.totalMaterialCost / this.recipe.batch_size;
        },

        get profitMargin() {
            if (this.recipe.estimated_selling_price <= 0 || this.costPerUnit <= 0) return 0;
            return Math.round(((this.recipe.estimated_selling_price - this.costPerUnit) / this.recipe.estimated_selling_price) * 100);
        },

        calculateMargin() {
            // This will trigger the computed property update
        },

        async saveRecipe() {
            try {
                // Prepare data for API
                const formData = {
                    name: this.recipe.name,
                    code: this.recipe.code,
                    description: this.recipe.description,
                    product_id: this.recipe.product_id,
                    batch_size: this.recipe.batch_size,
                    unit: this.recipe.unit,
                    production_time: this.recipe.production_time,
                    instructions: this.recipe.instructions,
                    notes: this.recipe.notes,
                    ingredients: this.recipe.ingredients.map(ing => ({
                        raw_material_id: ing.raw_material_id,
                        quantity: ing.quantity,
                        unit: ing.unit
                    }))
                };

                const response = await fetch('/api/recipes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok) {
                    window.location.href = '/manufacturing/recipes';
                } else {
                    console.error('Failed to save recipe:', data);
                    alert('Gagal menyimpan resep: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                console.error('Error saving recipe:', error);
                alert('Terjadi kesalahan: ' + error.message);
            }
        },

        async saveAsDraft() {
            try {
                const formData = {
                    ...this.recipe,
                    is_active: false,
                    ingredients: this.recipe.ingredients.map(ing => ({
                        raw_material_id: ing.raw_material_id,
                        quantity: ing.quantity,
                        unit: ing.unit
                    }))
                };

                const response = await fetch('/api/recipes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok) {
                    window.location.href = '/manufacturing/recipes';
                } else {
                    console.error('Failed to save draft:', data);
                    alert('Gagal menyimpan draft: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                console.error('Error saving draft:', error);
                alert('Terjadi kesalahan: ' + error.message);
            }
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }
    }
}
</script>
@endpush