@extends('layouts.dashboard')

@section('title', 'Pesanan Baru')
@section('page-title')
<span class="text-base lg:text-2xl">Pesanan Baru</span>
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
        <a href="{{ route('orders.index') }}" class="ml-1 text-sm font-medium text-muted hover:text-foreground md:ml-2">Pesanan</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Pesanan Baru</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="orderForm()" x-init="init()" class="max-w-6xl mx-auto space-y-4 lg:space-y-6 p-4 lg:p-0">
    <form @submit.prevent="submitOrder()">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
            <!-- Left Column: Order Form -->
            <div class="lg:col-span-2 space-y-4 lg:space-y-6">
                <!-- Customer Information -->
                <div class="card">
                    <div class="p-4 lg:p-6 border-b border-border">
                        <h3 class="text-lg font-semibold text-foreground">Informasi Pelanggan</h3>
                        <p class="text-sm text-muted">Data pelanggan untuk pesanan ini</p>
                    </div>
                    
                    <div class="p-4 lg:p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-2">Nama Pelanggan *</label>
                                <input type="text" 
                                       x-model="order.customer_name"
                                       class="input"
                                       placeholder="Masukkan nama pelanggan"
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-2">Nomor Telepon *</label>
                                <input type="tel" 
                                       x-model="order.customer_phone"
                                       class="input"
                                       placeholder="08xxxxxxxxxx"
                                       required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Alamat Pengiriman</label>
                            <textarea x-model="order.customer_address"
                                      class="input"
                                      rows="3"
                                      placeholder="Alamat lengkap untuk pengiriman (opsional)"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-2">Tanggal Pesanan</label>
                                <input type="date" 
                                       x-model="order.order_date"
                                       class="input"
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-2">Waktu Pengambilan</label>
                                <input type="time" 
                                       x-model="order.pickup_time"
                                       class="input">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card">
                    <div class="p-4 lg:p-6 border-b border-border">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-foreground">Item Pesanan</h3>
                                <p class="text-sm text-muted">Pilih produk untuk pesanan ini</p>
                            </div>
                            <button type="button" @click="addOrderItem()" class="btn-primary text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Tambah Item
                            </button>
                        </div>
                    </div>
                    
                    <div class="p-4 lg:p-6">
                        <div class="space-y-4">
                            <template x-for="(item, index) in order.items" :key="index">
                                <!-- Desktop Layout -->
                                <div class="hidden lg:grid grid-cols-12 gap-4 items-end">
                                    <div class="col-span-5">
                                        <label class="block text-sm font-medium text-foreground mb-2">Produk</label>
                                        <select x-model="item.product_id" 
                                                @change="updateItemPrice(index)"
                                                class="input">
                                            <option value="">Pilih Produk</option>
                                            <template x-for="product in products" :key="product.id">
                                                <option :value="product.id" x-text="product.name + ' - ' + formatCurrency(product.price)"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-foreground mb-2">Jumlah</label>
                                        <input type="number" 
                                               x-model="item.quantity"
                                               @input="calculateItemTotal(index)"
                                               class="input"
                                               placeholder="0"
                                               min="1">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-foreground mb-2">Harga</label>
                                        <input type="number" 
                                               x-model="item.price"
                                               @input="calculateItemTotal(index)"
                                               class="input"
                                               placeholder="0"
                                               step="1000">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-foreground mb-2">Total</label>
                                        <div class="text-sm font-medium text-foreground py-2" x-text="formatCurrency(item.total)"></div>
                                    </div>
                                    <div class="col-span-1">
                                        <button type="button" @click="removeOrderItem(index)" 
                                                class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Mobile Layout -->
                                <div class="lg:hidden card p-4 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-medium text-foreground">Item #<span x-text="index + 1"></span></h4>
                                        <button type="button" @click="removeOrderItem(index)" 
                                                class="mobile-action-button p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-foreground mb-2">Produk</label>
                                            <select x-model="item.product_id" 
                                                    @change="updateItemPrice(index)"
                                                    class="input">
                                                <option value="">Pilih Produk</option>
                                                <template x-for="product in products" :key="product.id">
                                                    <option :value="product.id" x-text="product.name"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-sm font-medium text-foreground mb-2">Jumlah</label>
                                                <input type="number" 
                                                       x-model="item.quantity"
                                                       @input="calculateItemTotal(index)"
                                                       class="input"
                                                       placeholder="0"
                                                       min="1">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-foreground mb-2">Harga</label>
                                                <input type="number" 
                                                       x-model="item.price"
                                                       @input="calculateItemTotal(index)"
                                                       class="input"
                                                       placeholder="0"
                                                       step="1000">
                                            </div>
                                        </div>
                                        <div class="bg-surface p-3 rounded-lg">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-medium text-foreground">Total Item:</span>
                                                <span class="text-base font-semibold text-primary" x-text="formatCurrency(item.total)"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <div x-show="order.items.length === 0" class="text-center py-8 text-muted">
                                <svg class="w-12 h-12 mx-auto mb-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                <p>Belum ada item yang ditambahkan</p>
                                <button type="button" @click="addOrderItem()" class="btn-primary mt-4">Tambah Item Pertama</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="card">
                    <div class="p-4 lg:p-6 border-b border-border">
                        <h3 class="text-lg font-semibold text-foreground">Catatan Pesanan</h3>
                        <p class="text-sm text-muted">Catatan tambahan untuk pesanan ini</p>
                    </div>
                    
                    <div class="p-4 lg:p-6">
                        <textarea x-model="order.notes"
                                  class="input"
                                  rows="4"
                                  placeholder="Catatan khusus, permintaan pelanggan, dll..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="space-y-4 lg:space-y-6">
                <!-- Order Summary -->
                <div class="card lg:sticky lg:top-6">
                    <div class="p-4 lg:p-6 border-b border-border">
                        <h3 class="text-lg font-semibold text-foreground">Ringkasan Pesanan</h3>
                    </div>
                    
                    <div class="p-4 lg:p-6 space-y-4">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-muted">Subtotal:</span>
                                <span class="font-medium" x-text="formatCurrency(orderSubtotal)"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-muted">Pajak (10%):</span>
                                <span class="font-medium" x-text="formatCurrency(orderTax)"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-muted">Biaya Pengiriman:</span>
                                <input type="number" 
                                       x-model="order.shipping_cost"
                                       @input="calculateOrderTotal()"
                                       class="input text-right w-24"
                                       placeholder="0"
                                       step="1000">
                            </div>
                            <hr class="border-border">
                            <div class="flex justify-between items-center text-lg font-semibold">
                                <span class="text-foreground">Total:</span>
                                <span class="text-primary" x-text="formatCurrency(orderTotal)"></span>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-foreground">Metode Pembayaran</label>
                            <select x-model="order.payment_method" class="input">
                                <option value="cash">Tunai</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="ewallet">E-Wallet</option>
                                <option value="credit">Kredit</option>
                            </select>
                        </div>

                        <!-- Order Status -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-foreground">Status Pesanan</label>
                            <select x-model="order.status" class="input">
                                <option value="pending">Menunggu</option>
                                <option value="confirmed">Dikonfirmasi</option>
                                <option value="processing">Diproses</option>
                                <option value="ready">Siap</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <button type="submit" 
                                    class="btn-primary w-full py-3 lg:py-2"
                                    :disabled="loading || order.items.length === 0">
                                <span x-show="!loading" class="flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                    </svg>
                                    Simpan Pesanan
                                </span>
                                <span x-show="loading" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Menyimpan...
                                </span>
                            </button>
                            <a href="{{ route('orders.index') }}" class="btn-secondary w-full py-3 lg:py-2 text-center block">
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function orderForm() {
    return {
        loading: false,
        order: {
            customer_name: '',
            customer_phone: '',
            customer_address: '',
            order_date: new Date().toISOString().split('T')[0],
            pickup_time: '',
            payment_method: 'cash',
            status: 'pending',
            shipping_cost: 0,
            notes: '',
            items: []
        },
        products: [],

        async init() {
            console.log('Initializing order form...');
            await this.loadProducts();
            this.addOrderItem(); // Add first item
        },

        async loadProducts() {
            try {
                const response = await fetch('/api/products/active');
                const data = await response.json();
                this.products = data.data;
            } catch (error) {
                console.error('Failed to load products:', error);
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal memuat daftar produk'
                });
            }
        },

        get orderSubtotal() {
            return this.order.items.reduce((total, item) => total + (item.total || 0), 0);
        },

        get orderTax() {
            return this.orderSubtotal * 0.1;
        },

        get orderTotal() {
            return this.orderSubtotal + this.orderTax + (this.order.shipping_cost || 0);
        },

        init() {
            this.addOrderItem(); // Add first item
        },

        addOrderItem() {
            this.order.items.push({
                product_id: '',
                quantity: 1,
                price: 0,
                total: 0
            });
        },

        removeOrderItem(index) {
            this.order.items.splice(index, 1);
        },

        updateItemPrice(index) {
            const item = this.order.items[index];
            const product = this.products.find(p => p.id == item.product_id);
            if (product) {
                item.price = product.price;
                this.calculateItemTotal(index);
            }
        },

        calculateItemTotal(index) {
            const item = this.order.items[index];
            item.total = (item.quantity || 0) * (item.price || 0);
        },

        calculateOrderTotal() {
            // This will trigger reactive calculations
        },

        async submitOrder() {
            this.loading = true;

            try {
                // Validate form
                if (!this.order.customer_name || !this.order.customer_phone) {
                    throw new Error('Nama dan nomor telepon pelanggan wajib diisi');
                }

                if (this.order.items.length === 0) {
                    throw new Error('Minimal harus ada satu item pesanan');
                }

                // Check if all items are valid
                for (let item of this.order.items) {
                    if (!item.product_id || !item.quantity || !item.price) {
                        throw new Error('Semua item harus diisi dengan lengkap');
                    }
                }

                // API call
                const response = await fetch('/api/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.order)
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal membuat pesanan');
                }

                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: 'Pesanan berhasil dibuat.'
                });

                // Redirect to orders list
                window.location.href = '/orders';
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: error.message
                });
            } finally {
                this.loading = false;
            }
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount || 0);
        }
    }
}
</script>
@endpush