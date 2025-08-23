@extends('layouts.dashboard')

@section('title', 'Arus Kas Perusahaan')
@section('page-title')
<span class="text-base lg:text-2xl">Arus Kas Perusahaan</span>
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
<div x-data="journalManager()" class="space-y-4 lg:space-y-6 p-4 lg:p-0">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Arus Kas Perusahaan</h2>
            <p class="text-sm text-muted">Pantau kondisi keuangan perusahaan secara real-time</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="exportCashflow()" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor Laporan
            </button>
            
            <button @click="showTransactionForm = true" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Catat Transaksi
            </button>
        </div>
    </div>

    <!-- Cashflow Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Cash Inflow -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">💰 Kas Masuk</p>
                    <p class="text-2xl lg:text-3xl font-bold text-green-600" x-text="formatCurrency(summary.cash_inflow)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span :class="summary.inflow_change >= 0 ? 'text-green-600' : 'text-red-600'"
                              x-text="(summary.inflow_change >= 0 ? '+' : '') + summary.inflow_change + '%'"></span>
                        vs bulan lalu
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
                    <p class="text-sm font-medium text-muted">💸 Kas Keluar</p>
                    <p class="text-2xl lg:text-3xl font-bold text-red-600" x-text="formatCurrency(summary.cash_outflow)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span :class="summary.outflow_change >= 0 ? 'text-red-600' : 'text-green-600'"
                              x-text="(summary.outflow_change >= 0 ? '+' : '') + summary.outflow_change + '%'"></span>
                        vs bulan lalu
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Net Cashflow -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">📈 Saldo Bersih</p>
                    <p class="text-2xl lg:text-3xl font-bold"
                       :class="summary.net_cashflow >= 0 ? 'text-green-600' : 'text-red-600'"
                       x-text="formatCurrency(summary.net_cashflow)"></p>
                    <p class="text-xs font-medium mt-1"
                       :class="summary.net_cashflow >= 0 ? 'text-green-600' : 'text-red-600'"
                       x-text="summary.net_cashflow >= 0 ? 'SURPLUS' : 'DEFICIT'"></p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                     :class="summary.net_cashflow >= 0 ? 'bg-green-100' : 'bg-red-100'">
                    <svg class="w-6 h-6"
                         :class="summary.net_cashflow >= 0 ? 'text-green-600' : 'text-red-600'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              :d="summary.net_cashflow >= 0 ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z'"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Current Cash Balance -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">💳 Saldo Saat Ini</p>
                    <p class="text-2xl lg:text-3xl font-bold text-primary" x-text="formatCurrency(summary.current_balance)"></p>
                    <p class="text-xs font-medium mt-1"
                       :class="summary.health_status === 'safe' ? 'text-green-600' : summary.health_status === 'caution' ? 'text-yellow-600' : 'text-red-600'"
                       x-text="summary.health_status === 'safe' ? '🟢 POSISI AMAN' : summary.health_status === 'caution' ? '🟡 HATI-HATI' : '🔴 BAHAYA'"></p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Alerts -->
    <div x-show="alerts.length > 0" class="card p-4 lg:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-foreground">🔔 Peringatan & Notifikasi</h3>
            <button @click="dismissAllAlerts()" class="text-sm text-muted hover:text-foreground">Tutup Semua</button>
        </div>
        <div class="space-y-3">
            <template x-for="alert in alerts" :key="alert.id">
                <div class="flex items-start p-3 rounded-lg border"
                     :class="alert.level === 'warning' ? 'bg-yellow-50 border-yellow-200' : alert.level === 'critical' ? 'bg-red-50 border-red-200' : 'bg-blue-50 border-blue-200'">
                    <div class="flex-shrink-0 mr-3">
                        <span x-text="alert.level === 'warning' ? '⚠️' : alert.level === 'critical' ? '🔴' : '💡'" class="text-lg"></span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium"
                           :class="alert.level === 'warning' ? 'text-yellow-800' : alert.level === 'critical' ? 'text-red-800' : 'text-blue-800'"
                           x-text="alert.title"></p>
                        <p class="text-xs mt-1"
                           :class="alert.level === 'warning' ? 'text-yellow-700' : alert.level === 'critical' ? 'text-red-700' : 'text-blue-700'"
                           x-text="alert.message"></p>
                    </div>
                    <button @click="dismissAlert(alert.id)" class="text-muted hover:text-foreground ml-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </template>
        </div>
    </div>

    <!-- Cashflow Transactions -->
    <div class="card">
        <div class="p-4 lg:p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-foreground">📋 Transaksi Arus Kas Terbaru</h3>
                <div class="flex items-center space-x-2">
                    <select x-model="filterPeriod" @change="loadTransactions()" class="input py-1 text-sm">
                        <option value="today">Hari Ini</option>
                        <option value="week">Minggu Ini</option>
                        <option value="month">Bulan Ini</option>
                        <option value="all">Semua</option>
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
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Saldo Berjalan</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="transaction in cashflowTransactions" :key="transaction.id">
                        <tr class="hover:bg-surface/50">
                            <td>
                                <div class="text-sm text-foreground" x-text="formatDate(transaction.date)"></div>
                                <div class="text-xs text-muted" x-text="transaction.reference"></div>
                            </td>
                            <td>
                                <div class="font-medium text-foreground" x-text="transaction.description"></div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                      x-text="transaction.category_name">
                                </span>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="transaction.type === 'inflow' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                    <span x-text="transaction.type === 'inflow' ? '💰' : '💸'" class="mr-1"></span>
                                    <span x-text="transaction.type === 'inflow' ? 'Masuk' : 'Keluar'"></span>
                                </span>
                            </td>
                            <td>
                                <span class="font-medium text-lg"
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
            <template x-for="transaction in cashflowTransactions" :key="transaction.id">
                <div class="card rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span x-text="transaction.type === 'inflow' ? '💰' : '💸'" class="text-lg"></span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                  :class="transaction.type === 'inflow' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                  x-text="transaction.type === 'inflow' ? 'Masuk' : 'Keluar'">
                            </span>
                        </div>
                        <span class="font-semibold text-lg"
                              :class="transaction.type === 'inflow' ? 'text-green-600' : 'text-red-600'"
                              x-text="(transaction.type === 'inflow' ? '+' : '-') + formatCurrency(transaction.amount)">
                        </span>
                    </div>
                    
                    <div class="space-y-2">
                        <h3 class="font-medium text-foreground" x-text="transaction.description"></h3>
                        <div class="grid grid-cols-2 gap-4 pt-3 border-t border-border">
                            <div>
                                <span class="text-xs font-medium text-muted uppercase tracking-wide">Kategori</span>
                                <p class="text-sm text-foreground mt-1" x-text="transaction.category_name"></p>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-muted uppercase tracking-wide">Tanggal</span>
                                <p class="text-sm text-foreground mt-1" x-text="formatDate(transaction.date)"></p>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-muted uppercase tracking-wide">Referensi</span>
                                <p class="text-sm text-foreground mt-1" x-text="transaction.reference || '-'"></p>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-muted uppercase tracking-wide">Saldo</span>
                                <p class="text-sm font-medium text-foreground mt-1" x-text="formatCurrency(transaction.running_balance)"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="cashflowTransactions.length === 0" class="text-center py-12">
            <div class="text-6xl mb-4">💰</div>
            <h3 class="text-lg font-medium text-foreground">Belum ada transaksi arus kas</h3>
            <p class="mt-2 text-sm text-muted">Mulai catat transaksi pertama Anda untuk memantau kondisi keuangan perusahaan.</p>
            <div class="mt-6">
                <button @click="showTransactionForm = true" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Catat Transaksi Pertama
                </button>
            </div>
        </div>
    </div>

    <!-- Transaction Form Modal -->
    <div x-show="showTransactionForm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div x-show="showTransactionForm"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             @click.away="closeTransactionForm()"
             class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-foreground">➕ Catat Transaksi Baru</h3>
                    <button @click="closeTransactionForm()" class="text-muted hover:text-foreground">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="saveTransaction()">
                    <!-- Transaction Type -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-foreground mb-2">Tipe Transaksi</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" x-model="transactionForm.type" value="inflow" class="mr-2">
                                <span class="text-sm">💰 Kas Masuk</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" x-model="transactionForm.type" value="outflow" class="mr-2">
                                <span class="text-sm">💸 Kas Keluar</span>
                            </label>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-foreground mb-2">Kategori</label>
                        <select x-model="transactionForm.category_id" class="input w-full" required>
                            <option value="">Pilih kategori...</option>
                            <template x-for="category in getAvailableCategories()" :key="category.id">
                                <option :value="category.id" x-text="category.icon + ' ' + category.name"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-foreground mb-2">Jumlah</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted">Rp</span>
                            <input type="number" x-model="transactionForm.amount" class="input w-full pl-10"
                                   placeholder="0" min="0" step="1000" required>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-foreground mb-2">Deskripsi</label>
                        <textarea x-model="transactionForm.description" class="input w-full" rows="3"
                                  placeholder="Contoh: Penjualan Minuman Temulawak ke Toko ABC" required></textarea>
                    </div>

                    <!-- Date -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-foreground mb-2">Tanggal</label>
                        <input type="date" x-model="transactionForm.date" class="input w-full" required>
                    </div>

                    <!-- Reference -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-foreground mb-2">Referensi (Opsional)</label>
                        <input type="text" x-model="transactionForm.reference" class="input w-full"
                               placeholder="Contoh: INV-2025-001">
                    </div>

                    <!-- Impact Preview -->
                    <div x-show="transactionForm.amount > 0" class="mb-6 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center mb-2">
                            <span class="text-blue-600 mr-2">💡</span>
                            <span class="text-sm font-medium text-blue-800">Dampak Transaksi</span>
                        </div>
                        <p class="text-sm text-blue-700">
                            <span x-text="transactionForm.type === 'inflow' ? 'Saldo kas akan bertambah' : 'Saldo kas akan berkurang'"></span>
                            <span x-text="formatCurrency(transactionForm.amount)"></span>
                        </p>
                        <p class="text-sm text-blue-700 mt-1">
                            Saldo setelah transaksi:
                            <span class="font-medium" x-text="formatCurrency(calculateNewBalance())"></span>
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3">
                        <button type="button" @click="closeTransactionForm()" class="btn-secondary flex-1">
                            Batal
                        </button>
                        <button type="submit" class="btn-primary flex-1" :disabled="!isFormValid()">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/cashflow-manager.js') }}"></script>
@endsection