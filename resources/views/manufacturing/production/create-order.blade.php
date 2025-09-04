@extends('layouts.dashboard')

@section('title', 'Buat Order Produksi')
@section('page-title', 'Buat Order Produksi Baru')

@section('content')
<div x-data="createProductionOrder()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Buat Order Produksi</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Buat order produksi berdasarkan resep yang tersedia</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.production.orders') }}"
               class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Orders
            </a>
            <button @click="saveAsDraft" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Simpan Draft
            </button>
            <button @click="createOrder" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Buat Order
            </button>
        </div>
    </div>

    <form @submit.prevent="createOrder" class="space-y-4 md:space-y-6">
        <!-- Basic Information -->
        <div class="card p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Order</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Nomor Order *</label>
                    <input type="text" x-model="order.order_number" class="input w-full h-12 text-base bg-border/30" required readonly>
                    <p class="text-xs text-muted mt-1">Nomor akan digenerate otomatis</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Tanggal Order *</label>
                    <input type="date" x-model="order.order_date" class="input w-full h-12 text-base" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Target Selesai *</label>
                    <input type="date" x-model="order.target_date" class="input w-full h-12 text-base" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Prioritas</label>
                    <select x-model="order.priority" class="input w-full h-12 text-base">
                        <option value="normal">Normal</option>
                        <option value="high">Tinggi</option>
                        <option value="urgent">Mendesak</option>
                        <option value="low">Rendah</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-4 md:mt-6">
                <label class="block text-sm font-medium text-foreground mb-2">Catatan Order</label>
                <textarea x-model="order.notes" class="input w-full text-base min-h-[80px]" rows="3" placeholder="Catatan khusus untuk order ini"></textarea>
            </div>
        </div>

        <!-- Product Selection -->
        <div class="card p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Pilih Produk</h3>
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 lg:gap-6">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Resep Produk *</label>
                    <select x-model="order.recipe_id" @change="loadRecipeDetails" class="input w-full h-12 text-base" required>
                        <option value="">Pilih Resep</option>
                        <template x-for="recipe in recipes" :key="recipe.id">
                            <option :value="recipe.id" x-text="recipe.product.name + ' (' + recipe.product.sku + ')'"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Jumlah Batch *</label>
                    <input type="number" x-model="order.batch_count" @input="calculateTotals" class="input w-full h-12 text-base" required min="1" placeholder="1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Total Produksi</label>
                    <div class="flex gap-2">
                        <input type="number" :value="totalProduction" class="input bg-border/30 flex-1 h-12 text-base" readonly>
                        <span class="input bg-border/30 text-muted w-16 md:w-20 h-12 flex items-center justify-center text-base" x-text="selectedRecipe?.batch_unit || 'pcs'"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recipe Details -->
        <div x-show="selectedRecipe" class="card p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Detail Resep</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                <div>
                    <h4 class="text-md font-medium text-foreground mb-3">Informasi Produk</h4>
                    <div class="space-y-3">
                        <div class="flex flex-col sm:flex-row sm:justify-between gap-1">
                            <span class="text-sm text-muted">Nama Produk:</span>
                            <span class="text-sm text-foreground font-medium" x-text="selectedRecipe?.product_name"></span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between gap-1">
                            <span class="text-sm text-muted">SKU:</span>
                            <span class="text-sm text-foreground font-medium" x-text="selectedRecipe?.product_sku"></span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between gap-1">
                            <span class="text-sm text-muted">Kategori:</span>
                            <span class="text-sm text-foreground font-medium" x-text="selectedRecipe?.category"></span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between gap-1">
                            <span class="text-sm text-muted">Batch Size:</span>
                            <span class="text-sm text-foreground font-medium" x-text="selectedRecipe?.batch_size + ' ' + selectedRecipe?.batch_unit"></span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="text-md font-medium text-foreground mb-3">Estimasi Biaya</h4>
                    <div class="space-y-3">
                        <div class="flex flex-col sm:flex-row sm:justify-between gap-1">
                            <span class="text-sm text-muted">Biaya per Batch:</span>
                            <span class="text-sm text-foreground font-medium" x-text="formatCurrency(selectedRecipe?.cost_per_batch || 0)"></span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between gap-1">
                            <span class="text-sm text-muted">Total Biaya:</span>
                            <span class="text-sm font-semibold text-foreground" x-text="formatCurrency(totalCost)"></span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between gap-1">
                            <span class="text-sm text-muted">Estimasi Durasi:</span>
                            <span class="text-sm text-foreground font-medium" x-text="estimatedDuration + ' jam'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Material Requirements -->
        <div x-show="selectedRecipe && selectedRecipe.ingredients" class="card">
            <div class="p-4 md:p-6 border-b border-border">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight">Kebutuhan Bahan Baku</h3>
            </div>
            
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-border/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Bahan Baku</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Kebutuhan per Batch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Total Kebutuhan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Stok Tersedia</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <template x-for="ingredient in selectedRecipe?.ingredients || []" :key="ingredient.id">
                            <tr class="hover:bg-border/30">
                                <td class="px-6 py-4">
                                    <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="ingredient.material_name"></div>
                                    <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="ingredient.material_sku"></div>
                                </td>
                                <td class="px-6 py-4 text-sm text-foreground" x-text="ingredient.quantity + ' ' + ingredient.unit"></td>
                                <td class="px-6 py-4 text-sm font-semibold text-foreground" x-text="(ingredient.quantity * order.batch_count) + ' ' + ingredient.unit"></td>
                                <td class="px-6 py-4 text-sm text-foreground" x-text="ingredient.available_stock + ' ' + ingredient.unit"></td>
                                <td class="px-6 py-4">
                                    <span :class="getStockStatusColor(ingredient)"
                                          class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                          x-text="getStockStatusText(ingredient)"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden divide-y divide-border">
                <template x-for="ingredient in selectedRecipe?.ingredients || []" :key="ingredient.id">
                    <div class="p-4 space-y-3">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="ingredient.material_name"></h4>
                                <p class="text-xs md:text-sm text-muted leading-relaxed" x-text="ingredient.material_sku"></p>
                            </div>
                            <span :class="getStockStatusColor(ingredient)"
                                  class="inline-flex px-2 py-1 text-xs font-medium rounded-full ml-2"
                                  x-text="getStockStatusText(ingredient)"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-muted">Per Batch:</span>
                                <span class="text-foreground font-medium ml-1" x-text="ingredient.quantity + ' ' + ingredient.unit"></span>
                            </div>
                            <div>
                                <span class="text-muted">Total:</span>
                                <span class="text-foreground font-semibold ml-1" x-text="(ingredient.quantity * order.batch_count) + ' ' + ingredient.unit"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-muted">Stok Tersedia:</span>
                                <span class="text-foreground font-medium ml-1" x-text="ingredient.available_stock + ' ' + ingredient.unit"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Production Schedule -->
        <div class="card p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Jadwal Produksi</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Shift Produksi</label>
                    <select x-model="order.shift" class="input w-full h-12 text-base">
                        <option value="morning">Pagi (07:00 - 15:00)</option>
                        <option value="afternoon">Siang (15:00 - 23:00)</option>
                        <option value="night">Malam (23:00 - 07:00)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Operator</label>
                    <select x-model="order.operator_id" class="input w-full h-12 text-base">
                        <option value="">Pilih Operator</option>
                        <template x-for="operator in operators" :key="operator.id">
                            <option :value="operator.id" x-text="operator.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Mesin/Line Produksi</label>
                    <select x-model="order.production_line" class="input w-full h-12 text-base">
                        <option value="">Pilih Line</option>
                        <option value="line_1">Line 1 - Minuman</option>
                        <option value="line_2">Line 2 - Makanan</option>
                        <option value="line_3">Line 3 - Kemasan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Estimasi Mulai</label>
                    <input type="datetime-local" x-model="order.estimated_start" class="input w-full h-12 text-base">
                </div>
            </div>
        </div>

        <!-- Quality Control -->
        <div class="card p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Quality Control</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                <div class="pt-2">
                    <label class="flex items-center">
                        <input type="checkbox" x-model="order.qc_required" class="w-5 h-5 mr-3 rounded border-border text-primary focus:ring-primary touch-manipulation">
                        <span class="text-base text-foreground">Memerlukan QC Check</span>
                    </label>
                </div>
                <div class="pt-2">
                    <label class="flex items-center">
                        <input type="checkbox" x-model="order.batch_testing" class="w-5 h-5 mr-3 rounded border-border text-primary focus:ring-primary touch-manipulation">
                        <span class="text-base text-foreground">Testing per Batch</span>
                    </label>
                </div>
            </div>
            
            <div class="mt-4 md:mt-6">
                <label class="block text-sm font-medium text-foreground mb-2">Catatan QC</label>
                <textarea x-model="order.qc_notes" class="input w-full text-base min-h-[80px]" rows="3" placeholder="Standar kualitas dan parameter yang harus dicek"></textarea>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="card p-4 md:p-6 bg-blue-50 dark:bg-blue-900/20">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Ringkasan Order</h3>
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 lg:gap-6">
                <div class="text-center">
                    <p class="text-xs md:text-sm text-muted leading-tight">Total Batch</p>
                    <p class="text-lg md:text-xl font-bold text-foreground leading-none" x-text="order.batch_count"></p>
                </div>
                <div class="text-center">
                    <p class="text-xs md:text-sm text-muted leading-tight">Total Produksi</p>
                    <p class="text-lg md:text-xl font-bold text-foreground leading-none" x-text="totalProduction + ' ' + (selectedRecipe?.batch_unit || 'pcs')"></p>
                </div>
                <div class="text-center">
                    <p class="text-xs md:text-sm text-muted leading-tight">Estimasi Biaya</p>
                    <p class="text-lg md:text-xl font-bold text-foreground leading-none" x-text="formatCurrency(totalCost)"></p>
                </div>
                <div class="text-center">
                    <p class="text-xs md:text-sm text-muted leading-tight">Estimasi Durasi</p>
                    <p class="text-lg md:text-xl font-bold text-foreground leading-none" x-text="estimatedDuration + ' jam'"></p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function createProductionOrder() {
    return {
        order: {
            order_number: '',
            order_date: new Date().toISOString().split('T')[0],
            target_date: '',
            priority: 'normal',
            notes: '',
            recipe_id: '',
            batch_count: 1,
            shift: 'morning',
            operator_id: '',
            production_line: '',
            estimated_start: '',
            qc_required: true,
            batch_testing: false,
            qc_notes: ''
        },
        recipes: [],
        operators: [],
        selectedRecipe: null,

        init() {
            this.generateOrderNumber();
            this.loadRecipes();
            this.loadOperators();
        },

        generateOrderNumber() {
            const today = new Date();
            const year = today.getFullYear().toString().substr(-2);
            const month = (today.getMonth() + 1).toString().padStart(2, '0');
            const day = today.getDate().toString().padStart(2, '0');
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            
            this.order.order_number = `PO${year}${month}${day}${random}`;
        },

        async loadRecipes() {
            try {
                const response = await fetch('/api/recipes?status=active');
                const data = await response.json();
                this.recipes = data.data.data;
            } catch (error) {
                console.error('Error loading recipes:', error);
            }
        },

        async loadOperators() {
            try {
                // const response = await fetch('/api/operators');
                // const data = await response.json();
                // this.operators = data.data;
                this.operators = [{"id" : "1","name" : "sultan"}];
            } catch (error) {
                console.error('Error loading operators:', error);
            }
        },

        async loadRecipeDetails() {
            if (!this.order.recipe_id) {
                this.selectedRecipe = null;
                return;
            }

            try {
                const response = await fetch(`/api/recipes/${this.order.recipe_id}/details`);
                const data = await response.json();
                this.selectedRecipe = data.data;
                this.calculateTotals();
            } catch (error) {
                console.error('Error loading recipe details:', error);
            }
        },

        calculateTotals() {
            // This will trigger computed properties to update
        },

        get totalProduction() {
            if (!this.selectedRecipe || !this.order.batch_count) return 0;
            return this.selectedRecipe.batch_size * this.order.batch_count;
        },

        get totalCost() {
            if (!this.selectedRecipe || !this.order.batch_count) return 0;
            return (this.selectedRecipe.cost_per_batch || 0) * this.order.batch_count;
        },

        get estimatedDuration() {
            if (!this.selectedRecipe || !this.order.batch_count) return 0;
            const baseHours = this.selectedRecipe.estimated_duration || 4;
            return Math.ceil(baseHours * this.order.batch_count);
        },

        getStockStatusColor(ingredient) {
            const required = ingredient.quantity * this.order.batch_count;
            const available = ingredient.available_stock || 0;
            
            if (available >= required) {
                return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
            } else if (available >= required * 0.8) {
                return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400';
            } else {
                return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400';
            }
        },

        getStockStatusText(ingredient) {
            const required = ingredient.quantity * this.order.batch_count;
            const available = ingredient.available_stock || 0;
            
            if (available >= required) {
                return 'Cukup';
            } else if (available >= required * 0.8) {
                return 'Terbatas';
            } else {
                return 'Kurang';
            }
        },

        async createOrder() {
            try {
                console.log("raja" + JSON.stringify(this.order));
                const response = await fetch('/api/production/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        ...this.order,
                        status: 'pending',
                        total_production: this.totalProduction,
                        total_cost: this.totalCost,
                        estimated_duration: this.estimatedDuration
                    })
                });

                if (response.ok) {
                    window.location.href = '/manufacturing/production/orders';
                } else {
                    console.error('Failed to create production order');
                }
            } catch (error) {
                console.error('Error creating production order:', error);
            }
        },

        async saveAsDraft() {
            try {
                const response = await fetch('/api/production/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        ...this.order,
                        status: 'draft',
                        total_production: this.totalProduction,
                        total_cost: this.totalCost,
                        estimated_duration: this.estimatedDuration
                    })
                });

                if (response.ok) {
                    window.location.href = '/manufacturing/production/orders';
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