@extends('layouts.dashboard')

@section('title', 'Daftar Resep')
@section('page-title', 'Manajemen Resep')

@section('content')
<div x-data="recipesIndex()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Daftar Resep</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Kelola resep dan formula produksi</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('manufacturing.recipes.create') }}"
               class="btn btn-primary text-sm sm:text-base px-3 py-2 sm:px-4 sm:py-2">
                <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="hidden xs:inline">Tambah Resep</span>
                <span class="xs:hidden">Tambah</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <!-- <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Total Resep</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="stats.total_recipes || 0">0</p>
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
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="stats.active_recipes || 0">0</p>
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
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Rata-rata Biaya</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="formatCurrency(stats.average_cost || 0)">Rp 0</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Total Bahan</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="stats.total_ingredients || 0">0</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Filters and Search -->
    <!-- <div class="card p-6">
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
                    <option value="inactive">Non-Aktif</option>
                </select>
                <select x-model="categoryFilter" @change="filterData" class="input">
                    <option value="">Semua Kategori</option>
                    <template x-for="cat in uniqueCategories" :key="cat">
                        <option :value="cat" x-text="cat"></option>
                    </template>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button @click="exportData" class="btn btn-secondary text-sm px-3 py-2">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export
                </button>
            </div>
        </div>
    </div> -->

    <!-- Recipes Table -->
    <div class="card">
        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-border/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Nama Resep</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Versi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Hasil</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Total Biaya</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Biaya/Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="recipe in filteredData" :key="recipe.id">
                        <tr class="hover:bg-border/30">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-foreground" x-text="recipe.code || '-'"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-foreground" x-text="recipe.name || '-'"></div>
                                <div class="text-xs text-muted" x-text="recipe.description ? recipe.description.substring(0, 50) + (recipe.description.length > 50 ? '...' : '') : '-'"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-foreground" x-text="recipe.product?.name || '-'"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="recipe.version || '1.0'"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="recipe.category || '-'"></td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-foreground" x-text="formatQuantity(recipe.yield_quantity) + ' ' + (recipe.yield_unit || 'unit')"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="formatCurrency(recipe.total_cost || 0)"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="formatCurrency(recipe.cost_per_unit || 0)"></td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-foreground" x-text="formatMinutes(recipe.total_time || 0)"></div>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="recipe.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'"
                                      class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                      x-text="recipe.is_active ? 'Aktif' : 'Non-Aktif'"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a :href="'/manufacturing/recipes/' + recipe.id"
                                       class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors touch-manipulation"
                                       title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a :href="'/manufacturing/recipes/' + recipe.id + '/edit'"
                                       class="p-2 text-primary hover:text-primary/80 hover:bg-primary/10 rounded-lg transition-colors touch-manipulation"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button @click="deleteRecipe(recipe)"
                                            class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors touch-manipulation"
                                            title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredData.length === 0">
                        <tr>
                            <td colspan="11" class="px-6 py-8 text-center text-muted">
                                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p>Tidak ada data resep</p>
                                <p class="text-sm mt-1" x-show="search || statusFilter || categoryFilter">
                                    Coba ubah filter pencarian Anda
                                </p>
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
                    <!-- Header with code and status -->
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-base font-medium text-foreground leading-tight" x-text="recipe.name || '-'"></h3>
                            <p class="text-xs text-muted leading-relaxed" x-text="'Kode: ' + (recipe.code || '-')"></p>
                        </div>
                        <span :class="recipe.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'"
                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              x-text="recipe.is_active ? 'Aktif' : 'Non-Aktif'"></span>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-muted">Produk:</span>
                            <p class="font-medium text-foreground" x-text="recipe.product?.name || '-'"></p>
                        </div>
                        <div>
                            <span class="text-muted">Versi:</span>
                            <p class="font-medium text-foreground" x-text="recipe.version || '1.0'"></p>
                        </div>
                        <div>
                            <span class="text-muted">Hasil:</span>
                            <p class="font-medium text-foreground" x-text="formatQuantity(recipe.yield_quantity) + ' ' + (recipe.yield_unit || 'unit')"></p>
                        </div>
                        <div>
                            <span class="text-muted">Biaya Total:</span>
                            <p class="font-medium text-foreground" x-text="formatCurrency(recipe.total_cost || 0)"></p>
                        </div>
                        <div>
                            <span class="text-muted">Biaya/Unit:</span>
                            <p class="font-medium text-foreground" x-text="formatCurrency(recipe.cost_per_unit || 0)"></p>
                        </div>
                        <div>
                            <span class="text-muted">Waktu:</span>
                            <p class="font-medium text-foreground" x-text="formatMinutes(recipe.total_time || 0)"></p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div x-show="recipe.description" class="text-sm">
                        <span class="text-muted">Deskripsi:</span>
                        <p class="text-foreground" x-text="recipe.description.substring(0, 100) + (recipe.description.length > 100 ? '...' : '')"></p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-2 border-t border-border">
                        <a :href="'/manufacturing/recipes/' + recipe.id"
                           class="flex items-center px-3 py-2 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Lihat
                        </a>
                        <a :href="'/manufacturing/recipes/' + recipe.id + '/edit'"
                           class="flex items-center px-3 py-2 text-sm text-primary hover:text-primary/80 hover:bg-primary/10 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </a>
                    </div>
                </div>
            </template>

            <template x-if="filteredData.length === 0">
                <div class="text-center py-8 text-muted">
                    <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p>Tidak ada data resep</p>
                    <p class="text-sm mt-1" x-show="search || statusFilter || categoryFilter">
                        Coba ubah filter pencarian Anda
                    </p>
                </div>
            </template>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-border" x-show="pagination">
            <div class="flex items-center justify-between">
                <div class="text-sm text-muted">
                    Menampilkan <span x-text="pagination.from"></span> sampai <span x-text="pagination.to"></span> 
                    dari <span x-text="pagination.total"></span> hasil
                </div>
                <div class="flex space-x-2">
                    <button @click="prevPage" :disabled="pagination.current_page === 1" 
                            :class="{'opacity-50 cursor-not-allowed': pagination.current_page === 1}"
                            class="btn btn-secondary text-sm px-3 py-1">
                        Sebelumnya
                    </button>
                    <button @click="nextPage" :disabled="pagination.current_page === pagination.last_page"
                            :class="{'opacity-50 cursor-not-allowed': pagination.current_page === pagination.last_page}"
                            class="btn btn-secondary text-sm px-3 py-1">
                        Selanjutnya
                    </button>
                </div>
            </div>
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
        uniqueCategories: [],
        stats: {
            total_recipes: 0,
            active_recipes: 0,
            average_cost: 0,
            total_ingredients: 0
        },
        pagination: null,
        currentPage: 1,

        async init() {
            await this.loadData();
        },

        async loadData(page = 1) {
            try {
                this.currentPage = page;
                const params = new URLSearchParams({
                    page: page,
                    search: this.search,
                    status: this.statusFilter,
                    category: this.categoryFilter
                });

                const response = await fetch(`/manufacturing/recipes/data?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) throw new Error('HTTP error! status: ' + response.status);
                
                const result = await response.json();
                
                if (result.success) {
                    this.recipes = result.data.data || result.data || [];
                    this.filteredData = this.recipes;
                    this.pagination = result.data;
                    
                    // Extract unique categories
                    const categories = new Set();
                    this.recipes.forEach(recipe => {
                        if (recipe.category) {
                            categories.add(recipe.category);
                        }
                    });
                    this.uniqueCategories = Array.from(categories).sort();
                }
                
                // Load stats
                await this.loadStats();
                
            } catch (error) {
                console.error('Error loading data:', error);
                this.recipes = [];
                this.filteredData = [];
            }
        },

        async loadStats() {
            try {
                const response = await fetch('/manufacturing/recipes/stats', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        this.stats = result.data;
                    }
                }
            } catch (error) {
                console.warn('Failed to load stats:', error);
            }
        },

        filterData() {
            this.loadData(1);
        },

        async deleteRecipe(recipe) {
            if (!recipe || !recipe.id) return;
            
            if (confirm(`Apakah Anda yakin ingin menghapus resep "${recipe.name}"?`)) {
                try {
                    const response = await fetch(`/manufacturing/recipes/${recipe.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Resep berhasil dihapus');
                        this.loadData(this.currentPage);
                    } else {
                        alert('Gagal menghapus resep: ' + (result.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Error deleting recipe:', error);
                    alert('Terjadi kesalahan saat menghapus resep');
                }
            }
        },

        async exportData() {
            try {
                const params = new URLSearchParams({
                    search: this.search,
                    status: this.statusFilter,
                    category: this.categoryFilter
                });

                window.open(`/manufacturing/recipes/export?${params}`, '_blank');
            } catch (error) {
                console.error('Error exporting data:', error);
                alert('Terjadi kesalahan saat export data');
            }
        },

        nextPage() {
            if (this.pagination && this.pagination.current_page < this.pagination.last_page) {
                this.loadData(this.pagination.current_page + 1);
            }
        },

        prevPage() {
            if (this.pagination && this.pagination.current_page > 1) {
                this.loadData(this.pagination.current_page - 1);
            }
        },

        formatCurrency(amount) {
            // Pastikan amount adalah number, handle NaN dan null
            const value = parseFloat(amount) || 0;
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        },

        formatQuantity(value) {
            // Handle null, undefined, dan NaN
            if (value == null || value === undefined || isNaN(value)) return '0';
            const num = parseFloat(value);
            if (isNaN(num)) return '0';
            if (num === 0) return '0';
            if (num % 1 === 0) return num.toString();
            return num.toFixed(2);
        },

        formatMinutes(minutes) {
            // Handle null, undefined, dan NaN
            const mins = parseFloat(minutes) || 0;
            if (mins <= 0) return '0 menit';
            
            const hours = Math.floor(mins / 60);
            const remainingMinutes = mins % 60;
            
            if (hours > 0 && remainingMinutes > 0) {
                return `${hours}j ${remainingMinutes}m`;
            } else if (hours > 0) {
                return `${hours} jam`;
            } else {
                return `${mins} menit`;
            }
        },

        // Tambahkan method untuk handle perhitungan yang aman dari NaN
        safeCalculate(value, defaultValue = 0) {
            const num = parseFloat(value);
            return isNaN(num) ? defaultValue : num;
        }

    }
}
</script>
@endpush