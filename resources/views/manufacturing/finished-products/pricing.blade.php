@extends('layouts.dashboard')

@section('title', 'Pengaturan Harga')
@section('page-title', 'Pengaturan Harga Produk')

@section('content')
<div x-data="productPricing()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Pengaturan Harga</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Kelola harga jual dan margin keuntungan produk</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.finished-products.catalog') }}"
               class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Katalog
            </a>
            <button @click="bulkUpdatePrices" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Update Massal
            </button>
            <!-- <button @click="exportPricing" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export
            </button> -->
        </div>
    </div>

    <!-- Pricing Strategy Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight">Margin Rata-rata</h3>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="averageMargin + '%'">0%</p>
            <p class="text-xs md:text-sm text-muted leading-relaxed">Dari semua produk</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight">Produk Margin Rendah</h3>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
            <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="lowMarginProducts">0</p>
            <p class="text-xs md:text-sm text-muted leading-relaxed">Margin < 20%</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight">Total Potensi Profit</h3>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
            <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="formatCurrency(totalPotentialProfit)">Rp 0</p>
            <p class="text-xs md:text-sm text-muted leading-relaxed">Berdasarkan stok saat ini</p>
        </div>
    </div>

    <!-- Bulk Pricing Tools -->
    <div class="card p-4 md:p-6">
        <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Alat Pengaturan Harga Massal</h3>
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 lg:gap-6">
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Kategori</label>
                <select x-model="bulkCategory" class="input w-full h-12 text-base">
                    <option value="">Semua Kategori</option>
                    <option value="Minuman Herbal">Minuman Herbal</option>
                    <option value="Makanan Ringan">Makanan Ringan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Jenis Update</label>
                <select x-model="bulkUpdateType" class="input w-full h-12 text-base">
                    <option value="margin">Berdasarkan Margin (%)</option>
                    <option value="markup">Berdasarkan Markup (%)</option>
                    <option value="fixed">Tambah/Kurang Tetap (Rp)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Nilai</label>
                <input type="number" x-model="bulkValue" class="input w-full h-12 text-base" placeholder="Masukkan nilai">
            </div>
        </div>
        <div class="mt-4 md:mt-6 flex flex-col sm:flex-row gap-3">
            <button @click="previewBulkUpdate" class="btn btn-outline h-12 touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Preview
            </button>
            <button @click="applyBulkUpdate" class="btn btn-primary h-12 touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Terapkan
            </button>
        </div>
    </div>

    <!-- Products Pricing Table -->
    <div class="card">
        <div class="p-4 md:p-6 border-b border-border">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight">Daftar Harga Produk</h3>
                <div class="flex flex-col gap-2 sm:flex-row sm:gap-3">
                    <div class="relative">
                        <input type="text"
                               x-model="search"
                               @input="filterData"
                               placeholder="Cari produk..."
                               class="input pl-10 w-full h-12 text-base sm:w-64">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <select x-model="categoryFilter" @change="filterData" class="input w-full h-12 text-base sm:w-48">
                        <option value="">Semua Kategori</option>
                        <option value="Minuman Herbal">Minuman Herbal</option>
                        <option value="Makanan Ringan">Makanan Ringan</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-border/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Biaya Produksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Harga Jual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Margin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Profit per Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Total Profit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="product in filteredData" :key="product.id">
                        <tr class="hover:bg-border/30">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="product.name"></div>
                                    <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="product.sku + ' • ' + product.category"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="formatCurrency(product.unit_cost)"></td>
                            <td class="px-6 py-4">
                                <div x-show="!product.editing">
                                    <span class="text-sm font-semibold text-foreground" x-text="formatCurrency(product.selling_price)"></span>
                                </div>
                                <div x-show="product.editing" class="flex gap-2">
                                    <input type="number" 
                                           x-model="product.new_price" 
                                           class="input w-32"
                                           @keyup.enter="savePrice(product)"
                                           @keyup.escape="cancelEdit(product)">
                                    <button @click="savePrice(product)" class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg touch-manipulation">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                    <button @click="cancelEdit(product)" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg touch-manipulation">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="getMarginColor(calculateMargin(product))" 
                                      class="inline-flex px-2 py-1 text-xs font-medium rounded-full" 
                                      x-text="calculateMargin(product) + '%'"></span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-foreground" x-text="formatCurrency(product.selling_price - product.unit_cost)"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="product.current_stock + ' unit'"></td>
                            <td class="px-6 py-4 text-sm font-semibold text-foreground" x-text="formatCurrency((product.selling_price - product.unit_cost) * product.current_stock)"></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <button @click="editPrice(product)"
                                            x-show="!product.editing"
                                            class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg touch-manipulation"
                                            title="Edit Harga">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="viewPriceHistory(product)" class="p-2 text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-lg touch-manipulation" title="Riwayat Harga">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                    <button @click="calculateOptimalPrice(product)" class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg touch-manipulation" title="Harga Optimal">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function productPricing() {
    return {
        products: [],
        filteredData: [],
        search: '',
        categoryFilter: '',
        bulkCategory: '',
        bulkUpdateType: 'margin',
        bulkValue: '',

        init() {
            this.loadData();
        },

        async loadData() {
    try {
        const params = new URLSearchParams({
            search: this.search,
            category: this.categoryFilter
        });

        const response = await fetch(`/api/finished-products/pricing?${params}`);
        const data = await response.json();
        
        if (data.success) {
            this.products = data.data.map(product => ({
                ...product,
                editing: false,
                new_price: product.selling_price
            }));
            this.filteredData = this.products;
        }
    } catch (error) {
        console.error('Error loading pricing data:', error);
    }
}

async savePrice(product) {
    try {
        const response = await fetch(`/api/finished-products/${product.id}/price`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                selling_price: product.new_price,
                reason: 'Manual price adjustment',
                notes: 'Changed through pricing dashboard'
            })
        });

        const result = await response.json();
        
        if (result.success) {
            product.selling_price = product.new_price;
            product.editing = false;
            // Reload data to get updated stats
            this.loadData();
        }
    } catch (error) {
        console.error('Error updating price:', error);
    }
},

        filterData() {
            this.filteredData = this.products.filter(product => {
                const matchesSearch = product.name.toLowerCase().includes(this.search.toLowerCase()) ||
                                    product.sku.toLowerCase().includes(this.search.toLowerCase());
                const matchesCategory = !this.categoryFilter || product.category === this.categoryFilter;
                
                return matchesSearch && matchesCategory;
            });
        },

        get averageMargin() {
            if (this.products.length === 0) return 0;
            const totalMargin = this.products.reduce((sum, product) => sum + this.calculateMargin(product), 0);
            return Math.round(totalMargin / this.products.length);
        },

        get lowMarginProducts() {
            return this.products.filter(product => this.calculateMargin(product) < 20).length;
        },

        get totalPotentialProfit() {
            return this.products.reduce((sum, product) => {
                return sum + ((product.selling_price - product.unit_cost) * product.current_stock);
            }, 0);
        },

        calculateMargin(product) {
            if (product.unit_cost === 0) return 0;
            return Math.round(((product.selling_price - product.unit_cost) / product.selling_price) * 100);
        },

        getMarginColor(margin) {
            if (margin >= 30) return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
            if (margin >= 20) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400';
            return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400';
        },

        editPrice(product) {
            product.editing = true;
            product.new_price = product.selling_price;
        },

        cancelEdit(product) {
            product.editing = false;
            product.new_price = product.selling_price;
        },

        async savePrice(product) {
    try {
        const response = await fetch(`/api/finished-products/${product.id}/price`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                selling_price: product.new_price,
                reason: 'Manual price adjustment',
                notes: 'Changed through pricing dashboard'
            })
        });

        const result = await response.json();
        
        if (result.success) {
            product.selling_price = product.new_price;
            product.editing = false;
            // Reload data to get updated stats
            this.loadData();
        }
    } catch (error) {
        console.error('Error updating price:', error);
    }
},

        previewBulkUpdate() {
            console.log('Preview bulk update:', {
                category: this.bulkCategory,
                type: this.bulkUpdateType,
                value: this.bulkValue
            });
        },

        applyBulkUpdate() {
            console.log('Apply bulk update');
        },

        viewPriceHistory(product) {
            console.log('View price history:', product);
        },

        calculateOptimalPrice(product) {
            console.log('Calculate optimal price:', product);
        },

        exportPricing() {
            console.log('Export pricing data');
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