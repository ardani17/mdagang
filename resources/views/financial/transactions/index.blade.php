@extends('layouts.dashboard')

@section('title', 'Manajemen Transaksi')
@section('page-title')
<span class="text-base lg:text-2xl">Manajemen Transaksi</span>
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
        <a href="{{ route('financial.dashboard') }}" class="ml-1 text-sm font-medium text-muted hover:text-foreground md:ml-2">Keuangan</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Transaksi</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="transactionManager()" class="space-y-4 lg:space-y-6 p-4 lg:p-0">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Manajemen Transaksi</h2>
            <p class="text-sm text-muted">Catat dan kelola semua transaksi keuangan</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="importTransactions()" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 12l3 3m0 0l3-3m-3 3V9"/>
                </svg>
                Impor
            </button>
            
            <button @click="exportTransactions()" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor
            </button>
            
            <a href="{{ route('financial.transactions.create') }}" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Transaksi Baru
            </a>
        </div>
    </div>

    <!-- Transaction Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Total Income -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Total Pemasukan</p>
                    <p class="text-2xl lg:text-3xl font-bold text-green-600" x-text="formatCurrency(summary.total_income)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span x-text="summary.income_count"></span> transaksi
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Expenses -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Total Pengeluaran</p>
                    <p class="text-2xl lg:text-3xl font-bold text-red-600" x-text="formatCurrency(summary.total_expenses)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span x-text="summary.expense_count"></span> transaksi
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Net Balance -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Saldo Bersih</p>
                    <p class="text-2xl lg:text-3xl font-bold text-primary" x-text="formatCurrency(summary.net_balance)"></p>
                    <p class="text-xs text-muted mt-1">
                        Periode saat ini
                    </p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Transactions -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Tertunda</p>
                    <p class="text-2xl lg:text-3xl font-bold text-yellow-600" x-text="summary.pending_count"></p>
                    <p class="text-xs text-muted mt-1">
                        Menunggu persetujuan
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card p-4 lg:p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-foreground mb-2">Cari Transaksi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           x-model="filters.search"
                           @input.debounce.300ms="loadTransactions()"
                           class="input pl-10" 
                           placeholder="Cari berdasarkan deskripsi, referensi, atau jumlah...">
                </div>
            </div>

            <!-- Transaction Type -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Tipe</label>
                <select x-model="filters.type" 
                        @change="loadTransactions()"
                        class="input">
                    <option value="">Semua Tipe</option>
                    <option value="income">Pemasukan</option>
                    <option value="expense">Pengeluaran</option>
                    <option value="transfer">Transfer</option>
                </select>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Kategori</label>
                <select x-model="filters.category" 
                        @change="loadTransactions()"
                        class="input">
                    <option value="">Semua Kategori</option>
                    <option value="sales">Penjualan</option>
                    <option value="marketing">Pemasaran</option>
                    <option value="operations">Operasional</option>
                    <option value="equipment">Peralatan</option>
                    <option value="utilities">Utilitas</option>
                    <option value="salaries">Gaji</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>

            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Rentang Tanggal</label>
                <select x-model="filters.date_range" 
                        @change="loadTransactions()"
                        class="input">
                    <option value="">Semua Tanggal</option>
                    <option value="today">Hari Ini</option>
                    <option value="yesterday">Kemarin</option>
                    <option value="this_week">Minggu Ini</option>
                    <option value="last_week">Minggu Lalu</option>
                    <option value="this_month">Bulan Ini</option>
                    <option value="last_month">Bulan Lalu</option>
                    <option value="this_quarter">Kuartal Ini</option>
                    <option value="this_year">Tahun Ini</option>
                </select>
            </div>
        </div>

        <!-- Advanced Filters Toggle -->
        <div class="mt-4 pt-4 border-t border-border">
            <button @click="showAdvancedFilters = !showAdvancedFilters" 
                    class="flex items-center text-sm text-primary hover:text-primary/80">
                <svg :class="showAdvancedFilters ? 'rotate-90' : ''" class="w-4 h-4 mr-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                Filter Lanjutan
            </button>
            
            <div x-show="showAdvancedFilters" x-transition class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                <!-- Amount Range -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Jumlah Minimum</label>
                    <input type="number" 
                           x-model="filters.min_amount"
                           @input.debounce.500ms="loadTransactions()"
                           class="input" 
                           placeholder="0">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Jumlah Maksimum</label>
                    <input type="number" 
                           x-model="filters.max_amount"
                           @input.debounce.500ms="loadTransactions()"
                           class="input" 
                           placeholder="Tanpa batas">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                    <select x-model="filters.status" 
                            @change="loadTransactions()"
                            class="input">
                        <option value="">Semua Status</option>
                        <option value="pending">Tertunda</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                        <option value="completed">Selesai</option>
                    </select>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Metode Pembayaran</label>
                    <select x-model="filters.payment_method" 
                            @change="loadTransactions()"
                            class="input">
                        <option value="">Semua Metode</option>
                        <option value="cash">Tunai</option>
                        <option value="bank_transfer">Transfer Bank</option>
                        <option value="credit_card">Kartu Kredit</option>
                        <option value="digital_wallet">Dompet Digital</option>
                        <option value="check">Cek</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div x-show="selectedTransactions.length > 0" x-transition class="card p-4 bg-primary/5 border-primary/20">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-foreground">
                    <span x-text="selectedTransactions.length"></span> transaksi dipilih
                </span>
                <button @click="selectedTransactions = []" class="text-sm text-muted hover:text-foreground">
                    Hapus pilihan
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <button @click="bulkApprove()" class="btn-secondary text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Setujui
                </button>
                <button @click="bulkReject()" class="btn-secondary text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Tolak
                </button>
                <button @click="bulkExport()" class="btn-secondary text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Ekspor
                </button>
                <button @click="bulkDelete()" class="btn-secondary text-sm text-red-600 hover:text-red-800">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">
        <div class="p-4 lg:p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-foreground">
                    Transaksi (<span x-text="pagination.total"></span>)
                </h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-muted">Tampilkan:</span>
                    <select x-model="pagination.per_page" 
                            @change="loadTransactions()"
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
                        <th>Tanggal</th>
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="transaction in transactions" :key="transaction.id">
                        <tr class="hover:bg-surface/50">
                            <td>
                                <input type="checkbox"
                                       :value="transaction.id"
                                       x-model="selectedTransactions"
                                       class="rounded border-border text-primary focus:ring-primary">
                            </td>
                            <td>
                                <div class="text-sm text-foreground" x-text="formatDate(transaction.date)"></div>
                                <div class="text-xs text-muted" x-text="formatTime(transaction.date)"></div>
                            </td>
                            <td>
                                <div class="font-medium text-foreground" x-text="transaction.description"></div>
                                <div class="text-sm text-muted" x-text="transaction.reference || 'Tidak ada referensi'"></div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                      x-text="transaction.category">
                                </span>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="getTypeClass(transaction.type)"
                                      x-text="getTypeText(transaction.type)">
                                </span>
                            </td>
                            <td>
                                <span class="font-medium"
                                      :class="transaction.type === 'income' ? 'text-green-600' : transaction.type === 'expense' ? 'text-red-600' : 'text-blue-600'"
                                      x-text="(transaction.type === 'income' ? '+' : transaction.type === 'expense' ? '-' : '') + formatCurrency(transaction.amount)">
                                </span>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="getStatusClass(transaction.status)"
                                      x-text="getStatusText(transaction.status)">
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <button @click="viewTransaction(transaction)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <a :href="`/financial/transactions/${transaction.id}/edit`"
                                       class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button @click="duplicateTransaction(transaction)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                    <button @click="deleteTransaction(transaction)"
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
            <template x-for="transaction in transactions" :key="transaction.id">
                <div class="card rounded-xl p-4 shadow-sm">
                    <!-- Header with checkbox and status -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox"
                                   :value="transaction.id"
                                   x-model="selectedTransactions"
                                   class="w-5 h-5 rounded border-border text-primary focus:ring-primary focus:ring-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                  :class="getStatusClass(transaction.status)"
                                  x-text="getStatusText(transaction.status)">
                            </span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <button @click="viewTransaction(transaction)"
                                    class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <a :href="`/financial/transactions/${transaction.id}/edit`"
                               class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <button @click="duplicateTransaction(transaction)"
                                    class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Transaction Info -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-foreground text-base leading-tight" x-text="transaction.description"></h3>
                            <span class="font-semibold text-base"
                                  :class="transaction.type === 'income' ? 'text-green-600' : transaction.type === 'expense' ? 'text-red-600' : 'text-blue-600'"
                                  x-text="(transaction.type === 'income' ? '+' : transaction.type === 'expense' ? '-' : '') + formatCurrency(transaction.amount)">
                            </span>
                        </div>
                        
                        <p class="text-sm text-muted" x-text="transaction.reference || 'Tidak ada referensi'"></p>
                        
                        <!-- Transaction Details -->
                        <div class="grid grid-cols-2 gap-4 pt-3 border-t border-border">
                            <div>
                                <span class="text-xs font-medium text-muted uppercase tracking-wide">Kategori</span>
                                <p class="text-sm text-foreground mt-1" x-text="transaction.category"></p>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-muted uppercase tracking-wide">Tipe</span>
                                <p class="text-sm text-foreground mt-1" x-text="getTypeText(transaction.type)"></p>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-muted uppercase tracking-wide">Tanggal</span>
                                <p class="text-sm text-foreground mt-1" x-text="formatDate(transaction.date)"></p>
                                <p class="text-xs text-muted" x-text="formatTime(transaction.date)"></p>
                            </div>
                            <div class="flex items-end justify-end">
                                <button @click="deleteTransaction(transaction)"
                                        class="mobile-action-button p-2.5 text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="transactions.length === 0 && !loading" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-foreground">Tidak ada transaksi ditemukan</h3>
            <p class="mt-1 text-sm text-muted">Mulai dengan membuat transaksi pertama Anda.</p>
            <div class="mt-6">
                <a href="{{ route('financial.transactions.create') }}" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Transaksi Baru
                </a>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-12">
            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-foreground bg-surface transition ease-in-out duration-150">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-foreground" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memuat transaksi...
            </div>
        </div>

        <!-- Pagination -->
        <div x-show="pagination.total > pagination.per_page" class="px-4 lg:px-6 py-4 border-t border-border">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button @click="previousPage()" 
                            :disabled="pagination.current_page === 1"
                            :class="pagination.current_page === 1 ? 'opacity-50 cursor-not-allowed' : ''"
                            class="relative inline-flex items-center px-4 py-2 border border-border text-sm font-medium rounded-md text-foreground bg-background hover:bg-surface">
                        Sebelumnya
                    </button>
                    <button @click="nextPage()" 
                            :disabled="pagination.current_page === pagination.last_page"
                            :class="pagination.current_page === pagination.last_page ? 'opacity-50 cursor-not-allowed' : ''"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-border text-sm font-medium rounded-md text-foreground bg-background hover:bg-surface">
                        Selanjutnya
                    </button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-muted">
                            Menampilkan
                            <span class="font-medium" x-text="pagination.from"></span>
                            sampai
                            <span class="font-medium" x-text="pagination.to"></span>
                            dari
                            <span class="font-medium" x-text="pagination.total"></span>
                            hasil
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <button @click="previousPage()" 
                                    :disabled="pagination.current_page === 1"
                                    :class="pagination.current_page === 1 ? 'opacity-50 cursor-not-allowed' : ''"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-border bg-background text-sm font-medium text-muted hover:bg-surface">
                                <span class="sr-only">Sebelumnya</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                            
                            <template x-for="page in getVisiblePages()" :key="page">
                                <button @click="goToPage(page)" 
                                        :class="page === pagination.current_page ? 'bg-primary text-white' : 'bg-background text-foreground hover:bg-surface'"
                                        class="relative inline-flex items-center px-4 py-2 border border-border text-sm font-medium"
                                        x-text="page">
                                </button>
                            </template>
                            
                            <button @click="nextPage()" 
                                    :disabled="pagination.current_page === pagination.last_page"
                                    :class="pagination.current_page === pagination.last_page ? 'opacity-50 cursor-not-allowed' : ''"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-border bg-background text-sm font-medium text-muted hover:bg-surface">
                                <span class="sr-only">Selanjutnya</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Detail Modal -->
    <div x-show="showTransactionModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-background rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-background px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-foreground mb-4">
                                Detail Transaksi
                            </h3>
                            
                            <div x-show="selectedTransaction" class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-muted">Deskripsi</label>
                                        <p class="mt-1 text-sm text-foreground" x-text="selectedTransaction?.description"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-muted">Jumlah</label>
                                        <p class="mt-1 text-sm font-semibold"
                                           :class="selectedTransaction?.type === 'income' ? 'text-green-600' : selectedTransaction?.type === 'expense' ? 'text-red-600' : 'text-blue-600'"
                                           x-text="selectedTransaction ? formatCurrency(selectedTransaction.amount) : ''"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-muted">Tipe</label>
                                        <p class="mt-1 text-sm text-foreground" x-text="selectedTransaction ? getTypeText(selectedTransaction.type) : ''"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-muted">Kategori</label>
                                        <p class="mt-1 text-sm text-foreground" x-text="selectedTransaction?.category"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-muted">Tanggal</label>
                                        <p class="mt-1 text-sm text-foreground" x-text="selectedTransaction ? formatDate(selectedTransaction.date) : ''"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-muted">Status</label>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              :class="selectedTransaction ? getStatusClass(selectedTransaction.status) : ''"
                                              x-text="selectedTransaction ? getStatusText(selectedTransaction.status) : ''">
                                        </span>
                                    </div>
                                </div>
                                
                                <div x-show="selectedTransaction?.reference">
                                    <label class="block text-sm font-medium text-muted">Referensi</label>
                                    <p class="mt-1 text-sm text-foreground" x-text="selectedTransaction?.reference"></p>
                                </div>
                                
                                <div x-show="selectedTransaction?.notes">
                                    <label class="block text-sm font-medium text-muted">Catatan</label>
                                    <p class="mt-1 text-sm text-foreground" x-text="selectedTransaction?.notes"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-surface px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="showTransactionModal = false" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function transactionManager() {
    return {
        loading: false,
        showAdvancedFilters: false,
        showTransactionModal: false,
        selectedTransaction: null,
        selectedTransactions: [],
        transactions: [],
        summary: {
            total_income: 0,
            total_expenses: 0,
            net_balance: 0,
            income_count: 0,
            expense_count: 0,
            pending_count: 0
        },
        filters: {
            search: '',
            type: '',
            category: '',
            date_range: '',
            min_amount: '',
            max_amount: '',
            status: '',
            payment_method: ''
        },
        pagination: {
            current_page: 1,
            per_page: 25,
            total: 0,
            last_page: 1,
            from: 0,
            to: 0
        },

        init() {
            this.loadTransactions();
            this.loadSummary();
        },

        async loadTransactions() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    ...this.filters
                });

                const response = await fetch(`/api/financial/transactions?${params}`);
                const data = await response.json();
                
                this.transactions = data.data || [];
                this.pagination = {
                    current_page: data.current_page || 1,
                    per_page: data.per_page || 25,
                    total: data.total || 0,
                    last_page: data.last_page || 1,
                    from: data.from || 0,
                    to: data.to || 0
                };
            } catch (error) {
                console.error('Error loading transactions:', error);
                this.transactions = this.getDemoTransactions();
            } finally {
                this.loading = false;
            }
        },

        async loadSummary() {
            try {
                const response = await fetch('/api/financial/transactions/summary');
                const data = await response.json();
                this.summary = data;
            } catch (error) {
                console.error('Error loading summary:', error);
                this.summary = {
                    total_income: 125000,
                    total_expenses: 87500,
                    net_balance: 37500,
                    income_count: 15,
                    expense_count: 23,
                    pending_count: 3
                };
            }
        },

        getDemoTransactions() {
            return [
                {
                    id: 1,
                    description: 'Pendapatan Penjualan Produk',
                    amount: 15000,
                    type: 'income',
                    category: 'penjualan',
                    status: 'completed',
                    date: '2024-01-15T10:30:00Z',
                    reference: 'INV-2024-001',
                    notes: 'Penjualan produk bulanan'
                },
                {
                    id: 2,
                    description: 'Pembayaran Sewa Kantor',
                    amount: 5000,
                    type: 'expense',
                    category: 'operasional',
                    status: 'completed',
                    date: '2024-01-14T09:00:00Z',
                    reference: 'RENT-JAN-2024',
                    notes: 'Sewa kantor bulanan'
                },
                {
                    id: 3,
                    description: 'Kampanye Pemasaran',
                    amount: 2500,
                    type: 'expense',
                    category: 'pemasaran',
                    status: 'pending',
                    date: '2024-01-13T14:15:00Z',
                    reference: 'MKT-001',
                    notes: 'Iklan media sosial'
                }
            ];
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
        },

        getTypeText(type) {
            const types = {
                income: 'Pemasukan',
                expense: 'Pengeluaran',
                transfer: 'Transfer'
            };
            return types[type] || type;
        },

        getTypeClass(type) {
            const classes = {
                income: 'bg-green-100 text-green-800',
                expense: 'bg-red-100 text-red-800',
                transfer: 'bg-blue-100 text-blue-800'
            };
            return classes[type] || 'bg-gray-100 text-gray-800';
        },

        getStatusText(status) {
            const statuses = {
                pending: 'Tertunda',
                approved: 'Disetujui',
                rejected: 'Ditolak',
                completed: 'Selesai'
            };
            return statuses[status] || status;
        },

        getStatusClass(status) {
            const classes = {
                pending: 'bg-yellow-100 text-yellow-800',
                approved: 'bg-blue-100 text-blue-800',
                rejected: 'bg-red-100 text-red-800',
                completed: 'bg-green-100 text-green-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },

        toggleSelectAll(checked) {
            if (checked) {
                this.selectedTransactions = this.transactions.map(t => t.id);
            } else {
                this.selectedTransactions = [];
            }
        },

        viewTransaction(transaction) {
            // Redirect to transaction detail page
            window.location.href = `/financial/transactions/${transaction.id}`;
        },

        duplicateTransaction(transaction) {
            window.location.href = `/financial/transactions/create?duplicate=${transaction.id}`;
        },

        async deleteTransaction(transaction) {
            if (confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
                try {
                    await fetch(`/api/financial/transactions/${transaction.id}`, {
                        method: 'DELETE'
                    });
                    this.loadTransactions();
                    this.loadSummary();
                } catch (error) {
                    console.error('Error deleting transaction:', error);
                }
            }
        },

        async bulkApprove() {
            if (this.selectedTransactions.length === 0) return;
            
            try {
                await fetch('/api/financial/transactions/bulk-approve', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ids: this.selectedTransactions })
                });
                this.selectedTransactions = [];
                this.loadTransactions();
                this.loadSummary();
            } catch (error) {
                console.error('Error approving transactions:', error);
            }
        },

        async bulkReject() {
            if (this.selectedTransactions.length === 0) return;
            
            try {
                await fetch('/api/financial/transactions/bulk-reject', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ids: this.selectedTransactions })
                });
                this.selectedTransactions = [];
                this.loadTransactions();
                this.loadSummary();
            } catch (error) {
                console.error('Error rejecting transactions:', error);
            }
        },

        async bulkExport() {
            if (this.selectedTransactions.length === 0) return;
            
            try {
                const response = await fetch('/api/financial/transactions/export', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ids: this.selectedTransactions })
                });
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'transactions.xlsx';
                a.click();
            } catch (error) {
                console.error('Error exporting transactions:', error);
            }
        },

        async bulkDelete() {
            if (this.selectedTransactions.length === 0) return;
            
            if (confirm(`Apakah Anda yakin ingin menghapus ${this.selectedTransactions.length} transaksi?`)) {
                try {
                    await fetch('/api/financial/transactions/bulk-delete', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ ids: this.selectedTransactions })
                    });
                    this.selectedTransactions = [];
                    this.loadTransactions();
                    this.loadSummary();
                } catch (error) {
                    console.error('Error deleting transactions:', error);
                }
            }
        },

        async importTransactions() {
            // Implementation for import functionality
            console.log('Import transactions');
        },

        async exportTransactions() {
            try {
                const response = await fetch('/api/financial/transactions/export-all');
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'all-transactions.xlsx';
                a.click();
            } catch (error) {
                console.error('Error exporting transactions:', error);
            }
        },

        previousPage() {
            if (this.pagination.current_page > 1) {
                this.pagination.current_page--;
                this.loadTransactions();
            }
        },

        nextPage() {
            if (this.pagination.current_page < this.pagination.last_page) {
                this.pagination.current_page++;
                this.loadTransactions();
            }
        },

        goToPage(page) {
            this.pagination.current_page = page;
            this.loadTransactions();
        },

        getVisiblePages() {
            const current = this.pagination.current_page;
            const last = this.pagination.last_page;
            const pages = [];
            
            if (last <= 7) {
                for (let i = 1; i <= last; i++) {
                    pages.push(i);
                }
            } else {
                if (current <= 4) {
                    for (let i = 1; i <= 5; i++) {
                        pages.push(i);
                    }
                    pages.push('...');
                    pages.push(last);
                } else if (current >= last - 3) {
                    pages.push(1);
                    pages.push('...');
                    for (let i = last - 4; i <= last; i++) {
                        pages.push(i);
                    }
                } else {
                    pages.push(1);
                    pages.push('...');
                    for (let i = current - 1; i <= current + 1; i++) {
                        pages.push(i);
                    }
                    pages.push('...');
                    pages.push(last);
                }
            }
            
            return pages;
        }
    }
}
</script>
@endsection
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 