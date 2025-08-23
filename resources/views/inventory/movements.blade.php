@extends('layouts.dashboard')

@section('title', 'Pergerakan Stok')
@section('page-title')
<span class="text-base lg:text-2xl">Pergerakan Stok</span>
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
<li class="inline-flex items-center">
    <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <a href="{{ route('inventory.index') }}" class="text-sm font-medium text-muted hover:text-foreground">Inventori</a>
</li>
<li>
    <div class="flex items-center">
        <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Pergerakan Stok</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="movementManager()" class="space-y-4 lg:space-y-6">
    <!-- Mobile Back Button -->
    <div class="sm:hidden mb-4">
        <a href="{{ route('inventory.index') }}"
           class="inline-flex items-center px-4 py-2 bg-background border border-border rounded-lg text-foreground hover:bg-surface transition-colors">
            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="text-sm font-medium">Kembali ke Inventori</span>
        </a>
    </div>

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Pergerakan Stok</h2>
            <p class="text-sm text-muted">Kelola stok masuk dan keluar inventori</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="exportMovements()" 
                    class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor
            </button>
            <button @click="addStockIn()" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Stok Masuk
            </button>
        </div>
    </div>

    <!-- Movement Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Stok Masuk Hari Ini</p>
                    <p class="text-2xl lg:text-3xl font-bold text-green-600" x-text="stats.stock_in_today"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-green-600">+5</span> dari kemarin
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Stok Keluar Hari Ini</p>
                    <p class="text-2xl lg:text-3xl font-bold text-red-600" x-text="stats.stock_out_today"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-red-600">+3</span> dari kemarin
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Total Transaksi</p>
                    <p class="text-2xl lg:text-3xl font-bold text-blue-600" x-text="stats.total_transactions"></p>
                    <p class="text-xs text-muted mt-1">
                        Bulan ini
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Nilai Pergerakan</p>
                    <p class="text-2xl lg:text-3xl font-bold text-primary" x-text="formatCurrency(stats.movement_value)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-green-600">+12%</span> dari bulan lalu
                    </p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
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
                <label class="block text-sm font-medium text-foreground mb-2">Cari Pergerakan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           x-model="filters.search"
                           @input.debounce.300ms="loadMovements()"
                           class="input pl-10" 
                           placeholder="Cari berdasarkan produk, referensi...">
                </div>
            </div>

            <!-- Type Filter -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Jenis</label>
                <select x-model="filters.type" 
                        @change="loadMovements()"
                        class="input">
                    <option value="">Semua Jenis</option>
                    <option value="in">Stok Masuk</option>
                    <option value="out">Stok Keluar</option>
                    <option value="adjustment">Penyesuaian</option>
                </select>
            </div>

            <!-- Date Filter -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Tanggal</label>
                <input type="date" 
                       x-model="filters.date"
                       @change="loadMovements()"
                       class="input">
            </div>
        </div>
    </div>

    <!-- Movements Table -->
    <div class="card">
        <div class="p-4 lg:p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-foreground">
                    Riwayat Pergerakan (<span x-text="pagination.total"></span>)
                </h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-muted">Tampilkan:</span>
                    <select x-model="pagination.per_page" 
                            @change="loadMovements()"
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
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Total Nilai</th>
                        <th>Referensi</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="movement in movements" :key="movement.id">
                        <tr class="hover:bg-surface/50">
                            <td>
                                <div class="text-sm text-foreground" x-text="formatDate(movement.created_at)"></div>
                                <div class="text-xs text-muted" x-text="formatTime(movement.created_at)"></div>
                            </td>
                            <td>
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-surface rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-foreground" x-text="movement.product_name"></div>
                                        <div class="text-xs text-muted" x-text="'SKU: ' + movement.product_sku"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="getTypeClass(movement.type)"
                                      x-text="getTypeText(movement.type)">
                                </span>
                            </td>
                            <td>
                                <span class="font-medium"
                                      :class="movement.type === 'in' ? 'text-green-600' : 'text-red-600'"
                                      x-text="(movement.type === 'in' ? '+' : '-') + movement.quantity + ' ' + movement.unit">
                                </span>
                            </td>
                            <td>
                                <span class="text-foreground" x-text="formatCurrency(movement.unit_price)"></span>
                            </td>
                            <td>
                                <span class="font-medium text-foreground" x-text="formatCurrency(movement.total_value)"></span>
                            </td>
                            <td>
                                <span class="text-foreground" x-text="movement.reference || '-'"></span>
                            </td>
                            <td>
                                <span class="text-muted" x-text="movement.notes || '-'"></span>
                            </td>
                            <td>
                                <button @click="viewMovement(movement)"
                                        class="p-1 text-muted hover:text-foreground">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden space-y-3 mobile-card-spacing">
            <template x-for="movement in movements" :key="movement.id">
                <div class="card rounded-xl p-4 shadow-sm">
                    <!-- Header with type and action -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-background rounded-xl flex items-center justify-center border border-border">
                                <svg class="w-5 h-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-muted" x-text="formatDate(movement.created_at) + ' - ' + formatTime(movement.created_at)"></p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1"
                                      :class="getTypeClass(movement.type)"
                                      x-text="getTypeText(movement.type)">
                                </span>
                            </div>
                        </div>
                        <button @click="viewMovement(movement)"
                                class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Movement Info -->
                    <div class="space-y-3">
                        <div>
                            <h3 class="font-semibold text-foreground text-base leading-tight" x-text="movement.product_name"></h3>
                            <p class="text-sm text-muted mt-1" x-text="'SKU: ' + movement.product_sku"></p>
                            <p class="text-sm text-muted" x-text="'Ref: ' + (movement.reference || '-')"></p>
                        </div>

                        <!-- Movement Details -->
                        <div class="grid grid-cols-2 gap-4 pt-3 border-t border-border">
                            <div>
                                <span class="text-xs font-medium text-muted uppercase tracking-wide">Jumlah</span>
                                <p class="font-medium mt-1"
                                   :class="movement.type === 'in' ? 'text-green-600' : 'text-red-600'"
                                   x-text="(movement.type === 'in' ? '+' : '-') + movement.quantity + ' ' + movement.unit"></p>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-muted uppercase tracking-wide">Harga Satuan</span>
                                <p class="font-medium text-foreground mt-1" x-text="formatCurrency(movement.unit_price)"></p>
                            </div>
                            <div class="col-span-2">
                                <span class="text-xs font-medium text-muted uppercase tracking-wide">Total Nilai</span>
                                <p class="font-semibold text-foreground text-base mt-1" x-text="formatCurrency(movement.total_value)"></p>
                            </div>
                        </div>

                        <div class="text-sm text-muted" x-show="movement.notes">
                            <p>Keterangan: <span x-text="movement.notes"></span></p>
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
                    (<span x-text="pagination.total"></span> pergerakan)
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
                    dari <span x-text="pagination.total"></span> pergerakan
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
</div>
@endsection

