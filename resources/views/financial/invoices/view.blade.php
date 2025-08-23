@extends('layouts.dashboard')

@section('title', 'Detail Invoice')

@section('content')
<div x-data="invoiceView({{ $id }})" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('invoices.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Invoice</h1>
            </div>
            <p class="text-gray-600 dark:text-gray-400" x-text="invoice.invoice_number"></p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button @click="printInvoice" class="btn-secondary">
                <i class="fas fa-print mr-2"></i>
                Cetak
            </button>
            <button @click="sendInvoice" class="btn-secondary">
                <i class="fas fa-paper-plane mr-2"></i>
                Kirim Email
            </button>
            <button @click="editInvoice" class="btn-primary">
                <i class="fas fa-edit mr-2"></i>
                Edit Invoice
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Invoice Content -->
    <div x-show="!loading && invoice" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <!-- Invoice Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-2xl font-bold mb-2">INVOICE</h2>
                    <div class="space-y-1">
                        <p class="text-blue-100">Nomor: <span class="text-white font-medium" x-text="invoice.invoice_number"></span></p>
                        <p class="text-blue-100">Tanggal: <span class="text-white font-medium" x-text="formatDate(invoice.invoice_date)"></span></p>
                        <p class="text-blue-100">Jatuh Tempo: <span class="text-white font-medium" x-text="formatDate(invoice.due_date)"></span></p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="mb-4">
                        <span :class="getStatusClass(invoice.status)" class="px-4 py-2 rounded-full text-sm font-semibold" x-text="getStatusText(invoice.status)"></span>
                    </div>
                    <div class="text-right">
                        <p class="text-blue-100 text-sm">Total Amount</p>
                        <p class="text-3xl font-bold" x-text="formatCurrency(invoice.total_amount)"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company & Customer Info -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- From -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Dari:</h3>
                    <div class="text-gray-600 dark:text-gray-400">
                        <p class="font-medium text-gray-900 dark:text-white">PT. MDagang Indonesia</p>
                        <p>Jl. Sudirman No. 123</p>
                        <p>Jakarta Pusat, DKI Jakarta 10220</p>
                        <p>Telp: +62 21 1234 5678</p>
                        <p>Email: info@mdagang.com</p>
                        <p>NPWP: 01.234.567.8-901.000</p>
                    </div>
                </div>

                <!-- To -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Kepada:</h3>
                    <div class="text-gray-600 dark:text-gray-400">
                        <p class="font-medium text-gray-900 dark:text-white" x-text="invoice.customer_name"></p>
                        <p x-text="invoice.customer_address || 'Alamat tidak tersedia'"></p>
                        <p x-text="invoice.customer_phone || ''"></p>
                        <p x-text="invoice.customer_email"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Item</h3>
            
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Deskripsi</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-900 dark:text-white">Qty</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-900 dark:text-white">Harga Satuan</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-900 dark:text-white">Diskon</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-900 dark:text-white">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in invoice.items" :key="item.id">
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-4 px-4 text-gray-900 dark:text-white" x-text="item.description"></td>
                                <td class="py-4 px-4 text-center text-gray-600 dark:text-gray-400" x-text="item.quantity"></td>
                                <td class="py-4 px-4 text-right text-gray-600 dark:text-gray-400" x-text="formatCurrency(item.unit_price)"></td>
                                <td class="py-4 px-4 text-right text-gray-600 dark:text-gray-400" x-text="item.discount + '%'"></td>
                                <td class="py-4 px-4 text-right font-medium text-gray-900 dark:text-white" x-text="formatCurrency(item.total)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-4">
                <template x-for="item in invoice.items" :key="item.id">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2" x-text="item.description"></h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Qty:</span>
                                <span class="text-gray-900 dark:text-white ml-1" x-text="item.quantity"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Harga:</span>
                                <span class="text-gray-900 dark:text-white ml-1" x-text="formatCurrency(item.unit_price)"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Diskon:</span>
                                <span class="text-gray-900 dark:text-white ml-1" x-text="item.discount + '%'"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Total:</span>
                                <span class="font-medium text-gray-900 dark:text-white ml-1" x-text="formatCurrency(item.total)"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Invoice Summary -->
        <div class="bg-gray-50 dark:bg-gray-700 p-6">
            <div class="max-w-md ml-auto">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                        <span class="text-gray-900 dark:text-white font-medium" x-text="formatCurrency(invoice.subtotal)"></span>
                    </div>
                    <div class="flex justify-between" x-show="invoice.total_discount > 0">
                        <span class="text-gray-600 dark:text-gray-400">Total Diskon:</span>
                        <span class="text-red-600 dark:text-red-400 font-medium" x-text="'-' + formatCurrency(invoice.total_discount)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">PPN (11%):</span>
                        <span class="text-gray-900 dark:text-white font-medium" x-text="formatCurrency(invoice.tax_amount)"></span>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-3">
                        <div class="flex justify-between">
                            <span class="text-lg font-bold text-gray-900 dark:text-white">Total:</span>
                            <span class="text-lg font-bold text-gray-900 dark:text-white" x-text="formatCurrency(invoice.total_amount)"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div x-show="invoice.notes" class="p-6 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Catatan</h3>
            <p class="text-gray-600 dark:text-gray-400" x-text="invoice.notes"></p>
        </div>

        <!-- Payment Info -->
        <div class="bg-blue-50 dark:bg-blue-900 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Informasi Pembayaran</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div>
                    <p class="font-medium text-gray-900 dark:text-white mb-2">Bank BCA</p>
                    <p class="text-gray-600 dark:text-gray-400">No. Rekening: 1234567890</p>
                    <p class="text-gray-600 dark:text-gray-400">A/N: PT. MDagang Indonesia</p>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white mb-2">Bank Mandiri</p>
                    <p class="text-gray-600 dark:text-gray-400">No. Rekening: 0987654321</p>
                    <p class="text-gray-600 dark:text-gray-400">A/N: PT. MDagang Indonesia</p>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">
                Mohon sertakan nomor invoice sebagai keterangan transfer
            </p>
        </div>
    </div>

    <!-- Error State -->
    <div x-show="!loading && !invoice" class="text-center py-12">
        <i class="fas fa-exclamation-triangle text-red-400 text-6xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Invoice tidak ditemukan</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-4">Invoice yang Anda cari tidak dapat ditemukan.</p>
        <a href="{{ route('invoices.index') }}" class="btn-primary">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Daftar Invoice
        </a>
    </div>
</div>

<script>
function invoiceView(invoiceId) {
    return {
        invoice: null,
        loading: true,

        init() {
            this.loadInvoice();
        },

        async loadInvoice() {
            this.loading = true;
            try {
                const response = await fetch(`/api/invoices/${invoiceId}`);
                const data = await response.json();
                
                if (data.success) {
                    this.invoice = data.data;
                } else {
                    this.invoice = null;
                }
            } catch (error) {
                console.error('Error loading invoice:', error);
                this.invoice = null;
            } finally {
                this.loading = false;
            }
        },

        printInvoice() {
            window.open(`/invoices/${this.invoice.id}/print`, '_blank');
        },

        async sendInvoice() {
            try {
                const response = await fetch(`/api/invoices/${this.invoice.id}/send`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    this.$store.notifications.add('success', 'Invoice berhasil dikirim');
                    this.loadInvoice(); // Reload to update status
                } else {
                    this.$store.notifications.add('error', data.message || 'Gagal mengirim invoice');
                }
            } catch (error) {
                console.error('Error sending invoice:', error);
                this.$store.notifications.add('error', 'Gagal mengirim invoice');
            }
        },

        editInvoice() {
            window.location.href = `/invoices/${this.invoice.id}/edit`;
        },

        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        formatCurrency(amount) {
            if (!amount) return 'Rp 0';
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },

        getStatusClass(status) {
            const classes = {
                'draft': 'bg-gray-100 text-gray-800',
                'sent': 'bg-blue-100 text-blue-800',
                'paid': 'bg-green-100 text-green-800',
                'overdue': 'bg-red-100 text-red-800',
                'cancelled': 'bg-gray-100 text-gray-800'
            };
            return classes[status] || classes['draft'];
        },

        getStatusText(status) {
            const texts = {
                'draft': 'Draft',
                'sent': 'Terkirim',
                'paid': 'Lunas',
                'overdue': 'Overdue',
                'cancelled': 'Dibatalkan'
            };
            return texts[status] || 'Draft';
        }
    }
}
</script>
@endsection