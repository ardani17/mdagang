@extends('layouts.dashboard')

@section('title', 'Edit Pesanan')
@section('page-title', 'Edit Pesanan')

@section('content')
<div x-data="editOrder()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight" x-text="'Edit Pesanan ' + order.order_number">Edit Pesanan</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Ubah detail pesanan penjualan</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('orders.index') }}" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <button @click="viewOrder" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Lihat Detail
            </button>
            <button @click="printOrder" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </button>
        </div>
    </div>

    <!-- Form -->
    <form @submit.prevent="submitForm" class="space-y-4 md:space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-4 lg:space-y-6">
                <!-- Order Information -->
                <div class="card p-4 md:p-6">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Pesanan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Nomor Pesanan *</label>
                            <input type="text"
                                   x-model="order.order_number"
                                   class="input w-full h-12 text-base bg-border/30"
                                   readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Status *</label>
                            <select x-model="order.status" class="input w-full h-12 text-base" required>
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
                            <label class="block text-sm font-medium text-foreground mb-2">Tanggal Dibutuhkan</label>
                            <input type="date"
                                   x-model="order.required_date"
                                   class="input w-full h-12 text-base">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Sales Person</label>
                            <input type="text"
                                   x-model="order.sales_person"
                                   class="input w-full h-12 text-base"
                                   placeholder="Nama sales person">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Metode Pembayaran *</label>
                            <select x-model="order.payment_method" class="input w-full h-12 text-base" required>
                                <option value="">Pilih Metode</option>
                                <option value="cash">Tunai</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="credit">Kredit</option>
                                <option value="cod">COD</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Status Pembayaran</label>
                            <select x-model="order.payment_status" class="input w-full h-12 text-base">
                                <option value="pending">Menunggu</option>
                                <option value="paid">Lunas</option>
                                <option value="partial">Sebagian</option>
                                <option value="failed">Gagal</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="card p-4 md:p-6">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Pelanggan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Nama Pelanggan *</label>
                            <input type="text"
                                   x-model="order.customer_name"
                                   class="input w-full h-12 text-base"
                                   placeholder="Nama lengkap pelanggan"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Nomor Telepon *</label>
                            <input type="tel"
                                   x-model="order.customer_phone"
                                   class="input w-full h-12 text-base"
                                   placeholder="081234567890"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Email</label>
                            <input type="email"
                                   x-model="order.customer_email"
                                   class="input w-full h-12 text-base"
                                   placeholder="email@example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Tipe Pelanggan</label>
                            <select x-model="order.customer_type" class="input w-full h-12 text-base">
                                <option value="retail">Retail</option>
                                <option value="wholesale">Grosir</option>
                                <option value="distributor">Distributor</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-6">
                        <label class="block text-sm font-medium text-foreground mb-2">Alamat Pengiriman</label>
                        <textarea x-model="order.shipping_address"
                                  class="input w-full text-base min-h-[80px]"
                                  rows="3"
                                  placeholder="Alamat lengkap untuk pengiriman"></textarea>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card p-4 md:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight">Item Pesanan</h3>
                        <button type="button" @click="addItem" class="btn btn-sm btn-primary">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Tambah Item
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(item, index) in order.items" :key="index">
                            <div class="p-4 border border-border rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Produk *</label>
                                        <select x-model="item.product_id" @change="updateItemProduct(index)" class="input w-full" required>
                                            <option value="">Pilih Produk</option>
                                            <template x-for="product in products" :key="product.id">
                                                <option :value="product.id" x-text="product.name + ' - ' + product.sku"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Harga *</label>
                                        <input type="number"
                                               x-model="item.unit_price"
                                               @input="calculateItemSubtotal(index)"
                                               class="input w-full"
                                               placeholder="0"
                                               min="0"
                                               step="100"
                                               required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Quantity *</label>
                                        <input type="number"
                                               x-model="item.quantity"
                                               @input="calculateItemSubtotal(index)"
                                               class="input w-full"
                                               placeholder="0"
                                               min="1"
                                               step="1"
                                               required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Diskon (%)</label>
                                        <input type="number"
                                               x-model="item.discount_percentage"
                                               @input="calculateItemSubtotal(index)"
                                               class="input w-full"
                                               placeholder="0"
                                               min="0"
                                               max="100"
                                               step="0.1">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Subtotal</label>
                                        <div class="flex items-center justify-between">
                                            <span class="text-base font-medium text-foreground" x-text="formatCurrency(item.subtotal || 0)">Rp 0</span>
                                            <button type="button" @click="removeItem(index)" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-foreground mb-2">Catatan Item</label>
                                    <textarea x-model="item.notes"
                                              class="input w-full text-sm"
                                              rows="2"
                                              placeholder="Catatan khusus untuk item ini (opsional)"></textarea>
                                </div>
                            </div>
                        </template>

                        <div x-show="order.items.length === 0" class="text-center py-8 text-muted border-2 border-dashed border-border rounded-lg">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <p>Belum ada item yang ditambahkan</p>
                            <button type="button" @click="addItem" class="btn btn-sm btn-primary mt-2">
                                Tambah Item Pertama
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="card p-4 md:p-6">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Pengiriman</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Metode Pengiriman</label>
                            <select x-model="order.shipping_method" class="input w-full h-12 text-base">
                                <option value="">Pilih Metode</option>
                                <option value="pickup">Ambil Sendiri</option>
                                <option value="delivery">Antar Langsung</option>
                                <option value="courier">Kurir</option>
                                <option value="cargo">Cargo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Biaya Pengiriman</label>
                            <input type="number"
                                   x-model="order.shipping_cost"
                                   @input="calculateTotal"
                                   class="input w-full h-12 text-base"
                                   placeholder="0"
                                   min="0"
                                   step="1000">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Nomor Resi</label>
                            <input type="text"
                                   x-model="order.tracking_number"
                                   class="input w-full h-12 text-base"
                                   placeholder="Nomor resi pengiriman">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Estimasi Tiba</label>
                            <input type="date"
                                   x-model="order.estimated_delivery"
                                   class="input w-full h-12 text-base">
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="card p-4 md:p-6">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Catatan Pesanan</h3>
                    <textarea x-model="order.notes"
                              class="input w-full text-base min-h-[120px]"
                              rows="5"
                              placeholder="Catatan tambahan untuk pesanan ini..."></textarea>
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

                <!-- Additional Settings -->
                <div class="card p-4 md:p-6">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Pengaturan Tambahan</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Diskon Global (%)</label>
                            <input type="number"
                                   x-model="order.global_discount_percentage"
                                   @input="calculateTotal"
                                   class="input w-full"
                                   placeholder="0"
                                   min="0"
                                   max="100"
                                   step="0.1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Pajak (%)</label>
                            <input type="number"
                                   x-model="order.tax_percentage"
                                   @input="calculateTotal"
                                   class="input w-full"
                                   placeholder="0"
                                   min="0"
                                   max="100"
                                   step="0.1">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card p-4 md:p-6">
                    <div class="space-y-3">
                        <button type="submit"
                                class="btn btn-primary w-full h-12 text-base touch-manipulation"
                                :disabled="loading">
                            <span x-show="!loading">Update Pesanan</span>
                            <span x-show="loading" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                        <button type="button"
                                @click="resetForm"
                                class="btn btn-outline w-full h-12 text-base touch-manipulation">
                            Reset Form
                        </button>
                        <button type="button"
                                @click="deleteOrder"
                                class="btn btn-danger w-full h-12 text-base touch-manipulation">
                            Hapus Pesanan
                        </button>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card p-4 md:p-6">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Aksi Cepat</h3>
                    <div class="space-y-2">
                        <button type="button" @click="duplicateOrder" class="btn btn-sm btn-outline w-full">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Duplikasi Pesanan
                        </button>
                        <button type="button" @click="sendInvoice" class="btn btn-sm btn-outline w-full">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Kirim Invoice
                        </button>
                        <button type="button" @click="viewCustomer" class="btn btn-sm btn-outline w-full">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Lihat Pelanggan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function editOrder() {
    return {
        loading: false,
        originalData: {},
        products: [],
        order: {
            id: {{ $id ?? 1 }}, // This would come from the route parameter
            order_number: '',
            status: '',
            payment_status: 'pending',
            required_date: '',
            sales_person: '',
            payment_method: '',
            customer_name: '',
            customer_phone: '',
            customer_email: '',
            customer_type: 'retail',
            shipping_address: '',
            shipping_method: '',
            shipping_cost: 0,
            tracking_number: '',
            estimated_delivery: '',
            global_discount_percentage: 0,
            tax_percentage: 0,
            subtotal: 0,
            discount_amount: 0,
            tax_amount: 0,
            total_amount: 0,
            notes: '',
            items: []
        },

        async init() {
            await this.loadProducts();
            await this.loadData();
        },

        async loadProducts() {
            try {
                const response = await fetch('/api/products');
                const result = await response.json();
                
                if (result.success) {
                    this.products = result.data;
                }
            } catch (error) {
                console.error('Error loading products:', error);
            }
        },

        async loadData() {
            try {
                const response = await fetch(`/api/orders/${this.order.id}`);
                const result = await response.json();
                
                if (result.success) {
                    this.order = { ...this.order, ...result.data };
                    this.originalData = { ...this.order };
                    this.calculateTotal();
                } else {
                    alert('Gagal memuat data: ' + result.message);
                }
            } catch (error) {
                console.error('Error loading data:', error);
                alert('Terjadi kesalahan saat memuat data');
            }
        },

        async submitForm() {
            this.loading = true;
            
            try {
                const response = await fetch(`/api/orders/${this.order.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify(this.order)
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('Pesanan berhasil diperbarui!');
                    this.originalData = { ...this.order };
                } else {
                    alert('Gagal memperbarui pesanan: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
            } finally {
                this.loading = false;
            }
        },

        addItem() {
            this.order.items.push({
                product_id: '',
                product_name: '',
                product_sku: '',
                unit_price: 0,
                quantity: 1,
                discount_percentage: 0,
                subtotal: 0,
                notes: ''
            });
        },

        removeItem(index) {
            this.order.items.splice(index, 1);
            this.calculateTotal();
        },

        updateItemProduct(index) {
            const item = this.order.items[index];
            const product = this.products.find(p => p.id == item.product_id);
            
            if (product) {
                item.product_name = product.name;
                item.product_sku = product.sku;
                item.unit_price = product.selling_price || 0;
                this.calculateItemSubtotal(index);
            }
        },

        calculateItemSubtotal(index) {
            const item = this.order.items[index];
            const baseAmount = item.unit_price * item.quantity;
            const discountAmount = baseAmount * (item.discount_percentage / 100);
            item.subtotal = baseAmount - discountAmount;
            this.calculateTotal();
        },

        calculateTotal() {
            // Calculate subtotal from items
            this.order.subtotal = this.order.items.reduce((sum, item) => {
                return sum + (item.subtotal || 0);
            }, 0);

            // Calculate global discount
            this.order.discount_amount = this.order.subtotal * (this.order.global_discount_percentage / 100);

            // Calculate tax
            const taxableAmount = this.order.subtotal - this.order.discount_amount;
            this.order.tax_amount = taxableAmount * (this.order.tax_percentage / 100);

            // Calculate total
            this.order.total_amount = this.order.subtotal - this.order.discount_amount + this.order.tax_amount + (this.order.shipping_cost || 0);
        },

        resetForm() {
            if (confirm('Apakah Anda yakin ingin mereset form ke data asli?')) {
                this.order = { ...this.originalData };
                this.calculateTotal();
            }
        },

        async deleteOrder() {
            if (!confirm('Apakah Anda yakin ingin menghapus pesanan ini? Tindakan ini tidak dapat dibatal
kan.')) {
                return;
            }

            try {
                const response = await fetch(`/api/orders/${this.order.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('Pesanan berhasil dihapus!');
                    window.location.href = '/orders';
                } else {
                    alert('Gagal menghapus pesanan: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus data');
            }
        },

        viewOrder() {
            window.location.href = `/orders/${this.order.id}`;
        },

        printOrder() {
            window.open(`/orders/${this.order.id}/print`, '_blank');
        },

        duplicateOrder() {
            if (confirm('Apakah Anda yakin ingin menduplikasi pesanan ini?')) {
                window.location.href = `/orders/create?duplicate=${this.order.id}`;
            }
        },

        sendInvoice() {
            if (confirm('Kirim invoice ke pelanggan?')) {
                alert('Invoice berhasil dikirim!');
            }
        },

        viewCustomer() {
            window.location.href = `/customers/${this.order.customer_id}`;
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