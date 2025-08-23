@extends('layouts.dashboard')

@section('title', 'Manajemen Inventori')
@section('page-title')
<span class="text-base lg:text-2xl">Manajemen Inventori</span>
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
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Inventori</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="inventoryManager()" class="space-y-4 lg:space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Daftar Inventori</h2>
            <p class="text-sm text-muted">Kelola stok dan inventori produk Anda</p>
        </div>
        
        <!-- Desktop Actions -->
        <div class="hidden sm:flex items-center justify-end space-x-3">
            <button @click="exportInventory()"
                    class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor
            </button>
            <a href="{{ route('inventory.adjustments') }}" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                </svg>
                Penyesuaian Stok
            </a>
            <a href="{{ route('inventory.movements') }}" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                </svg>
                Pergerakan Stok
            </a>
        </div>

        <!-- Mobile Actions -->
        <div class="sm:hidden grid grid-cols-1 gap-3">
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('inventory.adjustments') }}" class="btn-secondary flex items-center justify-center text-sm py-3 px-4">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                    </svg>
                    <span class="truncate">Penyesuaian</span>
                </a>
                <a href="{{ route('inventory.movements') }}" class="btn-primary flex items-center justify-center text-sm py-3 px-4">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                    </svg>
                    <span class="truncate">Pergerakan</span>
                </a>
            </div>
            <button @click="exportInventory()"
                    class="btn-secondary flex items-center justify-center text-sm py-3 px-4">
                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="truncate">Ekspor Data</span>
            </button>
        </div>
    </div>

    <!-- Inventory Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Total Item</p>
                    <p class="text-2xl lg:text-3xl font-bold text-foreground" x-text="stats.total_items"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-green-600">+8</span> bulan ini
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4.5m8-4.5v10l-8 4.5m0-9L4 7m8 4.5v9"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Stok Rendah</p>
                    <p class="text-2xl lg:text-3xl font-bold text-red-600" x-text="stats.low_stock"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-red-600">Perlu perhatian</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Nilai Inventori</p>
                    <p class="text-2xl lg:text-3xl font-bold text-green-600" x-text="formatCurrency(stats.inventory_value)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-green-600">+5.2%</span> dari bulan lalu
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Perputaran Stok</p>
                    <p class="text-2xl lg:text-3xl font-bold text-primary" x-text="stats.turnover_ratio + 'x'"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-green-600">+0.3x</span> dari bulan lalu
                    </p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card p-4 lg:p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-foreground mb-2">Cari Inventori</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           x-model="filters.search"
                           @input.debounce.300ms="loadInventory()"
                           class="input pl-10" 
                           placeholder="Cari berdasarkan nama, SKU, atau kategori...">
                </div>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Kategori</label>
                <select x-model="filters.category" 
                        @change="loadInventory()"
                        class="input">
                    <option value="">Semua Kategori</option>
                    <option value="raw_materials">Bahan Baku</option>
                    <option value="packaging">Kemasan</option>
                    <option value="finished_goods">Produk Jadi</option>
                    <option value="equipment">Peralatan</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                <select x-model="filters.status" 
                        @change="loadInventory()"
                        class="input">
                    <option value="">Semua Status</option>
                    <option value="normal">Stok Normal</option>
                    <option value="low">Stok Rendah</option>
                    <option value="out">Habis</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card">
        <div class="p-4 lg:p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-foreground">
                    Daftar Inventori (<span x-text="pagination.total"></span>)
                </h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-muted">Tampilkan:</span>
                    <select x-model="pagination.per_page" 
                            @change="loadInventory()"
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
                        <th>Kategori</th>
                        <th>Stok Saat Ini</th>
                        <th>Stok Minimum</th>
                        <th>Harga Satuan</th>
                        <th>Nilai Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in inventory" :key="item.id">
                        <tr class="hover:bg-surface/50">
                            <td>
                                <input type="checkbox"
                                       :value="item.id"
                                       x-model="selectedItems"
                                       class="rounded border-border text-primary focus:ring-primary">
                            </td>
                            <td>
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-surface rounded-lg flex items-center justify-center overflow-hidden">
                                        <svg class="w-5 h-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-foreground" x-text="item.name"></div>
                                        <div class="text-sm text-muted" x-text="'SKU: ' + item.sku"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="getCategoryClass(item.category)"
                                      x-text="getCategoryText(item.category)">
                                </span>
                            </td>
                            <td>
                                <span class="font-medium text-foreground" x-text="item.current_stock + ' ' + item.unit"></span>
                            </td>
                            <td>
                                <span class="text-foreground" x-text="item.min_stock + ' ' + item.unit"></span>
                            </td>
                            <td>
                                <span class="font-medium text-foreground" x-text="formatCurrency(item.unit_price)"></span>
                            </td>
                            <td>
                                <span class="font-medium text-foreground" x-text="formatCurrency(item.total_value)"></span>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="getStatusClass(item.status)"
                                      x-text="getStatusText(item.status)">
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <button @click="viewItem(item)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button @click="adjustStock(item)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
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
            <template x-for="item in inventory" :key="item.id">
                <div class="card rounded-xl p-4 shadow-sm">
                    <!-- Header with checkbox and status -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox"
                                   :value="item.id"
                                   x-model="selectedItems"
                                   class="w-5 h-5 rounded border-border text-primary focus:ring-primary focus:ring-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                  :class="getStatusClass(item.status)"
                                  x-text="getStatusText(item.status)">
                            </span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <button @click="viewItem(item)"
                                    class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <button @click="adjustStock(item)"
                                    class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Item Info -->
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-background rounded-xl flex items-center justify-center flex-shrink-0 border border-border">
                            <svg class="w-6 h-6 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-foreground text-base leading-tight" x-text="item.name"></h3>
                            <p class="text-sm text-muted mt-1" x-text="'SKU: ' + item.sku"></p>
                            <div class="flex items-center space-x-2 mt-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                      :class="getCategoryClass(item.category)"
                                      x-text="getCategoryText(item.category)">
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Item Details -->
                    <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-border">
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Stok Saat Ini</span>
                            <p class="font-medium text-foreground mt-1" x-text="item.current_stock + ' ' + item.unit"></p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Stok Minimum</span>
                            <p class="text-sm text-foreground mt-1" x-text="item.min_stock + ' ' + item.unit"></p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Harga Satuan</span>
                            <p class="font-semibold text-foreground text-base mt-1" x-text="formatCurrency(item.unit_price)"></p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Nilai Total</span>
                            <p class="font-semibold text-foreground text-base mt-1" x-text="formatCurrency(item.total_value)"></p>
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
                    (<span x-text="pagination.total"></span> item)
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
                    dari <span x-text="pagination.total"></span> item
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
<!-- Bulk Actions -->
    <div x-show="selectedItems.length > 0" 
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
                    <span x-text="selectedItems.length"></span> item dipilih
                </span>
                <button @click="selectedItems = []" class="p-1 text-muted hover:text-foreground">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <button @click="bulkStockIn()" class="btn-secondary text-sm py-2 px-3 text-center">
                    Stok Masuk
                </button>
                <button @click="bulkStockOut()" class="btn-secondary text-sm py-2 px-3 text-center">
                    Stok Keluar
                </button>
            </div>
        </div>

        <!-- Desktop Layout -->
        <div class="hidden lg:flex items-center space-x-4">
            <span class="text-sm text-foreground">
                <span x-text="selectedItems.length"></span> item dipilih
            </span>
            <div class="flex items-center space-x-2">
                <button @click="bulkStockIn()" class="btn-secondary text-sm py-1 px-3">
                    Stok Masuk
                </button>
                <button @click="bulkStockOut()" class="btn-secondary text-sm py-1 px-3">
                    Stok Keluar
                </button>
                <button @click="selectedItems = []" class="text-muted hover:text-foreground">
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
function inventoryManager() {
    return {
        inventory: [],
        selectedItems: [],
        loading: false,
        stats: {
            total_items: 156,
            low_stock: 12,
            inventory_value: 45200000,
            turnover_ratio: 8.5
        },
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
            await this.loadInventory();
        },

        async loadInventory() {
            this.loading = true;
            
            try {
                // Dummy data untuk testing frontend
                const dummyInventory = [
                    {
                        id: 1,
                        name: 'Tepung Terigu Premium',
                        sku: 'TPG-001',
                        category: 'raw_materials',
                        current_stock: 250,
                        min_stock: 100,
                        unit: 'kg',
                        unit_price: 12000,
                        total_value: 3000000,
                        status: 'normal'
                    },
                    {
                        id: 2,
                        name: 'Gula Pasir',
                        sku: 'GUL-001',
                        category: 'raw_materials',
                        current_stock: 45,
                        min_stock: 50,
                        unit: 'kg',
                        unit_price: 15000,
                        total_value: 675000,
                        status: 'low'
                    },
                    {
                        id: 3,
                        name: 'Kemasan Plastik Premium',
                        sku: 'KMP-001',
                        category: 'packaging',
                        current_stock: 1500,
                        min_stock: 500,
                        unit: 'pcs',
                        unit_price: 1500,
                        total_value: 2250000,
                        status: 'normal'
                    },
                    {
                        id: 4,
                        name: 'Mentega',
                        sku: 'MTG-001',
                        category: 'raw_materials',
                        current_stock: 8,
                        min_stock: 10,
                        unit: 'kg',
                        unit_price: 45000,
                        total_value: 360000,
                        status: 'low'
                    },
                    {
                        id: 5,
                        name: 'Vanilla Extract',
                        sku: 'VNL-001',
                        category: 'raw_materials',
                        current_stock: 12,
                        min_stock: 15,
                        unit: 'botol',
                        unit_price: 25000,
                        total_value: 300000,
                        status: 'low'
                    }
                ];

                // Apply filters
                let filteredInventory = dummyInventory;
                
                if (this.filters.search) {
                    filteredInventory = filteredInventory.filter(item => 
                        item.name.toLowerCase().includes(this.filters.search.toLowerCase()) ||
                        item.sku.toLowerCase().includes(this.filters.search.toLowerCase())
                    );
                }
                
                if (this.filters.category) {
                    filteredInventory = filteredInventory.filter(item => item.category === this.filters.category);
                }
                
                if (this.filters.status) {
                    filteredInventory = filteredInventory.filter(item => item.status === this.filters.status);
                }

                // Simulate pagination
                const total = filteredInventory.length;
                const from = (this.pagination.current_page - 1) * this.pagination.per_page + 1;
                const to = Math.min(from + this.pagination.per_page - 1, total);
                
                this.inventory = filteredInventory.slice(from - 1, to);
                this.pagination = {
                    current_page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    total: total,
                    from: from,
                    to: to,
                    prev_page_url: this.pagination.current_page > 1 ? '#' : null,
                    next_page_url: to < total ? '#' : null,
                    last_page: Math.ceil(total / this.pagination.per_page)
                };
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal memuat data inventori'
                });
            } finally {
                this.loading = false;
            }
        },

        toggleSelectAll(checked) {
            if (checked) {
                this.selectedItems = this.inventory.map(i => i.id);
            } else {
                this.selectedItems = [];
            }
        },

        async viewItem(item) {
            this.$store.modal.open(`
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Detail Inventori</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><span class="font-medium">Nama:</span> ${item.name}</div>
                            <div><span class="font-medium">SKU:</span> ${item.sku}</div>
                            <div><span class="font-medium">Kategori:</span> ${this.getCategoryText(item.category)}</div>
                            <div><span class="font-medium">Stok Saat Ini:</span> ${item.current_stock} ${item.unit}</div>
                            <div><span class="font-medium">Stok Minimum:</span> ${item.min_stock} ${item.unit}</div>
                            <div><span class="font-medium">Harga Satuan:</span> ${this.formatCurrency(item.unit_price)}</div>
                            <div><span class="font-medium">Nilai Total:</span> ${this.formatCurrency(item.total_value)}</div>
                            <div><span class="font-medium">Status:</span> ${this.getStatusText(item.status)}</div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button onclick="Alpine.store('modal').close()" class="btn-secondary">Tutup</button>
                        <button onclick="Alpine.store('modal').close()" class="btn-primary">Penyesuaian Stok</button>
                    </div>
                </div>
            `);
        },

        async adjustStock(item) {
            // Redirect to stock adjustment page
            window.location.href = `/inventory/adjustments?item_id=${item.id}`;
        },

        async bulkStockIn() {
            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: `Berhasil menambah stok ${this.selectedItems.length} item.`
                });
                this.selectedItems = [];
                await this.loadInventory();
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal menambah stok'
                });
            }
        },

        async bulkStockOut() {
            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: `Berhasil mengurangi stok ${this.selectedItems.length} item.`
                });
                this.selectedItems = [];
                await this.loadInventory();
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal mengurangi stok'
                });
            }
        },

        async exportInventory() {
            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: 'Data inventori berhasil diekspor.'
                });
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal mengekspor data'
                });
            }
        },

        previousPage() {
            if (this.pagination.prev_page_url) {
                this.pagination.current_page--;
                this.loadInventory();
            }
        },

        nextPage() {
            if (this.pagination.next_page_url) {
                this.pagination.current_page++;
                this.loadInventory();
            }
        },

        goToPage(page) {
            this.pagination.current_page = page;
            this.loadInventory();
        },

        getCategoryClass(category) {
            const classes = {
                'raw_materials': 'bg-blue-100 text-blue-800',
                'packaging': 'bg-green-100 text-green-800',
                'finished_goods': 'bg-purple-100 text-purple-800',
                'equipment': 'bg-orange-100 text-orange-800'
            };
            return classes[category] || 'bg-gray-100 text-gray-800';
        },

        getCategoryText(category) {
            const texts = {
                'raw_materials': 'Bahan Baku',
                'packaging': 'Kemasan',
                'finished_goods': 'Produk Jadi',
                'equipment': 'Peralatan'
            };
            return texts[category] || category;
        },

        getStatusClass(status) {
            const classes = {
                'normal': 'bg-green-100 text-green-800',
                'low': 'bg-red-100 text-red-800',
                'out': 'bg-gray-100 text-gray-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },

        getStatusText(status) {
            const texts = {
                'normal': 'Normal',
                'low': 'Stok Rendah',
                'out': 'Habis'
            };
            return texts[status] || status;
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