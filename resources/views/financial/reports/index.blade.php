@extends('layouts.dashboard')

@section('title', 'Laporan Keuangan')
@section('page-title')
<span class="text-base lg:text-2xl">Laporan Keuangan</span>
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
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Laporan</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="financialReports()" class="space-y-4 lg:space-y-6 p-4 lg:p-0">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Laporan Keuangan</h2>
            <p class="text-sm text-muted">Buat laporan dan pernyataan keuangan yang komprehensif</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="scheduleReport()" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Jadwalkan Laporan
            </button>
            
            <button @click="exportAllReports()" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor Semua
            </button>
        </div>
    </div>

    <!-- Report Filters -->
    <div class="card p-4 lg:p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Periode Laporan</label>
                <select x-model="filters.period" 
                        @change="updateDateRange()"
                        class="input">
                    <option value="this_month">Bulan Ini</option>
                    <option value="last_month">Bulan Lalu</option>
                    <option value="this_quarter">Kuartal Ini</option>
                    <option value="last_quarter">Kuartal Lalu</option>
                    <option value="this_year">Tahun Ini</option>
                    <option value="last_year">Tahun Lalu</option>
                    <option value="custom">Rentang Khusus</option>
                </select>
            </div>

            <!-- Custom Date Range -->
            <div x-show="filters.period === 'custom'">
                <label class="block text-sm font-medium text-foreground mb-2">Tanggal Mulai</label>
                <input type="date" 
                       x-model="filters.start_date"
                       @change="loadReports()"
                       class="input">
            </div>

            <div x-show="filters.period === 'custom'">
                <label class="block text-sm font-medium text-foreground mb-2">Tanggal Akhir</label>
                <input type="date" 
                       x-model="filters.end_date"
                       @change="loadReports()"
                       class="input">
            </div>

            <!-- Report Type -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Jenis Laporan</label>
                <select x-model="filters.report_type" 
                        @change="loadReports()"
                        class="input">
                    <option value="all">Semua Laporan</option>
                    <option value="profit_loss">Laba Rugi</option>
                    <option value="balance_sheet">Neraca</option>
                    <option value="cash_flow">Arus Kas</option>
                    <option value="budget_variance">Varians Anggaran</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Profit & Loss -->
        <div class="card p-4 lg:p-6 cursor-pointer hover:shadow-lg transition-shadow" 
             @click="activeReport = 'profit_loss'">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-foreground">Laba Rugi</h3>
                    <p class="text-sm text-muted">Laporan laba rugi</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-bold text-green-600" x-text="formatCurrency(reportSummary.net_profit)"></p>
                <p class="text-xs text-muted">Laba Bersih</p>
            </div>
        </div>

        <!-- Balance Sheet -->
        <div class="card p-4 lg:p-6 cursor-pointer hover:shadow-lg transition-shadow" 
             @click="activeReport = 'balance_sheet'">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-foreground">Neraca</h3>
                    <p class="text-sm text-muted">Posisi keuangan</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-bold text-blue-600" x-text="formatCurrency(reportSummary.total_assets)"></p>
                <p class="text-xs text-muted">Total Aset</p>
            </div>
        </div>

        <!-- Cash Flow -->
        <div class="card p-4 lg:p-6 cursor-pointer hover:shadow-lg transition-shadow" 
             @click="activeReport = 'cash_flow'">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-foreground">Arus Kas</h3>
                    <p class="text-sm text-muted">Pergerakan kas</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-bold text-purple-600" x-text="formatCurrency(reportSummary.net_cash_flow)"></p>
                <p class="text-xs text-muted">Arus Kas Bersih</p>
            </div>
        </div>

        <!-- Budget Variance -->
        <div class="card p-4 lg:p-6 cursor-pointer hover:shadow-lg transition-shadow" 
             @click="activeReport = 'budget_variance'">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-foreground">Varians Anggaran</h3>
                    <p class="text-sm text-muted">Anggaran vs aktual</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-bold" 
                   :class="reportSummary.budget_variance >= 0 ? 'text-green-600' : 'text-red-600'"
                   x-text="formatCurrency(reportSummary.budget_variance)"></p>
                <p class="text-xs text-muted">Varians</p>
            </div>
        </div>
    </div>

    <!-- Report Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Report -->
        <div class="lg:col-span-2">
            <!-- Profit & Loss Statement -->
            <div x-show="activeReport === 'profit_loss'" class="card">
                <div class="p-4 lg:p-6 border-b border-border">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-foreground">Laporan Laba Rugi</h3>
                        <div class="flex items-center space-x-2">
                            <button @click="exportReport('profit_loss')" class="btn-secondary text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Ekspor
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-4 lg:p-6">
                    <!-- Revenue Section -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-foreground mb-3">Pendapatan</h4>
                        <div class="space-y-2">
                            <template x-for="item in profitLossData.revenue" :key="item.category">
                                <div class="flex justify-between items-center py-2 border-b border-border">
                                    <span class="text-sm text-foreground" x-text="item.category"></span>
                                    <span class="text-sm font-medium text-foreground" x-text="formatCurrency(item.amount)"></span>
                                </div>
                            </template>
                            <div class="flex justify-between items-center py-2 font-semibold border-t-2 border-border">
                                <span class="text-foreground">Total Pendapatan</span>
                                <span class="text-green-600" x-text="formatCurrency(profitLossData.total_revenue)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Expenses Section -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-foreground mb-3">Pengeluaran</h4>
                        <div class="space-y-2">
                            <template x-for="item in profitLossData.expenses" :key="item.category">
                                <div class="flex justify-between items-center py-2 border-b border-border">
                                    <span class="text-sm text-foreground" x-text="item.category"></span>
                                    <span class="text-sm font-medium text-foreground" x-text="formatCurrency(item.amount)"></span>
                                </div>
                            </template>
                            <div class="flex justify-between items-center py-2 font-semibold border-t-2 border-border">
                                <span class="text-foreground">Total Pengeluaran</span>
                                <span class="text-red-600" x-text="formatCurrency(profitLossData.total_expenses)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Net Profit -->
                    <div class="bg-surface p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-foreground">Laba Bersih</span>
                            <span class="text-lg font-bold" 
                                  :class="profitLossData.net_profit >= 0 ? 'text-green-600' : 'text-red-600'"
                                  x-text="formatCurrency(profitLossData.net_profit)"></span>
                        </div>
                        <div class="mt-2 text-sm text-muted">
                            <span x-text="`Margin Laba: ${((profitLossData.net_profit / profitLossData.total_revenue) * 100).toFixed(2)}%`"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Balance Sheet -->
            <div x-show="activeReport === 'balance_sheet'" class="card">
                <div class="p-4 lg:p-6 border-b border-border">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-foreground">Neraca</h3>
                        <div class="flex items-center space-x-2">
                            <button @click="exportReport('balance_sheet')" class="btn-secondary text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Ekspor
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-4 lg:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Assets -->
                        <div>
                            <h4 class="text-md font-semibold text-foreground mb-3">Aset</h4>
                            
                            <!-- Current Assets -->
                            <div class="mb-4">
                                <h5 class="text-sm font-medium text-muted mb-2">Aset Lancar</h5>
                                <div class="space-y-2">
                                    <template x-for="item in balanceSheetData.current_assets" :key="item.category">
                                        <div class="flex justify-between items-center py-1">
                                            <span class="text-sm text-foreground" x-text="item.category"></span>
                                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(item.amount)"></span>
                                        </div>
                                    </template>
                                    <div class="flex justify-between items-center py-1 font-medium border-t border-border">
                                        <span class="text-sm text-foreground">Total Aset Lancar</span>
                                        <span class="text-sm text-foreground" x-text="formatCurrency(balanceSheetData.total_current_assets)"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Fixed Assets -->
                            <div class="mb-4">
                                <h5 class="text-sm font-medium text-muted mb-2">Aset Tetap</h5>
                                <div class="space-y-2">
                                    <template x-for="item in balanceSheetData.fixed_assets" :key="item.category">
                                        <div class="flex justify-between items-center py-1">
                                            <span class="text-sm text-foreground" x-text="item.category"></span>
                                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(item.amount)"></span>
                                        </div>
                                    </template>
                                    <div class="flex justify-between items-center py-1 font-medium border-t border-border">
                                        <span class="text-sm text-foreground">Total Aset Tetap</span>
                                        <span class="text-sm text-foreground" x-text="formatCurrency(balanceSheetData.total_fixed_assets)"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50 p-3 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-foreground">Total Aset</span>
                                    <span class="font-bold text-blue-600" x-text="formatCurrency(balanceSheetData.total_assets)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Liabilities & Equity -->
                        <div>
                            <h4 class="text-md font-semibold text-foreground mb-3">Kewajiban & Ekuitas</h4>
                            
                            <!-- Current Liabilities -->
                            <div class="mb-4">
                                <h5 class="text-sm font-medium text-muted mb-2">Kewajiban Lancar</h5>
                                <div class="space-y-2">
                                    <template x-for="item in balanceSheetData.current_liabilities" :key="item.category">
                                        <div class="flex justify-between items-center py-1">
                                            <span class="text-sm text-foreground" x-text="item.category"></span>
                                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(item.amount)"></span>
                                        </div>
                                    </template>
                                    <div class="flex justify-between items-center py-1 font-medium border-t border-border">
                                        <span class="text-sm text-foreground">Total Kewajiban Lancar</span>
                                        <span class="text-sm text-foreground" x-text="formatCurrency(balanceSheetData.total_current_liabilities)"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Long-term Liabilities -->
                            <div class="mb-4">
                                <h5 class="text-sm font-medium text-muted mb-2">Kewajiban Jangka Panjang</h5>
                                <div class="space-y-2">
                                    <template x-for="item in balanceSheetData.long_term_liabilities" :key="item.category">
                                        <div class="flex justify-between items-center py-1">
                                            <span class="text-sm text-foreground" x-text="item.category"></span>
                                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(item.amount)"></span>
                                        </div>
                                    </template>
                                    <div class="flex justify-between items-center py-1 font-medium border-t border-border">
                                        <span class="text-sm text-foreground">Total Kewajiban Jangka Panjang</span>
                                        <span class="text-sm text-foreground" x-text="formatCurrency(balanceSheetData.total_long_term_liabilities)"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Equity -->
                            <div class="mb-4">
                                <h5 class="text-sm font-medium text-muted mb-2">Ekuitas</h5>
                                <div class="space-y-2">
                                    <template x-for="item in balanceSheetData.equity" :key="item.category">
                                        <div class="flex justify-between items-center py-1">
                                            <span class="text-sm text-foreground" x-text="item.category"></span>
                                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(item.amount)"></span>
                                        </div>
                                    </template>
                                    <div class="flex justify-between items-center py-1 font-medium border-t border-border">
                                        <span class="text-sm text-foreground">Total Ekuitas</span>
                                        <span class="text-sm text-foreground" x-text="formatCurrency(balanceSheetData.total_equity)"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-blue-50 p-3 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-foreground">Total Kewajiban & Ekuitas</span>
                                    <span class="font-bold text-blue-600" x-text="formatCurrency(balanceSheetData.total_liabilities_equity)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cash Flow Statement -->
            <div x-show="activeReport === 'cash_flow'" class="card">
                <div class="p-4 lg:p-6 border-b border-border">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-foreground">Laporan Arus Kas</h3>
                        <div class="flex items-center space-x-2">
                            <button @click="exportReport('cash_flow')" class="btn-secondary text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Ekspor
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-4 lg:p-6">
                    <!-- Operating Activities -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-foreground mb-3">Aktivitas Operasional</h4>
                        <div class="space-y-2">
                            <template x-for="item in cashFlowData.operating" :key="item.category">
                                <div class="flex justify-between items-center py-2 border-b border-border">
                                    <span class="text-sm text-foreground" x-text="item.category"></span>
                                    <span class="text-sm font-medium" 
                                          :class="item.amount >= 0 ? 'text-green-600' : 'text-red-600'"
                                          x-text="formatCurrency(item.amount)"></span>
                                </div>
                            </template>
                            <div class="flex justify-between items-center py-2 font-semibold border-t-2 border-border">
                                <span class="text-foreground">Kas Bersih dari Operasional</span>
                                <span :class="cashFlowData.net_operating >= 0 ? 'text-green-600' : 'text-red-600'"
                                      x-text="formatCurrency(cashFlowData.net_operating)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Investing Activities -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-foreground mb-3">Aktivitas Investasi</h4>
                        <div class="space-y-2">
                            <template x-for="item in cashFlowData.investing" :key="item.category">
                                <div class="flex justify-between items-center py-2 border-b border-border">
                                    <span class="text-sm text-foreground" x-text="item.category"></span>
                                    <span class="text-sm font-medium" 
                                          :class="item.amount >= 0 ? 'text-green-600' : 'text-red-600'"
                                          x-text="formatCurrency(item.amount)"></span>
                                </div>
                            </template>
                            <div class="flex justify-between items-center py-2 font-semibold border-t-2 border-border">
                                <span class="text-foreground">Kas Bersih dari Investasi</span>
                                <span :class="cashFlowData.net_investing >= 0 ? 'text-green-600' : 'text-red-600'"
                                      x-text="formatCurrency(cashFlowData.net_investing)"></span>
