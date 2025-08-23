@extends('layouts.dashboard')

@section('title', 'Manajemen Pengeluaran')
@section('page-title')
<span class="text-base lg:text-2xl">Manajemen Pengeluaran</span>
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
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Pengeluaran</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="expenseManager()" class="space-y-4 lg:space-y-6 p-4 lg:p-0">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Manajemen Pengeluaran</h2>
            <p class="text-sm text-muted">Lacak dan kategorikan semua pengeluaran bisnis</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="importExpenses()" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 12l3 3m0 0l3-3m-3 3V9"/>
                </svg>
                Impor
            </button>
            
            <button @click="exportExpenses()" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor
            </button>
            
            <button @click="showExpenseForm = true" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Pengeluaran Baru
            </button>
        </div>
    </div>

    <!-- Expense Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Total Expenses -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Total Pengeluaran</p>
                    <p class="text-2xl lg:text-3xl font-bold text-red-600" x-text="formatCurrency(summary.total_expenses)"></p>
                    <p class="text-xs text-muted mt-1">
                        Bulan ini
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Average Daily -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Rata-rata Harian</p>
                    <p class="text-2xl lg:text-3xl font-bold text-orange-600" x-text="formatCurrency(summary.daily_average)"></p>
                    <p class="text-xs text-muted mt-1">
                        Bulan ini
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h6m-6 0l-.5 8.5A2 2 0 0013.5 21h-3A2 2 0 019 19.5L8.5 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Menunggu Persetujuan</p>
                    <p class="text-2xl lg:text-3xl font-bold text-yellow-600" x-text="summary.pending_count"></p>
                    <p class="text-xs text-muted mt-1">
                        <span x-text="formatCurrency(summary.pending_amount)"></span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Top Category -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Kategori Teratas</p>
                    <p class="text-lg font-bold text-primary" x-text="summary.top_category.name"></p>
                    <p class="text-xs text-muted mt-1">
                        <span x-text="formatCurrency(summary.top_category.amount)"></span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Expense by Category Chart -->
        <div class="card p-4 lg:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Pengeluaran per Kategori</h3>
            <div class="relative h-64">
                <canvas id="expenseCategoryChart"></canvas>
            </div>
        </div>

        <!-- Monthly Trend -->
        <div class="card p-4 lg:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Tren Pengeluaran Bulanan</h3>
            <div class="relative h-64">
                <canvas id="expenseTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card p-4 lg:p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-foreground mb-2">Cari Pengeluaran</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           x-model="filters.search"
                           @input.debounce.300ms="loadExpenses()"
                           class="input pl-10" 
                           placeholder="Cari berdasarkan deskripsi, vendor, atau jumlah...">
                </div>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Kategori</label>
                <select x-model="filters.category" 
                        @change="loadExpenses()"
                        class="input">
                    <option value="">Semua Kategori</option>
                    <option value="operations">Operasional</option>
                    <option value="marketing">Pemasaran</option>
                    <option value="equipment">Peralatan</option>
                    <option value="utilities">Utilitas</option>
                    <option value="travel">Perjalanan</option>
                    <option value="professional">Layanan Profesional</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                <select x-model="filters.status" 
                        @change="loadExpenses()"
                        class="input">
                    <option value="">Semua Status</option>
                    <option value="pending">Tertunda</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                    <option value="paid">Dibayar</option>
                </select>
            </div>

            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Rentang Tanggal</label>
                <select x-model="filters.date_range" 
                        @change="loadExpenses()"
                        class="input">
                    <option value="">Semua Tanggal</option>
                    <option value="today">Hari Ini</option>
                    <option value="this_week">Minggu Ini</option>
                    <option value="this_month">Bulan Ini</option>
                    <option value="last_month">Bulan Lalu</option>
                    <option value="this_quarter">Kuartal Ini</option>
                    <option value="this_year">Tahun Ini</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card">
        <div class="p-4 lg:p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-foreground">
                    Pengeluaran (<span x-text="pagination.total"></span>)
                </h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-muted">Tampilkan:</span>
                    <select x-model="pagination.per_page" 
                            @change="loadExpenses()"
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
                        <th>Vendor</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="expense in expenses" :key="expense.id">
                        <tr class="hover:bg-surface/50">
                            <td>
                                <input type="checkbox"
                                       :value="expense.id"
                                       x-model="selectedExpenses"
                                       class="rounded border-border text-primary focus:ring-primary">
                            </td>
                            <td>
                                <div class="text-sm text-foreground" x-text="formatDate(expense.date)"></div>
                            </td>
                            <td>
                                <div class="font-medium text-foreground" x-text="expense.description"></div>
                                <div class="text-sm text-muted" x-text="expense.reference || 'Tidak ada referensi'"></div>
                            </td>
                            <td>
                                <div class="text-sm text-foreground" x-text="expense.vendor || 'T/A'"></div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                      x-text="expense.category">
                                </span>
                            </td>
                            <td>
                                <span class="font-medium text-red-600" x-text="formatCurrency(expense.amount)"></span>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="getStatusClass(expense.status)"
                                      x-text="getStatusText(expense.status)">
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <button @click="viewExpense(expense)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button @click="editExpense(expense)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button @click="deleteExpense(expense)"
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
            <template x-for="expense in expenses" :key="expense.id">
                <div class="card rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox"
                                   :value="expense.id"
                                   x-model="selectedExpenses"
                                   class="w-5 h-5 rounded border-border text-primary focus:ring-primary focus:ring-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                  :class="getStatusClass(expense.status)"
                                  x-text="getStatusText(expense.status)">
                            </span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <button @click="viewExpense(expense)"
                                    class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <button @click="editExpense(expense)"
                                    class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-foreground text-base leading-tight" x-text="expense.description"></h3>
                            <span class="font-semibold text-base text-red-600" x-text="formatCurrency(expense.amount)"></span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 pt-3 border-t border-border">
                            <div>
                                <span class="text-xs font-medium text-muted uppercase tracking-wide">Kategori</span>
                                <p class="text-sm text-foreground mt-1" x-text="expense.category"></p>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-muted uppercase tracking-wide">Vendor</span>
                                <p class="text-sm text-foreground mt-1" x-text="expense.vendor || 'T/A'"></p>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-muted uppercase tracking-wide">Tanggal</span>
                                <p class="text-sm text-foreground mt-1" x-text="formatDate(expense.date)"></p>
                            </div>
                            <div class="flex items-end justify-end">
                                <button @click="deleteExpense(expense)"
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
        <div x-show="expenses.length === 0 && !loading" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-foreground">Tidak ada pengeluaran ditemukan</h3>
            <p class="mt-1 text-sm text-muted">Mulai dengan mencatat pengeluaran pertama Anda.</p>
            <div class="mt-6">
                <button @click="showExpenseForm = true" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Pengeluaran Baru
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function expenseManager() {
    return {
        loading: false,
        showExpenseForm: false,
        selectedExpenses: [],
        expenses: [],
        summary: {
            total_expenses: 0,
            daily_average: 0,
            pending_count: 0,
            pending_amount: 0,
            top_category: { name: '', amount: 0 }
        },
        filters: {
            search: '',
            category: '',
            status: '',
            date_range: ''
        },
        pagination: {
            current_page: 1,
            per_page: 25,
            total: 0,
            last_page: 1
        },
        expenseCategoryChart: null,
        expenseTrendChart: null,

        init() {
            this.loadExpenses();
            this.loadSummary();
            this.initCharts();
        },

        async loadExpenses() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    ...this.filters
                });

                const response = await fetch(`/api/financial/expenses?${params}`);
                const data = await response.json();
                
                this.expenses = data.data || [];
                this.pagination = {
                    current_page: data.current_page || 1,
                    per_page: data.per_page || 25,
                    total: data.total || 0,
                    last_page: data.last_page || 1
                };
            } catch (error) {
                console.error('Error loading expenses:', error);
                this.expenses = this.getDemoExpenses();
            } finally {
                this.loading = false;
            }
        },

        async loadSummary() {
            try {
                const response = await fetch('/api/financial/expenses/summary');
                const data = await response.json();
                this.summary = data;
            } catch (error) {
                console.error('Error loading summary:', error);
                this.summary = {
                    total_expenses: 87500000,
                    daily_average: 2900000,
                    pending_count: 5,
                    pending_amount: 12500000,
top_category: { name: 'Operasional', amount: 25000000 }
                };
            }
        },

        getDemoExpenses() {
            return [
                {
                    id: 1,
                    description: 'Pembayaran Sewa Kantor',
                    amount: 5000000,
                    category: 'operations',
                    vendor: 'Property Management Co.',
                    status: 'paid',
                    date: '2024-01-15',
                    reference: 'RENT-JAN-2024'
                },
                {
                    id: 2,
                    description: 'Kampanye Pemasaran',
                    amount: 2500000,
                    category: 'marketing',
                    vendor: 'Digital Agency',
                    status: 'pending',
                    date: '2024-01-14',
                    reference: 'MKT-001'
                },
                {
                    id: 3,
                    description: 'Perlengkapan Kantor',
                    amount: 750000,
                    category: 'operations',
                    vendor: 'Office Supply Store',
                    status: 'approved',
                    date: '2024-01-13',
                    reference: 'SUP-001'
                }
            ];
        },

        initCharts() {
            this.$nextTick(() => {
                this.initCategoryChart();
                this.initTrendChart();
            });
        },

        initCategoryChart() {
            const ctx = document.getElementById('expenseCategoryChart');
            if (!ctx) return;

            this.expenseCategoryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Operasional', 'Pemasaran', 'Peralatan', 'Utilitas', 'Perjalanan'],
                    datasets: [{
                        data: [25000000, 15000000, 12000000, 8000000, 5000000],
                        backgroundColor: [
                            '#EF4444',
                            '#F59E0B',
                            '#10B981',
                            '#3B82F6',
                            '#8B5CF6'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                        minimumFractionDigits: 0
                                    }).format(context.parsed);
                                }
                            }
                        }
                    }
                }
            });
        },

        initTrendChart() {
            const ctx = document.getElementById('expenseTrendChart');
            if (!ctx) return;

            this.expenseTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Pengeluaran Bulanan',
                        data: [65000000, 70000000, 68000000, 72000000, 75000000, 87500000],
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                        minimumFractionDigits: 0
                                    }).format(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                        minimumFractionDigits: 0
                                    }).format(context.parsed.y);
                                }
                            }
                        }
                    }
                }
            });
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

        getStatusText(status) {
            const statuses = {
                pending: 'Tertunda',
                approved: 'Disetujui',
                rejected: 'Ditolak',
                paid: 'Dibayar'
            };
            return statuses[status] || status;
        },

        getStatusClass(status) {
            const classes = {
                pending: 'bg-yellow-100 text-yellow-800',
                approved: 'bg-blue-100 text-blue-800',
                rejected: 'bg-red-100 text-red-800',
                paid: 'bg-green-100 text-green-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },

        toggleSelectAll(checked) {
            if (checked) {
                this.selectedExpenses = this.expenses.map(e => e.id);
            } else {
                this.selectedExpenses = [];
            }
        },

        viewExpense(expense) {
            console.log('View expense:', expense);
        },

        editExpense(expense) {
            console.log('Edit expense:', expense);
        },

        async deleteExpense(expense) {
            if (confirm('Apakah Anda yakin ingin menghapus pengeluaran ini?')) {
                try {
                    await fetch(`/api/financial/expenses/${expense.id}`, {
                        method: 'DELETE'
                    });
                    this.loadExpenses();
                    this.loadSummary();
                } catch (error) {
                    console.error('Error deleting expense:', error);
                }
            }
        },

        async importExpenses() {
            console.log('Import expenses');
        },

        async exportExpenses() {
            try {
                const response = await fetch('/api/financial/expenses/export');
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'expenses.xlsx';
                a.click();
            } catch (error) {
                console.error('Error exporting expenses:', error);
            }
        }
    }
}
</script>
@endsection