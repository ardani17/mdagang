@extends('layouts.dashboard')

@section('title', 'Manajemen Produk')
@section('page-title', 'Manajemen Produk')

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
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Produk</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="productManager()" class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-foreground">Daftar Produk</h2>
            <p class="text-muted">Kelola semua produk makanan dan minuman Anda</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="exportProducts()" 
                    class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor
            </button>
            <a href="{{ route('products.create') }}" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Produk
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-foreground mb-2">Cari Produk</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           x-model="filters.search"
                           @input.debounce.300ms="loadProducts()"
                           class="input pl-10" 
                           placeholder="Cari berdasarkan nama, SKU, atau deskripsi...">
                </div>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Kategori</label>
                <select x-model="filters.category" 
                        @change="loadProducts()"
                        class="input">
                    <option value="">Semua Kategori</option>
                    <option value="food">Makanan</option>
                    <option value="beverage">Minuman</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                <select x-model="filters.status" 
                        @change="loadProducts()"
                        class="input">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                    <option value="low_stock">Stok Rendah</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-foreground">
                    Daftar Produk (<span x-text="pagination.total"></span>)
                </h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-muted">Tampilkan:</span>
                    <select x-model="pagination.per_page" 
                            @change="loadProducts()"
                            class="input py-1 text-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-12">
                            <input type="checkbox"
                                   @change="toggleSelectAll($event.target.checked)"
                                   class="rounded border-border text-primary focus:ring-primary">
                        </th>
                        <th>Produk</th>
                        <th>SKU</th>
                        <th>Kategori</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="product in products" :key="product.id">
                        <tr class="hover:bg-surface/50">
                            <td>
                                <input type="checkbox"
                                       :value="product.id"
                                       x-model="selectedProducts"
                                       class="rounded border-border text-primary focus:ring-primary">
                            </td>
                            <td>
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-surface rounded-lg flex items-center justify-center overflow-hidden">
                                        <img x-show="product.image"
                                             :src="product.image"
                                             :alt="product.name"
                                             class="w-full h-full object-cover">
                                        <svg x-show="!product.image" class="w-6 h-6 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-foreground" x-text="product.name"></div>
                                        <div class="text-sm text-muted" x-text="product.description"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="font-mono text-sm" x-text="product.sku"></span>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="product.type === 'food' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                                      x-text="product.type === 'food' ? 'Makanan' : 'Minuman'">
                                </span>
                            </td>
                            <td>
                                <span class="font-medium" x-text="formatCurrency(product.selling_price)"></span>
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <span x-text="product.current_stock + ' ' + product.unit"></span>
                                    <span x-show="product.current_stock <= product.min_stock"
                                          class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Rendah
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                      x-text="product.is_active ? 'Aktif' : 'Tidak Aktif'">
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <button @click="viewProduct(product)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <a :href="`/products/${product.id}/edit`"
                                       class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button @click="calculateCost(product)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                    <button @click="deleteProduct(product)"
                                            class="p-1 text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
        <div class="lg:hidden space-y-3 mobile-card-spacing">
            <template x-for="product in products" :key="product.id">
                <div class="card rounded-xl p-4 shadow-sm">
                    <!-- Header with checkbox and actions -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox"
                                   :value="product.id"
                                   x-model="selectedProducts"
                                   class="w-5 h-5 rounded border-border text-primary focus:ring-primary focus:ring-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                  :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                  x-text="product.is_active ? 'Aktif' : 'Tidak Aktif'">
                            </span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <button @click="viewProduct(product)"
                                    class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <a :href="`/products/${product.id}/edit`"
                               class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <button @click="calculateCost(product)"
                                    class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="flex items-start space-x-4">
                        <div class="w-18 h-18 bg-background rounded-xl flex items-center justify-center overflow-hidden flex-shrink-0 border border-border">
                            <img x-show="product.image"
                                 :src="product.image"
                                 :alt="product.name"
                                 class="w-full h-full object-cover">
                            <svg x-show="!product.image" class="w-8 h-8 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-foreground text-base leading-tight" x-text="product.name"></h3>
                            <p class="text-sm text-muted mt-1 line-clamp-2" x-text="product.description || 'Tidak ada deskripsi'"></p>
                            <div class="flex items-center space-x-2 mt-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                      :class="product.type === 'food' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                                      x-text="product.type === 'food' ? 'Makanan' : 'Minuman'">
                                </span>
                                <span x-show="product.current_stock <= product.min_stock"
                                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Stok Rendah
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Product Details -->
                    <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-border">
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">SKU</span>
                            <p class="font-mono text-sm text-foreground mt-1" x-text="product.sku"></p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Harga Jual</span>
                            <p class="font-semibold text-foreground text-base mt-1" x-text="formatCurrency(product.selling_price)"></p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Stok Tersedia</span>
                            <p class="text-sm text-foreground mt-1" x-text="product.current_stock + ' ' + product.unit"></p>
                        </div>
                        <div class="flex items-end justify-end">
                            <button @click="deleteProduct(product)"
                                    class="mobile-action-button p-2.5 text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Pagination -->
        <div class="p-4 lg:p-6 border-t border-border">
            <!-- Mobile Pagination -->
            <div class="lg:hidden">
                <div class="text-center text-sm text-muted mb-4">
                    Halaman <span x-text="pagination.current_page"></span> dari <span x-text="pagination.last_page"></span>
                    (<span x-text="pagination.total"></span> produk)
                </div>
                <div class="flex items-center justify-center space-x-2">
                    <button @click="previousPage()"
                            :disabled="!pagination.prev_page_url"
                            class="flex items-center justify-center w-12 h-12 rounded-lg border border-border bg-background text-muted hover:text-foreground disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <div class="flex items-center space-x-1">
                        <template x-for="page in paginationPages.slice(0, 3)" :key="page">
                            <button @click="goToPage(page)"
                                    :class="page === pagination.current_page ? 'bg-primary text-white border-primary' : 'bg-background text-muted border-border hover:text-foreground'"
                                    class="w-12 h-12 rounded-lg border transition-colors text-sm font-medium"
                                    x-text="page">
                            </button>
                        </template>
                        <span x-show="paginationPages.length > 3" class="text-muted px-2">...</span>
                    </div>
                    <button @click="nextPage()"
                            :disabled="!pagination.next_page_url"
                            class="flex items-center justify-center w-12 h-12 rounded-lg border border-border bg-background text-muted hover:text-foreground disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Desktop Pagination -->
            <div class="hidden lg:flex items-center justify-between">
                <div class="text-sm text-muted">
                    Menampilkan <span x-text="pagination.from"></span> sampai <span x-text="pagination.to"></span>
                    dari <span x-text="pagination.total"></span> produk
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="previousPage()"
                            :disabled="!pagination.prev_page_url"
                            class="btn-secondary text-sm py-1 px-3 disabled:opacity-50 disabled:cursor-not-allowed">
                        Sebelumnya
                    </button>
                    <template x-for="page in paginationPages" :key="page">
                        <button @click="goToPage(page)"
                                :class="page === pagination.current_page ? 'btn-primary' : 'btn-secondary'"
                                class="text-sm py-1 px-3"
                                x-text="page">
                        </button>
                    </template>
                    <button @click="nextPage()"
                            :disabled="!pagination.next_page_url"
                            class="btn-secondary text-sm py-1 px-3 disabled:opacity-50 disabled:cursor-not-allowed">
                        Selanjutnya
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div x-show="selectedProducts.length > 0"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-4"
         class="fixed bottom-4 left-4 right-4 lg:left-1/2 lg:right-auto lg:transform lg:-translate-x-1/2 lg:w-auto bg-surface border border-border rounded-xl shadow-xl p-4 z-50">
        
        <!-- Mobile Layout -->
        <div class="lg:hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-foreground">
                    <span x-text="selectedProducts.length"></span> produk dipilih
                </span>
                <button @click="selectedProducts = []" class="p-1 text-muted hover:text-foreground">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <button @click="bulkActivate()" class="btn-secondary text-sm py-2 px-3 text-center">
                    Aktifkan
                </button>
                <button @click="bulkDeactivate()" class="btn-secondary text-sm py-2 px-3 text-center">
                    Nonaktifkan
                </button>
                <button @click="bulkDelete()" class="bg-red-600 text-white text-sm py-2 px-3 rounded-lg hover:bg-red-700 transition-colors">
                    Hapus
                </button>
            </div>
        </div>

        <!-- Desktop Layout -->
        <div class="hidden lg:flex items-center space-x-4">
            <span class="text-sm text-foreground">
                <span x-text="selectedProducts.length"></span> produk dipilih
            </span>
            <div class="flex items-center space-x-2">
                <button @click="bulkActivate()" class="btn-secondary text-sm py-1 px-3">
                    Aktifkan
                </button>
                <button @click="bulkDeactivate()" class="btn-secondary text-sm py-1 px-3">
                    Nonaktifkan
                </button>
                <button @click="bulkDelete()" class="bg-red-600 text-white text-sm py-1 px-3 rounded hover:bg-red-700">
                    Hapus
                </button>
                <button @click="selectedProducts = []" class="text-muted hover:text-foreground">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function productManager() {
    return {
        products: [],
        selectedProducts: [],
        loading: false,
        filters: {
            search: '',
            category: '',
            status: ''
        },
        pagination: {
            current_page: 1,
            per_page: 25,
            total: 0,
            from: 0,
            to: 0,
            prev_page_url: null,
            next_page_url: null,
            last_page: 1
        },

        get paginationPages() {
            const pages = [];
            const current = this.pagination.current_page;
            const last = this.pagination.last_page;
            
            // Show max 5 pages
            let start = Math.max(1, current - 2);
            let end = Math.min(last, start + 4);
            
            if (end - start < 4) {
                start = Math.max(1, end - 4);
            }
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            
            return pages;
        },

        async init() {
            await this.loadProducts();
        },

        async loadProducts() {
            this.loading = true;
            
            try {
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    search: this.filters.search,
                    category: this.filters.category,
                    status: this.filters.status
                });

                const response = await fetch(`/api/products?${params}`);
                const data = await response.json();

                if (response.ok) {
                    this.products = data.data;
                    this.pagination = {
                        current_page: data.current_page,
                        per_page: data.per_page,
                        total: data.total,
                        from: data.from,
                        to: data.to,
                        prev_page_url: data.prev_page_url,
                        next_page_url: data.next_page_url,
                        last_page: data.last_page
                    };
                } else {
                    throw new Error(data.message || 'Gagal memuat produk');
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

        toggleSelectAll(checked) {
            if (checked) {
                this.selectedProducts = this.products.map(p => p.id);
            } else {
                this.selectedProducts = [];
            }
        },

        async viewProduct(product) {
            this.$store.modal.open(`
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Detail Produk</h3>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-20 h-20 bg-surface rounded-lg flex items-center justify-center overflow-hidden">
                                ${product.image ? `<img src="${product.image}" alt="${product.name}" class="w-full h-full object-cover">` : `<svg class="w-8 h-8 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>`}
                            </div>
                            <div>
                                <h4 class="font-semibold text-foreground">${product.name}</h4>
                                <p class="text-sm text-muted">${product.description || 'Tidak ada deskripsi'}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><span class="font-medium">SKU:</span> ${product.sku}</div>
                            <div><span class="font-medium">Kategori:</span> ${product.type === 'food' ? 'Makanan' : 'Minuman'}</div>
                            <div><span class="font-medium">Harga Jual:</span> ${this.formatCurrency(product.selling_price)}</div>
                            <div><span class="font-medium">Stok:</span> ${product.current_stock} ${product.unit}</div>
                            <div><span class="font-medium">Stok Minimum:</span> ${product.min_stock} ${product.unit}</div>
                            <div><span class="font-medium">Status:</span> ${product.is_active ? 'Aktif' : 'Tidak Aktif'}</div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button onclick="Alpine.store('modal').close()" class="btn-secondary">Tutup</button>
                        <a href="/products/${product.id}/edit" class="btn-primary">Edit Produk</a>
                    </div>
                </div>
            `);
        },

        async calculateCost(product) {
            // Redirect to cost calculation page
            window.location.href = `/products/${product.id}/costs`;
        },

        async deleteProduct(product) {
            if (!confirm(`Apakah Anda yakin ingin menghapus produk "${product.name}"?`)) {
                return;
            }

            try {
                const response = await fetch(`/api/products/${product.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    this.$store.notifications.add({
                        type: 'success',
                        title: 'Berhasil!',
                        message: 'Produk berhasil dihapus.'
                    });
                    await this.loadProducts();
                } else {
                    throw new Error('Gagal menghapus produk');
                }
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: error.message
                });
            }
        },

        async bulkActivate() {
            await this.bulkAction('activate', 'mengaktifkan');
        },

        async bulkDeactivate() {
            await this.bulkAction('deactivate', 'menonaktifkan');
        },

        async bulkDelete() {
            if (!confirm(`Apakah Anda yakin ingin menghapus ${this.selectedProducts.length} produk yang dipilih?`)) {
                return;
            }
            await this.bulkAction('delete', 'menghapus');
        },

        async bulkAction(action, actionText) {
            try {
                const response = await fetch('/api/products/bulk', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        action: action,
                        ids: this.selectedProducts
                    })
                });

                if (response.ok) {
                    this.$store.notifications.add({
                        type: 'success',
                        title: 'Berhasil!',
                        message: `Berhasil ${actionText} ${this.selectedProducts.length} produk.`
                    });
                    this.selectedProducts = [];
                    await this.loadProducts();
                } else {
                    throw new Error(`Gagal ${actionText} produk`);
                }
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: error.message
                });
            }
        },

        async exportProducts() {
            try {
                const response = await fetch('/api/products/export', {
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
                    a.download = `produk_${new Date().toISOString().split('T')[0]}.xlsx`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);

                    this.$store.notifications.add({
                        type: 'success',
                        title: 'Berhasil!',
                        message: 'Data produk berhasil diekspor.'
                    });
                } else {
                    throw new Error('Gagal mengekspor data');
                }
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: error.message
                });
            }
        },

        previousPage() {
            if (this.pagination.prev_page_url) {
                this.pagination.current_page--;
                this.loadProducts();
            }
        },

        nextPage() {
            if (this.pagination.next_page_url) {
                this.pagination.current_page++;
                this.loadProducts();
            }
        },

        goToPage(page) {
            this.pagination.current_page = page;
            this.loadProducts();
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }
    }
}
</script>
@endpush