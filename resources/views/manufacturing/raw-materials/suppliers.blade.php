@extends('layouts.dashboard')

@section('title', 'Pemasok Bahan Baku')
@section('page-title', 'Manajemen Pemasok')

@section('content')
<div x-data="suppliersIndex()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Pemasok Bahan Baku</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Kelola informasi pemasok dan hubungan bisnis</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.raw-materials.index') }}"
               class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Bahan Baku
            </a>
            <a href="{{ route('manufacturing.raw-materials.suppliers.create') }}" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Pemasok
            </a>
            <button @click="exportSuppliers" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Total Pemasok</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="suppliers.length">0</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Pemasok Aktif</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="activeSuppliers">0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Rata-rata Rating</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="averageRating.toFixed(1)">0.0</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Total Pembelian</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="formatCurrency(totalPurchases)">Rp 0</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative">
                    <input type="text" 
                           x-model="search" 
                           @input="filterData"
                           placeholder="Cari pemasok..." 
                           class="input pl-10">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <select x-model="statusFilter" @change="filterData" class="input">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                    <option value="pending">Pending</option>
                </select>
                <select x-model="categoryFilter" @change="filterData" class="input">
                    <option value="">Semua Kategori</option>
                    <option value="Bahan Utama">Bahan Utama</option>
                    <option value="Kemasan">Kemasan</option>
                    <option value="Bahan Tambahan">Bahan Tambahan</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Suppliers Table/Cards -->
    <div class="card">
        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-border/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Pemasok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Kategori Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Total Pembelian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Terakhir Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="supplier in filteredData" :key="supplier.id">
                        <tr class="hover:bg-border/30">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="supplier.name"></div>
                                    <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="supplier.address"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm md:text-base text-foreground leading-tight" x-text="supplier.phone"></div>
                                <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="supplier.email"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <template x-for="category in supplier.categories" :key="category">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 rounded-full" x-text="category"></span>
                                    </template>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex text-yellow-400">
                                        <template x-for="i in 5" :key="i">
                                            <svg :class="i <= supplier.rating ? 'text-yellow-400' : 'text-gray-300'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </template>
                                    </div>
                                    <span class="ml-2 text-sm text-muted" x-text="'(' + supplier.rating + '/5)'"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="formatCurrency(supplier.total_purchases)"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="formatDate(supplier.last_order_date)"></td>
                            <td class="px-6 py-4">
                                <span :class="getStatusColor(supplier.status)"
                                      class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                      x-text="getStatusText(supplier.status)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <button @click="viewSupplier(supplier)" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg touch-manipulation" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click="editSupplier(supplier)" class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg touch-manipulation" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="createPurchaseOrder(supplier)" class="p-2 text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-lg touch-manipulation" title="Buat PO">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </button>
                                    <button @click="contactSupplier(supplier)" class="p-2 text-orange-600 hover:text-orange-800 hover:bg-orange-50 rounded-lg touch-manipulation" title="Kontak">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
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
        <div class="lg:hidden space-y-4 p-4">
            <template x-for="supplier in filteredData" :key="supplier.id">
                <div class="bg-background border border-border rounded-lg p-4 space-y-3">
                    <!-- Header with name and status -->
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-base md:text-lg font-medium text-foreground leading-tight" x-text="supplier.name"></h3>
                            <p class="text-xs md:text-sm text-muted leading-relaxed" x-text="supplier.address"></p>
                        </div>
                        <span :class="getStatusColor(supplier.status)"
                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              x-text="getStatusText(supplier.status)"></span>
                    </div>

                    <!-- Contact Info -->
                    <div class="grid grid-cols-1 gap-2 text-sm">
                        <div>
                            <span class="text-muted">Telepon:</span>
                            <p class="font-medium text-foreground" x-text="supplier.phone"></p>
                        </div>
                        <div>
                            <span class="text-muted">Email:</span>
                            <p class="font-medium text-foreground" x-text="supplier.email"></p>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div>
                        <span class="text-sm text-muted">Kategori Produk:</span>
                        <div class="flex flex-wrap gap-1 mt-1">
                            <template x-for="category in supplier.categories" :key="category">
                                <span class="inline-flex px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 rounded-full" x-text="category"></span>
                            </template>
                        </div>
                    </div>

                    <!-- Rating and Stats -->
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-muted">Rating:</span>
                            <div class="flex items-center mt-1">
                                <div class="flex text-yellow-400">
                                    <template x-for="i in 5" :key="i">
                                        <svg :class="i <= supplier.rating ? 'text-yellow-400' : 'text-gray-300'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </template>
                                </div>
                                <span class="ml-1 text-xs text-muted" x-text="'(' + supplier.rating + '/5)'"></span>
                            </div>
                        </div>
                        <div>
                            <span class="text-muted">Total Pembelian:</span>
                            <p class="font-medium text-foreground" x-text="formatCurrency(supplier.total_purchases)"></p>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs md:text-sm text-muted">Terakhir Order:</span>
                        <p class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="formatDate(supplier.last_order_date)"></p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-2 pt-2 border-t border-border">
                        <button @click="viewSupplier(supplier)"
                                class="flex items-center px-2 py-1 text-xs text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Lihat
                        </button>
                        <button @click="editSupplier(supplier)"
                                class="flex items-center px-2 py-1 text-xs text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                        <button @click="createPurchaseOrder(supplier)"
                                class="flex items-center px-2 py-1 text-xs text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            PO
                        </button>
                        <button @click="contactSupplier(supplier)"
                                class="flex items-center px-2 py-1 text-xs text-orange-600 hover:text-orange-800 hover:bg-orange-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Call
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function suppliersIndex() {
    return {
        suppliers: [],
        filteredData: [],
        search: '',
        statusFilter: '',
        categoryFilter: '',

        init() {
            this.loadData();
        },

        async loadData() {
            try {
                const response = await fetch('/api/suppliers', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const result = await response.json();
                
                if (result.success) {
                    this.suppliers = result.data?.data || result.data || [];
                    this.filteredData = this.suppliers;
                } else {
                    console.error('Failed to load suppliers:', result.message);
                    this.suppliers = [];
                    this.filteredData = [];
                }
            } catch (error) {
                console.error('Error loading data:', error);
                this.suppliers = [];
                this.filteredData = [];
            }
        },

        filterData() {
            this.filteredData = this.suppliers.filter(supplier => {
                const matchesSearch = supplier.name.toLowerCase().includes(this.search.toLowerCase()) ||
                                    supplier.contact_person.toLowerCase().includes(this.search.toLowerCase()) ||
                                    supplier.phone.includes(this.search);
                const matchesStatus = !this.statusFilter || supplier.status === this.statusFilter;
                const matchesCategory = !this.categoryFilter || supplier.categories.includes(this.categoryFilter);
                
                return matchesSearch && matchesStatus && matchesCategory;
            });
        },

        get activeSuppliers() {
            return this.suppliers.filter(supplier => supplier.status === 'active').length;
        },

        get averageRating() {
            if (this.suppliers.length === 0) return 0;
            const totalRating = this.suppliers.reduce((sum, supplier) => sum + supplier.rating, 0);
            return totalRating / this.suppliers.length;
        },

        get totalPurchases() {
            return this.suppliers.reduce((sum, supplier) => sum + supplier.total_purchases, 0);
        },

        getStatusColor(status) {
            const colors = {
                'active': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'inactive': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getStatusText(status) {
            const texts = {
                'active': 'Aktif',
                'inactive': 'Tidak Aktif',
                'pending': 'Pending'
            };
            return texts[status] || status;
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        },

        viewSupplier(supplier) {
            window.location.href = `/manufacturing/raw-materials/supplier-detail?id=${supplier.id}`;
        },

        editSupplier(supplier) {
            window.location.href = `/manufacturing/raw-materials/edit-supplier?id=${supplier.id}`;
        },

        createPurchaseOrder(supplier) {
            window.location.href = `/manufacturing/raw-materials/purchasing/create?supplier=${supplier.id}`;
        },

        contactSupplier(supplier) {
            if (supplier.phone) {
                window.open(`tel:${supplier.phone}`);
            }
        },

        async exportSuppliers() {
            try {
                const response = await fetch('/api/suppliers/export', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const result = await response.json();
                
                if (result.success) {
                    // Create a download link for the CSV data
                    const blob = new Blob([result.data], { type: 'text/csv' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'suppliers_' + new Date().toISOString().split('T')[0] + '.csv';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                } else {
                    alert('Gagal mengekspor data suppliers');
                }
            } catch (error) {
                console.error('Error exporting suppliers:', error);
                alert('Terjadi kesalahan saat mengekspor data');
            }
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }
    }
}
</script>
@endpush