@extends('layouts.dashboard')

@section('title', 'Manajemen Pelanggan')
@section('page-title')
<span class="text-base lg:text-2xl">Manajemen Pelanggan</span>
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
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Pelanggan</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="customerManager()" class="space-y-4 lg:space-y-6 p-4 lg:p-0">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Daftar Pelanggan</h2>
            <p class="text-sm text-muted">Kelola data pelanggan dan riwayat pembelian</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="exportCustomers()" 
                    class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor
            </button>
            <a href="/customers/create" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Pelanggan
            </a>
        </div>
    </div>

    <!-- Customer Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6">
        <!-- Total Pelanggan -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Total Pelanggan</p>
                    <p class="text-2xl lg:text-3xl font-bold text-foreground" x-text="stats.total_customers"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-green-600">+8</span> bulan ini
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pelanggan Aktif -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Pelanggan Aktif</p>
                    <p class="text-2xl lg:text-3xl font-bold text-green-600" x-text="stats.active_customers"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-green-600">85%</span> dari total
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Rata-rata Pembelian -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Rata-rata Pembelian</p>
                    <p class="text-2xl lg:text-3xl font-bold text-primary" x-text="formatCurrency(stats.avg_purchase)"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-green-600">+12%</span> dari bulan lalu
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

    <!-- Filters and Search -->
    <div class="card p-4 lg:p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-foreground mb-2">Cari Pelanggan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           x-model="filters.search"
                           @input.debounce.300ms="loadCustomers()"
                           class="input pl-10" 
                           placeholder="Cari berdasarkan nama, telepon, email...">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                <select x-model="filters.status" 
                        @change="loadCustomers()"
                        class="input">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                    <option value="vip">VIP</option>
                </select>
            </div>

            <!-- Sort Filter -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Urutkan</label>
                <select x-model="filters.sort" 
                        @change="loadCustomers()"
                        class="input">
                    <option value="name">Nama A-Z</option>
                    <option value="recent">Terbaru</option>
                    <option value="purchase">Total Pembelian</option>
                    <option value="last_order">Pesanan Terakhir</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="card">
        <div class="p-4 lg:p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-foreground">
                    Daftar Pelanggan (<span x-text="pagination.total"></span>)
                </h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-muted">Tampilkan:</span>
                    <select x-model="pagination.per_page" 
                            @change="loadCustomers()"
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
                        <th>Pelanggan</th>
                        <th>Kontak</th>
                        <th>Total Pembelian</th>
                        <th>Pesanan Terakhir</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="customer in customers" :key="customer.id">
                        <tr class="hover:bg-surface/50">
                            <td>
                                <input type="checkbox"
                                       :value="customer.id"
                                       x-model="selectedCustomers"
                                       class="rounded border-border text-primary focus:ring-primary">
                            </td>
                            <td>
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-primary" x-text="customer.name.charAt(0).toUpperCase()"></span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-foreground" x-text="customer.name"></div>
                                        <div class="text-sm text-muted" x-text="'ID: ' + customer.customer_id"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm text-foreground" x-text="customer.phone"></div>
                                <div class="text-sm text-muted" x-text="customer.email"></div>
                            </td>
                            <td>
                                <div class="font-medium text-foreground" x-text="formatCurrency(customer.total_purchase)"></div>
                                <div class="text-sm text-muted" x-text="customer.total_orders + ' pesanan'"></div>
                            </td>
                            <td>
                                <div class="text-sm text-foreground" x-text="formatDate(customer.last_order_date)"></div>
                                <div class="text-xs text-muted" x-text="customer.last_order_amount ? formatCurrency(customer.last_order_amount) : '-'"></div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="getStatusClass(customer.status)"
                                      x-text="getStatusText(customer.status)">
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <button @click="viewCustomer(customer)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <a :href="`/customers/${customer.id}/edit`"
                                       class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button @click="createOrder(customer)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
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
            <template x-for="customer in customers" :key="customer.id">
                <div class="card rounded-xl p-4 shadow-sm">
                    <!-- Header with checkbox and status -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox"
                                   :value="customer.id"
                                   x-model="selectedCustomers"
                                   class="w-5 h-5 rounded border-border text-primary focus:ring-primary focus:ring-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                  :class="getStatusClass(customer.status)"
                                  x-text="getStatusText(customer.status)">
                            </span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <button @click="viewCustomer(customer)"
                                    class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <a :href="`/customers/${customer.id}/edit`"
                               class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <button @click="createOrder(customer)"
                                    class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-lg font-medium text-primary" x-text="customer.name.charAt(0).toUpperCase()"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-foreground text-base leading-tight" x-text="customer.name"></h3>
                            <p class="text-sm text-muted mt-1" x-text="'ID: ' + customer.customer_id"></p>
                        </div>
                    </div>

                    <!-- Customer Details -->
                    <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-border">
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Kontak</span>
                            <p class="text-sm text-foreground mt-1" x-text="customer.phone"></p>
                            <p class="text-xs text-muted line-clamp-1" x-text="customer.email"></p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Total Pembelian</span>
                            <p class="font-semibold text-foreground text-base mt-1" x-text="formatCurrency(customer.total_purchase)"></p>
                            <p class="text-xs text-muted" x-text="customer.total_orders + ' pesanan'"></p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Pesanan Terakhir</span>
                            <p class="text-sm text-foreground mt-1" x-text="formatDate(customer.last_order_date)"></p>
                            <p class="text-xs text-muted" x-text="customer.last_order_amount ? formatCurrency(customer.last_order_amount) : '-'"></p>
                        </div>
                        <div class="flex items-end justify-end">
                            <button @click="deleteCustomer(customer)"
                                    class="mobile-action-button p-2.5 text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
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
                    (<span x-text="pagination.total"></span> pelanggan)
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
                    dari <span x-text="pagination.total"></span> pelanggan
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

    <!-- Bulk Actions -->
    <div x-show="selectedCustomers.length > 0" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-4"
         class="fixed bottom-4 left-4 right-4 lg:left-1/2 lg:right-auto lg:transform lg:-translate-x-1/2 lg:w-auto bg-surface border border-border rounded-xl shadow-xl p-4 z-50">
        
        <!-- Mobile Layout -->
        <div class="lg:hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-foreground">
                    <span x-text="selectedCustomers.length"></span> pelanggan dipilih
                </span>
                <button @click="selectedCustomers = []" class="p-1 text-muted hover:text-foreground">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <button @click="bulkUpdateStatus('active')" class="btn-secondary text-sm py-2 px-3 text
