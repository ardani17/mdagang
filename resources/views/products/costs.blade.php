@extends('layouts.dashboard')

@section('title', 'Kalkulasi Biaya Produksi')
@section('page-title')
<span class="text-base lg:text-2xl">Kalkulasi Biaya Produksi</span>
@endsection

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-muted hover:text-foreground">
        <svg class="w-3 h-3 mr-2.5" fill="currentColor" viewBox="0 0 20 20">
            <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
        </svg>
        Dasbor
    </a>
</li>
<li>
    <div class="flex items-center">
        <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('products.index') }}" class="ml-1 text-sm font-medium text-muted hover:text-foreground md:ml-2">Produk</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Kalkulasi Biaya</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="costCalculator()" class="max-w-6xl mx-auto space-y-4 lg:space-y-6 p-4 lg:p-0">
    <!-- Product Info -->
    <div class="card p-4 lg:p-6">
        <div class="flex items-start lg:items-center space-x-4">
            <div class="w-16 h-16 lg:w-16 lg:h-16 bg-surface rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                <img x-show="product.image"
                     :src="product.image"
                     :alt="product.name"
                     class="w-full h-full object-cover">
                <svg x-show="!product.image" class="w-6 h-6 lg:w-8 lg:h-8 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-lg lg:text-xl font-bold text-foreground" x-text="product.name"></h2>
                <p class="text-sm text-muted">SKU: <span x-text="product.sku"></span></p>
                <div class="flex flex-col lg:flex-row lg:items-center lg:space-x-4 mt-2 space-y-1 lg:space-y-0">
                    <span class="text-sm text-muted">Harga Jual: <span class="font-medium" x-text="formatCurrency(product.selling_price)"></span></span>
                    <span class="text-sm text-muted">Stok: <span class="font-medium" x-text="product.current_stock + ' ' + product.unit"></span></span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Left Column: Input Forms -->
        <div class="lg:col-span-2 space-y-4 lg:space-y-6">
            <!-- Bahan Baku -->
            <div class="card">
                <div class="p-6 border-b border-border">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-foreground">Bahan Baku</h3>
                            <p class="text-sm text-muted">Daftar bahan yang dibutuhkan untuk produksi</p>
                        </div>
                        <button @click="addIngredient()" class="btn-primary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tambah Bahan
                        </button>
                    </div>
                </div>
                
                <div class="p-4 lg:p-6">
                    <div class="space-y-4 lg:space-y-4">
                        <template x-for="(ingredient, index) in ingredients" :key="index">
                            <!-- Desktop Layout -->
                            <div class="hidden lg:grid grid-cols-12 gap-4 items-end">
                                <div class="col-span-4">
                                    <label class="block text-sm font-medium text-foreground mb-2">Nama Bahan</label>
                                    <input type="text"
                                           x-model="ingredient.name"
                                           @input="calculateIngredientCost(index)"
                                           class="input"
                                           placeholder="Nama bahan">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-foreground mb-2">Jumlah</label>
                                    <input type="number"
                                           x-model="ingredient.quantity"
                                           @input="calculateIngredientCost(index)"
                                           class="input"
                                           placeholder="0"
                                           step="0.01">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-foreground mb-2">Satuan</label>
                                    <select x-model="ingredient.unit" class="input">
                                        <option value="kg">Kg</option>
                                        <option value="gram">Gram</option>
                                        <option value="liter">Liter</option>
                                        <option value="ml">ML</option>
                                        <option value="pcs">Pcs</option>
                                        <option value="pack">Pack</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-foreground mb-2">Harga/Unit</label>
                                    <input type="number"
                                           x-model="ingredient.cost_per_unit"
                                           @input="calculateIngredientCost(index)"
                                           class="input"
                                           placeholder="0"
                                           step="100">
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-foreground mb-2">Total</label>
                                    <div class="text-sm font-medium text-foreground py-2" x-text="formatCurrency(ingredient.total_cost)"></div>
                                </div>
                                <div class="col-span-1">
                                    <button @click="removeIngredient(index)"
                                            class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Mobile Layout -->
                            <div class="lg:hidden card p-4 space-y-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-medium text-foreground">Bahan #<span x-text="index + 1"></span></h4>
                                    <button @click="removeIngredient(index)"
                                            class="mobile-action-button p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Nama Bahan</label>
                                        <input type="text"
                                               x-model="ingredient.name"
                                               @input="calculateIngredientCost(index)"
                                               class="input"
                                               placeholder="Nama bahan">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-foreground mb-2">Jumlah</label>
                                            <input type="number"
                                                   x-model="ingredient.quantity"
                                                   @input="calculateIngredientCost(index)"
                                                   class="input"
                                                   placeholder="0"
                                                   step="0.01">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-foreground mb-2">Satuan</label>
                                            <select x-model="ingredient.unit" class="input">
                                                <option value="kg">Kg</option>
                                                <option value="gram">Gram</option>
                                                <option value="liter">Liter</option>
                                                <option value="ml">ML</option>
                                                <option value="pcs">Pcs</option>
                                                <option value="pack">Pack</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Harga per Unit</label>
                                        <input type="number"
                                               x-model="ingredient.cost_per_unit"
                                               @input="calculateIngredientCost(index)"
                                               class="input"
                                               placeholder="0"
                                               step="100">
                                    </div>
                                    <div class="bg-surface p-3 rounded-lg">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-foreground">Total Biaya:</span>
                                            <span class="text-base font-semibold text-primary" x-text="formatCurrency(ingredient.total_cost)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="ingredients.length === 0" class="text-center py-8 text-muted">
                            <svg class="w-12 h-12 mx-auto mb-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <p>Belum ada bahan baku yang ditambahkan</p>
                            <button @click="addIngredient()" class="btn-primary mt-4">Tambah Bahan Pertama</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Biaya Tenaga Kerja -->
            <div class="card">
                <div class="p-6 border-b border-border">
                    <h3 class="text-lg font-semibold text-foreground">Biaya Tenaga Kerja</h3>
                    <p class="text-sm text-muted">Biaya untuk proses produksi</p>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Jumlah Pekerja</label>
                            <input type="number" 
                                   x-model="laborCost.workers"
                                   @input="calculateLaborCost()"
                                   class="input"
                                   placeholder="1"
                                   min="1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Jam Kerja</label>
                            <input type="number" 
                                   x-model="laborCost.hours"
                                   @input="calculateLaborCost()"
                                   class="input"
                                   placeholder="8"
                                   step="0.5"
                                   min="0.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Upah per Jam</label>
                            <input type="number" 
                                   x-model="laborCost.rate_per_hour"
                                   @input="calculateLaborCost()"
                                   class="input"
                                   placeholder="15000"
                                   step="1000">
                        </div>
                    </div>
                    <div class="bg-surface p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-foreground">Total Biaya Tenaga Kerja:</span>
                            <span class="font-semibold text-foreground" x-text="formatCurrency(laborCost.total)"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Biaya Overhead -->
            <div class="card">
                <div class="p-6 border-b border-border">
                    <h3 class="text-lg font-semibold text-foreground">Biaya Overhead</h3>
                    <p class="text-sm text-muted">Biaya operasional lainnya</p>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Listrik & Utilitas</label>
                            <input type="number" 
                                   x-model="overheadCost.utilities"
                                   @input="calculateOverheadCost()"
                                   class="input"
                                   placeholder="0"
                                   step="1000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Sewa Tempat</label>
                            <input type="number" 
                                   x-model="overheadCost.rent"
                                   @input="calculateOverheadCost()"
                                   class="input"
                                   placeholder="0"
                                   step="1000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Peralatan & Maintenance</label>
                            <input type="number" 
                                   x-model="overheadCost.equipment"
                                   @input="calculateOverheadCost()"
                                   class="input"
                                   placeholder="0"
                                   step="1000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Lain-lain</label>
                            <input type="number" 
                                   x-model="overheadCost.others"
                                   @input="calculateOverheadCost()"
                                   class="input"
                                   placeholder="0"
                                   step="1000">
                        </div>
                    </div>
                    <div class="bg-surface p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-foreground">Total Biaya Overhead:</span>
                            <span class="font-semibold text-foreground" x-text="formatCurrency(overheadCost.total)"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ukuran Batch -->
            <div class="card">
                <div class="p-6 border-b border-border">
                    <h3 class="text-lg font-semibold text-foreground">Ukuran Batch Produksi</h3>
                    <p class="text-sm text-muted">Jumlah unit yang diproduksi dalam satu batch</p>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Jumlah Unit per Batch</label>
                            <input type="number" 
                                   x-model="batchSize"
                                   @input="calculateTotalCost()"
                                   class="input"
                                   placeholder="10"
                                   min="1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Satuan</label>
                            <input type="text" 
                                   :value="product.unit"
                                   class="input"
                                   readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Summary -->
        <div class="space-y-4 lg:space-y-6">
            <!-- Cost Summary -->
            <div class="card lg:sticky lg:top-6">
                <div class="p-6 border-b border-border">
                    <h3 class="text-lg font-semibold text-foreground">Ringkasan Biaya</h3>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-muted">Biaya Bahan Baku:</span>
                            <span class="font-medium" x-text="formatCurrency(totalIngredientCost)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-muted">Biaya Tenaga Kerja:</span>
                            <span class="font-medium" x-text="formatCurrency(laborCost.total)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-muted">Biaya Overhead:</span>
                            <span class="font-medium" x-text="formatCurrency(overheadCost.total)"></span>
                        </div>
                        <hr class="border-border">
                        <div class="flex justify-between items-center text-lg font-semibold">
                            <span class="text-foreground">Total Biaya Produksi:</span>
                            <span class="text-foreground" x-text="formatCurrency(totalProductionCost)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-muted">Ukuran Batch:</span>
                            <span class="font-medium" x-text="batchSize + ' ' + product.unit"></span>
                        </div>
                        <hr class="border-border">
                        <div class="flex justify-between items-center text-lg font-semibold text-primary">
                            <span>Biaya per Unit:</span>
                            <span x-text="formatCurrency(costPerUnit)"></span>
                        </div>
                    </div>

                    <!-- Profit Analysis -->
                    <div class="bg-surface p-4 rounded-lg space-y-2">
                        <h4 class="font-medium text-foreground">Analisis Keuntungan</h4>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted">Harga Jual:</span>
                            <span x-text="formatCurrency(product.selling_price)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted">Biaya Produksi:</span>
                            <span x-text="formatCurrency(costPerUnit)"></span>
                        </div>
                        <div class="flex justify-between text-sm font-medium">
                            <span :class="profitPerUnit >= 0 ? 'text-green-600' : 'text-red-600'">
                                Keuntungan per Unit:
                            </span>
                            <span :class="profitPerUnit >= 0 ? 'text-green-600' : 'text-red-600'" 
                                  x-text="formatCurrency(profitPerUnit)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted">Margin Keuntungan:</span>
                            <span :class="profitMargin >= 0 ? 'text-green-600' : 'text-red-600'" 
                                  x-text="profitMargin.toFixed(1) + '%'"></span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <button @click="saveCostCalculation()"
                                class="btn-primary w-full py-3 lg:py-2"
                                :disabled="loading">
                            <span x-show="!loading" class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                                Simpan Kalkulasi
                            </span>
                            <span x-show="loading" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                        <button @click="exportCalculation()" class="btn-secondary w-full py-3 lg:py-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Ekspor PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function costCalculator() {
    return {
        loading: false,
        product: {
            id: 1,
            name: 'Nasi Goreng Spesial',
            sku: 'NAS123456',
            selling_price: 25000,
            current_stock: 50,
            unit: 'porsi',
            image: null
        },
        ingredients: [],
        laborCost: {
            workers: 1,
            hours: 2,
            rate_per_hour: 15000,
            total: 0
        },
        overheadCost: {
            utilities: 5000,
            rent: 10000,
            equipment: 3000,
            others: 2000,
            total: 0
        },
        batchSize: 10,

        get totalIngredientCost() {
            return this.ingredients.reduce((total, ingredient) => total + (ingredient.total_cost || 0), 0);
        },

        get totalProductionCost() {
            return this.totalIngredientCost + this.laborCost.total + this.overheadCost.total;
        },

        get costPerUnit() {
            return this.batchSize > 0 ? this.totalProductionCost / this.batchSize : 0;
        },

        get profitPerUnit() {
            return this.product.selling_price - this.costPerUnit;
        },

        get profitMargin() {
            return this.product.selling_price > 0 ? (this.profitPerUnit / this.product.selling_price) * 100 : 0;
        },

        init() {
            this.calculateLaborCost();
            this.calculateOverheadCost();
            this.addIngredient(); // Add first ingredient row
        },

        addIngredient() {
            this.ingredients.push({
                name: '',
                quantity: 0,
                unit: 'kg',
                cost_per_unit: 0,
                total_cost: 0,
                supplier: '',
                notes: ''
            });
        },

        removeIngredient(index) {
            this.ingredients.splice(index, 1);
        },

        calculateIngredientCost(index) {
            const ingredient = this.ingredients[index];
            ingredient.total_cost = (ingredient.quantity || 0) * (ingredient.cost_per_unit || 0);
        },

        calculateLaborCost() {
            this.laborCost.total = (this.laborCost.workers || 0) * (this.laborCost.hours || 0) * (this.laborCost.rate_per_hour || 0);
        },

        calculateOverheadCost() {
            this.overheadCost.total = (this.overheadCost.utilities || 0) + 
                                     (this.overheadCost.rent || 0) + 
                                     (this.overheadCost.equipment || 0) + 
                                     (this.overheadCost.others || 0);
        },

        calculateTotalCost() {
            // This will trigger reactive calculations
        },

        async saveCostCalculation() {
            this.loading = true;

            try {
                const data = {
                    product_id: this.product.id,
                    ingredients: this.ingredients.filter(ing => ing.name && ing.quantity > 0),
                    labor_cost: this.laborCost,
                    overhead_cost: this.overheadCost,
                    batch_size: this.batchSize,
                    total_cost: this.totalProductionCost,
                    cost_per_unit: this.costPerUnit
                };

                const response = await fetch(`/api/products/${this.product.id}/costs`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    this.$store.notifications.add({
                        type: 'success',
                        title: 'Berhasil!',
                        message: 'Kalkulasi biaya berhasil disimpan.'
                    });
                } else {
                    throw new Error('Gagal menyimpan kalkulasi');
                }
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: error.message
                });
            } finally {
                this.loading = false;
            }
        },

        async exportCalculation() {
            try {
                const response = await fetch(`/api/products/${this.product.id}/costs/export`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `kalkulasi_biaya_${this.product.sku}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);

                    this.$store.notifications.add({
                        type: 'success',
                        title: 'Berhasil!',
                        message: 'Kalkulasi berhasil diekspor ke PDF.'
                    });
                } else {
                    throw new Error('Gagal mengekspor kalkulasi');
                }
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: error.message
                });
            }
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount || 0);
        }
    }
}
</script>
@endpush