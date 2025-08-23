@extends('layouts.dashboard')

@section('title', 'Detail Supplier')
@section('page-title', 'Detail Supplier')

@section('content')
<div x-data="supplierDetail()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight" x-text="supplier.name">Detail Supplier</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Informasi lengkap supplier dan riwayat kerjasama</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.raw-materials.suppliers') }}" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <button @click="editSupplier" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Supplier
            </button>
            <button @click="createPurchaseOrder" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Buat PO
            </button>
        </div>
    </div>

    <!-- Status Alert -->
    <div x-show="!supplier.is_active" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <div>
                <h4 class="text-sm font-medium text-red-800 dark:text-red-200">Supplier Tidak Aktif</h4>
                <p class="text-sm text-red-700 dark:text-red-300">Supplier ini sedang dalam status tidak aktif. Hubungi admin untuk mengaktifkan kembali.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4 lg:space-y-6">
            <!-- Basic Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Dasar</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Nama Supplier</label>
                        <p class="text-base font-medium text-foreground" x-text="supplier.name">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Kode Supplier</label>
                        <p class="text-base font-medium text-foreground" x-text="supplier.code">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Contact Person</label>
                        <p class="text-base font-medium text-foreground" x-text="supplier.contact_person">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Telepon</label>
                        <div class="flex items-center gap-2">
                            <p class="text-base font-medium text-foreground" x-text="supplier.phone">-</p>
                            <button @click="contactSupplier('phone')" x-show="supplier.phone" class="p-1 text-primary hover:text-primary/80 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Email</label>
                        <div class="flex items-center gap-2">
                            <p class="text-base font-medium text-foreground" x-text="supplier.email">-</p>
                            <button @click="contactSupplier('email')" x-show="supplier.email" class="p-1 text-primary hover:text-primary/80 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Payment Terms</label>
                        <p class="text-base font-medium text-foreground" x-text="supplier.payment_terms || 'Belum ditentukan'">-</p>
                    </div>
                </div>
                <div class="mt-4" x-show="supplier.address">
                    <label class="block text-sm font-medium text-muted mb-1">Alamat</label>
                    <p class="text-base text-foreground" x-text="supplier.address">-</p>
                </div>
                <div class="mt-4" x-show="supplier.notes">
                    <label class="block text-sm font-medium text-muted mb-1">Catatan</label>
                    <p class="text-base text-foreground" x-text="supplier.notes">-</p>
                </div>
            </div>

            <!-- Product Categories -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Kategori Produk</h3>
                <div class="flex flex-wrap gap-2">
                    <template x-for="category in supplier.categories" :key="category">
                        <span class="inline-flex px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 rounded-full" x-text="category"></span>
                    </template>
                    <div x-show="!supplier.categories || supplier.categories.length === 0" class="text-muted text-sm">
                        Belum ada kategori produk yang ditentukan
                    </div>
                </div>
            </div>

            <!-- Purchase History -->
            <div class="card p-4 md:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight">Riwayat Pembelian</h3>
                    <button @click="loadPurchaseHistory" class="btn btn-sm btn-outline">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh
                    </button>
                </div>
                
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-border/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">No. PO</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Items</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <template x-for="po in purchaseHistory" :key="po.id">
                                <tr class="hover:bg-border/30">
                                    <td class="px-4 py-3 text-sm font-medium text-foreground" x-text="po.po_number"></td>
                                    <td class="px-4 py-3 text-sm text-foreground" x-text="formatDate(po.order_date)"></td>
                                    <td class="px-4 py-3 text-sm text-foreground" x-text="po.total_items + ' items'"></td>
                                    <td class="px-4 py-3 text-sm font-medium text-foreground" x-text="formatCurrency(po.total_amount)"></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                              :class="getStatusColor(po.status)"
                                              x-text="getStatusText(po.status)"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <button @click="viewPO(po)" class="p-1 text-blue-600 hover:text-blue-800 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden space-y-3">
                    <template x-for="po in purchaseHistory" :key="po.id">
                        <div class="bg-border/30 rounded-lg p-3">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="text-sm font-medium text-foreground" x-text="po.po_number"></h4>
                                    <p class="text-xs text-muted" x-text="formatDate(po.order_date)"></p>
                                </div>
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                      :class="getStatusColor(po.status)"
                                      x-text="getStatusText(po.status)"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-muted" x-text="po.total_items + ' items'"></p>
                                    <p class="text-sm font-medium text-foreground" x-text="formatCurrency(po.total_amount)"></p>
                                </div>
                                <button @click="viewPO(po)" class="p-2 text-blue-600 hover:text-blue-800 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="purchaseHistory.length === 0" class="text-center py-8 text-muted">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p>Belum ada riwayat pembelian dengan supplier ini</p>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 lg:space-y-6">
            <!-- Status & Rating Card -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Status & Rating</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Status</span>
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              :class="supplier.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'"
                              x-text="supplier.is_active ? 'Aktif' : 'Tidak Aktif'">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Rating</span>
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
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Statistik</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Total Pembelian</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatCurrency(supplier.total_purchases)">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Total PO</span>
                        <span class="text-sm font-medium text-foreground" x-text="supplier.total_orders || 0">0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Terakhir Order</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatDate(supplier.last_order_date)">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Bergabung Sejak</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatDate(supplier.created_at)">-</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <button @click="createPurchaseOrder" class="btn btn-sm btn-primary w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Buat Purchase Order
                    </button>
                    <button @click="contactSupplier('phone')" x-show="supplier.phone" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        Telepon Supplier
                    </button>
                    <button @click="contactSupplier('email')" x-show="supplier.email" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Email Supplier
                    </button>
                    <button @click="viewPerformanceReport" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Laporan Performa
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function supplierDetail() {
    return {
        supplier: {
            id: {{ $id ?? 1 }}, // This would come from the route parameter
            name: '',
            code: '',
            contact_person: '',
            phone: '',
            email: '',
            address: '',
            payment_terms: '',
            notes: '',
            is_active: true,
            rating: 0,
            total_purchases: 0,
            total_orders: 0,
            last_order_date: '',
            created_at: '',
            categories: []
        },
        purchaseHistory: [],

        async init() {
            await this.loadData();
            await this.loadPurchaseHistory();
        },

        async loadData() {
            try {
                const response = await fetch(`/api/suppliers/${this.supplier.id}`);
                const result = await response.json();
                
                if (result.success) {
                    this.supplier = { ...this.supplier, ...result.data };
                } else {
                    alert('Gagal memuat data: ' + result.message);
                }
            } catch (error) {
                console.error('Error loading data:', error);
                alert('Terjadi kesalahan saat memuat data');
            }
        },

        async loadPurchaseHistory() {
            try {
                const response = await fetch(`/api/suppliers/${this.supplier.id}/purchase-history`);
                const result = await response.json();
                
                if (result.success) {
                    this.purchaseHistory = result.data;
                }
            } catch (error) {
                console.error('Error loading purchase history:', error);
            }
        },

        editSupplier() {
            window.location.href = `/suppliers/${this.supplier.id}/edit`;
        },

        createPurchaseOrder() {
            window.location.href = `/purchase-orders/create?supplier=${this.supplier.id}`;
        },

        contactSupplier(type) {
            if (type === 'phone' && this.supplier.phone) {
                window.open(`tel:${this.supplier.phone}`);
            } else if (type === 'email' && this.supplier.email) {
                window.open(`mailto:${this.supplier.email}?subject=Inquiry from ${document.title}`);
            }
        },

        viewPO(po) {
            window.location.href = `/purchase-orders/${po.id}`;
        },

        viewPerformanceReport() {
            window.location.href = `/suppliers/${this.supplier.id}/performance-report`;
        },

        getStatusColor(status) {
            const colors = {
                'draft': 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
                'sent': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                'confirmed': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                'partial': 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
                'completed': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getStatusText(status) {
            const texts = {
                'draft': 'Draft',
                'sent': 'Terkirim',
                'confirmed': 'Dikonfirmasi',
                'partial': 'Sebagian',
                'completed': 'Selesai',
                'cancelled': 'Dibatalkan'
            };
            return texts[status] || status;
        },

        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
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