-center">
                    Aktifkan
                </button>
                <button @click="bulkUpdateStatus('inactive')" class="btn-secondary text-sm py-2 px-3 text-center">
                    Nonaktifkan
                </button>
                <button @click="bulkDelete()" class="bg-red-600 text-white text-sm py-2 px-3 rounded-lg hover:bg-red-700 transition-colors">
                    Hapus
                </button>
            </div>
        </div>

        <!-- Desktop Layout -->
        <div class="hidden lg:flex items-center space-x-4">
            <span class="text-sm text-foreground">
                <span x-text="selectedCustomers.length"></span> pelanggan dipilih
            </span>
            <div class="flex items-center space-x-2">
                <button @click="bulkUpdateStatus('active')" class="btn-secondary text-sm py-1 px-3">
                    Aktifkan
                </button>
                <button @click="bulkUpdateStatus('inactive')" class="btn-secondary text-sm py-1 px-3">
                    Nonaktifkan
                </button>
                <button @click="bulkDelete()" class="bg-red-600 text-white text-sm py-1 px-3 rounded hover:bg-red-700">
                    Hapus
                </button>
                <button @click="selectedCustomers = []" class="text-muted hover:text-foreground">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function customerManager() {
    return {
        customers: [],
        selectedCustomers: [],
        loading: false,
        stats: {
            total_customers: 127,
            active_customers: 108,
            avg_purchase: 85000
        },
        filters: {
            search: '',
            status: '',
            sort: 'name'
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
            await this.loadCustomers();
        },

        async loadCustomers() {
            this.loading = true;
            
            try {
                // Dummy data untuk testing frontend
                const dummyCustomers = [
                    {
                        id: 1,
                        customer_id: 'CUST-001',
                        name: 'Ahmad Wijaya',
                        phone: '081234567890',
                        email: 'ahmad.wijaya@email.com',
                        total_purchase: 2500000,
                        total_orders: 15,
                        last_order_date: '2024-01-15T10:30:00Z',
                        last_order_amount: 125000,
                        status: 'active'
                    },
                    {
                        id: 2,
                        customer_id: 'CUST-002',
                        name: 'Siti Nurhaliza',
                        phone: '081234567891',
                        email: 'siti.nurhaliza@email.com',
                        total_purchase: 1800000,
                        total_orders: 12,
                        last_order_date: '2024-01-14T14:20:00Z',
                        last_order_amount: 75000,
                        status: 'vip'
                    },
                    {
                        id: 3,
                        customer_id: 'CUST-003',
                        name: 'Budi Santoso',
                        phone: '081234567892',
                        email: 'budi.santoso@email.com',
                        total_purchase: 950000,
                        total_orders: 8,
                        last_order_date: '2024-01-10T16:45:00Z',
                        last_order_amount: 150000,
                        status: 'active'
                    },
                    {
                        id: 4,
                        customer_id: 'CUST-004',
                        name: 'Dewi Lestari',
                        phone: '081234567893',
                        email: 'dewi.lestari@email.com',
                        total_purchase: 3200000,
                        total_orders: 22,
                        last_order_date: '2024-01-13T11:15:00Z',
                        last_order_amount: 200000,
                        status: 'vip'
                    },
                    {
                        id: 5,
                        customer_id: 'CUST-005',
                        name: 'Eko Prasetyo',
                        phone: '081234567894',
                        email: 'eko.prasetyo@email.com',
                        total_purchase: 450000,
                        total_orders: 3,
                        last_order_date: '2024-01-05T09:30:00Z',
                        last_order_amount: 85000,
                        status: 'inactive'
                    }
                ];

                // Apply filters
                let filteredCustomers = dummyCustomers;
                
                if (this.filters.search) {
                    filteredCustomers = filteredCustomers.filter(customer => 
                        customer.name.toLowerCase().includes(this.filters.search.toLowerCase()) ||
                        customer.phone.includes(this.filters.search) ||
                        customer.email.toLowerCase().includes(this.filters.search.toLowerCase())
                    );
                }
                
                if (this.filters.status) {
                    filteredCustomers = filteredCustomers.filter(customer => customer.status === this.filters.status);
                }

                // Apply sorting
                if (this.filters.sort === 'recent') {
                    filteredCustomers.sort((a, b) => new Date(b.last_order_date) - new Date(a.last_order_date));
                } else if (this.filters.sort === 'purchase') {
                    filteredCustomers.sort((a, b) => b.total_purchase - a.total_purchase);
                } else if (this.filters.sort === 'name') {
                    filteredCustomers.sort((a, b) => a.name.localeCompare(b.name));
                }

                // Simulate pagination
                const total = filteredCustomers.length;
                const from = (this.pagination.current_page - 1) * this.pagination.per_page + 1;
                const to = Math.min(from + this.pagination.per_page - 1, total);
                
                this.customers = filteredCustomers.slice(from - 1, to);
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
                    message: 'Gagal memuat data pelanggan'
                });
            } finally {
                this.loading = false;
            }
        },

        toggleSelectAll(checked) {
            if (checked) {
                this.selectedCustomers = this.customers.map(c => c.id);
            } else {
                this.selectedCustomers = [];
            }
        },

        async viewCustomer(customer) {
            // Redirect to customer detail page
            window.location.href = `/customers/${customer.id}`;
        },

        async createOrder(customer) {
            // Redirect to create order with customer pre-filled
            window.location.href = `/orders/create?customer_id=${customer.id}`;
        },

        async deleteCustomer(customer) {
            if (!confirm(`Apakah Anda yakin ingin menghapus pelanggan "${customer.name}"?`)) {
                return;
            }

            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: 'Pelanggan berhasil dihapus.'
                });
                await this.loadCustomers();
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal menghapus pelanggan'
                });
            }
        },

        async bulkUpdateStatus(status) {
            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: `Berhasil mengubah status ${this.selectedCustomers.length} pelanggan.`
                });
                this.selectedCustomers = [];
                await this.loadCustomers();
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal mengubah status pelanggan'
                });
            }
        },

        async bulkDelete() {
            if (!confirm(`Apakah Anda yakin ingin menghapus ${this.selectedCustomers.length} pelanggan yang dipilih?`)) {
                return;
            }

            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: `Berhasil menghapus ${this.selectedCustomers.length} pelanggan.`
                });
                this.selectedCustomers = [];
                await this.loadCustomers();
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal menghapus pelanggan'
                });
            }
        },

        async exportCustomers() {
            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: 'Data pelanggan berhasil diekspor.'
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
                this.loadCustomers();
            }
        },

        nextPage() {
            if (this.pagination.next_page_url) {
                this.pagination.current_page++;
                this.loadCustomers();
            }
        },

        goToPage(page) {
            this.pagination.current_page = page;
            this.loadCustomers();
        },

        getStatusClass(status) {
            const classes = {
                'active': 'bg-green-100 text-green-800',
                'inactive': 'bg-red-100 text-red-800',
                'vip': 'bg-purple-100 text-purple-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },

        getStatusText(status) {
            const texts = {
                'active': 'Aktif',
                'inactive': 'Tidak Aktif',
                'vip': 'VIP'
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
        }
    }
}
</script>
@endpush