@extends('layouts.dashboard')

@section('title', 'Dashboard Keuangan')
@section('page-title')
<span class="text-base lg:text-2xl">Dashboard Keuangan</span>
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
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Dashboard Keuangan</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="financialDashboard()" class="space-y-4 lg:space-y-6 p-4 lg:p-0">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Ringkasan Keuangan</h2>
            <p class="text-sm text-muted">Pantau kesehatan dan kinerja keuangan bisnis Anda</p>
        </div>
        <div class="flex items-center space-x-3">
            <!-- Period Selector -->
            <select x-model="selectedPeriod" @change="loadDashboardData()" class="input py-2 text-sm">
                <option value="today">Hari Ini</option>
                <option value="week">Minggu Ini</option>
                <option value="month">Bulan Ini</option>
                <option value="quarter">Kuartal Ini</option>
                <option value="year">Tahun Ini</option>
                <option value="custom">Rentang Khusus</option>
            </select>
            
            <button @click="exportFinancialReport()" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor Laporan
            </button>
            
            <a href="{{ route('financial.transactions.create') }}" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Transaksi Baru
            </a>
        </div>
    </div>

    <!-- Financial KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Total Revenue -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Total Pendapatan</p>
                    <p class="text-2xl lg:text-3xl font-bold text-green-600" x-text="formatCurrency(kpis.total_revenue)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span :class="kpis.revenue_change >= 0 ? 'text-green-600' : 'text-red-600'" 
                              x-text="(kpis.revenue_change >= 0 ? '+' : '') + kpis.revenue_change + '%'"></span>
                        vs periode lalu
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
                    <p class="text-2xl lg:text-3xl font-bold text-red-600" x-text="formatCurrency(kpis.total_expenses)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span :class="kpis.expense_change >= 0 ? 'text-red-600' : 'text-green-600'" 
                              x-text="(kpis.expense_change >= 0 ? '+' : '') + kpis.expense_change + '%'"></span>
                        vs periode lalu
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Net Profit -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Laba Bersih</p>
                    <p class="text-2xl lg:text-3xl font-bold text-primary" x-text="formatCurrency(kpis.net_profit)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span :class="kpis.profit_margin >= 0 ? 'text-green-600' : 'text-red-600'" 
                              x-text="kpis.profit_margin + '% margin'"></span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Cash Balance -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Saldo Kas</p>
                    <p class="text-2xl lg:text-3xl font-bold text-blue-600" x-text="formatCurrency(kpis.cash_balance)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-blue-600">Dana tersedia</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Health Indicators -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Quick Ratio -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-foreground">Rasio Cepat</h3>
                <span :class="healthIndicators.quick_ratio >= 1 ? 'text-green-600' : 'text-red-600'" 
                      class="text-2xl font-bold" x-text="healthIndicators.quick_ratio"></span>
            </div>
            <div class="w-full bg-border rounded-full h-2">
                <div class="bg-primary h-2 rounded-full transition-all duration-300" 
                     :style="`width: ${Math.min(healthIndicators.quick_ratio * 50, 100)}%`"></div>
            </div>
            <p class="text-xs text-muted mt-2">Ukuran likuiditas (target: ≥1.0)</p>
        </div>

        <!-- Debt-to-Equity -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-foreground">Rasio Utang-Ekuitas</h3>
                <span :class="healthIndicators.debt_to_equity <= 0.5 ? 'text-green-600' : 'text-red-600'" 
                      class="text-2xl font-bold" x-text="healthIndicators.debt_to_equity"></span>
            </div>
            <div class="w-full bg-border rounded-full h-2">
                <div class="bg-red-500 h-2 rounded-full transition-all duration-300" 
                     :style="`width: ${Math.min(healthIndicators.debt_to_equity * 100, 100)}%`"></div>
            </div>
            <p class="text-xs text-muted mt-2">Leverage keuangan (target: ≤0.5)</p>
        </div>

        <!-- ROI -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-foreground">ROI</h3>
                <span :class="healthIndicators.roi >= 15 ? 'text-green-600' : 'text-red-600'" 
                      class="text-2xl font-bold" x-text="healthIndicators.roi + '%'"></span>
            </div>
            <div class="w-full bg-border rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full transition-all duration-300" 
                     :style="`width: ${Math.min(healthIndicators.roi * 2, 100)}%`"></div>
            </div>
            <p class="text-xs text-muted mt-2">Pengembalian investasi (target: ≥15%)</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <!-- Revenue vs Expenses Trend -->
        <div class="card">
            <div class="p-4 lg:p-6 border-b border-border">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-foreground">Pendapatan vs Pengeluaran</h3>
                    <select x-model="chartPeriod" @change="updateCharts()" class="input py-1 text-sm">
                        <option value="7">7 Hari</option>
                        <option value="30">30 Hari</option>
                        <option value="90">90 Hari</option>
                        <option value="365">1 Tahun</option>
                    </select>
                </div>
            </div>
            <div class="p-4 lg:p-6">
                <canvas id="revenueExpenseChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Cash Flow Chart -->
        <div class="card">
            <div class="p-4 lg:p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-foreground">Analisis Arus Kas</h3>
            </div>
            <div class="p-4 lg:p-6">
                <canvas id="cashFlowChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Expense Categories -->
        <div class="card">
            <div class="p-4 lg:p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-foreground">Rincian Pengeluaran</h3>
            </div>
            <div class="p-4 lg:p-6">
                <canvas id="expenseCategoriesChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Profit Margin Trend -->
        <div class="card">
            <div class="p-4 lg:p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-foreground">Tren Margin Keuntungan</h3>
            </div>
            <div class="p-4 lg:p-6">
                <canvas id="profitMarginChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Transactions & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <!-- Recent Transactions -->
        <div class="card">
            <div class="p-4 lg:p-6 border-b border-border">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-foreground">Transaksi Terbaru</h3>
                    <a href="{{ route('financial.transactions.index') }}" class="text-sm text-primary hover:text-primary/80">Lihat Semua</a>
                </div>
            </div>
            
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="transaction in recentTransactions" :key="transaction.id">
                            <tr class="hover:bg-surface/50">
                                <td>
                                    <div class="text-sm text-foreground" x-text="formatDate(transaction.date)"></div>
                                </td>
                                <td>
                                    <div class="font-medium text-foreground" x-text="transaction.description"></div>
                                    <div class="text-sm text-muted" x-text="transaction.reference"></div>
                                </td>
                                <td>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                          x-text="transaction.category">
                                    </span>
                                </td>
                                <td>
                                    <span class="font-medium"
                                          :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'"
                                          x-text="(transaction.type === 'income' ? '+' : '-') + formatCurrency(transaction.amount)">
                                    </span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden space-y-3 mobile-card-spacing">
                <template x-for="transaction in recentTransactions" :key="transaction.id">
                    <div class="card rounded-xl p-4 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                  :class="transaction.type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                  x-text="transaction.type === 'income' ? 'Pemasukan' : 'Pengeluaran'">
                            </span>
                            <span class="font-semibold text-base"
                                  :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'"
                                  x-text="(transaction.type === 'income' ? '+' : '-') + formatCurrency(transaction.amount)">
                            </span>
                        </div>
                        <div class="space-y-2">
                            <h3 class="font-medium text-foreground" x-text="transaction.description"></h3>
                            <p class="text-sm text-muted" x-text="transaction.reference"></p>
                            <div class="grid grid-cols-2 gap-4 pt-3 border-t border-border">
                                <div>
                                    <span class="text-xs font-medium text-muted uppercase tracking-wide">Kategori</span>
                                    <p class="text-sm text-foreground mt-1" x-text="transaction.category"></p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-muted uppercase tracking-wide">Tanggal</span>
                                    <p class="text-sm text-foreground mt-1" x-text="formatDate(transaction.date)"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Quick Actions & Alerts -->
        <div class="space-y-4 lg:space-y-6">
            <!-- Quick Actions -->
            <div class="card">
                <div class="p-4 lg:p-6 border-b border-border">
                    <h3 class="text-lg font-semibold text-foreground">Aksi Cepat</h3>
                </div>
                <div class="p-4 lg:p-6 space-y-3">
                    <a href="{{ route('financial.transactions.create') }}" 
                       class="flex items-center p-3 bg-border/30 rounded-lg hover:bg-border/50 transition-colors">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-foreground">Catat Pemasukan</p>
                            <p class="text-xs text-muted">Tambah transaksi pemasukan baru</p>
                        </div>
                    </a>

                    <a href="{{ route('expenses.index') }}"
                       class="flex items-center p-3 bg-border/30 rounded-lg hover:bg-border/50 transition-colors">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-foreground">Catat Pengeluaran</p>
                            <p class="text-xs text-muted">Tambah transaksi pengeluaran baru</p>
                        </div>
                    </a>

                    <a href="{{ route('financial.transactions.create') }}"
                       class="flex items-center p-3 bg-border/30 rounded-lg hover:bg-border/50 transition-colors">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-foreground">Buat Faktur</p>
                            <p class="text-xs text-muted">Buat faktur baru</p>
                        </div>
                    </a>

                    <a href="{{ route('financial.reports.index') }}"
                       class="flex items-center p-3 bg-border/30 rounded-lg hover:bg-border/50 transition-colors">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-foreground">Lihat Laporan</p>
                            <p class="text-xs text-muted">Laporan keuangan & analitik</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Financial Alerts -->
            <div class="card">
                <div class="p-4 lg:p-6 border-b border-border">
                    <h3 class="text-lg font-semibold text-foreground">Peringatan Keuangan</h3>
                </div>
                <div class="p-4 lg:p-6 space-y-3">
                    <template x-for="alert in financialAlerts" :key="alert.id">
                        <div class="flex items-start p-3 rounded-lg border"
                             :class="alert.type === 'warning' ? 'bg-yellow-50 border-yellow-200' : alert.type === 'danger' ? 'bg-red-50 border-red-200' : 'bg-blue-50 border-blue-200'">
                            <div class="flex-shrink-0 mr-3">
                                <svg x-show="alert.type === 'warning'" class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                <svg x-show="alert.type === 'danger'" class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <svg x-show="alert.type === 'info'" class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium" 
                                   :class="alert.type === 'warning' ? 'text-yellow-800' : alert.type === 'danger' ? 'text-red-800' : 'text-blue-800'"
                                   x-text="alert.title"></p>
                                <p class="text-xs mt-1" 
                                   :class="alert.type === 'warning' ? 'text-yellow-700' : alert.type === 'danger' ? 'text-red-700' : 'text-blue-700'"
                                   x-text="alert.message"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function financialDashboard() {
    return {
        selectedPeriod: 'month',
        chartPeriod: '30',
        loading: false,
        
        kpis: {
            total_revenue: 25750000,
            total_expenses: 18250000,
            net_profit: 7500000,
            cash_balance: 15300000,
            revenue_change: 12.5,
            expense_change: 8.3,
            profit_margin: 29.1
        },
        
        healthIndicators: {
            quick_ratio: 1.2,
            debt_to_equity: 0.3,
            roi: 18.5
        },
        
        recentTransactions: [
            {
                id: 1,
                date: '2024-01-15T14:30:00Z',
                description: 'Pendapatan Penjualan Produk',
                reference: 'INV-2024-001',
                category: 'Penjualan',
                type: 'income',
                amount: 1250000
            },
            {
                id: 2,
                date: '2024-01-15T10:15:00Z',
                description: 'Pembelian Perlengkapan Kantor',
                reference: 'EXP-2024-005',
                category: 'Biaya Kantor',
type: 'expense',
                amount: 350000
            },
            {
                id: 3,
                date: '2024-01-14T16:45:00Z',
                description: 'Kampanye Pemasaran',
                reference: 'EXP-2024-006',
                category: 'Pemasaran',
                type: 'expense',
                amount: 750000
            },
            {
                id: 4,
                date: '2024-01-14T12:20:00Z',
                description: 'Layanan Konsultasi',
                reference: 'INV-2024-002',
                category: 'Layanan',
                type: 'income',
                amount: 2250000
            },
            {
                id: 5,
                date: '2024-01-13T09:30:00Z',
                description: 'Pembelian Peralatan',
                reference: 'EXP-2024-007',
                category: 'Peralatan',
                type: 'expense',
                amount: 1500000
            }
        ],
        
        financialAlerts: [
            {
                id: 1,
                type: 'warning',
                title: 'Peringatan Anggaran',
                message: 'Anggaran pemasaran telah terpakai 85% bulan ini'
            },
            {
                id: 2,
                type: 'danger',
                title: 'Faktur Terlambat',
                message: '3 faktur terlambat dengan total Rp 2,500,000'
            },
            {
                id: 3,
                type: 'info',
                title: 'Prakiraan Arus Kas',
                message: 'Arus kas positif diperkirakan bulan depan'
            }
        ],
        
        // Chart instances
        revenueExpenseChart: null,
        cashFlowChart: null,
        expenseCategoriesChart: null,
        profitMarginChart: null,

        async init() {
            await this.$nextTick();
            this.loadDashboardData();
            this.initCharts();
        },

        async loadDashboardData() {
            this.loading = true;
            try {
                // In a real application, this would fetch data from API
                // For now, we'll simulate the data loading
                await new Promise(resolve => setTimeout(resolve, 500));
                
                // Update KPIs based on selected period
                this.updateKPIsForPeriod();
                
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Error!',
                    message: 'Failed to load dashboard data'
                });
            } finally {
                this.loading = false;
            }
        },

        updateKPIsForPeriod() {
            // Simulate different data for different periods
            const periodMultipliers = {
                'today': 0.1,
                'week': 0.3,
                'month': 1,
                'quarter': 3,
                'year': 12
            };
            
            const multiplier = periodMultipliers[this.selectedPeriod] || 1;
            
            this.kpis = {
                total_revenue: Math.round(25750000 * multiplier),
                total_expenses: Math.round(18250000 * multiplier),
                net_profit: Math.round(7500000 * multiplier),
                cash_balance: 15300000, // Cash balance doesn't change with period
                revenue_change: 12.5 + (Math.random() * 10 - 5), // Add some variance
                expense_change: 8.3 + (Math.random() * 6 - 3),
                profit_margin: 29.1 + (Math.random() * 4 - 2)
            };
        },

        initCharts() {
            this.initRevenueExpenseChart();
            this.initCashFlowChart();
            this.initExpenseCategoriesChart();
            this.initProfitMarginChart();
        },

        initRevenueExpenseChart() {
            const ctx = document.getElementById('revenueExpenseChart');
            if (!ctx) return;

            this.revenueExpenseChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.getChartLabels(),
                    datasets: [{
                        label: 'Pendapatan',
                        data: [3200000, 4100000, 3800000, 4500000, 4200000, 4800000, 5100000],
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Pengeluaran',
                        data: [2200000, 2800000, 2600000, 3100000, 2900000, 3200000, 3400000],
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                }
                            }
                        }
                    }
                }
            });
        },

        initCashFlowChart() {
            const ctx = document.getElementById('cashFlowChart');
            if (!ctx) return;

            this.cashFlowChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    datasets: [{
                        label: 'Arus Kas Masuk',
                        data: [4200000, 3800000, 4500000, 5100000],
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1
                    }, {
                        label: 'Arus Kas Keluar',
                        data: [-2800000, -2600000, -3100000, -3400000],
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (Math.abs(value) / 1000000).toFixed(1) + 'M';
                                }
                            }
                        }
                    }
                }
            });
        },

        initExpenseCategoriesChart() {
            const ctx = document.getElementById('expenseCategoriesChart');
            if (!ctx) return;

            this.expenseCategoriesChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Operasional', 'Pemasaran', 'Gaji', 'Peralatan', 'Utilitas'],
                    datasets: [{
                        data: [35, 25, 20, 12, 8],
                        backgroundColor: [
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)'
                        ],
                        borderColor: [
                            'rgb(255, 193, 7)',
                            'rgb(54, 162, 235)',
                            'rgb(255, 99, 132)',
                            'rgb(75, 192, 192)',
                            'rgb(153, 102, 255)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        },

        initProfitMarginChart() {
            const ctx = document.getElementById('profitMarginChart');
            if (!ctx) return;

            this.profitMarginChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.getChartLabels(),
                    datasets: [{
                        label: 'Margin Keuntungan %',
                        data: [28.5, 31.2, 29.8, 32.1, 30.5, 33.2, 35.1],
                        borderColor: 'rgb(255, 193, 7)',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 40,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        },

        getChartLabels() {
            const days = parseInt(this.chartPeriod);
            const labels = [];
            
            if (days <= 7) {
                for (let i = days - 1; i >= 0; i--) {
                    const date = new Date();
                    date.setDate(date.getDate() - i);
                    labels.push(date.toLocaleDateString('id-ID', { month: 'short', day: 'numeric' }));
                }
            } else if (days <= 30) {
                for (let i = 6; i >= 0; i--) {
                    const date = new Date();
                    date.setDate(date.getDate() - (i * Math.floor(days / 7)));
                    labels.push(date.toLocaleDateString('id-ID', { month: 'short', day: 'numeric' }));
                }
            } else {
                for (let i = 11; i >= 0; i--) {
                    const date = new Date();
                    date.setMonth(date.getMonth() - i);
                    labels.push(date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' }));
                }
            }
            
            return labels;
        },

        updateCharts() {
            // Update chart data based on selected period
            if (this.revenueExpenseChart) {
                this.revenueExpenseChart.data.labels = this.getChartLabels();
                this.revenueExpenseChart.update();
            }
            
            if (this.profitMarginChart) {
                this.profitMarginChart.data.labels = this.getChartLabels();
                this.profitMarginChart.update();
            }
        },

        async exportFinancialReport() {
            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Success!',
                    message: 'Financial report exported successfully.'
                });
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Error!',
                    message: 'Failed to export financial report'
                });
            }
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount || 0);
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }
    }
}
</script>
@endpush
                type: 'expense