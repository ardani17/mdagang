@extends('layouts.dashboard')

@section('title', 'Detail Purchase Order')
@section('page-title', 'Detail Purchase Order')

@section('content')
<div x-data="purchaseOrderDetail()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Detail Purchase Order</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed" x-text="purchaseOrder.po_number || 'Loading...'"></p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.raw-materials.purchasing') }}"
               class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Daftar PO
            </a>
            <button @click="printPO()" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print PO
            </button>
        </div>
    </div>

    <!-- Purchase Order Info Card -->
    <div class="card p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-muted mb-1">No. Purchase Order</label>
                <p class="text-lg font-semibold text-foreground" x-text="purchaseOrder.po_number || '-'"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-muted mb-1">Status</label>
                <span :class="getStatusColor(purchaseOrder.status || '')"
                      class="inline-flex px-3 py-1 text-sm font-medium rounded-full"
                      x-text="getStatusText(purchaseOrder.status || '')"></span>
            </div>
            <div>
                <label class="block text-sm font-medium text-muted mb-1">Tanggal Order</label>
                <p class="text-foreground" x-text="formatDate(purchaseOrder.order_date)"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-muted mb-1">Expected Date</label>
                <p class="text-foreground" x-text="formatDate(purchaseOrder.expected_date)"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-muted mb-1">Supplier</label>
                <p class="text-foreground font-medium" x-text="purchaseOrder.supplier?.name || '-'"></p>
                <p class="text-sm text-muted" x-text="purchaseOrder.supplier?.contact_person || ''"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-muted mb-1">Total Amount</label>
                <p class="text-lg font-bold text-foreground" x-text="formatCurrency(purchaseOrder.total_amount)"></p>
            </div>
        </div>
        
        <div x-show="purchaseOrder.notes" class="mt-4 pt-4 border-t border-border">
            <label class="block text-sm font-medium text-muted mb-1">Notes</label>
            <p class="text-foreground" x-text="purchaseOrder.notes"></p>
        </div>
    </div>

    <!-- Items Table -->
    <div class="card">
        <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold text-foreground">Items</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-border/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Unit Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Received</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="item in purchaseOrder.items || []" :key="item.id">
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-foreground" x-text="item.item_name || item.raw_material?.name || '-'"></div>
                                <div class="text-sm text-muted" x-text="item.item_code || item.raw_material?.code || '-'"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground">
                                <span x-text="item.quantity"></span>
                                <span class="text-muted" x-text="item.unit || 'pcs'"></span>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="formatCurrency(item.unit_price)"></td>
                            <td class="px-6 py-4 text-sm font-semibold text-foreground" x-text="formatCurrency(item.total_price)"></td>
                            <td class="px-6 py-4 text-sm text-foreground">
                                <span x-text="item.received_quantity || 0"></span>
                                <span class="text-muted" x-text="'/ ' + item.quantity"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <!-- Totals -->
        <div class="p-6 border-t border-border bg-border/20">
            
            <div class="flex justify-end">
                <div class="w-full max-w-sm space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-muted">Subtotal:</span>
                        <span class="text-foreground" x-text="formatCurrency(purchaseOrder.subtotal)"></span>
                    </div>
                    <div x-show="parseFloat(purchaseOrder.tax_amount || 0) > 0" class="flex justify-between text-sm">
                        <span class="text-muted">Pajak:</span>
                        <span class="text-foreground" x-text="formatCurrency(purchaseOrder.tax_amount)"></span>
                    </div>
                    <div x-show="parseFloat(purchaseOrder.shipping_cost || 0) > 0" class="flex justify-between text-sm">
                        <span class="text-muted">Ongkos Kirim:</span>
                        <span class="text-foreground" x-text="formatCurrency(purchaseOrder.shipping_cost)"></span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t border-border pt-2">
                        <span class="text-foreground">Total:</span>
                        <span class="text-foreground" x-text="formatCurrency(purchaseOrder.total_amount)"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end space-x-3">
        <button @click="sendPO()"
                x-show="purchaseOrder.status === 'draft'"
                class="btn btn-primary touch-manipulation">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
            Kirim PO
        </button>
        
        <button @click="receiveGoods()"
                x-show="purchaseOrder.status === 'sent' || purchaseOrder.status === 'confirmed' || purchaseOrder.status === 'partial'"
                class="btn btn-success touch-manipulation">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            Terima Barang
        </button>
        
        <button @click="cancelPO()"
                x-show="purchaseOrder.status === 'draft' || purchaseOrder.status === 'sent'"
                class="btn btn-danger touch-manipulation">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Batalkan PO
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
function purchaseOrderDetail() {
    return {
        purchaseOrder: {},
        loading: false,

        init() {
            this.loadPurchaseOrder();
        },

        async loadPurchaseOrder() {
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get('id');
            
            if (!id) {
                alert('Purchase Order ID tidak ditemukan');
                window.location.href = '{{ route("manufacturing.raw-materials.purchasing") }}';
                return;
            }

            this.loading = true;
            try {
                const response = await fetch(`/ajax/purchase-orders/${id}`);
                const result = await response.json();
                
                console.log('API Response:', result); // Debug log
                
                if (result.success) {
                    // The API returns data in result.data.purchase_order
                    this.purchaseOrder = result.data?.purchase_order || result.purchase_order || {};
                    console.log('Loaded PO:', this.purchaseOrder); // Debug log
                } else {
                    alert('Gagal memuat data purchase order: ' + result.message);
                }
            } catch (error) {
                console.error('Error loading purchase order:', error);
                alert('Terjadi kesalahan saat memuat data');
            } finally {
                this.loading = false;
            }
        },

        async sendPO() {
            if (confirm(`Apakah Anda yakin ingin mengirim PO ${this.purchaseOrder.po_number}?`)) {
                try {
                    const response = await fetch(`/ajax/purchase-orders/${this.purchaseOrder.id}/send`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Purchase order berhasil dikirim');
                        this.loadPurchaseOrder();
                    } else {
                        alert('Gagal mengirim purchase order: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error sending PO:', error);
                    alert('Terjadi kesalahan saat mengirim purchase order');
                }
            }
        },

        receiveGoods() {
            // For now, just use quick receive
            this.quickReceive();
        },

        async quickReceive() {
            if (confirm(`Apakah Anda yakin ingin menandai PO ${this.purchaseOrder.po_number} sebagai diterima?`)) {
                try {
                    const response = await fetch(`/ajax/purchase-orders/${this.purchaseOrder.id}/quick-receive`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Purchase order berhasil diterima dan stok telah diperbarui');
                        this.loadPurchaseOrder();
                    } else {
                        alert('Gagal menerima purchase order: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error receiving PO:', error);
                    alert('Terjadi kesalahan saat menerima purchase order');
                }
            }
        },

        printPO() {
            if (this.purchaseOrder.id) {
                window.open(`/ajax/purchase-orders/${this.purchaseOrder.id}/print`, '_blank');
            }
        },

        async cancelPO() {
            if (confirm(`Apakah Anda yakin ingin membatalkan PO ${this.purchaseOrder.po_number}? Tindakan ini tidak dapat dibatalkan.`)) {
                try {
                    const response = await fetch(`/ajax/purchase-orders/${this.purchaseOrder.id}/cancel`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Purchase order berhasil dibatalkan');
                        this.loadPurchaseOrder();
                    } else {
                        alert('Gagal membatalkan purchase order: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error cancelling PO:', error);
                    alert('Terjadi kesalahan saat membatalkan purchase order');
                }
            }
        },

        getStatusColor(status) {
            const colors = {
                'draft': 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
                'sent': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                'confirmed': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                'partial': 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
                'received': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getStatusText(status) {
            const texts = {
                'draft': 'Draft',
                'sent': 'Terkirim',
                'confirmed': 'Dikonfirmasi',
                'partial': 'Sebagian Diterima',
                'received': 'Diterima',
                'cancelled': 'Dibatalkan'
            };
            return texts[status] || status;
        },

        formatDate(dateString) {
            if (!dateString) return '-';
            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '-';
                
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            } catch (error) {
                return '-';
            }
        },

        formatCurrency(amount) {
            if (amount === null || amount === undefined || isNaN(amount)) return 'Rp 0';
            try {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(amount);
            } catch (error) {
                return 'Rp 0';
            }
        }
    };
}
</script>
@endpush