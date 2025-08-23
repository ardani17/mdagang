@extends('layouts.dashboard')

@section('title', 'Detail Resep Produk')
@section('page-title', 'Detail Resep Produk')

@section('content')
<div x-data="recipeDetail()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight" x-text="recipe.name">Detail Resep Produk</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Informasi lengkap resep dan bill of materials</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.recipes.index') }}" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <button @click="editRecipe" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Resep
            </button>
            <button @click="calculateCost" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Kalkulasi Biaya
            </button>
            <button @click="printRecipe" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </button>
        </div>
    </div>

    <!-- Status Alert -->
    <div x-show="!recipe.is_active" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <div>
                <h4 class="text-sm font-medium text-red-800 dark:text-red-200">Resep Tidak Aktif</h4>
                <p class="text-sm text-red-700 dark:text-red-300">Resep ini sedang dalam status tidak aktif dan tidak dapat digunakan untuk produksi.</p>
            </div>
        </div>
    </div>

    <!-- Low Margin Alert -->
    <div x-show="recipe.profit_margin < 20" class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <div>
                <h4 class="text-sm font-medium text-orange-800 dark:text-orange-200">Margin Rendah</h4>
                <p class="text-sm text-orange-700 dark:text-orange-300">Profit margin resep ini di bawah 20%. Pertimbangkan untuk meninjau harga jual atau biaya produksi.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4 lg:space-y-6">
            <!-- Basic Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Dasar</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Nama Resep</label>
                        <p class="text-base font-medium text-foreground" x-text="recipe.name">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Kode Resep</label>
                        <p class="text-base font-medium text-foreground" x-text="recipe.code">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Kategori</label>
                        <span class="inline-flex px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 rounded-full" x-text="recipe.category">-</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Batch Size</label>
                        <p class="text-base font-medium text-foreground" x-text="recipe.batch_size + ' ' + recipe.unit">-</p>
                    </div>
                </div>
                <div class="mt-4" x-show="recipe.description">
                    <label class="block text-sm font-medium text-muted mb-1">Deskripsi</label>
                    <p class="text-base text-foreground" x-text="recipe.description">-</p>
                </div>
            </div>

            <!-- Cost Breakdown -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Breakdown Biaya</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                    <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-2xl font-bold text-blue-600" x-text="formatCurrency(recipe.ingredient_cost)">Rp 0</p>
                        <p class="text-sm text-blue-600">Biaya Bahan</p>
                        <p class="text-xs text-muted mt-1">Per Batch</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(recipe.labor_cost)">Rp 0</p>
                        <p class="text-sm text-green-600">Biaya Tenaga Kerja</p>
                        <p class="text-xs text-muted mt-1">Per Batch</p>
                    </div>
                    <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                        <p class="text-2xl font-bold text-orange-600" x-text="formatCurrency(recipe.overhead_cost)">Rp 0</p>
                        <p class="text-sm text-orange-600">Biaya Overhead</p>
                        <p class="text-xs text-muted mt-1">Per Batch</p>
                    </div>
                    <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <p class="text-2xl font-bold text-purple-600" x-text="formatCurrency(recipe.total_cost)">Rp 0</p>
                        <p class="text-sm text-purple-600">Total Biaya</p>
                        <p class="text-xs text-muted mt-1">Per Batch</p>
                    </div>
                </div>
                
                <!-- Cost per Unit -->
                <div class="mt-6 p-4 bg-border/30 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-muted">Biaya per Unit:</span>
                        <span class="text-lg font-bold text-foreground" x-text="formatCurrency(recipe.cost_per_unit)">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Ingredients List -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Daftar Bahan</h3>
                
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-border/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Bahan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Unit Cost</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Total Cost</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">% of Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <template x-for="ingredient in recipe.ingredients" :key="ingredient.id">
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-foreground" x-text="ingredient.name"></div>
                                        <div class="text-xs text-muted" x-text="ingredient.supplier || 'Supplier tidak ditentukan'"></div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-foreground" x-text="ingredient.quantity + ' ' + ingredient.unit"></td>
                                    <td class="px-4 py-3 text-sm text-foreground" x-text="formatCurrency(ingredient.cost_per_unit)"></td>
                                    <td class="px-4 py-3 text-sm font-medium text-foreground" x-text="formatCurrency(ingredient.total_cost)"></td>
                                    <td class="px-4 py-3 text-sm text-foreground" x-text="getIngredientPercentage(ingredient) + '%'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden space-y-3">
                    <template x-for="ingredient in recipe.ingredients" :key="ingredient.id">
                        <div class="bg-border/30 rounded-lg p-3">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="text-sm font-medium text-foreground" x-text="ingredient.name"></h4>
                                    <p class="text-xs text-muted" x-text="ingredient.supplier || 'Supplier tidak ditentukan'"></p>
                                </div>
                                <span class="text-xs font-medium text-muted" x-text="getIngredientPercentage(ingredient) + '%'"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <span class="text-muted">Quantity:</span>
                                    <span class="font-medium" x-text="ingredient.quantity + ' ' + ingredient.unit"></span>
                                </div>
                                <div>
                                    <span class="text-muted">Unit Cost:</span>
                                    <span class="font-medium" x-text="formatCurrency(ingredient.cost_per_unit)"></span>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-muted">Total Cost:</span>
                                    <span class="font-medium" x-text="formatCurrency(ingredient.total_cost)"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="!recipe.ingredients || recipe.ingredients.length === 0" class="text-center py-8 text-muted">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p>Belum ada bahan yang ditambahkan ke resep ini</p>
                </div>
            </div>

            <!-- Production Instructions -->
            <div class="card p-4 md:p-6" x-show="recipe.instructions">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Instruksi Produksi</h3>
                <div class="prose prose-sm max-w-none">
                    <div x-html="recipe.instructions || 'Belum ada instruksi produksi'"></div>
                </div>
            </div>

            <!-- Production History -->
            <div class="card p-4 md:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight">Riwayat Produksi</h3>
                    <button @click="loadProductionHistory" class="btn btn-sm btn-outline">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh
                    </button>
                </div>
                
                <div class="space-y-3">
                    <template x-for="production in productionHistory" :key="production.id">
                        <div class="flex items-center justify-between p-3 bg-border/30 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-foreground" x-text="'Batch #' + production.batch_number"></p>
                                <p class="text-xs text-muted" x-text="formatDate(production.production_date) + ' oleh ' + production.operator"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-foreground" x-text="production.quantity_produced + ' ' + recipe.unit"></p>
                                <p class="text-xs text-muted" x-text="'Efisiensi: ' + production.efficiency + '%'"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="productionHistory.length === 0" class="text-center py-8 text-muted">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p>Belum ada riwayat produksi untuk resep ini</p>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 lg:space-y-6">
            <!-- Status & Profitability -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Status & Profitabilitas</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Status</span>
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              :class="recipe.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'"
                              x-text="recipe.is_active ? 'Aktif' : 'Tidak Aktif'">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Harga Jual</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatCurrency(recipe.selling_price)">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Profit Margin</span>
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              :class="getMarginColor(recipe.profit_margin)"
                              x-text="recipe.profit_margin + '%'">0%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Profit per Unit</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatCurrency(recipe.profit_per_unit)">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <button @click="calculateCost" class="btn btn-sm btn-primary w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Kalkulasi Ulang Biaya
                    </button>
                    <button @click="createProductionOrder" class="btn btn-sm btn-primary w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Buat Order Produksi
                    </button>
                    <button @click="duplicateRecipe" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Duplikasi Resep
                    </button>
                    <button @click="exportRecipe" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Resep
                    </button>
                </div>
            </div>

            <!-- Recipe Statistics -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Statistik</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Total Produksi</span>
                        <span class="text-sm font-medium text-foreground" x-text="recipe.total_produced || 0">0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Rata-rata Efisiensi</span>
                        <span class="text-sm font-medium text-foreground" x-text="(recipe.average_efficiency || 0) + '%'">0%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Terakhir Diproduksi</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatDate(recipe.last_production_date)">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Dibuat Tanggal</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatDate(recipe.created_at)">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function recipeDetail() {
    return {
        recipe: {
            id: {{ $id ?? 1 }}, // This would come from the route parameter
            name: '',
            code: '',
            category: '',
            batch_size: 0,
            unit: '',
            description: '',
            instructions: '',
            ingredient_cost: 0,
            labor_cost: 0,
            overhead_cost: 0,
            total_cost: 0,
            cost_per_unit: 0,
            selling_price: 0,
            profit_margin: 0,
            profit_per_unit: 0,
            is_active: true,
            ingredients: [],
            total_produced: 0,
            average_efficiency: 0,
            last_production_date: '',
            created_at: ''
        },
        productionHistory: [],

        async init() {
            await this.loadData();
            await this.loadProductionHistory();
        },

        async loadData() {
            try {
                const response = await fetch(`/api/recipes/${this.recipe.id}`);
                const result = await response.json();
                
                if (result.success) {
                    this.recipe = { ...this.recipe, ...result.data };
                } else {
                    alert('Gagal memuat data: ' + result.message);
                }
            } catch (error) {
                console.error('Error loading data:', error);
                alert('Terjadi kesalahan saat memuat data');
            }
        },

        async loadProductionHistory() {
            try {
                const response = await fetch(`/api/recipes/${this.recipe.id}/production-history`);
                const result = await response.json();
                
                if (result.success) {
                    this.productionHistory = result.data;
                }
            } catch (error) {
                console.error('Error loading production history:', error);
            }
        },

        getIngredientPercentage(ingredient) {
            if (this.recipe.ingredient_cost === 0) return 0;
            return Math.round((ingredient.total_cost / this.recipe.ingredient_cost) * 100);
        },

        getMarginColor(margin) {
            if (margin >= 30) return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
            if (margin >= 20) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400';
            return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400';
        },

        editRecipe() {
            window.location.href = `/recipes/${this.recipe.id}/edit`;
        },

        calculateCost() {
            window.location.href = `/recipes/${this.recipe.id}/cost-calculation`;
        },

        createProduction
Order() {
            window.location.href = `/production/orders/create?recipe=${this.recipe.id}`;
        },

        duplicateRecipe() {
            if (confirm('Apakah Anda yakin ingin menduplikasi resep ini?')) {
                window.location.href = `/recipes/create?duplicate=${this.recipe.id}`;
            }
        },

        exportRecipe() {
            window.open(`/recipes/${this.recipe.id}/export`, '_blank');
        },

        printRecipe() {
            window.print();
        },

        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
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