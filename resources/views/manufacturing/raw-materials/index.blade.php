@extends('layouts.dashboard')

@section('title', 'Bahan Baku')
@section('page-title', 'Manajemen Bahan Baku')

@section('content')
<div x-data="rawMaterialsIndex()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Bahan Baku</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Kelola stok dan informasi bahan baku produksi</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('manufacturing.raw-materials.create') }}"
               class="btn btn-primary text-sm sm:text-base px-3 py-2 sm:px-4 sm:py-2">
                <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="hidden xs:inline">Tambah Bahan Baku</span>
                <span class="xs:hidden">Tambah</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Total Item</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="stats.total_items || 0">0</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Stok Rendah</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="stats.low_stock_items || 0">0</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Stok Kritis</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="stats.critical_items || 0">0</p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">
                        <span class="sm:hidden">Nilai</span>
                        <span class="hidden sm:inline">Total Nilai</span>
                    </p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="formatCurrency(stats.total_value || 0)">Rp 0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                           placeholder="Cari bahan baku..." 
                           class="input pl-10">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <select x-model="statusFilter" @change="filterData" class="input">
                    <option value="">Semua Status</option>
                    <option value="good">Stok Baik</option>
                    <option value="low_stock">Stok Rendah</option>
                    <option value="critical">Stok Kritis</option>
                    <option value="out_of_stock">Habis</option>
                </select>
                <select x-model="categoryFilter" @change="filterData" class="input">
                    <option value="">Semua Kategori</option>
                    <template x-for="cat in uniqueCategories" :key="cat">
                        <option :value="cat" x-text="cat"></option>
                    </template>
                </select>
            </div>
        </div>
    </div>

    <!-- Raw Materials Table/Cards -->
    <div class="card">
        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-border/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Bahan Baku</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Harga/Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="item in filteredData" :key="item.id">
                        <tr class="hover:bg-border/30">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="item.name || '-'"></div>
                                    <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="'Unit: ' + (item.unit || '-')"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="item.code || '-'"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="getCategoryName(item)"></td>
                            <td class="px-6 py-4">
                                <div class="text-sm md:text-base text-foreground leading-tight" x-text="formatStock(item.current_stock) + ' ' + (item.unit || '')"></div>
                                <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="'Min: ' + formatStock(item.minimum_stock) + ' ' + (item.unit || '')"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="formatCurrency(item.average_price || 0)"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="getSupplierName(item)"></td>
                            <td class="px-6 py-4">
                                <span :class="getStatusColor(item.status)"
                                      class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                      x-text="getStatusText(item.status)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <button @click="editItem(item)"
                                            class="p-2 text-primary hover:text-primary/80 hover:bg-primary/10 rounded-lg transition-colors touch-manipulation"
                                            title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="deleteItem(item)"
                                            class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors touch-manipulation"
                                            title="Hapus">
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
            <template x-for="item in filteredData" :key="item.id">
                <div class="bg-background border border-border rounded-lg p-4 space-y-3">
                    <!-- Header with name and status -->
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-base md:text-lg font-medium text-foreground leading-tight" x-text="item.name || '-'"></h3>
                            <p class="text-xs md:text-sm text-muted leading-relaxed" x-text="'Kode: ' + (item.code || '-')"></p>
                        </div>
                        <span :class="getStatusColor(item.status)"
                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              x-text="getStatusText(item.status)"></span>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-muted">Kategori:</span>
                            <p class="font-medium text-foreground" x-text="getCategoryName(item)"></p>
                        </div>
                        <div>
                            <span class="text-muted">Unit:</span>
                            <p class="font-medium text-foreground" x-text="item.unit || '-'"></p>
                        </div>
                        <div>
                            <span class="text-muted">Stok Saat Ini:</span>
                            <p class="font-medium text-foreground" x-text="formatStock(item.current_stock) + ' ' + (item.unit || '')"></p>
                        </div>
                        <div>
                            <span class="text-muted">Stok Minimum:</span>
                            <p class="font-medium text-foreground" x-text="formatStock(item.minimum_stock) + ' ' + (item.unit || '')"></p>
                        </div>
                        <div>
                            <span class="text-muted">Harga/Unit:</span>
                            <p class="font-medium text-foreground" x-text="formatCurrency(item.average_price || 0)"></p>
                        </div>
                        <div>
                            <span class="text-muted">Supplier:</span>
                            <p class="font-medium text-foreground" x-text="getSupplierName(item)"></p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-2 border-t border-border">
                        <button @click="editItem(item)"
                                class="flex items-center px-3 py-2 text-sm text-primary hover:text-primary/80 hover:bg-primary/10 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                        <button @click="deleteItem(item)"
                                class="flex items-center px-3 py-2 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors touch-manipulation">
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
function rawMaterialsIndex() {
    return {
        rawMaterials: [],
        filteredData: [],
        search: '',
        statusFilter: '',
        categoryFilter: '',
        uniqueCategories: [],
        stats: {
            total_items: 0,
            low_stock_items: 0,
            critical_items: 0,
            total_value: 0
        },

        init() {
            this.loadData();
        },

        async loadData() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const headers = {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                };
                
                if (csrfToken) {
                    headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
                }

                const response = await fetch('/manufacturing/raw-materials/data', {
                    headers: headers,
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    this.rawMaterials = result.data.data || result.data || [];
                    this.filteredData = this.rawMaterials;
                    
                    // Extract unique categories
                    const categories = new Set();
                    this.rawMaterials.forEach(item => {
                        const categoryName = this.getCategoryName(item);
                        if (categoryName && categoryName !== '-') {
                            categories.add(categoryName);
                        }
                    });
                    this.uniqueCategories = Array.from(categories).sort();
                } else {
                    console.error('Failed to load raw materials:', result.message);
                    this.rawMaterials = [];
                    this.filteredData = [];
                }
                
                // Load stats
                try {
                    const statsResponse = await fetch('/manufacturing/raw-materials/stats', {
                        headers: headers,
                        credentials: 'same-origin'
                    });
                    
                    if (statsResponse.ok) {
                        const statsResult = await statsResponse.json();
                        if (statsResult.success) {
                            this.stats = statsResult.data || this.stats;
                        }
                    }
                } catch (statsError) {
                    console.warn('Failed to load stats:', statsError);
                }
                
            } catch (error) {
                console.error('Error loading data:', error);
                this.rawMaterials = [];
                this.filteredData = [];
                
                if (error.message && (error.message.includes('401') || error.message.includes('Unauthenticated'))) {
                    alert('Session expired. Please refresh the page and login again.');
                } else if (error.message && error.message.includes('500')) {
                    alert('Server error occurred. Please contact administrator.');
                } else {
                    alert('Failed to load raw materials data. Please refresh the page.');
                }
            }
        },

        filterData() {
            this.filteredData = this.rawMaterials.filter(item => {
                const matchesSearch = !this.search || 
                    (item.name && item.name.toLowerCase().includes(this.search.toLowerCase())) ||
                    (item.code && item.code.toLowerCase().includes(this.search.toLowerCase()));
                    
                const matchesStatus = !this.statusFilter || item.status === this.statusFilter;
                
                const itemCategory = this.getCategoryName(item);
                const matchesCategory = !this.categoryFilter || itemCategory === this.categoryFilter;
                
                return matchesSearch && matchesStatus && matchesCategory;
            });
        },

        getCategoryName(item) {
            if (!item) return '-';
            
            // Check if category is loaded as relationship
            if (item.category && typeof item.category === 'object' && item.category.name) {
                return item.category.name;
            }
            
            // Check category_name field
            if (item.category_name) {
                return item.category_name;
            }
            
            // Check category field (text)
            if (item.category && typeof item.category === 'string') {
                return item.category;
            }
            
            return '-';
        },

        getSupplierName(item) {
            if (!item) return '-';
            
            // Check if supplier is loaded as relationship
            if (item.supplier && typeof item.supplier === 'object' && item.supplier.name) {
                return item.supplier.name;
            }
            
            // Check supplier_name field
            if (item.supplier_name) {
                return item.supplier_name;
            }
            
            return '-';
        },

        getStatusColor(status) {
            const colors = {
                'good': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'low_stock': 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
                'critical': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                'out_of_stock': 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getStatusText(status) {
            const texts = {
                'good': 'Stok Baik',
                'low_stock': 'Stok Rendah',
                'critical': 'Stok Kritis',
                'out_of_stock': 'Habis'
            };
            return texts[status] || status || '-';
        },

        editItem(item) {
            if (item && item.id) {
                window.location.href = `/manufacturing/raw-materials/${item.id}/edit`;
            }
        },

        async deleteItem(item) {
            if (!item || !item.id) return;
            
            let confirmMessage = `Apakah Anda yakin ingin menghapus "${item.name || 'item ini'}"?`;
            
            if (item.current_stock && item.current_stock > 0) {
                const stockValue = (item.current_stock || 0) * (item.average_price || 0);
                confirmMessage += `\n\n⚠️ PERHATIAN: Bahan baku ini memiliki stok!`;
                confirmMessage += `\n• Stok saat ini: ${this.formatStock(item.current_stock)} ${item.unit || ''}`;
                confirmMessage += `\n• Nilai stok: ${this.formatCurrency(stockValue)}`;
                confirmMessage += `\n\nStok akan otomatis disesuaikan menjadi 0 dan dicatat dalam riwayat pergerakan stok.`;
                confirmMessage += `\n\nTindakan ini TIDAK DAPAT DIBATALKAN!`;
            }
            
            if (confirm(confirmMessage)) {
                try {
                    const response = await fetch(`/manufacturing/raw-materials/${item.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        },
                        credentials: 'same-origin'
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        let successMessage = 'Bahan baku berhasil dihapus';
                        if (result.data && result.data.had_stock) {
                            const stockValue = result.data.stock_value_adjusted || 0;
                            successMessage += `\n\nStok sebesar ${this.formatCurrency(stockValue)} telah disesuaikan dan dicatat dalam riwayat pergerakan.`;
                        }
                        alert(successMessage);
                        this.loadData();
                    } else {
                        alert('Gagal menghapus bahan baku: ' + (result.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Error deleting item:', error);
                    alert('Terjadi kesalahan saat menghapus bahan baku');
                }
            }
        },

        formatCurrency(amount) {
            const value = parseFloat(amount) || 0;
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        },

        formatStock(value) {
            if (value == null || value === undefined) return '0';
            const num = parseFloat(value);
            if (isNaN(num)) return '0';
            if (num === 0) return '0';
            // If it's a whole number, show without decimals
            if (num % 1 === 0) return num.toString();
            // Otherwise show with minimal decimal places
            return num.toFixed(2).replace(/\.?0+$/, '');
        }
    }
}
</script>
@endpush