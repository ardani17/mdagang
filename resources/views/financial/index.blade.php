@extends('layouts.dashboard')

@section('title', 'Manajemen Keuangan')
@section('page-title')
<span class="text-base lg:text-2xl">Manajemen Keuangan</span>
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
@endsection

@section('content')
<div x-data="financialManager()" class="space-y-4 lg:space-y-6 p-4 lg:p-0">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Laporan Keuangan</h2>
            <p class="text-sm text-muted">Pantau kesehatan keuangan bisnis Anda</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="exportReport()" 
                    class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor Laporan
            </button>
            <a href="/financial/transactions/create" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Transaksi Baru
            </a>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Total Pendapatan -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Total Pendapatan</p>
                    <p class="text-2xl lg:text-3xl font-bold text-green-600" x-text="formatCurrency(summary.total_income)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-green-600">+12.5%</span> dari bulan lalu
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Pengeluaran -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Total Pengeluaran</p>
                    <p class="text-2xl lg:text-3xl font-bold text-red-600" x-text="formatCurrency(summary.total_expense)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-red-600">+5.2%</span> dari bulan lalu
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Laba Bersih -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Laba Bersih</p>
                    <p class="text-2xl lg:text-3xl font-bold text-primary" x-text="formatCurrency(summary.net_profit)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-green-600">+18.3%</span> dari bulan lalu
                    </p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Saldo Kas -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Saldo Kas</p>
                    <p class="text-2xl lg:text-3xl font-bold text-blue-600" x-text="formatCurrency(summary.cash_balance)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-blue-600">Tersedia</span>
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

    <!-- Charts and Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <!-- Income vs Expense Chart -->
        <div class="card">
            <div class="p-4 lg:p-6 border-b border-border">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-foreground">Pendapatan vs Pengeluaran</h3>
                    <select x-model="chartPeriod" @change="updateCharts()" class="input py-1 text-sm">
                        <option value="7">7 Hari</option>
                        <option value="30">30 Hari</option>
                        <option value="90">90 Hari</option>
                    </select>
                </div>
            </div>
            <div class="p-4 lg:p-6">
                <canvas id="incomeExpenseChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Cash Flow Chart -->
        <div class="card">
            <div class="p-4 lg:p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-foreground">Arus Kas</h3>
            </div>
            <div class="p-4 lg:p-6">
                <canvas id="cashFlowChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card">
        <div class="p-4 lg:p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-foreground">Transaksi Terbaru</h3>
                <a href="/financial/transactions" class="text-sm text-primary hover:text-primary/80">Lihat Semua</a>
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
                        <th>Jenis</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="transaction in recentTransactions" :key="transaction.id">
                        <tr class="hover:bg-surface/50">
                            <td>
                                <div class="text-sm text-foreground" x-text="formatDate(transaction.date)"></div>
                                <div class="text-xs text-muted" x-text="formatTime(transaction.date)"></div>
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
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="transaction.type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                      x-text="transaction.type === 'income' ? 'Pemasukan' : 'Pengeluaran'">
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
                    <!-- Header with type and amount -->
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

                    <!-- Transaction Info -->
                    <div class="space-y-2">
                        <h3 class="font-medium text-foreground" x-text="transaction.description"></h3>
                        <p class="text-sm text-muted" x-text="transaction.reference"></p>
                        
                        <!-- Transaction Details -->
                        <div class="grid grid-cols-2 gap-4 pt-3 border-t border-border">
                            <div>
                                <span class="text-xs font-medium text-muted uppercase tracking-wide">Kategori</span>
                                <p class="text-sm text-foreground mt-1" x-text="transaction.category"></p>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-muted uppercase tracking-wide">Tanggal</span>
                                <p class="text-sm text-foreground mt-1" x-text="formatDate(transaction.date)"></p>
                                <p class="text-xs text-muted" x-text="formatTime(transaction.date)"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function financialManager() {
    return {
        chartPeriod: '30',
        summary: {
            total_income: 15750000,
            total_expense: 8250000,
            net_profit: 7500000,
            cash_balance: 12300000
        },
        recentTransactions: [
            {
                id: 1,
                date: '2024-01-15T14:30:00Z',
                description: 'Penjualan Nasi Goreng',
                reference: 'ORD-2024-001',
                category: 'Penjualan',
                type: 'income',
                amount: 125000
            },
            {
                id: 2,
                date: '2024-01-15T10:15:00Z',
                description: 'Pembelian Bahan Baku',
                reference: 'PUR-2024-005',
                category: 'Bahan Baku',
                type: 'expense',
                amount: 350000
            },
            {
                id: 3,
                date: '2024-01-14T16:45:00Z',
                description: 'Pembayaran Listrik',
                reference: 'UTIL-2024-001',
                category: 'Utilitas',
                type: 'expense',
                amount: 450000
            },
            {
                id: 4,
                date: '2024-01-14T12:20:00Z',
                description: 'Penjualan Catering',
                reference: 'ORD-2024-002',
                category: 'Penjualan',
                type: 'income',
                amount: 750000
            },
            {
                id: 5,
                date: '2024-01-13T09:30:00Z',
                description: 'Gaji Karyawan',
                reference: 'SAL-2024-001',
                category: 'Gaji',
                type: 'expense',
                amount: 2500000
            }
        ],
        incomeExpenseChart: null,
        cashFlowChart: null,

        async init() {
            await this.$nextTick();
            this.initCharts();
        },

        initCharts() {
            this.initIncomeExpenseChart();
            this.initCashFlowChart();
        },

        initIncomeExpenseChart() {
            const ctx = document.getElementById('incomeExpenseChart');
            if (!ctx) return;

            this.incomeExpenseChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan 1', 'Jan 8', 'Jan 15', 'Jan 22', 'Jan 29'],
                    datasets: [{
                        label: 'Pendapatan',
                        data: [3200000, 4100000, 3800000, 4500000, 4200000],
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Pengeluaran',
                        data: [1800000, 2200000, 1900000, 2400000, 2100000],
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4
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
                    labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                    datasets: [{
                        label: 'Arus Kas',
                        data: [1400000, 1900000, 1600000, 2100000],
                        backgroundColor: 'rgba(255, 193, 7, 0.8)',
                        borderColor: 'rgb(255, 193, 7)',
                        borderWidth: 1
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

        updateCharts() {
            // Update chart data based on selected period
            // This would typically fetch new data from API
        },

        async exportReport() {
            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: 'Laporan keuangan berhasil diekspor.'
                });
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal mengekspor laporan'
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