@extends('layouts.dashboard')

@section('title', 'Katalog Produk')
@section('page-title', 'Katalog Produk Jadi')

@section('content')
<div x-data="productCatalog()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Katalog Produk</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Tampilan katalog produk jadi untuk penjualan</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('manufacturing.finished-products.index') }}"
               class="btn btn-outline">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Stok
            </a>
            <button @click="toggleView" class="btn btn-outline">
                <svg x-show="viewMode === 'grid'" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                <svg x-show="viewMode === 'list'" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span x-text="viewMode === 'grid' ? 'List View' : 'Grid View'"></span>
            </button>
            <button @click="printCatalog" class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Katalog
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-4 md:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-col gap-3 sm:flex-row sm:gap-4">
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
                <select x-model="availabilityFilter" @change="filterData" class="input w-full h-12 text-base sm:w-40">
                    <option value="">Semua Status</option>
                    <option value="available">Tersedia</option>
                    <option value="limited">Terbatas</option>
                    <option value="out_of_stock">Habis</option>
                </select>
            </div>
            <div class="flex items-center gap-4">
                <label class="flex items-center">
                    <input type="checkbox" x-model="showPrices" class="w-5 h-5 mr-3 rounded border-border text-primary focus:ring-primary touch-manipulation">
                    <span class="text-base">Tampilkan Harga</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Grid View -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        <template x-for="product in filteredData" :key="product.id">
            <div class="card overflow-hidden hover:shadow-lg transition-shadow duration-200">
                <div class="relative w-full bg-gray-100 dark:bg-gray-800">
                    <div class="aspect-w-4 aspect-h-3">
                        <img :src="product.image || '/images/product-placeholder.jpg'"
                             :alt="product.name"
                             class="w-full h-full object-cover rounded-t-lg"
                             loading="lazy">
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-start justify-between mb-2">
                        <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight" x-text="product.name"></h3>
                        <span :class="getAvailabilityColor(product.availability)" 
                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full" 
                              x-text="getAvailabilityText(product.availability)"></span>
                    </div>
                    <p class="text-xs md:text-sm text-muted leading-relaxed mb-3" x-text="product.description"></p>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-muted">SKU:</span>
                            <span class="text-foreground" x-text="product.sku"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted">Kategori:</span>
                            <span class="text-foreground" x-text="product.category"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted">Stok:</span>
                            <span class="text-foreground" x-text="product.current_stock + ' unit'"></span>
                        </div>
                        <div x-show="showPrices" class="flex justify-between text-sm">
                            <span class="text-muted">Harga:</span>
                            <span class="text-foreground font-semibold" x-text="formatCurrency(product.selling_price)"></span>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button @click="viewDetails(product)"
                                class="flex-1 btn btn-outline btn-sm touch-manipulation">
                            Detail
                        </button>
                        <button @click="addToQuote(product)"
                                :disabled="product.availability === 'out_of_stock'"
                                class="flex-1 btn btn-primary btn-sm touch-manipulation">
                            <span x-show="product.availability !== 'out_of_stock'">Tambah Quote</span>
                            <span x-show="product.availability === 'out_of_stock'">Habis</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- List View -->
    <div x-show="viewMode === 'list'" class="card">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-border/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Stok</th>
                        <th x-show="showPrices" class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="product in filteredData" :key="product.id">
                        <tr class="hover:bg-border/30">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-12 h-12 mr-4">
                                        <img :src="product.image || '/images/product-placeholder.jpg'"
                                             :alt="product.name"
                                             class="w-full h-full rounded-lg object-cover"
                                             loading="lazy">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm md:text-base font-medium text-foreground leading-tight truncate" x-text="product.name"></div>
                                        <div class="text-xs md:text-sm text-muted leading-relaxed truncate" x-text="product.description"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="product.sku"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="product.category"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="product.current_stock + ' unit'"></td>
                            <td x-show="showPrices" class="px-6 py-4 text-sm font-semibold text-foreground" x-text="formatCurrency(product.selling_price)"></td>
                            <td class="px-6 py-4">
                                <span :class="getAvailabilityColor(product.availability)" 
                                      class="inline-flex px-2 py-1 text-xs font-medium rounded-full" 
                                      x-text="getAvailabilityText(product.availability)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <button @click="viewDetails(product)"
                                            class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors touch-manipulation"
                                            title="Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click="addToQuote(product)"
                                            :disabled="product.availability === 'out_of_stock'"
                                            class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 disabled:text-gray-400 disabled:hover:bg-transparent rounded-lg transition-colors touch-manipulation"
                                            title="Tambah ke Quote">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
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

    <!-- Empty State -->
    <div x-show="filteredData.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        <h3 class="mt-2 text-sm md:text-base font-medium text-foreground leading-tight">Tidak ada produk</h3>
        <p class="mt-1 text-xs md:text-sm text-muted leading-relaxed">Tidak ada produk yang sesuai dengan filter yang dipilih.</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function productCatalog() {
    return {
        products: [],
        filteredData: [],
        search: '',
        categoryFilter: '',
        availabilityFilter: '',
        viewMode: 'grid',
        showPrices: false,

        init() {
            this.loadData();
        },

        async loadData() {
            try {
                const response = await fetch('/api/finished-products/catalog');
                const data = await response.json();
                this.products = data.data;
                this.filteredData = this.products;
            } catch (error) {
                console.error('Error loading data:', error);
            }
        },

        filterData() {
            this.filteredData = this.products.filter(product => {
                const matchesSearch = product.name.toLowerCase().includes(this.search.toLowerCase()) ||
                                    product.sku.toLowerCase().includes(this.search.toLowerCase()) ||
                                    product.description.toLowerCase().includes(this.search.toLowerCase());
                const matchesCategory = !this.categoryFilter || product.category === this.categoryFilter;
                const matchesAvailability = !this.availabilityFilter || product.availability === this.availabilityFilter;
                
                return matchesSearch && matchesCategory && matchesAvailability;
            });
        },

        toggleView() {
            this.viewMode = this.viewMode === 'grid' ? 'list' : 'grid';
        },

        getAvailabilityColor(availability) {
            const colors = {
                'available': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'limited': 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
                'out_of_stock': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
            };
            return colors[availability] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getAvailabilityText(availability) {
            const texts = {
                'available': 'Tersedia',
                'limited': 'Terbatas',
                'out_of_stock': 'Habis'
            };
            return texts[availability] || availability;
        },

        viewDetails(product) {
            console.log('View product details:', product);
        },

        addToQuote(product) {
            console.log('Add to quote:', product);
            // Here you would typically add to a quote/cart system
        },

        printCatalog() {
            window.print();
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

@push('styles')
<style>
@media print {
    .btn, .input, nav, .sidebar {
        display: none !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #e5e7eb !important;
    }
}
</style>
@endpush