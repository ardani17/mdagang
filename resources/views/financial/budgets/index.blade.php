@extends('layouts.dashboard')

@section('title', 'Manajemen Anggaran')
@section('page-title')
<span class="text-base lg:text-2xl">Manajemen Anggaran</span>
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
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Anggaran</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="budgetManager()" class="space-y-4 lg:space-y-6 p-4 lg:p-0">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Manajemen Anggaran</h2>
            <p class="text-sm text-muted">Rencanakan dan lacak anggaran keuangan Anda</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="exportBudgets()" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor
            </button>
            
            <a href="{{ route('financial.budgets.create') }}" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Anggaran Baru
            </a>
        </div>
    </div>

    <!-- Budget Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Total Budget -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Total Anggaran</p>
                    <p class="text-2xl lg:text-3xl font-bold text-primary" x-text="formatCurrency(overview.total_budget)"></p>
                    <p class="text-xs text-muted mt-1">
                        Periode saat ini
                    </p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Spent Amount -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Total Terpakai</p>
                    <p class="text-2xl lg:text-3xl font-bold text-red-600" x-text="formatCurrency(overview.total_spent)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span x-text="overview.spent_percentage"></span>% dari anggaran
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Remaining Budget -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Sisa</p>
                    <p class="text-2xl lg:text-3xl font-bold text-green-600" x-text="formatCurrency(overview.remaining_budget)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span x-text="overview.remaining_percentage"></span>% tersedia
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Budget Alerts -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Peringatan</p>
                    <p class="text-2xl lg:text-3xl font-bold text-yellow-600" x-text="overview.alert_count"></p>
                    <p class="text-xs text-muted mt-1">
                        Peringatan anggaran
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Period Selector -->
    <div class="card p-4 lg:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-foreground">Periode Anggaran</h3>
                <p class="text-sm text-muted">Pilih periode untuk melihat anggaran</p>
            </div>
            <div class="flex items-center space-x-3">
                <select x-model="selectedPeriod" 
                        @change="loadBudgets()"
                        class="input">
                    <option value="current">Bulan Ini</option>
                    <option value="next">Bulan Depan</option>
                    <option value="quarter">Kuartal Ini</option>
                    <option value="year">Tahun Ini</option>
                    <option value="custom">Periode Kustom</option>
                </select>
                
                <div x-show="selectedPeriod === 'custom'" class="flex items-center space-x-2">
                    <input type="date" 
                           x-model="customPeriod.start"
                           @change="loadBudgets()"
                           class="input">
                    <span class="text-muted">sampai</span>
                    <input type="date" 
                           x-model="customPeriod.end"
                           @change="loadBudgets()"
                           class="input">
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Categories -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Budget List -->
        <div class="card">
            <div class="p-4 lg:p-6 border-b border-border">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-foreground">Kategori Anggaran</h3>
                    <div class="flex items-center space-x-2">
                        <button @click="showBudgetForm = true" class="btn-secondary text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tambah Kategori
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-4 lg:p-6 space-y-4">
                <template x-for="budget in budgets" :key="budget.id">
                    <div class="border border-border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h4 class="font-semibold text-foreground" x-text="budget.category"></h4>
                                <p class="text-sm text-muted" x-text="budget.description"></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button @click="editBudget(budget)" class="p-1 text-muted hover:text-foreground">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button @click="deleteBudget(budget)" class="p-1 text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Budget Progress -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-muted">Terpakai: <span x-text="formatCurrency(budget.spent)"></span></span>
                                <span class="text-muted">Anggaran: <span x-text="formatCurrency(budget.amount)"></span></span>
                            </div>
                            
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-300"
                                     :class="getProgressBarColor(budget.spent, budget.amount)"
                                     :style="`width: ${Math.min((budget.spent / budget.amount) * 100, 100)}%`">
                                </div>
                            </div>
                            
                            <div class="flex justify-between text-xs">
                                <span :class="getSpentTextColor(budget.spent, budget.amount)"
                                      x-text="`${Math.round((budget.spent / budget.amount) * 100)}% terpakai`">
                                </span>
                                <span class="text-muted"
                                      x-text="`${formatCurrency(budget.amount - budget.spent)} tersisa`">
                                </span>
                            </div>
                        </div>

                        <!-- Budget Status -->
                        <div class="mt-3 flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  :class="getBudgetStatusClass(budget.spent, budget.amount)"
                                  x-text="getBudgetStatus(budget.spent, budget.amount)">
                            </span>
                            
                            <div class="flex items-center space-x-2 text-xs text-muted">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h6m-6 0l-.5 8.5A2 2 0 0013.5 21h-3A2 2 0 019 19.5L8.5 7"/>
                                </svg>
                                <span x-text="`${budget.transaction_count} transaksi`"></span>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <div x-show="budgets.length === 0" class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-foreground">Tidak ada anggaran ditemukan</h3>
                    <p class="mt-1 text-sm text-muted">Mulai dengan membuat anggaran pertama Anda.</p>
                    <div class="mt-6">
                        <button @click="showBudgetForm = true" class="btn-primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Buat Anggaran
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget Analytics -->
        <div class="space-y-6">
            <!-- Budget vs Actual Chart -->
            <div class="card p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Anggaran vs Aktual</h3>
                <div class="relative h-64">
                    <canvas id="budgetChart"></canvas>
                </div>
            </div>

            <!-- Budget Trends -->
            <div class="card p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Tren Bulanan</h3>
                <div class="relative h-48">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Budget Alerts -->
            <div class="card p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Peringatan Anggaran</h3>
                <div class="space-y-3">
                    <template x-for="alert in budgetAlerts" :key="alert.id">
                        <div class="flex items-start space-x-3 p-3 rounded-lg"
                             :class="alert.type === 'warning' ? 'bg-yellow-50 border border-yellow-200' : 'bg-red-50 border border-red-200'">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5"
                                     :class="alert.type === 'warning' ? 'text-yellow-600' : 'text-red-600'"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium"
                                   :class="alert.type === 'warning' ? 'text-yellow-800' : 'text-red-800'"
                                   x-text="alert.title">
                                </p>
                                <p class="text-sm"
                                   :class="alert.type === 'warning' ? 'text-yellow-700' : 'text-red-700'"
                                   x-text="alert.message">
                                </p>
                            </div>
                            <button @click="dismissAlert(alert)" class="flex-shrink-0 p-1 rounded-full hover:bg-white/50">
                                <svg class="w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>

                    <div x-show="budgetAlerts.length === 0" class="text-center py-4">
                        <svg class="mx-auto h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-2 text-sm text-muted">Semua anggaran sesuai target!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Form Modal -->
    <div x-show="showBudgetForm" 
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
                <form @submit.prevent="saveBudget()">
                    <div class="bg-background px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-foreground mb-4">
                                    <span x-text="editingBudget ? 'Edit Anggaran' : 'Buat Anggaran'"></span>
                                </h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Kategori *</label>
                                        <select x-model="budgetForm.category" class="input" required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="Marketing">Pemasaran</option>
                                            <option value="Operations">Operasional</option>
                                            <option value="Equipment">Peralatan</option>
                                            <option value="Utilities">Utilitas</option>
                                            <option value="Salaries">Gaji</option>
                                            <option value="Travel">Perjalanan</option>
                                            <option value="Other">Lainnya</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Jumlah Anggaran *</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-muted text-sm">Rp</span>
                                            </div>
                                            <input type="number" 
                                                   x-model="budgetForm.amount"
                                                   class="input pl-12"
                                                   placeholder="0"
                                                   step="0.01"
                                                   min="0"
                                                   required>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Deskripsi</label>
                                        <textarea x-model="budgetForm.description"
                                                  rows="3"
                                                  class="input"
                                                  placeholder="Deskripsi anggaran"></textarea>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-foreground mb-2">Tanggal Mulai *</label>
                                            <input type="date" 
                                                   x-model="budgetForm.start_date"
                                                   class="input"
                                                   required>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-foreground mb-2">Tanggal Selesai *</label>
                                            <input type="date" 
                                                   x-model="budgetForm.end_date"
                                                   class="input"
                                                   required>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Batas Peringatan (%)</label>
                                        <input type="number" 
                                               x-model="budgetForm.alert_threshold"
                                               class="input"
                                               placeholder="80"
                                               min="0"
                                               max="100">
                                        <p class="text-xs text-muted mt-1">Dapatkan notifikasi ketika pengeluaran mencapai persentase ini</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-surface px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                :disabled="budgetFormLoading"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm"
                                :class="budgetFormLoading ? 'opacity-50 cursor-not-allowed' : ''">
                            <span x-text="budgetFormLoading ? 'Menyimpan...' : (editingBudget ? 'Perbarui' : 'Buat')"></span>
                        </button>
                        <button type="button" 
                                @click="closeBudgetForm()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-border shadow-sm px-4 py-2 bg-background text-base font-medium text-foreground hover:bg-surface focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function budgetManager() {
    return {
        loading: false,
        showBudgetForm: false,
        editingBudget: null,
        budgetFormLoading: false,
        selectedPeriod: 'current',
        customPeriod: {
            start: '',
            end: ''
        },
        budgets: [],
        budgetAlerts: [],
        overview: {
            total_budget: 0,
            total_spent: 0,
            remaining_budget: 0,
            spent_percentage: 
0,
            remaining_percentage: 0,
            alert_count: 0
        },
        budgetForm: {
            category: '',
            amount: '',
            description: '',
            start_date: '',
            end_date: '',
            alert_threshold: 80
        },
        budgetChart: null,
        trendChart: null,

        init() {
            this.loadBudgets();
            this.loadOverview();
            this.loadBudgetAlerts();
            this.initCharts();
        },

        async loadBudgets() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    period: this.selectedPeriod,
                    start_date: this.customPeriod.start,
                    end_date: this.customPeriod.end
                });

                const response = await fetch(`/api/financial/budgets?${params}`);
                const data = await response.json();
                
                this.budgets = data.data || [];
            } catch (error) {
                console.error('Error loading budgets:', error);
                this.budgets = this.getDemoBudgets();
            } finally {
                this.loading = false;
            }
        },

        async loadOverview() {
            try {
                const response = await fetch('/api/financial/budgets/overview');
                const data = await response.json();
                this.overview = data;
            } catch (error) {
                console.error('Error loading overview:', error);
                this.overview = {
                    total_budget: 50000000,
                    total_spent: 32500000,
                    remaining_budget: 17500000,
                    spent_percentage: 65,
                    remaining_percentage: 35,
                    alert_count: 2
                };
            }
        },

        async loadBudgetAlerts() {
            try {
                const response = await fetch('/api/financial/budgets/alerts');
                const data = await response.json();
                this.budgetAlerts = data.data || [];
            } catch (error) {
                console.error('Error loading alerts:', error);
                this.budgetAlerts = [
                    {
                        id: 1,
                        type: 'warning',
                        title: 'Peringatan Anggaran Pemasaran',
                        message: 'Anggaran pemasaran telah terpakai 85% dengan 10 hari tersisa'
                    },
                    {
                        id: 2,
                        type: 'danger',
                        title: 'Anggaran Operasional Terlampaui',
                        message: 'Anggaran operasional telah terlampaui sebesar 15%'
                    }
                ];
            }
        },

        getDemoBudgets() {
            return [
                {
                    id: 1,
                    category: 'Pemasaran',
                    description: 'Pemasaran digital dan periklanan',
                    amount: 10000000,
                    spent: 8500000,
                    transaction_count: 15
                },
                {
                    id: 2,
                    category: 'Operasional',
                    description: 'Biaya operasional',
                    amount: 15000000,
                    spent: 12000000,
                    transaction_count: 28
                },
                {
                    id: 3,
                    category: 'Peralatan',
                    description: 'Peralatan dan perlengkapan kantor',
                    amount: 8000000,
                    spent: 3500000,
                    transaction_count: 8
                }
            ];
        },

        initCharts() {
            this.$nextTick(() => {
                this.initBudgetChart();
                this.initTrendChart();
            });
        },

        initBudgetChart() {
            const ctx = document.getElementById('budgetChart');
            if (!ctx) return;

            this.budgetChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: this.budgets.map(b => b.category),
                    datasets: [
                        {
                            label: 'Anggaran',
                            data: this.budgets.map(b => b.amount),
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1
                        },
                        {
                            label: 'Terpakai',
                            data: this.budgets.map(b => b.spent),
                            backgroundColor: 'rgba(239, 68, 68, 0.5)',
                            borderColor: 'rgb(239, 68, 68)',
                            borderWidth: 1
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
        },

        initTrendChart() {
            const ctx = document.getElementById('trendChart');
            if (!ctx) return;

            this.trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [
                        {
                            label: 'Anggaran',
                            data: [45000000, 48000000, 50000000, 52000000, 50000000, 50000000],
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'Pengeluaran Aktual',
                            data: [42000000, 46000000, 48000000, 49000000, 47000000, 32500000],
                            borderColor: 'rgb(239, 68, 68)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.4
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
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },

        getProgressBarColor(spent, budget) {
            const percentage = (spent / budget) * 100;
            if (percentage >= 100) return 'bg-red-500';
            if (percentage >= 80) return 'bg-yellow-500';
            return 'bg-green-500';
        },

        getSpentTextColor(spent, budget) {
            const percentage = (spent / budget) * 100;
            if (percentage >= 100) return 'text-red-600';
            if (percentage >= 80) return 'text-yellow-600';
            return 'text-green-600';
        },

        getBudgetStatus(spent, budget) {
            const percentage = (spent / budget) * 100;
            if (percentage >= 100) return 'Melebihi Anggaran';
            if (percentage >= 80) return 'Peringatan';
            return 'Sesuai Target';
        },

        getBudgetStatusClass(spent, budget) {
            const percentage = (spent / budget) * 100;
            if (percentage >= 100) return 'bg-red-100 text-red-800';
            if (percentage >= 80) return 'bg-yellow-100 text-yellow-800';
            return 'bg-green-100 text-green-800';
        },

        editBudget(budget) {
            this.editingBudget = budget;
            this.budgetForm = {
                category: budget.category,
                amount: budget.amount,
                description: budget.description,
                start_date: budget.start_date,
                end_date: budget.end_date,
                alert_threshold: budget.alert_threshold || 80
            };
            this.showBudgetForm = true;
        },

        async deleteBudget(budget) {
            if (confirm('Apakah Anda yakin ingin menghapus anggaran ini?')) {
                try {
                    await fetch(`/api/financial/budgets/${budget.id}`, {
                        method: 'DELETE'
                    });
                    this.loadBudgets();
                    this.loadOverview();
                } catch (error) {
                    console.error('Error deleting budget:', error);
                }
            }
        },

        async saveBudget() {
            this.budgetFormLoading = true;
            
            try {
                const url = this.editingBudget 
                    ? `/api/financial/budgets/${this.editingBudget.id}`
                    : '/api/financial/budgets';
                
                const method = this.editingBudget ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.budgetForm)
                });

                if (response.ok) {
                    this.closeBudgetForm();
                    this.loadBudgets();
                    this.loadOverview();
                    this.showNotification('Anggaran berhasil disimpan!', 'success');
                } else {
                    const data = await response.json();
                    this.showNotification(data.message || 'Gagal menyimpan anggaran', 'error');
                }
            } catch (error) {
                console.error('Error saving budget:', error);
                this.showNotification('Kesalahan jaringan. Silakan coba lagi.', 'error');
            } finally {
                this.budgetFormLoading = false;
            }
        },

        closeBudgetForm() {
            this.showBudgetForm = false;
            this.editingBudget = null;
            this.budgetForm = {
                category: '',
                amount: '',
                description: '',
                start_date: '',
                end_date: '',
                alert_threshold: 80
            };
        },

        dismissAlert(alert) {
            const index = this.budgetAlerts.findIndex(a => a.id === alert.id);
            if (index > -1) {
                this.budgetAlerts.splice(index, 1);
            }
        },

        async exportBudgets() {
            try {
                const response = await fetch('/api/financial/budgets/export');
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'budgets.xlsx';
                a.click();
            } catch (error) {
                console.error('Error exporting budgets:', error);
            }
        },

        showNotification(message, type) {
            // Integration with existing notification system
            if (window.Alpine && window.Alpine.store('notifications')) {
                window.Alpine.store('notifications').add({
                    message: message,
                    type: type,
                    duration: 5000
                });
            } else {
                alert(message);
            }
        }
    }
}
</script>
@endsection