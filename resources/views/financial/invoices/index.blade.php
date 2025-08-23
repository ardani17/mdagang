@extends('layouts.dashboard')

@section('title', 'Manajemen Invoice')
@section('page-title')
<span class="text-base lg:text-2xl">Manajemen Invoice</span>
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
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Keuangan</span>
    </div>
</li>
<li>
    <div class="flex items-center">
        <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Invoice</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="invoiceManager()" class="space-y-4 lg:space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Manajemen Invoice</h2>
            <p class="text-sm text-muted">Kelola invoice penjualan dan tagihan</p>
        </div>
        
        <!-- Desktop Actions -->
        <div class="hidden sm:flex items-center justify-end space-x-3">
            <button @click="exportData" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor Data
            </button>
            <button @click="openCreateModal" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Invoice
            </button>
        </div>

        <!-- Mobile Actions -->
        <div class="sm:hidden grid grid-cols-2 gap-3">
            <button @click="exportData" class="btn-secondary flex items-center justify-center text-sm py-3 px-4">
                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="truncate">Ekspor</span>
            </button>
            <button @click="openCreateModal" class="btn-primary flex items-center justify-center text-sm py-3 px-4">
                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="truncate">Buat Invoice</span>
            </button>
        </div>
    </div>

    <!-- Invoice Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Total Invoice</p>
                    <p class="text-2xl lg:text-3xl font-bold text-foreground" x-text="stats.total_invoices"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-green-600">+12</span> bulan ini
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Lunas</p>
                    <p class="text-2xl lg:text-3xl font-bold text-green-600" x-text="stats.paid_invoices"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-green-600">Pembayaran lancar</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Pending</p>
                    <p class="text-2xl lg:text-3xl font-bold text-yellow-600" x-text="stats.pending_invoices"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-yellow-600">Menunggu pembayaran</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Overdue</p>
                    <p class="text-2xl lg:text-3xl font-bold text-red-600" x-text="stats.overdue_invoices"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-red-600">Perlu tindakan</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
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
                <label class="block text-sm font-medium text-foreground mb-2">Cari Invoice</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           x-model="filters.search"
                           @input.debounce.300ms="filterInvoices()"
                           class="input pl-10" 
                           placeholder="Nomor invoice, pelanggan...">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                <select x-model="filters.status" 
                        @change="filterInvoices()"
                        class="input">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="sent">Terkirim</option>
                    <option value="paid">Lunas</option>
                    <option value="overdue">Overdue</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
            </div>

            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Periode</label>
                <select x-model="filters.period" 
                        @change="filterInvoices()"
                        class="input">
                    <option value="">Semua Periode</option>
                    <option value="today">Hari Ini</option>
                    <option value="week">Minggu Ini</option>
                    <option value="month">Bulan Ini</option>
                    <option value="quarter">Kuartal Ini</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Invoice Table -->
    <div class="card">
        <div class="p-4 lg:p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-foreground">
                    Daftar Invoice (<span x-text="totalInvoices"></span>)
                </h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-muted">Tampilkan:</span>
                    <select x-model="perPage" 
                            @change="loadInvoices()"
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
                        <th>Invoice</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Jatuh Tempo</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="invoice in filteredInvoices" :key="invoice.id">
                        <tr class="hover:bg-surface/50">
                            <td>
                                <div class="font-medium text-foreground" x-text="invoice.invoice_number"></div>
                            </td>
                            <td>
                                <div class="text-foreground" x-text="invoice.customer_name"></div>
                                <div class="text-sm text-muted" x-text="invoice.customer_email"></div>
                            </td>
                            <td>
                                <span class="text-foreground" x-text="formatDate(invoice.invoice_date)"></span>
                            </td>
                            <td>
                                <span class="text-foreground" x-text="formatDate(invoice.due_date)"></span>
                            </td>
                            <td>
                                <span class="font-medium text-foreground" x-text="formatCurrency(invoice.total_amount)"></span>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="getStatusClass(invoice.status)"
                                      x-text="getStatusText(invoice.status)">
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <button @click="viewInvoice(invoice)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button @click="editInvoice(invoice)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button @click="printInvoice(invoice)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                    </button>
                                    <button @click="sendInvoice(invoice)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
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
            <template x-for="invoice in filteredInvoices" :key="invoice.id">
                <div class="card rounded-xl p-4 shadow-sm">
                    <!-- Header with status -->
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-semibold text-foreground text-base" x-text="invoice.invoice_number"></h3>
                            <p class="text-sm text-muted" x-text="invoice.customer_name"></p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                              :class="getStatusClass(invoice.status)"
                              x-text="getStatusText(invoice.status)">
                        </span>
                    </div>

                    <!-- Invoice Details -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Tanggal</span>
                            <p class="text-sm text-foreground mt-1" x-text="formatDate(invoice.invoice_date)"></p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Jatuh Tempo</span>
                            <p class="text-sm text-foreground mt-1" x-text="formatDate(invoice.due_date)"></p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Total</span>
                            <p class="font-semibold text-foreground text-lg mt-1" x-text="formatCurrency(invoice.total_amount)"></p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-1">
                        <button @click="viewInvoice(invoice)"
                                class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                        <button @click="editInvoice(invoice)"
                                class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button @click="printInvoice(invoice)"
                                class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                        </button>
                        <button @click="sendInvoice(invoice)"
                                class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="filteredInvoices.length === 0" class="text-center py-12">
            <svg class="w-16 h-16 text-muted mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-lg font-medium text-foreground mb-2">Tidak ada invoice</h3>
            <p class="text-muted mb-4">Belum ada invoice yang dibuat atau sesuai dengan filter.</p>
            <button @click="openCreateModal" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Invoice Pertama
            </button>
        </div>

        <!-- Pagination -->
        <div class="p-4 lg:p-6 border-t border-border">
            <!-- Mobile Pagination -->
            <div class="lg:hidden">
                <div class="text-center text-sm text-muted mb-4">
                    Halaman <span x-text="currentPage"></span> dari <span x-text="lastPage"></span>
                    (<span x-text="totalInvoices"></span> invoice)
                </div>
                <div class="flex items-center justify-center space-x-2">
                    <button @click="previousPage()" 
                            :disabled="currentPage === 1"
                            class="flex items-center justify-center w-12 h-12 rounded-lg border border-border bg-background text-muted hover:text-foreground disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button @click="nextPage()" 
                            :disabled="currentPage === lastPage"
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
                    Menampilkan <span x-text="((currentPage - 1) * perPage) + 1"></span> sampai 
                    <span x-text="Math.min(currentPage * perPage, totalInvoices)"></span> dari 
                    <span x-text="totalInvoices"></span> invoice
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="previousPage()" 
                            :disabled="currentPage === 1"
                            class="btn-secondary text-sm py-1 px-3 disabled:opacity-50 disabled:cursor-not-allowed">
                        Sebelumnya
                    </button>
                    <button @click="nextPage()" 
                            :disabled="currentPage === lastPage"
                            class="btn-secondary text-sm py-1 px-3 disabled:opacity-50 disabled:cursor-not-allowed">
                        Selanjutnya
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50" x-cloak>
        <div class="relative top-