</div>
                        </div>
                    </div>

                    <!-- Financing Activities -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-foreground mb-3">Aktivitas Pendanaan</h4>
                        <div class="space-y-2">
                            <template x-for="item in cashFlowData.financing" :key="item.category">
                                <div class="flex justify-between items-center py-2 border-b border-border">
                                    <span class="text-sm text-foreground" x-text="item.category"></span>
                                    <span class="text-sm font-medium" 
                                          :class="item.amount >= 0 ? 'text-green-600' : 'text-red-600'"
                                          x-text="formatCurrency(item.amount)"></span>
                                </div>
                            </template>
                            <div class="flex justify-between items-center py-2 font-semibold border-t-2 border-border">
                                <span class="text-foreground">Kas Bersih dari Pendanaan</span>
                                <span :class="cashFlowData.net_financing >= 0 ? 'text-green-600' : 'text-red-600'"
                                      x-text="formatCurrency(cashFlowData.net_financing)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Net Cash Flow -->
                    <div class="bg-surface p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-foreground">Arus Kas Bersih</span>
                            <span class="text-lg font-bold" 
                                  :class="cashFlowData.net_cash_flow >= 0 ? 'text-green-600' : 'text-red-600'"
                                  x-text="formatCurrency(cashFlowData.net_cash_flow)"></span>
                        </div>
                        <div class="mt-2 space-y-1 text-sm text-muted">
                            <div class="flex justify-between">
                                <span>Saldo Kas Awal:</span>
                                <span x-text="formatCurrency(cashFlowData.beginning_cash)"></span>
                            </div>
                            <div class="flex justify-between font-medium text-foreground">
                                <span>Saldo Kas Akhir:</span>
                                <span x-text="formatCurrency(cashFlowData.ending_cash)"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Budget Variance Report -->
            <div x-show="activeReport === 'budget_variance'" class="card">
                <div class="p-4 lg:p-6 border-b border-border">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-foreground">Laporan Varians Anggaran</h3>
                        <div class="flex items-center space-x-2">
                            <button @click="exportReport('budget_variance')" class="btn-secondary text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Ekspor
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-4 lg:p-6">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Anggaran</th>
                                    <th>Aktual</th>
                                    <th>Varians</th>
                                    <th>Varians %</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="item in budgetVarianceData" :key="item.category">
                                    <tr>
                                        <td>
                                            <span class="font-medium text-foreground" x-text="item.category"></span>
                                        </td>
                                        <td>
                                            <span class="text-foreground" x-text="formatCurrency(item.budget)"></span>
                                        </td>
                                        <td>
                                            <span class="text-foreground" x-text="formatCurrency(item.actual)"></span>
                                        </td>
                                        <td>
                                            <span :class="item.variance >= 0 ? 'text-green-600' : 'text-red-600'"
                                                  x-text="formatCurrency(item.variance)"></span>
                                        </td>
                                        <td>
                                            <span :class="item.variance_percentage >= 0 ? 'text-green-600' : 'text-red-600'"
                                                  x-text="`${item.variance_percentage.toFixed(1)}%`"></span>
                                        </td>
                                        <td>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                  :class="getBudgetVarianceStatusClass(item.variance_percentage)"
                                                  x-text="getBudgetVarianceStatus(item.variance_percentage)">
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Report Summary -->
            <div class="card p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Ringkasan Laporan</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-muted">Periode Laporan:</span>
                        <span class="text-sm font-medium text-foreground" x-text="getFormattedPeriod()"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-muted">Dibuat:</span>
                        <span class="text-sm font-medium text-foreground" x-text="new Date().toLocaleDateString()"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-muted">Mata Uang:</span>
                        <span class="text-sm font-medium text-foreground">IDR</span>
                    </div>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="card p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Metrik Utama</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-muted">Margin Laba Kotor</span>
                            <span class="text-sm font-medium text-foreground" x-text="`${reportMetrics.gross_profit_margin}%`"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full transition-all duration-300"
                                 :style="`width: ${Math.min(reportMetrics.gross_profit_margin, 100)}%`"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-muted">Margin Operasional</span>
                            <span class="text-sm font-medium text-foreground" x-text="`${reportMetrics.operating_margin}%`"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full transition-all duration-300"
                                 :style="`width: ${Math.min(reportMetrics.operating_margin, 100)}%`"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-muted">Rasio Lancar</span>
                            <span class="text-sm font-medium text-foreground" x-text="reportMetrics.current_ratio"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-500 h-2 rounded-full transition-all duration-300"
                                 :style="`width: ${Math.min((reportMetrics.current_ratio / 3) * 100, 100)}%`"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-muted">Rasio Utang-Ekuitas</span>
                            <span class="text-sm font-medium text-foreground" x-text="reportMetrics.debt_to_equity"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-500 h-2 rounded-full transition-all duration-300"
                                 :style="`width: ${Math.min((reportMetrics.debt_to_equity / 2) * 100, 100)}%`"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Actions -->
            <div class="card p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Aksi Laporan</h3>
                <div class="space-y-3">
                    <button @click="printReport()" class="w-full btn-secondary text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Cetak Laporan
                    </button>

                    <button @click="emailReport()" class="w-full btn-secondary text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Email Laporan
                    </button>

                    <button @click="saveTemplate()" class="w-full btn-secondary text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Simpan sebagai Template
                    </button>

                    <button @click="compareReports()" class="w-full btn-secondary text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Bandingkan Periode
                    </button>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="card p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Laporan Terbaru</h3>
                <div class="space-y-3">
                    <template x-for="report in recentReports" :key="report.id">
                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-surface cursor-pointer">
                            <div>
                                <p class="text-sm font-medium text-foreground" x-text="report.name"></p>
                                <p class="text-xs text-muted" x-text="report.date"></p>
                            </div>
                            <button @click="loadReport(report)" class="p-1 text-muted hover:text-foreground">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function financialReports() {
    return {
        loading: false,
        activeReport: 'profit_loss',
        filters: {
            period: 'this_month',
            start_date: '',
            end_date: '',
            report_type: 'all'
        },
        reportSummary: {
            net_profit: 0,
            total_assets: 0,
            net_cash_flow: 0,
            budget_variance: 0
        },
        reportMetrics: {
            gross_profit_margin: 0,
            operating_margin: 0,
            current_ratio: 0,
            debt_to_equity: 0
        },
        profitLossData: {
            revenue: [],
            expenses: [],
            total_revenue: 0,
            total_expenses: 0,
            net_profit: 0
        },
        balanceSheetData: {
            current_assets: [],
            fixed_assets: [],
            current_liabilities: [],
            long_term_liabilities: [],
            equity: [],
            total_current_assets: 0,
            total_fixed_assets: 0,
            total_assets: 0,
            total_current_liabilities: 0,
            total_long_term_liabilities: 0,
            total_equity: 0,
            total_liabilities_equity: 0
        },
        cashFlowData: {
            operating: [],
            investing: [],
            financing: [],
            net_operating: 0,
            net_investing: 0,
            net_financing: 0,
            net_cash_flow: 0,
            beginning_cash: 0,
            ending_cash: 0
        },
        budgetVarianceData: [],
        recentReports: [],

        init() {
            this.updateDateRange();
            this.loadReports();
            this.loadRecentReports();
        },

        updateDateRange() {
            const now = new Date();
            const currentYear = now.getFullYear();
            const currentMonth = now.getMonth();

            switch (this.filters.period) {
                case 'this_month':
                    this.filters.start_date = new Date(currentYear, currentMonth, 1).toISOString().split('T')[0];
                    this.filters.end_date = new Date(currentYear, currentMonth + 1, 0).toISOString().split('T')[0];
                    break;
                case 'last_month':
                    this.filters.start_date = new Date(currentYear, currentMonth - 1, 1).toISOString().split('T')[0];
                    this.filters.end_date = new Date(currentYear, currentMonth, 0).toISOString().split('T')[0];
                    break;
                case 'this_quarter':
                    const quarterStart = Math.floor(currentMonth / 3) * 3;
                    this.filters.start_date = new Date(currentYear, quarterStart, 1).toISOString().split('T')[0];
                    this.filters.end_date = new Date(currentYear, quarterStart + 3, 0).toISOString().split('T')[0];
                    break;
                case 'this_year':
                    this.filters.start_date = new Date(currentYear, 0, 1).toISOString().split('T')[0];
                    this.filters.end_date = new Date(currentYear, 11, 31).toISOString().split('T')[0];
                    break;
            }

            if (this.filters.period !== 'custom') {
                this.loadReports();
            }
        },

        async loadReports() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    start_date: this.filters.start_date,
                    end_date: this.filters.end_date,
                    report_type: this.filters.report_type
                });

                const response = await fetch(`/api/financial/reports?${params}`);
                const data = await response.json();
                
                this.reportSummary = data.summary || {};
                this.reportMetrics = data.metrics || {};
                this.profitLossData = data.profit_loss || {};
                this.balanceSheetData = data.balance_sheet || {};
                this.cashFlowData = data.cash_flow || {};
                this.budgetVarianceData = data.budget_variance || [];
            } catch (error) {
                console.error('Error loading reports:', error);
                this.loadDemoData();
            } finally {
                this.loading = false;
            }
        },

        loadDemoData() {
            this.reportSummary = {
                net_profit: 37500000,
                total_assets: 250000000,
                net_cash_flow: 15000000,
                budget_variance: -2500000
            };

            this.reportMetrics = {
                gross_profit_margin: 65.5,
                operating_margin: 22.3,
                current_ratio: 2.1,
                debt_to_equity: 0.8
            };

            this.profitLossData = {
                revenue: [
                    { category: 'Product Sales', amount: 85000000 },
                    { category: 'Service Revenue', amount: 25000000 },
                    { category: 'Other Income', amount: 5000000 }
                ],
                expenses: [
                    { category: 'Cost of Goods Sold', amount: 40000000 },
                    { category: 'Operating Expenses', amount: 25000000 },
                    { category: 'Marketing', amount: 8000000 },
                    { category: 'Administrative', amount: 9500000 }
                ],
                total_revenue: 115000000,
                total_expenses: 82500000,
                net_profit: 32500000
            };

            this.balanceSheetData = {
                current_assets: [
                    { category: 'Cash & Cash Equivalents', amount: 45000000 },
                    { category: 'Accounts Receivable', amount: 35000000 },
                    { category: 'Inventory', amount: 25000000 },
                    { category: 'Prepaid Expenses', amount: 5000000 }
                ],
                fixed_assets: [
                    { category: 'Property & Equipment', amount: 85000000 },
                    { category: 'Intangible Assets', amount: 15000000 },
                    { category: 'Investments', amount: 35000000 }
                ],
                current_liabilities: [
                    { category: 'Accounts Payable', amount: 20000000 },
                    { category: 'Short-term Debt', amount: 15000000 },
                    { category: 'Accrued Expenses', amount: 10000000 }
                ],
                long_term_liabilities: [
                    { category: 'Long-term Debt', amount: 65000000 },
                    { category: 'Deferred Tax', amount: 8000000 }
                ],
                equity: [
                    { category: 'Share Capital', amount: 100000000 },
                    { category: 'Retained Earnings', amount: 67000000 }
                ],
                total_current_assets: 110000000,
                total_fixed_assets: 135000000,
                total_assets: 245000000,
                total_current_liabilities: 45000000,
                total_long_term_liabilities: 73000000,
                total_equity: 167000000,
                total_liabilities_equity: 245000000
            };

            this.cashFlowData = {
                operating: [
                    { category: 'Net Income', amount: 32500000 },
                    { category: 'Depreciation', amount: 8000000 },
                    { category: 'Changes in Working Capital', amount: -5000000 }
                ],
                investing: [
                    { category: 'Equipment Purchase', amount: -15000000 },
                    { category: 'Investment Income', amount: 3000000 }
                ],
                financing: [
                    { category: 'Loan Proceeds', amount: 20000000 },
                    { category: 'Dividend Payments', amount: -8000000 }
                ],
                net_operating: 35500000,
                net_investing: -12000000,
                net_financing: 12000000,
                net_cash_flow: 35500000,
                beginning_cash: 25000000,
                ending_cash: 60500000
            };

            this.budgetVarianceData = [
                { category: 'Marketing', budget: 10000000, actual: 8500000, variance: 1500000, variance_percentage: 15.0 },
                { category: 'Operations', budget: 15000000, actual: 17500000, variance: -2500000, variance_percentage: -16.7 },
                { category: 'Equipment', budget: 8000000, actual: 6500000, variance: 1500000, variance_percentage: 18.8 }
            ];
        },

        async loadRecentReports() {
            try {
                const response = await fetch('/api/financial/reports/recent');
                const data = await response.json();
                this.recentReports = data.data || [];
            } catch (error) {
                console.error('Error loading recent reports:', error);
                this.recentReports = [
                    { id: 1, name: 'Monthly P&L - December', date: '2024-01-05' },
                    { id: 2, name: 'Balance Sheet - Q4', date: '2024-01-03' },
                    { id: 3, name: 'Cash Flow - December', date: '2024-01-02' }
                ];
            }
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },

        getFormattedPeriod() {
            const start = new Date(this.filters.start_date).toLocaleDateString('id-ID');
            const end = new Date(this.filters.end_date).toLocaleDateString('id-ID');
            return `${start} - ${end}`;
        },

        getBudgetVarianceStatus(percentage) {
            if (percentage >= 10) return 'Di Bawah Anggaran';
            if (percentage >= -10) return 'Sesuai Target';
            return 'Melebihi Anggaran';
        },

        getBudgetVarianceStatusClass(percentage) {
            if (percentage >= 10) return 'bg-green-100 text-green-800';
            if (percentage >= -10) return 'bg-yellow-100 text-yellow-800';
            return 'bg-red-100 text-red-800';
        },

        async exportReport(reportType) {
            try {
                const params = new URLSearchParams({
                    type: reportType,
                    start_date: this.filters.start_date,
                    end_date: this.filters.end_date,
                    format: 'xlsx'
                });

                const response = await fetch(`/api/financial/reports/export?${params}`);
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `${reportType}_${this.filters.start_date}_${this.filters.end_date}.xlsx`;
                a.click();
            } catch (error) {
                console.error('Error exporting report:', error);
            }
        },

        async exportAllReports() {
            try {
                const params = new URLSearchParams({
                    start_date: this.filters.start_date,
                    end_date: this.filters.end_date,
                    format: 'xlsx'
                });

                const response = await fetch(`/api/financial/reports/export-all?${params}`);
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `financial_reports_${this.filters.start_date}_${this.filters.end_date}.xlsx`;
                a.click();
            } catch (error) {
                console.error('Error exporting all reports:', error);
            }
        },

        printReport() {
            window.print();
        },

        emailReport() {
            // Implementation for email functionality
            console.log('Email report');
        },

        saveTemplate() {
            // Implementation for save template functionality
            console.log('Save template');
        },

        compareReports() {
            // Implementation for compare reports functionality
            console.log('Compare reports');
        },

        scheduleReport() {
            // Implementation for schedule report functionality
            console.log('Schedule report');
        },

        loadReport(report) {
            // Implementation for loading specific report
            console.log('Load report:', report);
        }
    }
}
</script>
@endsection