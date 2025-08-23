@extends('layouts.dashboard')

@section('title', 'Penyesuaian Stok')
@section('page-title')
<span class="text-base lg:text-2xl">Penyesuaian Stok</span>
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
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Penyesuaian Stok</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="adjustmentManager()" class="space-y-4 lg:space-y-6">
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
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Penyesuaian Stok</h2>
            <p class="text-sm text-muted">Kelola penyesuaian dan koreksi stok inventori</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="exportAdjustments()" 
                    class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor
            </button>
            <button @click="createAdjustment()" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                </svg>
                Penyesuaian Baru
            </button>
        </div>
    </div>

    <!-- Adjustment Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Penyesuaian Bulan Ini</p>
                    <p class="text-2xl lg:text-3xl font-bold text-blue-600" x-text="stats.monthly_adjustments"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-green-600">+3</span> dari bulan lalu
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Nilai Penyesuaian</p>
                    <p class="text-2xl lg:text-3xl font-bold text-primary" x-text="formatCurrency(stats.adjustment_value)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-green-600">+8.5%</span> dari bulan lalu
                    </p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Menunggu Approval</p>
                    <p class="text-2xl lg:text-3xl font-bold text-orange-600" x-text="stats.pending_approvals"></p>
                    <p class="text-xs text-muted mt-1">
                        Perlu ditinjau
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Stok Opname Terakhir</p>
                    <p class="text-2xl lg:text-3xl font-bold text-green-600" x-text="stats.last_stock_opname"></p>
                    <p class="text-xs text-muted mt-1">
                        2 hari yang lalu
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
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
                <label class="block text-sm font-medium text-foreground mb-2">Cari Penyesuaian</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           x-model="filters.search"
                           @input.debounce.300ms="loadAdjustments()"
                           class="input pl-10" 
                           placeholder="Cari berdasarkan nomor dokumen...">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                <select x-model="filters.status" 
                        @change="loadAdjustments()"
                        class="input">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="pending">Menunggu Approval</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Jenis</label>
                <select x-model="filters.type" 
                        @change="loadAdjustments()"
                        class="input">
                    <option value="">Semua Jenis</option>
                    <option value="manual">Penyesuaian Manual</option>
                    <option value="stock_opname">Stok Opname</option>
                    <option value="system_correction">Koreksi Sistem</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Adjustments Table -->
    <div class="card">
        <div class="p-4 lg:p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-foreground">
                    Daftar Penyesuaian (<span x-text="pagination.total"></span>)
                </h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-muted">Tampilkan:</span>
                    <select x-model="pagination.per_page" 
                            @change="loadAdjustments()"
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
                        <th>No. Dokumen</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Total Item</th>
                        <th>Nilai Penyesuaian</th>
                        <th>Status</th>
                        <th>Dibuat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="adjustment in adjustments" :key="adjustment.id">
                        <tr class="hover:bg-surface/50">
                            <td>
                                <div class="font-medium text-foreground" x-text="adjustment.document_number"></div>
                                <div class="text-sm text-muted" x-text="getTypeText(adjustment.type)"></div>
                            </td>
                            <td>
                                <div class="text-sm text-foreground" x-text="formatDate(adjustment.created_at)"></div>
                                <div class="text-xs text-muted" x-text="formatTime(adjustment.created_at)"></div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="getTypeClass(adjustment.type)"
                                      x-text="getTypeText(adjustment.type)">
                                </span>
                            </td>
                            <td>
                                <span class="text-foreground" x-text="adjustment.total_items + ' item'"></span>
                            </td>
                            <td>
                                <span class="font-medium text-foreground" x-text="formatCurrency(adjustment.adjustment_value)"></span>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="getStatusClass(adjustment.status)"
                                      x-text="getStatusText(adjustment.status)">
                                </span>
                            </td>
                            <td>
                                <span class="text-foreground" x-text="adjustment.created_by"></span>
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <button @click="viewAdjustment(adjustment)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button x-show="adjustment.status === 'pending'" 
                                            @click="approveAdjustment(adjustment)"
                                            class="p-1 text-green-600 hover:text-green-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                    <button x-show="adjustment.status === 'pending'" 
                                            @click="rejectAdjustment(adjustment)"
                                            class="p-1 text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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
            <template x-for="adjustment in adjustments" :key="adjustment.id">
                <div class="card rounded-xl p-4 shadow-sm">
                    <!-- Header with status and actions -->
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-foreground text-base leading-tight" x-text="adjustment.document_number"></h3>
                            <p class="text-sm text-muted mt-1" x-text="formatDate(adjustment.created_at) + ' - ' + formatTime(adjustment.created_at)"></p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-2"
                                  :class="getTypeClass(adjustment.type)"
                                  x-text="getTypeText(adjustment.type)">
                            </span>
                        </div>
                        <div class="flex flex-col items-end space-y-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  :class="getStatusClass(adjustment.status)"
                                  x-text="getStatusText(adjustment.status)">
                            </span>
                            <div class="flex items-center space-x-1">
                                <button @click="viewAdjustment(adjustment)"
                                        class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                <button x-show="adjustment.status === 'pending'" 
                                        @click="approveAdjustment(adjustment)"
                                        class="mobile-action-button p-2.5 text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                                <button x-show="adjustment.status === 'pending'" 
                                        @click="rejectAdjustment(adjustment)"
                                        class="mobile-action-button p-2.5 text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Adjustment Details -->
                    <div class="grid grid-cols-2 gap-4 pt-3 border-t border-border">
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Total Item</span>
                            <p class="font-medium text-foreground mt-1" x-text="adjustment.total_items + ' item'"></p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Nilai Penyesuaian</span>
                            <p class="font-semibold text-foreground text-base mt-1" x-text="formatCurrency(adjustment.adjustment_value)"></p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Dibuat Oleh</span>
                            <p class="font-medium text-foreground mt-1" x-text="adjustment.created_by"></p>
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
                    (<span x-text="pagination.total"></span> penyesuaian)
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
                    dari <span x-text="pagination.total"></span> penyesuaian
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
function adjustmentManager() {
    return {
        adjustments: [],
        loading: false,
        stats: {
            monthly_adjustments: 28,
            adjustment_value: 2100000,
            pending_approvals: 5,
            last_stock_opname: '15 Agu'
        },
        filters: {
            search: '',
            status: '',
            type: ''
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
            await this.loadAdjustments();
        },

        async loadAdjustments() {
            this.loading = true;
            
            try {
                // Dummy data untuk testing frontend
                const dummyAdjustments = [
                    {
                        id: 1,
                        document_number: 'ADJ-2024-001',
                        type: 'manual',
                        total_items: 5,
                        adjustment_value: 450000,
                        status: 'pending',
                        created_by: 'Admin User',
                        created_at: '2024-08-17T10:30:00Z'
                    },
                    {
                        id: 2,
                        document_number: 'SO-2024-008',
                        type: 'stock_opname',
                        total_items: 156,
                        adjustment_value: 1250000,
                        status: 'approved',
                        created_by: 'Admin User',
                        created_at: '2024-08-15T08:00:00Z'
                    },
                    {
                        id: 3,
                        document_number:
'ADJ-2024-002',
                        type: 'system_correction',
                        total_items: 12,
                        adjustment_value: 320000,
                        status: 'approved',
                        created_by: 'System',
                        created_at: '2024-08-14T16:45:00Z'
                    }
                ];

                // Apply filters
                let filteredAdjustments = dummyAdjustments;
                
                if (this.filters.search) {
                    filteredAdjustments = filteredAdjustments.filter(adjustment => 
                        adjustment.document_number.toLowerCase().includes(this.filters.search.toLowerCase())
                    );
                }
                
                if (this.filters.status) {
                    filteredAdjustments = filteredAdjustments.filter(adjustment => adjustment.status === this.filters.status);
                }
                
                if (this.filters.type) {
                    filteredAdjustments = filteredAdjustments.filter(adjustment => adjustment.type === this.filters.type);
                }

                // Simulate pagination
                const total = filteredAdjustments.length;
                const from = (this.pagination.current_page - 1) * this.pagination.per_page + 1;
                const to = Math.min(from + this.pagination.per_page - 1, total);
                
                this.adjustments = filteredAdjustments.slice(from - 1, to);
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
                    message: 'Gagal memuat data penyesuaian stok'
                });
            } finally {
                this.loading = false;
            }
        },

        async viewAdjustment(adjustment) {
            this.$store.modal.open(`
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Detail Penyesuaian Stok</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><span class="font-medium">No. Dokumen:</span> ${adjustment.document_number}</div>
                            <div><span class="font-medium">Jenis:</span> ${this.getTypeText(adjustment.type)}</div>
                            <div><span class="font-medium">Status:</span> ${this.getStatusText(adjustment.status)}</div>
                            <div><span class="font-medium">Total Item:</span> ${adjustment.total_items} item</div>
                            <div><span class="font-medium">Nilai Penyesuaian:</span> ${this.formatCurrency(adjustment.adjustment_value)}</div>
                            <div><span class="font-medium">Dibuat Oleh:</span> ${adjustment.created_by}</div>
                            <div><span class="font-medium">Tanggal:</span> ${this.formatDate(adjustment.created_at)}</div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button onclick="Alpine.store('modal').close()" class="btn-secondary">Tutup</button>
                        ${adjustment.status === 'pending' ? '<button onclick="Alpine.store(\'modal\').close()" class="btn-primary">Approve</button>' : ''}
                    </div>
                </div>
            `);
        },

        async createAdjustment() {
            // Redirect to create adjustment form
            window.location.href = '/inventory/adjustments/create';
        },

        async approveAdjustment(adjustment) {
            if (!confirm(`Apakah Anda yakin ingin menyetujui penyesuaian "${adjustment.document_number}"?`)) {
                return;
            }

            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: `Penyesuaian ${adjustment.document_number} berhasil disetujui.`
                });
                await this.loadAdjustments();
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal menyetujui penyesuaian'
                });
            }
        },

        async rejectAdjustment(adjustment) {
            if (!confirm(`Apakah Anda yakin ingin menolak penyesuaian "${adjustment.document_number}"?`)) {
                return;
            }

            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: `Penyesuaian ${adjustment.document_number} berhasil ditolak.`
                });
                await this.loadAdjustments();
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal menolak penyesuaian'
                });
            }
        },

        async exportAdjustments() {
            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: 'Data penyesuaian stok berhasil diekspor.'
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
                this.loadAdjustments();
            }
        },

        nextPage() {
            if (this.pagination.next_page_url) {
                this.pagination.current_page++;
                this.loadAdjustments();
            }
        },

        goToPage(page) {
            this.pagination.current_page = page;
            this.loadAdjustments();
        },

        getTypeClass(type) {
            const classes = {
                'manual': 'bg-blue-100 text-blue-800',
                'stock_opname': 'bg-purple-100 text-purple-800',
                'system_correction': 'bg-orange-100 text-orange-800'
            };
            return classes[type] || 'bg-gray-100 text-gray-800';
        },

        getTypeText(type) {
            const texts = {
                'manual': 'Penyesuaian Manual',
                'stock_opname': 'Stok Opname',
                'system_correction': 'Koreksi Sistem'
            };
            return texts[type] || type;
        },

        getStatusClass(status) {
            const classes = {
                'draft': 'bg-gray-100 text-gray-800',
                'pending': 'bg-orange-100 text-orange-800',
                'approved': 'bg-green-100 text-green-800',
                'rejected': 'bg-red-100 text-red-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },

        getStatusText(status) {
            const texts = {
                'draft': 'Draft',
                'pending': 'Menunggu Approval',
                'approved': 'Disetujui',
                'rejected': 'Ditolak'
            };
            return texts[status] || status;
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