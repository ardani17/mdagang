@extends('layouts.dashboard')

@section('title', 'Manajemen Arus Kas')
@section('page-title')
<span class="text-base lg:text-2xl">Manajemen Arus Kas</span>
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
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Arus Kas</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="cashFlowManager()" class="space-y-4 lg:space-y-6 p-4 lg:p-0">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Manajemen Arus Kas</h2>
            <p class="text-sm text-muted">Pantau dan analisis pola arus kas Anda</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="exportCashFlow()" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor
            </button>
            
            <button @click="generateForecast()" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                Buat Proyeksi
            </button>
        </div>
    </div>

    <!-- Cash Flow Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Current Cash Balance -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Saldo Saat Ini</p>
                    <p class="text-2xl lg:text-3xl font-bold text-primary" x-text="formatCurrency(summary.current_balance)"></p>
                    <p class="text-xs text-muted mt-1">
                        Per hari ini
                    </p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Cash Inflow -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Arus Kas Masuk</p>
                    <p class="text-2xl lg:text-3xl font-bold text-green-600" x-text="formatCurrency(summary.cash_inflow)"></p>
                    <p class="text-xs text-muted mt-1">
                        Bulan ini
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Cash Outflow -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Arus Kas Keluar</p>
                    <p class="text-2xl lg:text-3xl font-bold text-red-600" x-text="formatCurrency(summary.cash_outflow)"></p>
                    <p class="text-xs text-muted mt-1">
                        Bulan ini
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Net Cash Flow -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Arus Kas Bersih</p>
                    <p class="text-2xl lg:text-3xl font-bold" 
                       :class="summary.net_cash_flow >= 0 ? 'text-green-600' : 'text-red-600'"
                       x-text="formatCurrency(summary.net_cash_flow)"></p>
                    <p class="text-xs text-muted mt-1">
                        Bulan ini
                    </p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                     :class="summary.net_cash_flow >= 0 ? 'bg-green-100' : 'bg-red-100'">
                    <svg class="w-6 h-6" 
                         :class="summary.net_cash_flow >= 0 ? 'text-green-600' : 'text-red-600'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash Flow Chart -->
    <div class="card p-4 lg:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-foreground">Tren Arus Kas</h3>
            <div class="flex items-center space-x-2">
                <select x-model="chartPeriod" @change="updateChart()" class="input py-1 text-sm">
                    <option value="6months">6 Bulan Terakhir</option>
                    <option value="12months">12 Bulan Terakhir</option>
                    <option value="24months">24 Bulan Terakhir</option>
                </select>
            </div>
        </div>
        <div class="relative h-80">
            <canvas id="cashFlowChart"></canvas>
        </div>
    </div>

    <!-- Cash Flow Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Cash Flow by Category -->
        <div class="card p-4 lg:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Arus Kas per Kategori</h3>
            <div class="space-y-4">
                <template x-for="category in cashFlowByCategory" :key="category.name">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 rounded-full" :style="`background-color: ${category.color}`"></div>
                            <span class="text-sm font-medium text-foreground" x-text="category.name"></span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-medium" 
                                  :class="category.amount >= 0 ? 'text-green-600' : 'text-red-600'"
                                  x-text="formatCurrency(category.amount)"></span>
                            <p class="text-xs text-muted" x-text="`${category.percentage}%`"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Cash Flow Forecast -->
        <div class="card p-4 lg:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Proyeksi 3 Bulan</h3>
            <div class="space-y-4">
                <template x-for="forecast in cashFlowForecast" :key="forecast.month">
                    <div class="border border-border rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-foreground" x-text="forecast.month"></span>
                            <span class="text-sm font-medium" 
                                  :class="forecast.projected_balance >= 0 ? 'text-green-600' : 'text-red-600'"
                                  x-text="formatCurrency(forecast.projected_balance)"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-xs text-muted">
                            <div>
                                <span>Masuk: </span>
                                <span class="text-green-600" x-text="formatCurrency(forecast.projected_inflow)"></span>
                            </div>
                            <div>
                                <span>Keluar: </span>
                                <span class="text-red-600" x-text="formatCurrency(forecast.projected_outflow)"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Recent Cash Flow Transactions -->
    <div class="card">
        <div class="p-4 lg:p-6 border-b border-border">
            <h3 class="text-lg font-semibold text-foreground">Transaksi Arus Kas Terbaru</h3>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Saldo Berjalan</th>
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
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                      x-text="transaction.category">
                                </span>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="transaction.type === 'inflow' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                      x-text="transaction.type === 'inflow' ? 'Masuk' : 'Keluar'">
                                </span>
                            </td>
                            <td>
                                <span class="font-medium"
                                      :class="transaction.type === 'inflow' ? 'text-green-600' : 'text-red-600'"
                                      x-text="(transaction.type === 'inflow' ? '+' : '-') + formatCurrency(transaction.amount)">
                                </span>
                            </td>
                            <td>
                                <span class="font-medium text-foreground" x-text="formatCurrency(transaction.running_balance)"></span>
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
                        <h3 class="font-semibold text-foreground text-base leading-tight" x-text="transaction.description"></h3>
                        <span class="font-semibold text-base"
                              :class="transaction.type === 'inflow' ? 'text-green-600' : 'text-red-600'"
                              x-text="(transaction.type === 'inflow' ? '+' : '-') + formatCurrency(transaction.amount)">
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 pt-3 border-t border-border">
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Kategori</span>
                            <p class="text-sm text-foreground mt-1" x-text="transaction.category"></p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Tipe</span>
                            <p class="text-sm text-foreground mt-1" x-text="transaction.type === 'inflow' ? 'Masuk' : 'Keluar'"></p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Tanggal</span>
                            <p class="text-sm text-foreground mt-1" x-text="formatDate(transaction.date)"></p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Saldo</span>
                            <p class="text-sm font-medium text-foreground mt-1" x-text="formatCurrency(transaction.running_balance)"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="recentTransactions.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-foreground">Tidak ada data arus kas</h3>
            <p class="mt-1 text-sm text-muted">Transaksi arus kas akan muncul di sini setelah Anda memiliki aktivitas keuangan.</p>
        </div>
    </div>
