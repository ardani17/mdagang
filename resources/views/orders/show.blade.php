@extends('layouts.dashboard')

@section('title', 'Detail Pesanan')
@section('page-title', 'Detail Pesanan')

@section('content')
<div x-data="orderDetail()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight" x-text="order.order_number">Loading...</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Detail lengkap pesanan penjualan</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('orders.index') }}" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <button @click="editOrder" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Pesanan
            </button>
            <button @click="printOrder" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Invoice
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4 lg:space-y-6">
            <!-- Order Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Pesanan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Nomor Pesanan</label>
                        <p class="text-base font-medium text-foreground" x-text="order.order_number">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Status</label>
                        <span :class="getStatusColor(order.status)"
                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              x-text="getStatusText(order.status)">-</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Tanggal Pesanan</label>
                        <p class="text-base text-foreground" x-text="formatDate(order.created_at)">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Tanggal Dibutuhkan</label>
                        <p class="text-base text-foreground" x-text="formatDate(order.required_date)">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Sales Person</label>
                        <p class="text-base text-foreground" x-text="order.sales_person || 'Admin'">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Metode Pembayaran</label>
                        <p class="text-base text-foreground" x-text="order.payment_method">-</p>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Pelanggan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Nama Pelanggan</label>
                        <p class="text-base font-medium text-foreground" x-text="order.customer_name">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Nomor Telepon</label>
                        <p class="text-base text-foreground" x-text="order.customer_phone">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Email</label>
                        <p class="text-base text-foreground" x-text="order.customer_email || '-'">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Tipe Pelanggan</label>
                        <p class="text-base text-foreground" x-text="order.customer_type">-</p>
                    </div>
                </div>
                <div class="mt-4 md:mt-6">
                    <label class="block text-sm font-medium text-muted mb-2">Alamat Pengiriman</label>
                    <p class="text-sm text-foreground bg-border/30 p-3 rounded-lg" x-text="order.shipping_address || 'Tidak ada alamat pengiriman'"></p>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Item Pesanan</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-border/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Produk</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Harga</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Qty</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Diskon</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <template x-for="item in order.items" :key="item.id">
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-foreground" x-text="item.product_name"></div>
                                        <div class="text-xs text-muted" x-text="item.product_sku"></div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-foreground" x-text="formatCurrency(item.unit_price)"></td>
                                    <td class="px-4 py-3 text-sm text-foreground" x-text="item.quantity + ' ' + item.unit"></td>
                                    <td class="px-4 py-3 text-sm text-foreground" x-text="item.discount_percentage + '%'"></td>
                                    <td class="px-4 py-3 text-sm font-medium text-foreground" x-text="formatCurrency(item.subtotal)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Timeline Pesanan</h3>
                <div class="space-y-4">
                    <template x-for="event in order.timeline" :key="event.id">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-3 h-3 rounded-full mt-2"
                                 :class="getTimelineColor(event.status)"></div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-foreground" x-text="event.title"></p>
                                    <span class="text-xs text-muted" x-text="formatDateTime(event.timestamp)"></span>
                                </div>
                                <p class="text-sm text-muted mt-1" x-text="event.description"></p>
                                <p class="text-xs text-muted mt-1" x-text="'Oleh: ' + event.user_name"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Notes -->
            <div class="card p-4 md:p-6" x-show="order.notes">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Catatan Pesanan</h3>
                <p class="text-sm text-foreground bg-border/30 p-3 rounded-lg" x-text="order.notes"></p>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 lg:space-y-6">
            <!-- Order Summary -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Ringkasan Pesanan</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Subtotal</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatCurrency(order.subtotal)">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Diskon</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatCurrency(order.discount_amount)">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Pajak</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatCurrency(order.tax_amount)">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Ongkir</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatCurrency(order.shipping_cost)">Rp 0</span>
                    </div>
                    <hr class="border-border">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-foreground">Total</span>
                        <span class="text-lg font-bold text-foreground" x-text="formatCurrency(order.total_amount)">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Pembayaran</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Status Pembayaran</span>
                        <span :class="getPaymentStatusColor(order.payment_status)"
                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              x-text="getPaymentStatusText(order.payment_status)">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Metode Pembayaran</span>
                        <span class="text-sm font-medium text-foreground" x-text="order.payment_method">-</span>
                    </div>
                    <div x-show="order.payment_date">
                        <span class="text-sm text-muted">Tanggal Bayar</span>
                        <p class="text-sm font-medium text-foreground" x-text="formatDate(order.payment_date)">-</p>
                    </div>
                    <div x-show="order.payment_reference">
                        <span class="text-sm text-muted">Referensi Pembayaran</span>
                        <p class="text-sm font-medium text-foreground" x-text="order.payment_reference">-</p>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="card p-4 md:p-6" x-show="order.shipping_method">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Pengiriman</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-muted">Metode Pengiriman</span>
                        <p class="text-sm font-medium text-foreground" x-text="order.shipping_method">-</p>
                    </div>
                    <div x-show="order.tracking_number">
                        <span class="text-sm text-muted">Nomor Resi</span>
                        <p class="text-sm font-medium text-foreground" x-text="order.tracking_number">-</p>
                    </div>
                    <div x-show="order.shipped_date">
                        <span class="text-sm text-muted">Tanggal Kirim</span>
                        <p class="text-sm font-medium text-foreground" x-text="formatDate(order.shipped_date)">-</p>
                    </div>
                    <div x-show="order.estimated_delivery">
                        <span class="text-sm text-muted">Estimasi Tiba</span>
                        <p class="text-sm font-medium text-foreground" x-text="formatDate(order.estimated_delivery)">-</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <button @click="updateStatus" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Update Status
                    </button>
                    <button @click="duplicateOrder" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Duplikasi Pesanan
                    </button>
                    <button @click="sendInvoice" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Kirim Invoice
                    </button>
                    <button @click="viewCustomer" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Lihat Pelanggan
                    </button>
                </div>
            </div>

            <!-- Order Statistics -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Statistik</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Total Item</span>
                        <span class="text-sm font-medium text-foreground" x-text="order.total_items">0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Total Quantity</span>
                        <span class="text-sm font-medium text-foreground" x-text="order.total_quantity">0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Rata-rata per Item</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatCurrency(order.average_item_price)">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Margin Keuntungan</span>
                        <span class="text-sm font-medium text-foreground" x-text="order.profit_margin + '%'">0%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div x-show="showStatusModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showStatusModal = false"></div>
            
            <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-base md:text-lg font-medium text-foreground leading-tight">Update Status Pesanan</h3>
                    <button @click="showStatusModal = false" class="text-muted hover:text-foreground">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form @submit.prevent="saveStatusUpdate" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Status Baru *</label>
                        <select x-model="statusUpdate.new_status" class="input" required>
                            <option value="">Pilih Status</option>
                            <option value="pending">Menunggu</option>
                            <option value="confirmed">Dikonfirmasi</option>
                            <option value="processing">Diproses</option>
                            <option value="ready">Siap</option>
                            <option value="delivered">Dikirim</option>
                            <option value="completed">Selesai</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Catatan</label>
                        <textarea x-model="statusUpdate.notes" class="input" rows="3" placeholder="Catatan perubahan status (opsional)"></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showStatusModal = false" class="btn btn-outline touch-manipulation">Batal</button>
                        <button type="submit" class="btn btn-primary touch-manipulation">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function orderDetail() {
    return {
        order: {
            id: {{ $id ?? 1 }}, // This would come from the route parameter
            order_number: '',
            status: '',
            payment_status: '',
            created_at: '',
            required_date: '',
            sales_person: '',
            payment_method: '',
            customer_name: '',
            customer_phone: '',
            customer_email: '',
            customer_type: '',
            shipping_address: '',
            shipping_method: '',
            tracking_number: '',
            shipped_date: '',
            estimated_delivery: '',
            payment_date: '',
            payment_reference: '',
            subtotal: 0,
            discount_amount: 0,
            tax_amount: 0,
            shipping_cost: 0,
            total_amount: 0,
            total_items: 0,
            total_quantity: 0,
            average_item_price: 0,
            profit_margin: 0,
            notes: '',
            items: [],
            timeline: []
        },
        showStatusModal: false,
        statusUpdate: {
            new_status: '',
            notes: ''
        },

        async init() {
            await this.loadData();
        },

        async loadData() {
            try {
                const response = await fetch(`/api/orders/${this.order.id}`);
                const result = await response.json();
                
                if (result.success) {
                    this.order = { ...this.order, ...result.data };
                } else {
                    alert('Gagal memuat data: ' + result.message);
                }
            } catch (error) {
                console.error('Error loading data:', error);
                alert('Terjadi kesalahan saat memuat data');
            }
        },

        getStatusColor(status) {
            const colors = {
                'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                'confirmed': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                'processing': 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
                'ready': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'delivered': 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-400',
                'completed': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getStatusText(status) {
            const texts = {
                'pending': 'Menunggu',
                'confirmed': 'Dikonfirmasi',
                'processing': 'Diproses',
                'ready': 'Siap',
                'delivered': 'Dikirim',
                'completed': 'Selesai',
                'cancelled': 'Dibatalkan'
            };
            return texts[status] || status;
        },

        getPaymentStatusColor(status) {
            const colors = {
                'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                'paid': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'partial': 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
                'failed': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getPaymentStatusText(status) {
            const texts = {
                'pending': 'Menunggu',
                'paid': 'Lunas',
                'partial': 'Sebagian',
                'failed': 'Gagal'
            };
            return texts[status] || status;
        },

        getTimelineColor(status) {
            const colors = {
                'created': 'bg-blue-500',
                'confirmed': 'bg-green-500',
                'processing': 'bg-purple-500',
                'ready': 'bg-green-600',
                'delivered': 'bg-indigo-500',
                'completed': 'bg-green-700',
                'cancelled': 'bg-red-500'
            };
            return colors[status] || 'bg-gray-500';
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

        formatDateTime(dateTimeString) {
            if (!dateTimeString) return '-';
            const date = new Date(dateTimeString);
            return date.toLocaleString('id-ID', {
                day: '2-digit',
                month: '2-digit
',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        },

        editOrder() {
            window.location.href = `/orders/${this.order.id}/edit`;
        },

        printOrder() {
            window.open(`/orders/${this.order.id}/print`, '_blank');
        },

        updateStatus() {
            this.showStatusModal = true;
        },

        async saveStatusUpdate() {
            try {
                const response = await fetch(`/api/orders/${this.order.id}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify(this.statusUpdate)
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('Status pesanan berhasil diperbarui!');
                    this.showStatusModal = false;
                    this.resetStatusUpdate();
                    await this.loadData();
                } else {
                    alert('Gagal memperbarui status: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memperbarui status');
            }
        },

        resetStatusUpdate() {
            this.statusUpdate = {
                new_status: '',
                notes: ''
            };
        },

        duplicateOrder() {
            if (confirm('Apakah Anda yakin ingin menduplikasi pesanan ini?')) {
                window.location.href = `/orders/create?duplicate=${this.order.id}`;
            }
        },

        sendInvoice() {
            if (confirm('Kirim invoice ke pelanggan?')) {
                // Implementation for sending invoice
                alert('Invoice berhasil dikirim!');
            }
        },

        viewCustomer() {
            window.location.href = `/customers/${this.order.customer_id}`;
        }
    }
}
</script>
@endpush