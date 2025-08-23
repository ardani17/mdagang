@extends('layouts.dashboard')

@section('title', 'Resep Produk')
@section('page-title', 'Manajemen Resep Produk')

@section('content')
<div x-data="recipesIndex()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Resep Produk (BOM)</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Kelola resep dan bill of materials untuk produksi</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.recipes.create') }}"
               class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Resep
            </a>
            <button @click="exportData" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Total Resep</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="recipes.length">0</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Resep Aktif</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="activeRecipes">0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Rata-rata Margin</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="averageMargin + '%'">0%</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Total Nilai Produksi</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="formatCurrency(totalProductionValue)">Rp 0</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative">
                    <input type="text" 
                           x-model="search" 
                           @input="filterData"
                           placeholder="Cari resep..." 
                           class="input pl-10">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <select x-model="statusFilter" @change="filterData" class="input">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                </select>
                <select x-model="categoryFilter" @change="filterData" class="input">
                    <option value="">Semua Kategori</option>
                    <option value="Minuman Herbal">Minuman Herbal</option>
                    <option value="Makanan Ringan">Makanan Ringan</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Recipes Table/Cards -->
    <div class="card">
        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-border/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Resep</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Batch Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Biaya/Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Harga Jual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Margin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="recipe in filteredData" :key="recipe.id">
                        <tr class="hover:bg-border/30">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="recipe.name"></div>
                                    <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="recipe.ingredients.length + ' bahan'"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="recipe.code"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="recipe.category"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="recipe.batch_size + ' ' + recipe.unit"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="formatCurrency(recipe.cost_per_unit)"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="formatCurrency(recipe.selling_price)"></td>
                            <td class="px-6 py-4">
                                <span :class="getMarginColor(recipe.profit_margin)"
                                      class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                      x-text="recipe.profit_margin + '%'"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="getStatusColor(recipe.status)"
                                      class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                      x-text="getStatusText(recipe.status)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <button @click="viewRecipe(recipe)" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg touch-manipulation" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click="calculateCost(recipe)" class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg touch-manipulation" title="Kalkulasi Biaya">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                    <button @click="editRecipe(recipe)" class="p-2 text-primary hover:text-primary/80 hover:bg-primary/10 rounded-lg touch-manipulation" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="deleteRecipe(recipe)" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg touch-manipulation" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden space-y-4 p-4">
            <template x-for="recipe in filteredData" :key="recipe.id">
                <div class="bg-background border border-border rounded-lg p-4 space-y-3">
                    <!-- Header with name and status -->
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-base md:text-lg font-medium text-foreground leading-tight" x-text="recipe.name"></h3>
                            <p class="text-xs md:text-sm text-muted leading-relaxed" x-text="'Kode: ' + recipe.code + ' • ' + recipe.ingredients.length + ' bahan'"></p>
                        </div>
                        <span :class="getStatusColor(recipe.status)"
                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              x-text="getStatusText(recipe.status)"></span>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-muted">Kategori:</span>
                            <p class="font-medium text-foreground" x-text="recipe.category"></p>
                        </div>
                        <div>
                            <span class="text-muted">Batch Size:</span>
                            <p class="font-medium text-foreground" x-text="recipe.batch_size + ' ' + recipe.unit"></p>
                        </div>
                        <div>
                            <span class="text-muted">Biaya/Unit:</span>
                            <p class="font-medium text-foreground" x-text="formatCurrency(recipe.cost_per_unit)"></p>
                        </div>
                        <div>
                            <span class="text-muted">Harga Jual:</span>
                            <p class="font-medium text-foreground" x-text="formatCurrency(recipe.selling_price)"></p>
                        </div>
                    </div>

                    <!-- Margin -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Profit Margin:</span>
                        <span :class="getMarginColor(recipe.profit_margin)"
                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              x-text="recipe.profit_margin + '%'"></span>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-2 pt-2 border-t border-border">
                        <button @click="viewRecipe(recipe)"
                                class="flex items-center px-2 py-1 text-xs text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Lihat
                        </button>
                        <button @click="calculateCost(recipe)"
                                class="flex items-center px-2 py-1 text-xs text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Kalkulasi
                        </button>
                        <button @click="editRecipe(recipe)"
                                class="flex items-center px-2 py-1 text-xs text-primary hover:text-primary/80 hover:bg-primary/10 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                        <button @click="deleteRecipe(recipe)"
                                class="flex items-center px-2 py-1 text-xs text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function recipesIndex() {
    return {
        recipes: [],
        filteredData: [],
        search: '',
        statusFilter: '',
        categoryFilter: '',

        init() {
            this.loadData();
        },

        async loadData() {
            try {
                const response = await fetch('/api/recipes');
                const data = await response.json();
                this.recipes = data.data;
                this.filteredData = this.recipes;
            } catch (error) {
                console.error('Error loading data:', error);
            }
        },

        filterData() {
            this.filteredData = this.recipes.filter(recipe => {
                const matchesSearch = recipe.name.toLowerCase().includes(this.search.toLowerCase()) ||
                                    recipe.code.toLowerCase().includes(this.search.toLowerCase());
                const matchesStatus = !this.statusFilter || recipe.status === this.statusFilter;
                const matchesCategory = !this.categoryFilter || recipe.category === this.categoryFilter;
                
                return matchesSearch && matchesStatus && matchesCategory;
            });
        },

        get activeRecipes() {
            return this.recipes.filter(recipe => recipe.status === 'active').length;
        },

        get averageMargin() {
            if (this.recipes.length === 0) return 0;
            const totalMargin = this.recipes.reduce((sum, recipe) => sum + recipe.profit_margin, 0);
            return Math.round(totalMargin / this.recipes.length * 10) / 10;
        },

        get totalProductionValue() {
            return this.recipes.reduce((sum, recipe) => sum + recipe.total_cost, 0);
        },

        getStatusColor(status) {
            const colors = {
                'active': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'inactive': 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getStatusText(status) {
            const texts = {
                'active': 'Aktif',
                'inactive': 'Tidak Aktif'
            };
            return texts[status] || status;
        },

        getMarginColor(margin) {
            if (margin >= 30) return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
            if (margin >= 20) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400';
            return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400';
        },

        viewRecipe(recipe) {
            // Show recipe details modal or navigate to detail page
            console.log('View recipe:', recipe);
        },

        calculateCost(recipe) {
            window.location.href = `/recipes/${recipe.id}/cost-calculation`;
        },

        editRecipe(recipe) {
            window.location.href = `/recipes/${recipe.id}/edit`;
        },

        deleteRecipe(recipe) {
            if (confirm(`Apakah Anda yakin ingin menghapus resep ${recipe.name}?`)) {
                console.log('Delete recipe:', recipe);
            }
        },

        exportData() {
            console.log('Export recipes data');
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