</div>

<script>
function cashFlowManager() {
    return {
        loading: false,
        chartPeriod: '6months',
        summary: {
            current_balance: 0,
            cash_inflow: 0,
            cash_outflow: 0,
            net_cash_flow: 0
        },
        cashFlowByCategory: [],
        cashFlowForecast: [],
        recentTransactions: [],
        cashFlowChart: null,

        init() {
            this.loadSummary();
            this.loadCashFlowData();
            this.loadRecentTransactions();
            this.initChart();
        },

        async loadSummary() {
            try {
                const response = await fetch('/api/financial/cashflow/summary');
                const data = await response.json();
                this.summary = data;
            } catch (error) {
                console.error('Error loading summary:', error);
                this.summary = {
                    current_balance: 75000000,
                    cash_inflow: 125000000,
                    cash_outflow: 87500000,
                    net_cash_flow: 37500000
                };
            }
        },

        async loadCashFlowData() {
            try {
                const response = await fetch('/api/financial/cashflow/analysis');
                const data = await response.json();
                this.cashFlowByCategory = data.by_category || [];
                this.cashFlowForecast = data.forecast || [];
            } catch (error) {
                console.error('Error loading cash flow data:', error);
                this.loadDemoData();
            }
        },

        loadDemoData() {
            this.cashFlowByCategory = [
                { name: 'Pendapatan Penjualan', amount: 85000000, percentage: 68, color: '#10B981' },
                { name: 'Biaya Operasional', amount: -45000000, percentage: -36, color: '#EF4444' },
                { name: 'Pemasaran', amount: -8000000, percentage: -6, color: '#F59E0B' },
                { name: 'Pendapatan Lain', amount: 5000000, percentage: 4, color: '#3B82F6' }
            ];

            this.cashFlowForecast = [
                {
                    month: 'Februari 2024',
                    projected_inflow: 90000000,
                    projected_outflow: 65000000,
                    projected_balance: 100000000
                },
                {
                    month: 'Maret 2024',
                    projected_inflow: 95000000,
                    projected_outflow: 70000000,
                    projected_balance: 125000000
                },
                {
                    month: 'April 2024',
                    projected_inflow: 88000000,
                    projected_outflow: 68000000,
                    projected_balance: 145000000
                }
            ];
        },

        async loadRecentTransactions() {
            try {
                const response = await fetch('/api/financial/cashflow/transactions');
                const data = await response.json();
                this.recentTransactions = data.data || [];
            } catch (error) {
                console.error('Error loading transactions:', error);
                this.recentTransactions = [
                    {
                        id: 1,
                        date: '2024-01-15',
                        description: 'Pembayaran Penjualan Produk',
                        category: 'Penjualan',
                        type: 'inflow',
                        amount: 15000000,
                        running_balance: 75000000
                    },
                    {
                        id: 2,
                        date: '2024-01-14',
                        description: 'Pembayaran Sewa Kantor',
                        category: 'Operasional',
                        type: 'outflow',
                        amount: 5000000,
                        running_balance: 60000000
                    }
                ];
            }
        },

        initChart() {
            this.$nextTick(() => {
                const ctx = document.getElementById('cashFlowChart');
                if (!ctx) return;

                this.cashFlowChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [
                            {
                                label: 'Arus Kas Masuk',
                                data: [85000000, 92000000, 88000000, 95000000, 90000000, 125000000],
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: false
                            },
                            {
                                label: 'Arus Kas Keluar',
                                data: [65000000, 70000000, 68000000, 72000000, 75000000, 87500000],
                                borderColor: '#EF4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.4,
                                fill: false
                            },
                            {
                                label: 'Arus Kas Bersih',
                                data: [20000000, 22000000, 20000000, 23000000, 15000000, 37500000],
                                borderColor: '#3B82F6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                fill: true
                            }
                        ]
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
            });
        },

        updateChart() {
            // Update chart based on selected period
            console.log('Updating chart for period:', this.chartPeriod);
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

        async exportCashFlow() {
            try {
                const response = await fetch('/api/financial/cashflow/export');
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'cashflow_analysis.xlsx';
                a.click();
            } catch (error) {
                console.error('Error exporting cash flow:', error);
            }
        },

        generateForecast() {
            console.log('Generating cash flow forecast');
        }
    }
}
</script>
@endsection