20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-background">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-foreground" x-text="editingInvoice ? 'Edit Invoice' : 'Buat Invoice Baru'"></h3>
                <button @click="closeModal" class="text-muted hover:text-foreground">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="saveInvoice" class="space-y-6">
                <!-- Customer Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Pelanggan *</label>
                        <select x-model="form.customer_id" required class="input">
                            <option value="">Pilih Pelanggan</option>
                            <option value="1">PT. Maju Jaya</option>
                            <option value="2">CV. Berkah Sejahtera</option>
                            <option value="3">Toko Sumber Rezeki</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Nomor Invoice *</label>
                        <input type="text" x-model="form.invoice_number" required class="input">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Tanggal Invoice *</label>
                        <input type="date" x-model="form.invoice_date" required class="input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Jatuh Tempo *</label>
                        <input type="date" x-model="form.due_date" required class="input">
                    </div>
                </div>

                <!-- Invoice Items -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-medium text-foreground">Item Invoice</h4>
                        <button type="button" @click="addItem" class="btn-secondary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Item
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(item, index) in form.items" :key="index">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 p-4 border border-border rounded-lg">
                                <div class="md:col-span-4">
                                    <label class="block text-sm font-medium text-foreground mb-1">Produk/Layanan</label>
                                    <input type="text" x-model="item.description" required class="input">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-foreground mb-1">Qty</label>
                                    <input type="number" x-model="item.quantity" @input="calculateItemTotal(index)" min="1" required class="input">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-foreground mb-1">Harga</label>
                                    <input type="number" x-model="item.unit_price" @input="calculateItemTotal(index)" min="0" step="0.01" required class="input">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-foreground mb-1">Diskon (%)</label>
                                    <input type="number" x-model="item.discount" @input="calculateItemTotal(index)" min="0" max="100" step="0.01" class="input">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-sm font-medium text-foreground mb-1">Total</label>
                                    <div class="px-3 py-2 bg-surface border border-border rounded-md text-sm text-foreground" x-text="formatCurrency(item.total || 0)"></div>
                                </div>
                                <div class="md:col-span-1 flex items-end">
                                    <button type="button" @click="removeItem(index)" class="p-2 text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Invoice Totals -->
                <div class="bg-surface p-4 rounded-lg border border-border">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Catatan</label>
                            <textarea x-model="form.notes" rows="4" class="input"
                                      placeholder="Catatan tambahan untuk invoice..."></textarea>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-muted">Subtotal:</span>
                                <span class="text-sm font-medium text-foreground" x-text="formatCurrency(form.subtotal)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-muted">Diskon Total:</span>
                                <span class="text-sm font-medium text-foreground" x-text="formatCurrency(form.total_discount)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-muted">PPN (11%):</span>
                                <span class="text-sm font-medium text-foreground" x-text="formatCurrency(form.tax_amount)"></span>
                            </div>
                            <div class="flex justify-between border-t border-border pt-3">
                                <span class="text-lg font-bold text-foreground">Total:</span>
                                <span class="text-lg font-bold text-foreground" x-text="formatCurrency(form.total_amount)"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="closeModal" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary" :disabled="loading">
                        <span x-show="loading" class="mr-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </span>
                        <span x-text="editingInvoice ? 'Perbarui Invoice' : 'Simpan Invoice'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function invoiceManager() {
    return {
        invoices: [],
        filteredInvoices: [],
        stats: {
            total_invoices: 25,
            paid_invoices: 18,
            pending_invoices: 4,
            overdue_invoices: 3
        },
        filters: {
            search: '',
            status: '',
            period: ''
        },
        showModal: false,
        editingInvoice: null,
        loading: false,
        currentPage: 1,
        perPage: 25,
        totalInvoices: 0,
        lastPage: 1,
        form: {
            customer_id: '',
            invoice_number: '',
            invoice_date: '',
            due_date: '',
            items: [],
            notes: '',
            subtotal: 0,
            total_discount: 0,
            tax_amount: 0,
            total_amount: 0
        },

        init() {
            this.loadInvoices();
            this.loadStats();
        },

        async loadInvoices() {
            this.loading = true;
            try {
                // Dummy data untuk testing frontend
                const dummyInvoices = [
                    {
                        id: 1,
                        invoice_number: 'INV-202501-001',
                        customer_name: 'PT. Maju Jaya',
                        customer_email: 'finance@majujaya.com',
                        invoice_date: '2025-01-15',
                        due_date: '2025-02-14',
                        total_amount: 5272500,
                        status: 'paid'
                    },
                    {
                        id: 2,
                        invoice_number: 'INV-202501-002',
                        customer_name: 'CV. Berkah Sejahtera',
                        customer_email: 'admin@berkahsejahtera.com',
                        invoice_date: '2025-01-16',
                        due_date: '2025-02-15',
                        total_amount: 3330000,
                        status: 'paid'
                    },
                    {
                        id: 3,
                        invoice_number: 'INV-202501-003',
                        customer_name: 'Toko Sumber Rezeki',
                        customer_email: 'owner@sumberrezeki.com',
                        invoice_date: '2025-01-10',
                        due_date: '2025-01-25',
                        total_amount: 1581750,
                        status: 'overdue'
                    }
                ];

                this.invoices = dummyInvoices;
                this.filteredInvoices = [...this.invoices];
                this.totalInvoices = this.invoices.length;
                this.lastPage = Math.ceil(this.totalInvoices / this.perPage);
            } catch (error) {
                console.error('Error loading invoices:', error);
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal memuat data invoice'
                });
            } finally {
                this.loading = false;
            }
        },

        async loadStats() {
            // Stats sudah diset di init
        },

        filterInvoices() {
            let filtered = [...this.invoices];

            if (this.filters.search) {
                const search = this.filters.search.toLowerCase();
                filtered = filtered.filter(invoice =>
                    invoice.invoice_number.toLowerCase().includes(search) ||
                    invoice.customer_name.toLowerCase().includes(search) ||
                    invoice.customer_email.toLowerCase().includes(search)
                );
            }

            if (this.filters.status) {
                filtered = filtered.filter(invoice => invoice.status === this.filters.status);
            }

            this.filteredInvoices = filtered;
        },

        openCreateModal() {
            this.editingInvoice = null;
            this.resetForm();
            this.showModal = true;
        },

        editInvoice(invoice) {
            this.editingInvoice = invoice;
            this.form = {
                customer_id: invoice.customer_id,
                invoice_number: invoice.invoice_number,
                invoice_date: invoice.invoice_date,
                due_date: invoice.due_date,
                items: [...(invoice.items || [])],
                notes: invoice.notes || '',
                subtotal: invoice.subtotal || 0,
                total_discount: invoice.total_discount || 0,
                tax_amount: invoice.tax_amount || 0,
                total_amount: invoice.total_amount
            };
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.editingInvoice = null;
            this.resetForm();
        },

        resetForm() {
            this.form = {
                customer_id: '',
                invoice_number: this.generateInvoiceNumber(),
                invoice_date: new Date().toISOString().split('T')[0],
                due_date: '',
                items: [this.createEmptyItem()],
                notes: '',
                subtotal: 0,
                total_discount: 0,
                tax_amount: 0,
                total_amount: 0
            };
        },

        createEmptyItem() {
            return {
                description: '',
                quantity: 1,
                unit_price: 0,
                discount: 0,
                total: 0
            };
        },

        addItem() {
            this.form.items.push(this.createEmptyItem());
        },

        removeItem(index) {
            if (this.form.items.length > 1) {
                this.form.items.splice(index, 1);
                this.calculateTotals();
            }
        },

        calculateItemTotal(index) {
            const item = this.form.items[index];
            const subtotal = item.quantity * item.unit_price;
            const discountAmount = subtotal * (item.discount / 100);
            item.total = subtotal - discountAmount;
            this.calculateTotals();
        },

        calculateTotals() {
            this.form.subtotal = this.form.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
            this.form.total_discount = this.form.items.reduce((sum, item) => {
                const subtotal = item.quantity * item.unit_price;
                return sum + (subtotal * (item.discount / 100));
            }, 0);
            
            const taxableAmount = this.form.subtotal - this.form.total_discount;
            this.form.tax_amount = taxableAmount * 0.11; // PPN 11%
            this.form.total_amount = taxableAmount + this.form.tax_amount;
        },

        async saveInvoice() {
            this.loading = true;
            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: this.editingInvoice ? 'Invoice berhasil diperbarui' : 'Invoice berhasil dibuat'
                });
                this.closeModal();
                this.loadInvoices();
            } catch (error) {
                console.error('Error saving invoice:', error);
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal menyimpan invoice'
                });
            } finally {
                this.loading = false;
            }
        },

        viewInvoice(invoice) {
            window.open(`/invoices/${invoice.id}/view`, '_blank');
        },

        printInvoice(invoice) {
            window.open(`/invoices/${invoice.id}/print`, '_blank');
        },

        async sendInvoice(invoice) {
            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: 'Invoice berhasil dikirim'
                });
                this.loadInvoices();
            } catch (error) {
                console.error('Error sending invoice:', error);
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal mengirim invoice'
                });
            }
        },

        async exportData() {
            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: 'Data berhasil diekspor'
                });
            } catch (error) {
                console.error('Error exporting data:', error);
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal mengekspor data'
                });
            }
        },

        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.loadInvoices();
            }
        },

        nextPage() {
            if (this.currentPage < this.lastPage) {
                this.currentPage++;
                this.loadInvoices();
            }
        },

        generateInvoiceNumber() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            return `INV-${year}${month}-${random}`;
        },

        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('id-ID');
        },

        formatCurrency(amount) {
            if (!amount) return 'Rp 0';
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },

        getStatusClass(status) {
            const classes = {
                'draft': 'bg-gray-100 text-gray-800',
                'sent': 'bg-blue-100 text-blue-800',
                'paid': 'bg-green-100 text-green-800',
                'overdue': 'bg-red-100 text-red-800',
                'cancelled': 'bg-gray-100 text-gray-800'
            };
            return classes[status] || classes['draft'];
        },

        getStatusText(status) {
            const texts = {
                'draft': 'Draft',
                'sent': 'Terkirim',
                'paid': 'Lunas',
                'overdue': 'Overdue',
                'cancelled': 'Dibatalkan'
            };
            return texts[status] || 'Draft';
        }
    }
}
</script>
@endsection