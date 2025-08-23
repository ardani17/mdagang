@extends('layouts.dashboard')

@section('title', 'Detail Pelanggan')
@section('page-title')
<span class="text-base lg:text-2xl">Detail Pelanggan</span>
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
        <a href="/customers" class="ml-1 text-sm font-medium text-muted hover:text-foreground md:ml-2">Pelanggan</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Detail</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="customerDetail()" class="space-y-4 lg:space-y-6 p-4 lg:p-0">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="/customers" class="p-2 text-muted hover:text-foreground rounded-lg border border-border hover:border-primary/20 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl lg:text-2xl font-bold text-foreground" x-text="customer.name"></h2>
                <p class="text-sm text-muted" x-text="'ID: ' + customer.customer_id"></p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="createOrder()" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Buat Pesanan
            </button>
            <a :href="`/customers/${customer.id}/edit`" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Pelanggan
            </a>
        </div>
    </div>

    <!-- Customer Profile Card -->
    <div class="card p-4 lg:p-6">
        <div class="flex flex-col lg:flex-row lg:items-start lg:space-x-6 space-y-4 lg:space-y-0">
            <!-- Avatar and Basic Info -->
            <div class="flex items-center space-x-4 lg:flex-col lg:items-center lg:space-x-0 lg:space-y-4">
                <div class="w-20 h-20 lg:w-24 lg:h-24 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-2xl lg:text-3xl font-medium text-primary" x-text="customer.name ? customer.name.charAt(0).toUpperCase() : ''"></span>
                </div>
                <div class="lg:text-center">
                    <h3 class="text-xl font-semibold text-foreground" x-text="customer.name"></h3>
                    <p class="text-sm text-muted" x-text="'ID: ' + customer.customer_id"></p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-2"
                          :class="getStatusClass(customer.status)"
                          x-text="getStatusText(customer.status)">
                    </span>
                </div>
            </div>

            <!-- Customer Details -->
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Contact Information -->
                <div class="space-y-4">
                    <h4 class="font-semibold text-foreground border-b border-border pb-2">Informasi Kontak</h4>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-muted flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-muted">Telepon</p>
                                <p class="font-medium text-foreground" x-text="customer.phone || '-'"></p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-muted flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-muted">Email</p>
                                <p class="font-medium text-foreground" x-text="customer.email || '-'"></p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-muted flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-muted">Alamat</p>
                                <p class="font-medium text-foreground" x-text="customer.address || '-'"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Purchase Statistics -->
                <div class="space-y-4">
                    <h4 class="font-semibold text-foreground border-b border-border pb-2">Statistik Pembelian</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Total Pembelian</span>
                            <span class="font-semibold text-lg text-primary" x-text="formatCurrency(customer.total_purchase)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Total Pesanan</span>
                            <span class="font-medium text-foreground" x-text="customer.total_orders + ' pesanan'"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Rata-rata per Pesanan</span>
                            <span class="font-medium text-foreground" x-text="formatCurrency(customer.avg_order_value)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Pesanan Terakhir</span>
                            <span class="font-medium text-foreground" x-text="formatDate(customer.last_order_date)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Bergabung Sejak</span>
                            <span class="font-medium text-foreground" x-text="formatDate(customer.created_at)"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order History -->
    <div class="card">
        <div class="p-4 lg:p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-foreground">Riwayat Pesanan</h3>
                <div class="flex items-center space-x-2">
                    <select x-model="orderFilters.status" @change="loadOrders()" class="input py-1 text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Diproses</option>
                        <option value="shipped">Dikirim</option>
                        <option value="delivered">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Tanggal</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="order in orders" :key="order.id">
                        <tr class="hover:bg-surface/50">
                            <td>
                                <div class="font-medium text-foreground" x-text="order.order_number"></div>
                                <div class="text-sm text-muted" x-text="'#' + order.id"></div>
                            </td>
                            <td>
                                <div class="text-sm text-foreground" x-text="formatDate(order.created_at)"></div>
                                <div class="text-xs text-muted" x-text="formatTime(order.created_at)"></div>
                            </td>
                            <td>
                                <div class="text-sm text-foreground" x-text="order.total_items + ' item'"></div>
                                <div class="text-xs text-muted" x-text="order.product_names"></div>
                            </td>
                            <td>
                                <div class="font-medium text-foreground" x-text="formatCurrency(order.total_amount)"></div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="getOrderStatusClass(order.status)"
                                      x-text="getOrderStatusText(order.status)">
                                </span>
                            </td>
                            <td>
                                <a :href="`/orders/${order.id}`" class="p-1 text-muted hover:text-foreground">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden space-y-3 mobile-card-spacing">
            <template x-for="order in orders" :key="order.id">
                <div class="card rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h4 class="font-medium text-foreground" x-text="order.order_number"></h4>
                            <p class="text-sm text-muted" x-text="formatDate(order.created_at)"></p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                              :class="getOrderStatusClass(order.status)"
                              x-text="getOrderStatusText(order.status)">
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-muted">Items:</span>
                            <span class="font-medium text-foreground ml-1" x-text="order.total_items"></span>
                        </div>
                        <div>
                            <span class="text-muted">Total:</span>
                            <span class="font-medium text-foreground ml-1" x-text="formatCurrency(order.total_amount)"></span>
                        </div>
                    </div>
                    <div class="flex justify-end mt-3">
                        <a :href="`/orders/${order.id}`" class="btn-secondary text-sm py-1 px-3">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="orders.length === 0" class="p-8 text-center">
            <svg class="w-16 h-16 text-muted mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <h3 class="text-lg font-medium text-foreground mb-2">Belum Ada Pesanan</h3>
            <p class="text-muted mb-4">Pelanggan ini belum pernah melakukan pesanan.</p>
            <button @click="createOrder()" class="btn-primary">
                Buat Pesanan Pertama
            </button>
        </div>
    </div>

    <!-- Customer Notes -->
    <div class="card p-4 lg:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-foreground">Catatan Pelanggan</h3>
            <button @click="editNotes = !editNotes" class="btn-secondary text-sm">
                <span x-text="editNotes ? 'Batal' : 'Edit'"></span>
            </button>
        </div>
        <div x-show="!editNotes">
            <p class="text-foreground whitespace-pre-wrap" x-text="customer.notes || 'Tidak ada catatan untuk pelanggan ini.'"></p>
        </div>
        <div x-show="editNotes" class="space-y-4">
            <textarea x-model="customer.notes" 
                      class="input min-h-[100px]" 
                      placeholder="Tambahkan catatan tentang pelanggan ini..."></textarea>
            <div class="flex justify-end space-x-3">
                <button @click="editNotes = false" class="btn-secondary">Batal</button>
                <button @click="saveNotes()" class="btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function customerDetail() {
    return {
        customer: {
            id: 1,
            customer_id: 'CUST-001',
            name: 'Ahmad Wijaya',
            phone: '081234567890',
            email: 'ahmad.wijaya@email.com',
            address: 'Jl. Merdeka No. 123, Jakarta Pusat',
            total_purchase: 2500000,
            total_orders: 15,
            avg_order_value: 166667,
            last_order_date: '2024-01-15T10:30:00Z',
            created_at: '2023-06-15T08:00:00Z',
            status: 'active',
            notes: 'Pelanggan setia yang selalu membeli produk premium. Suka dengan kemasan khusus.'
        },
        orders: [],
        orderFilters: {
            status: ''
        },
        editNotes: false,
        loading: false,

        async init() {
            await this.loadOrders();
        },

        async loadOrders() {
            this.loading = true;
            
            try {
                // Dummy order data
                const dummyOrders = [
                    {
                        id: 1,
                        order_number: 'ORD-2024-001',
                        created_at: '2024-01-15T10:30:00Z',
                        total_items: 3,
                        total_amount: 125000,
                        status: 'delivered',
                        product_names: 'Roti Tawar, Kue Donat'
                    },
                    {
                        id: 2,
                        order_number: 'ORD-2024-002',
                        created_at: '2024-01-10T14:20:00Z',
                        total_items: 2,
                        total_amount: 85000,
                        status: 'delivered',
                        product_names: 'Roti Coklat, Kue Lapis'
                    },
                    {
                        id: 3,
                        order_number: 'ORD-2024-003',
                        created_at: '2024-01-08T09:15:00Z',
                        total_items: 5,
                        total_amount: 200000,
                        status: 'processing',
                        product_names: 'Roti Gandum, Kue Tart'
                    }
                ];

                // Apply filters
                let filteredOrders = dummyOrders;
                if (this.orderFilters.status) {
                    filteredOrders = filteredOrders.filter(order => order.status === this.orderFilters.status);
                }

                this.orders = filteredOrders;
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal memuat riwayat pesanan'
                });
            } finally {
                this.loading = false;
            }
        },

        async createOrder() {
            window.location.href = `/orders/create?customer_id=${this.customer.id}`;
        },

        async saveNotes() {
            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: 'Catatan pelanggan berhasil disimpan.'
                });
                this.editNotes = false;
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal menyimpan catatan'
                });
            }
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

        getOrderStatusClass(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'processing': 'bg-blue-100 text-blue-800',
                'shipped': 'bg-purple-100 text-purple-800',
                'delivered': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },

        getOrderStatusText(status) {
            const texts = {
                'pending': 'Pending',
                'processing': 'Diproses',
                'shipped': 'Dikirim',
                'delivered': 'Selesai',
                'cancelled': 'Dibatalkan'
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