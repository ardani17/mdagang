@extends('layouts.dashboard')

@section('title', 'Produk Jadi')
@section('page-title', 'Manajemen Produk Jadi')

@section('content')
<div x-data="finishedProductsIndex()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Produk Jadi</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Kelola stok dan informasi produk jadi siap jual</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.finished-products.catalog') }}"
               class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Katalog Produk
            </a>
            <a href="{{ route('manufacturing.finished-products.pricing') }}"
               class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                Atur Harga
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
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Total Produk</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="products.length">0</p>
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
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Siap Jual</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="availableProducts">0</p>
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
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Stok Rendah</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="lowStockProducts">0</p>
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
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Total Nilai Stok</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="formatCurrency(totalStockValue)">Rp 0</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                           placeholder="Cari produk..." 
                           class="input pl-10">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <select x-model="statusFilter" @change="filterData" class="input">
                    <option value="">Semua Status</option>
                    <option value="available">Tersedia</option>
                    <option value="low_stock">Stok Rendah</option>
                    <option value="out_of_stock">Habis</option>
                </select>
                <select x-model="categoryFilter" @change="filterData" class="input">
                    <option value="">Semua Kategori</option>
                    <option value="Minuman Herbal">Minuman Herbal</option>
                    <option value="Makanan Ringan">Makanan Ringan</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Products Table/Cards -->
    <div class="card">
        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-border/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Biaya Produksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Harga Jual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Produksi Terakhir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="product in filteredData" :key="product.id">
                        <tr class="hover:bg-border/30">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="product.name"></div>
                                    <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="'Exp: ' + formatDate(product.expiry_date)"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="product.sku"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="product.category"></td>
                            <td class="px-6 py-4">
                                <div class="text-sm md:text-base text-foreground leading-tight" x-text="product.current_stock + ' unit'"></div>
                                <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="'Min: ' + product.min_stock + ' unit'"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="formatCurrency(product.unit_cost)"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="formatCurrency(product.selling_price)"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="formatDate(product.last_production)"></td>
                            <td class="px-6 py-4">
                                <span :class="getStatusColor(product.status)"
                                      class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                      x-text="getStatusText(product.status)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <button @click="viewProduct(product)" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg touch-manipulation" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click="createProductionOrder(product)" class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg touch-manipulation" title="Buat Order Produksi">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </button>
                                    <button @click="adjustStock(product)" class="p-2 text-orange-600 hover:text-orange-800 hover:bg-orange-50 rounded-lg touch-manipulation" title="Sesuaikan Stok">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="updatePrice(product)" class="p-2 text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-lg touch-manipulation" title="Update Harga">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
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
            <template x-for="product in filteredData" :key="product.id">
                <div class="bg-background border border-border rounded-lg p-4 space-y-3">
                    <!-- Header with name and status -->
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-base md:text-lg font-medium text-foreground leading-tight" x-text="product.name"></h3>
                            <p class="text-xs md:text-sm text-muted leading-relaxed" x-text="'SKU: ' + product.sku"></p>
                        </div>
                        <span :class="getStatusColor(product.status)"
                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              x-text="getStatusText(product.status)"></span>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-muted">Kategori:</span>
                            <p class="font-medium text-foreground" x-text="product.category"></p>
                        </div>
                        <div>
                            <span class="text-muted">Stok Saat Ini:</span>
                            <p class="font-medium text-foreground" x-text="product.current_stock + ' unit'"></p>
                        </div>
                        <div>
                            <span class="text-muted">Stok Minimum:</span>
                            <p class="font-medium text-foreground" x-text="product.min_stock + ' unit'"></p>
                        </div>
                        <div>
                            <span class="text-muted">Biaya Produksi:</span>
                            <p class="font-medium text-foreground" x-text="formatCurrency(product.unit_cost)"></p>
                        </div>
                        <div>
                            <span class="text-muted">Harga Jual:</span>
                            <p class="font-medium text-foreground" x-text="formatCurrency(product.selling_price)"></p>
                        </div>
                        <div>
                            <span class="text-muted">Produksi Terakhir:</span>
                            <p class="font-medium text-foreground" x-text="formatDate(product.last_production)"></p>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs md:text-sm text-muted">Tanggal Kadaluarsa:</span>
                        <p class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="formatDate(product.expiry_date)"></p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-2 pt-2 border-t border-border">
                        <button @click="viewProduct(product)"
                                class="flex items-center px-2 py-1 text-xs text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Lihat
                        </button>
                        <button @click="createProductionOrder(product)"
                                class="flex items-center px-2 py-1 text-xs text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Produksi
                        </button>
                        <button @click="adjustStock(product)"
                                class="flex items-center px-2 py-1 text-xs text-orange-600 hover:text-orange-800 hover:bg-orange-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Stok
                        </button>
                        <button @click="updatePrice(product)"
                                class="flex items-center px-2 py-1 text-xs text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            Harga
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
function finishedProductsIndex() {
    return {
        products: [],
        filteredData: [],
        search: '',
        statusFilter: '',
        categoryFilter: '',

        init() {
            this.loadData();
        },

        async loadData() {
    try {
        const params = new URLSearchParams({
            search: this.search,
            status: this.statusFilter,
            category: this.categoryFilter
        });

        const response = await fetch(`/api/finished-products?${params}`);
        const data = await response.json();
        
        if (data.success) {
            this.products = data.data;
            this.filteredData = this.products;
        }
    } catch (error) {
        console.error('Error loading data:', error);
    }
},

async loadStats() {
    try {
        const response = await fetch('/api/finished-products/stats');
        const data = await response.json();
        
        if (data.success) {
            // Update stats cards
            this.totalProducts = data.data.total_products;
            this.availableProducts = data.data.available_products;
            this.lowStockProducts = data.data.low_stock_products;
            this.totalStockValue = data.data.total_stock_value;
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
},

        filterData() {
            this.filteredData = this.products.filter(product => {
                const matchesSearch = product.name.toLowerCase().includes(this.search.toLowerCase()) ||
                                    product.sku.toLowerCase().includes(this.search.toLowerCase());
                const matchesStatus = !this.statusFilter || product.status === this.statusFilter;
                const matchesCategory = !this.categoryFilter || product.category === this.categoryFilter;
                
                return matchesSearch && matchesStatus && matchesCategory;
            });
        },

        get availableProducts() {
            return this.products.filter(product => product.status === 'available').length;
        },

        get lowStockProducts() {
            return this.products.filter(product => product.status === 'low_stock').length;
        },

        get totalStockValue() {
            return this.products.reduce((sum, product) => sum + (product.current_stock * product.unit_cost), 0);
        },

        getStatusColor(status) {
            const colors = {
                'available': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'low_stock': 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
                'out_of_stock': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getStatusText(status) {
            const texts = {
                'available': 'Tersedia',
                'low_stock': 'Stok Rendah',
                'out_of_stock': 'Habis'
            };
            return texts[status] || status;
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        },

        viewProduct(product) {
            window.location.href = `/finished-products/${product.id}`;
        },

        createProductionOrder(product) {
            window.location.href = `/production/orders/create?product=${product.id}`;
        },

        adjustStock(product) {
            window.location.href = `/finished-products/${product.id}?action=adjust-stock`;
        },

        updatePrice(product) {
            window.location.href = `/finished-products/pricing?product=${product.id}`;
        },

        exportData() {
            console.log('Export finished products data');
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