@push('scripts')
<script>
function movementManager() {
    return {
        movements: [],
        loading: false,
        stats: {
            stock_in_today: 24,
            stock_out_today: 18,
            total_transactions: 156,
            movement_value: 8500000
        },
        filters: {
            search: '',
            type: '',
            date: ''
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
            await this.loadMovements();
        },

        async loadMovements() {
            this.loading = true;
            
            try {
                // Dummy data untuk testing frontend
                const dummyMovements = [
                    {
                        id: 1,
                        product_name: 'Tepung Terigu Premium',
                        product_sku: 'TPG-001',
                        type: 'in',
                        quantity: 50,
                        unit: 'kg',
                        unit_price: 12000,
                        total_value: 600000,
                        reference: 'PO-2024-001',
                        notes: 'Pembelian dari supplier',
                        created_at: '2024-08-17T09:30:00Z'
                    },
                    {
                        id: 2,
                        product_name: 'Gula Pasir',
                        product_sku: 'GUL-001',
                        type: 'out',
                        quantity: 15,
                        unit: 'kg',
                        unit_price: 15000,
                        total_value: 225000,
                        reference: 'PRD-2024-045',
                        notes: 'Untuk produksi kue',
                        created_at: '2024-08-17T14:15:00Z'
                    },
                    {
                        id: 3,
                        product_name: 'Kemasan Plastik Premium',
                        product_sku: 'KMP-001',
                        type: 'in',
                        quantity: 500,
                        unit: 'pcs',
                        unit_price: 1500,
                        total_value: 750000,
                        reference: 'PO-2024-002',
                        notes: 'Stok kemasan bulanan',
                        created_at: '2024-08-16T11:20:00Z'
                    }
                ];

                // Apply filters
                let filteredMovements = dummyMovements;
                
                if (this.filters.search) {
                    filteredMovements = filteredMovements.filter(movement => 
                        movement.product_name.toLowerCase().includes(this.filters.search.toLowerCase()) ||
                        movement.product_sku.toLowerCase().includes(this.filters.search.toLowerCase()) ||
                        (movement.reference && movement.reference.toLowerCase().includes(this.filters.search.toLowerCase()))
                    );
                }
                
                if (this.filters.type) {
                    filteredMovements = filteredMovements.filter(movement => movement.type === this.filters.type);
                }

                // Simulate pagination
                const total = filteredMovements.length;
                const from = (this.pagination.current_page - 1) * this.pagination.per_page + 1;
                const to = Math.min(from + this.pagination.per_page - 
1, total);
                
                this.movements = filteredMovements.slice(from - 1, to);
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
                    message: 'Gagal memuat data pergerakan stok'
                });
            } finally {
                this.loading = false;
            }
        },

        async viewMovement(movement) {
            this.$store.modal.open(`
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Detail Pergerakan Stok</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><span class="font-medium">Produk:</span> ${movement.product_name}</div>
                            <div><span class="font-medium">SKU:</span> ${movement.product_sku}</div>
                            <div><span class="font-medium">Jenis:</span> ${this.getTypeText(movement.type)}</div>
                            <div><span class="font-medium">Jumlah:</span> ${(movement.type === 'in' ? '+' : '-') + movement.quantity + ' ' + movement.unit}</div>
                            <div><span class="font-medium">Harga Satuan:</span> ${this.formatCurrency(movement.unit_price)}</div>
                            <div><span class="font-medium">Total Nilai:</span> ${this.formatCurrency(movement.total_value)}</div>
                            <div><span class="font-medium">Referensi:</span> ${movement.reference || '-'}</div>
                            <div><span class="font-medium">Tanggal:</span> ${this.formatDate(movement.created_at)}</div>
                        </div>
                        ${movement.notes ? `<div class="text-sm"><span class="font-medium">Keterangan:</span> ${movement.notes}</div>` : ''}
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button onclick="Alpine.store('modal').close()" class="btn-secondary">Tutup</button>
                    </div>
                </div>
            `);
        },

        async addStockIn() {
            // Redirect to stock in form
            window.location.href = '/inventory/movements/create?type=in';
        },

        async exportMovements() {
            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: 'Data pergerakan stok berhasil diekspor.'
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
                this.loadMovements();
            }
        },

        nextPage() {
            if (this.pagination.next_page_url) {
                this.pagination.current_page++;
                this.loadMovements();
            }
        },

        goToPage(page) {
            this.pagination.current_page = page;
            this.loadMovements();
        },

        getTypeClass(type) {
            const classes = {
                'in': 'bg-green-100 text-green-800',
                'out': 'bg-red-100 text-red-800',
                'adjustment': 'bg-blue-100 text-blue-800'
            };
            return classes[type] || 'bg-gray-100 text-gray-800';
        },

        getTypeText(type) {
            const texts = {
                'in': 'Stok Masuk',
                'out': 'Stok Keluar',
                'adjustment': 'Penyesuaian'
            };
            return texts[type] || type;
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        },

        formatTime(dateString) {
            return new Date(dateString).toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
}
</script>
@endpush