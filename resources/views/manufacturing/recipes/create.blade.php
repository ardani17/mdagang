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
                    <label class="block text-sm font-medium text-foreground mb-2">Nama Produk *</label>
                    <input type="text" x-model="recipe.product_name" class="input w-full h-12 text-base" required placeholder="Contoh: Minuman Temulawak 250ml">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">SKU Produk *</label>
                    <input type="text" x-model="recipe.product_sku" class="input w-full h-12 text-base" required placeholder="Contoh: TMW-250">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Kategori *</label>
                    <select x-model="recipe.category" class="input w-full h-12 text-base" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Minuman Herbal">Minuman Herbal</option>
                        <option value="Makanan Ringan">Makanan Ringan</option>
                        <option value="Suplemen">Suplemen</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Batch Size *</label>
                    <div class="flex gap-2">
                        <input type="number" x-model="recipe.batch_size" class="input flex-1 h-12 text-base" required placeholder="100">
                        <select x-model="recipe.batch_unit" class="input w-20 md:w-24 h-12 text-base">
                            <option value="pcs">pcs</option>
                            <option value="liter">liter</option>
                            <option value="kg">kg</option>
                            <option value="gram">gram</option>
                        </select>
                    </div>
                    <p class="text-xs text-muted mt-1">Jumlah produk yang dihasilkan per batch</p>
                </div>
            </div>
            
            <div class="mt-4 md:mt-6">
                <label class="block text-sm font-medium text-foreground mb-2">Deskripsi</label>
                <textarea x-model="recipe.description" class="input w-full text-base min-h-[80px]" rows="3" placeholder="Deskripsi produk dan catatan khusus"></textarea>
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
                            <select x-model="ingredient.material_id" @change="updateIngredientCost(index)" class="input w-full h-12 text-base" required>
                                <option value="">Pilih Bahan Baku</option>
                                <template x-for="material in rawMaterials" :key="material.id">
                                    <option :value="material.id" x-text="material.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Jumlah *</label>
                            <input type="number"
                                   x-model="ingredient.quantity"
                                   @input="updateIngredientCost(index)"
                                   class="input w-full h-12 text-base"
                                   step="0.01"
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
                                   @input="updateIngredientCost(index)"
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

        <!-- Production Process -->
        <div class="card p-4 md:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight">Proses Produksi</h3>
                <button type="button" @click="addProcessStep" class="btn btn-outline btn-sm h-10 touch-manipulation">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Langkah
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(step, index) in recipe.process_steps" :key="index">
                    <div class="flex flex-col sm:flex-row gap-4 p-4 border border-border rounded-lg">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-blue-600" x-text="index + 1"></span>
                        </div>
                        <div class="flex-1 space-y-3">
                            <input type="text"
                                   x-model="step.title"
                                   class="input w-full h-12 text-base"
                                   placeholder="Judul langkah (contoh: Pencampuran bahan)"
                                   required>
                            <textarea x-model="step.description"
                                      class="input w-full text-base min-h-[80px]"
                                      rows="2"
                                      placeholder="Deskripsi detail langkah produksi"
                                      required></textarea>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-muted mb-1">Durasi (menit)</label>
                                    <input type="number" x-model="step.duration" class="input w-full h-10 text-base" placeholder="30">
                                </div>
                                <div>
                                    <label class="block text-xs text-muted mb-1">Suhu (°C)</label>
                                    <input type="number" x-model="step.temperature" class="input w-full h-10 text-base" placeholder="80">
                                </div>
                            </div>
                        </div>
                        <button type="button" @click="removeProcessStep(index)" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg touch-manipulation self-start">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </template>
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

        <!-- Quality Control -->
        <div class="card p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Quality Control</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Shelf Life (hari)</label>
                    <input type="number" x-model="recipe.shelf_life" class="input w-full h-12 text-base" placeholder="30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Kondisi Penyimpanan</label>
                    <select x-model="recipe.storage_condition" class="input w-full h-12 text-base">
                        <option value="">Pilih Kondisi</option>
                        <option value="room_temperature">Suhu Ruang</option>
                        <option value="refrigerated">Didinginkan</option>
                        <option value="frozen">Dibekukan</option>
                        <option value="dry_place">Tempat Kering</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-4 md:mt-6">
                <label class="block text-sm font-medium text-foreground mb-2">Catatan Quality Control</label>
                <textarea x-model="recipe.quality_notes" class="input w-full text-base min-h-[80px]" rows="3" placeholder="Standar kualitas, parameter yang harus dicek, dll"></textarea>
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
            product_name: '',
            product_sku: '',
            category: '',
            batch_size: '',
            batch_unit: 'pcs',
            description: '',
            ingredients: [],
            process_steps: [],
            estimated_selling_price: 0,
            shelf_life: '',
            storage_condition: '',
            quality_notes: ''
        },
        rawMaterials: [],

        init() {
            this.loadRawMaterials();
        },

        async loadRawMaterials() {
            try {
                const response = await fetch('/api/raw-materials');
                const data = await response.json();
                this.rawMaterials = data.data;
            } catch (error) {
                console.error('Error loading raw materials:', error);
            }
        },

        addIngredient() {
            this.recipe.ingredients.push({
                material_id: '',
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
            const material = this.rawMaterials.find(m => m.id == ingredient.material_id);
            
            if (material) {
                ingredient.unit_cost = material.unit_cost;
                ingredient.total_cost = ingredient.quantity * ingredient.unit_cost;
            }
        },

        addProcessStep() {
            this.recipe.process_steps.push({
                title: '',
                description: '',
                duration: '',
                temperature: ''
            });
        },

        removeProcessStep(index) {
            this.recipe.process_steps.splice(index, 1);
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
                const response = await fetch('/api/recipes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        ...this.recipe,
                        status: 'active'
                    })
                });

                if (response.ok) {
                    window.location.href = '/recipes';
                } else {
                    console.error('Failed to save recipe');
                }
            } catch (error) {
                console.error('Error saving recipe:', error);
            }
        },

        async saveAsDraft() {
            try {
                const response = await fetch('/api/recipes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        ...this.recipe,
                        status: 'draft'
                    })
                });

                if (response.ok) {
                    window.location.href = '/recipes';
                } else {
                    console.error('Failed to save draft');
                }
            } catch (error) {
                console.error('Error saving draft:', error);
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