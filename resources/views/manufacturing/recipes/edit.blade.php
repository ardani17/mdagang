@extends('layouts.dashboard')

@section('title', 'Edit Resep Produk')
@section('page-title', 'Edit Resep Produk')

@section('content')
<div x-data="editRecipe()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Edit Resep Produk</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Ubah resep dan bill of materials produk</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.recipes.index') }}" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <button @click="viewRecipe" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Lihat Detail
            </button>
            <button @click="calculateCost" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Kalkulasi Biaya
            </button>
        </div>
    </div>

    <!-- Form -->
    <form @submit.prevent="submitForm" class="space-y-4 md:space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-4 lg:space-y-6">
                <!-- Basic Information -->
                <div class="card p-4 md:p-6">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Nama Resep *</label>
                            <input type="text"
                                   x-model="form.name"
                                   class="input w-full h-12 text-base"
                                   placeholder="Contoh: Minuman Temulawak 250ml"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Kode Resep *</label>
                            <input type="text"
                                   x-model="form.code"
                                   class="input w-full h-12 text-base"
                                   placeholder="Contoh: RCP-MT250"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Kategori *</label>
                            <select x-model="form.category" class="input w-full h-12 text-base" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Minuman Herbal">Minuman Herbal</option>
                                <option value="Makanan Ringan">Makanan Ringan</option>
                                <option value="Suplemen">Suplemen</option>
                                <option value="Minuman Segar">Minuman Segar</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Unit *</label>
                            <select x-model="form.unit" class="input w-full h-12 text-base" required>
                                <option value="">Pilih Unit</option>
                                <option value="botol">Botol</option>
                                <option value="pack">Pack</option>
                                <option value="karung">Karung</option>
                                <option value="kg">Kilogram</option>
                                <option value="liter">Liter</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Batch Size *</label>
                            <input type="number"
                                   x-model="form.batch_size"
                                   class="input w-full h-12 text-base"
                                   placeholder="100"
                                   min="1"
                                   step="1"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Harga Jual per Unit *</label>
                            <input type="number"
                                   x-model="form.selling_price"
                                   class="input w-full h-12 text-base"
                                   placeholder="12000"
                                   min="0"
                                   step="100"
                                   required>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-6">
                        <label class="block text-sm font-medium text-foreground mb-2">Deskripsi</label>
                        <textarea x-model="form.description"
                                  class="input w-full text-base min-h-[80px]"
                                  rows="3"
                                  placeholder="Deskripsi resep produk (opsional)"></textarea>
                    </div>
                </div>

                <!-- Ingredients -->
                <div class="card p-4 md:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight">Daftar Bahan</h3>
                        <button type="button" @click="addIngredient" class="btn btn-sm btn-primary">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Tambah Bahan
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(ingredient, index) in form.ingredients" :key="index">
                            <div class="p-4 border border-border rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Nama Bahan *</label>
                                        <input type="text"
                                               x-model="ingredient.name"
                                               class="input w-full"
                                               placeholder="Nama bahan"
                                               required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Quantity *</label>
                                        <input type="number"
                                               x-model="ingredient.quantity"
                                               @input="calculateIngredientCost(index)"
                                               class="input w-full"
                                               placeholder="0"
                                               min="0"
                                               step="0.01"
                                               required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Unit *</label>
                                        <select x-model="ingredient.unit" class="input w-full" required>
                                            <option value="">Pilih Unit</option>
                                            <option value="kg">Kilogram</option>
                                            <option value="gram">Gram</option>
                                            <option value="liter">Liter</option>
                                            <option value="ml">Mililiter</option>
                                            <option value="pcs">Pieces</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Biaya per Unit *</label>
                                        <input type="number"
                                               x-model="ingredient.cost_per_unit"
                                               @input="calculateIngredientCost(index)"
                                               class="input w-full"
                                               placeholder="0"
                                               min="0"
                                               step="1"
                                               required>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Supplier</label>
                                        <input type="text"
                                               x-model="ingredient.supplier"
                                               class="input w-full"
                                               placeholder="Nama supplier (opsional)">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Total Biaya</label>
                                        <div class="flex items-center justify-between">
                                            <span class="text-base font-medium text-foreground" x-text="formatCurrency(ingredient.total_cost || 0)">Rp 0</span>
                                            <button type="button" @click="removeIngredient(index)" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-foreground mb-2">Catatan</label>
                                    <textarea x-model="ingredient.notes"
                                              class="input w-full text-sm"
                                              rows="2"
                                              placeholder="Catatan khusus untuk bahan ini (opsional)"></textarea>
                                </div>
                            </div>
                        </template>

                        <div x-show="form.ingredients.length === 0" class="text-center py-8 text-muted border-2 border-dashed border-border rounded-lg">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <p>Belum ada bahan yang ditambahkan</p>
                            <button type="button" @click="addIngredient" class="btn btn-sm btn-primary mt-2">
                                Tambah Bahan Pertama
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cost Breakdown -->
                <div class="card p-4 md:p-6">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Breakdown Biaya</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Biaya Tenaga Kerja</label>
                            <input type="number"
                                   x-model="form.labor_cost"
                                   @input="calculateTotalCost"
                                   class="input w-full"
                                   placeholder="0"
                                   min="0"
                                   step="1000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Biaya Overhead</label>
                            <input type="number"
                                   x-model="form.overhead_cost"
                                   @input="calculateTotalCost"
                                   class="input w-full"
                                   placeholder="0"
                                   min="0"
                                   step="1000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted mb-2">Total Biaya Bahan</label>
                            <div class="input w-full bg-border/30 flex items-center" x-text="formatCurrency(form.ingredient_cost)">Rp 0</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted mb-2">Total Biaya Produksi</label>
                            <div class="input w-full bg-border/30 flex items-center font-semibold" x-text="formatCurrency(form.total_cost)">Rp 0</div>
                        </div>
                    </div>
                </div>

                <!-- Production Instructions -->
                <div class="card p-4 md:p-6">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Instruksi Produksi</h3>
                    <textarea x-model="form.instructions"
                              class="input w-full text-base min-h-[120px]"
                              rows="5"
                              placeholder="Tulis instruksi produksi step-by-step..."></textarea>
                </div>
            </div>

            <!-- Sidebar Information -->
            <div class="space-y-4 lg:space-y-6">
                <!-- Cost Summary -->
                <div class="card p-4 md:p-6">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Ringkasan Biaya</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-muted">Biaya per Unit</span>
                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(form.cost_per_unit)">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-muted">Harga Jual</span>
                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(form.selling_price)">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-muted">Profit per Unit</span>
                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(form.profit_per_unit)">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-muted">Profit Margin</span>
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                  :class="getMarginColor(form.profit_margin)"
                                  x-text="form.profit_margin + '%'">0%</span>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="card p-4 md:p-6">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Status</h3>
                    <div class="space-y-4">
                        <div class="pt-2">
                            <label class="flex items-center">
                                <input type="checkbox"
                                       x-model="form.is_active"
                                       class="w-5 h-5 rounded border-border text-primary focus:ring-primary touch-manipulation">
                                <span class="ml-3 text-base text-foreground">Resep Aktif</span>
                            </label>
                            <p class="text-xs text-muted mt-1 ml-8">Resep yang tidak aktif tidak dapat digunakan untuk produksi</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card p-4 md:p-6">
                    <div class="space-y-3">
                        <button type="submit"
                                class="btn btn-primary w-full h-12 text-base touch-manipulation"
                                :disabled="loading">
                            <span x-show="!loading">Update Resep</span>
                            <span x-show="loading" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                        <button type="button"
                                @click="resetForm"
                                class="btn btn-outline w-full h-12 text-base touch-manipulation">
                            Reset Form
                        </button>
                        <button type="button"
                                @click="deleteRecipe"
                                class="btn btn-danger w-full h-12 text-base touch-manipulation">
                            Hapus Resep
                        </button>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card p-4 md:p-6">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Aksi Cepat</h3>
                    <div class="space-y-2">
                        <button type="button" @click="calculateCost" class="btn btn-sm btn-outline w-full">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Kalkulasi Ulang
                        </button>
                        <button type="button" @click="duplicateRecipe" class="btn btn-sm btn-outline w-full">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Duplikasi Resep
                        </button>
                        <button type="button" @click="createProductionOrder" class="btn btn-sm btn-outline w-full">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Buat Order Produksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function editRecipe() {
    return {
        loading: false,
        originalData: {},
        form: {
            id: {{ $id ?? 1 }}, // This would come from the route parameter
            name: '',
            code: '',
            category: '',
            batch_size: 100,
            unit: '',
            description: '',
            instructions: '',
            selling_price: 0,
            labor_cost: 0,
            overhead_cost: 0,
            ingredient_cost: 0,
            total_cost: 0,
            cost_per_unit: 0,
            profit_per_unit: 0,
            profit_margin: 0,
            is_active: true,
            ingredients: []
        },

        async init() {
            await this.loadData();
        },

        async loadData() {
            try {
                const response = await fetch(`/api/recipes/${this.form.id}`);
                const result = await response.json();
                
                if (result.success) {
                    this.form = { ...this.form, ...result.data };
                    this.originalData = { ...this.form };
                    this.calculateTotalCost();
                } else {
                    alert('Gagal memuat data: ' + result.message);
                }
            } catch (error) {
                console.error('Error loading data:', error);
                alert('Terjadi kesalahan saat memuat data');
            }
        },

        async submitForm() {
            this.loading = true;
            
            try {
                const response = await fetch(`/api/recipes/${this.form.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify(this.form)
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('Resep berhasil diperbarui!');
                    this.originalData = { ...this.form };
                } else {
                    alert('Gagal memperbarui resep: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
            } finally {
                this.loading = false;
            }
        },

        addIngredient() {
            this.form.ingredients.push({
                name: '',
                quantity: 0,
                unit: '',
                cost_per_unit: 0,
                total_cost: 0,
                supplier: '',
                notes: ''
            });
        },

        removeIngredient(index) {
            this.form.ingredients.splice(index, 1);
            this.calculateTotalCost();
        },

        calculateIngredientCost(index) {
            const ingredient = this.form.ingredients[index];
            ingredient.total_cost = ingredient.quantity * ingredient.cost_per_unit;
            this.calculateTotalCost();
        },

        calculateTotalCost() {
            // Calculate ingredient cost
            this.form.ingredient_cost = this.form.ingredients.reduce((sum, ingredient) => {
                return sum + (ingredient.total_cost || 0);
            }, 0);

            // Calculate total cost
            this.form.total_cost = this.form.ingredient_cost + (this.form.labor_cost || 0) + (this.form.overhead_cost || 0);

            // Calculate cost per unit
            this.form.cost_per_unit = this.form.batch_size > 0 ? this.form.total_cost / this.form.batch_size : 0;

            // Calculate profit
            this.form.profit_per_unit = (this.form.selling_price || 0) - this.form.cost_per_unit;

            // Calculate profit margin
            this.form.profit_margin = this.form.selling_price > 0 
                ? Math.round((this.form.profit_per_unit / this.form.selling_price) * 100) 
                : 0;
        },

        getMarginColor(margin) {
            if (margin >= 30) return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
            if (margin >= 20) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400';
            return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400';
        },

        resetForm() {
            if (confirm('Apakah Anda yakin ingin mereset form ke data asli?')) {
                this.form = { ...this.originalData };
                this.calculateTotalCost();
            }
        },

        async deleteRecipe() {
            if (!confirm('Apakah Anda yakin ingin menghapus resep ini? Tindakan ini tidak dapat dibatalkan.')) {
                return;
            }

            try {
                const response = await fetch(`/api/recipes/${this.form.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('Resep berhasil dihapus!');
                    window.location.href = '/recipes';
                } else {
                    alert('Gagal menghapus resep: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus data');
            }
        },

        viewRecipe() {
            window.location.href = `/recipes/${this.form.id}`;
        },

        calculateCost() {
            window.location.href = `/recipes/${this.form.id}/cost-calculation`;
        },

        duplicateRecipe() {
            if (confirm('Apakah Anda yakin ingin menduplikasi resep ini?')) {
                window.location.href = `/recipes/create?duplicate=${this.form.id}`;
            }
        },

        createProductionOrder() {
            window.location.href = `/production/orders/create?recipe=${this.form.id}`;
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