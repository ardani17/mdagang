<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Print</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
            .print-break { page-break-after: always; }
        }
    </style>
</head>
<body class="bg-white">
    <div x-data="invoicePrint({{ $id }})" class="max-w-4xl mx-auto p-8">
        <!-- Print Controls -->
        <div class="no-print mb-6 flex justify-between items-center border-b pb-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Preview Invoice</h1>
                <p class="text-gray-600">Siap untuk dicetak</p>
            </div>
            <div class="flex gap-3">
                <button @click="window.close()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                    <i class="fas fa-times mr-2"></i>
                    Tutup
                </button>
                <button @click="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-print mr-2"></i>
                    Cetak
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>

        <!-- Invoice Content -->
        <div x-show="!loading && invoice" class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <!-- Invoice Header -->
            <div class="bg-blue-600 text-white p-8">
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <h1 class="text-4xl font-bold mb-4">INVOICE</h1>
                        <div class="space-y-2">
                            <p class="text-blue-100">Nomor: <span class="text-white font-semibold" x-text="invoice.invoice_number"></span></p>
                            <p class="text-blue-100">Tanggal: <span class="text-white font-semibold" x-text="formatDate(invoice.invoice_date)"></span></p>
                            <p class="text-blue-100">Jatuh Tempo: <span class="text-white font-semibold" x-text="formatDate(invoice.due_date)"></span></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="mb-6">
                            <span :class="getStatusClass(invoice.status)" class="px-4 py-2 rounded-full text-sm font-semibold" x-text="getStatusText(invoice.status)"></span>
                        </div>
                        <div>
                            <p class="text-blue-100 text-lg">Total Amount</p>
                            <p class="text-4xl font-bold" x-text="formatCurrency(invoice.total_amount)"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company & Customer Info -->
            <div class="p-8 border-b border-gray-200">
                <div class="grid grid-cols-2 gap-12">
                    <!-- From -->
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-4 border-b border-gray-200 pb-2">Dari:</h2>
                        <div class="text-gray-700 space-y-1">
                            <p class="text-xl font-bold text-gray-900">PT. MDagang Indonesia</p>
                            <p>Jl. Sudirman No. 123</p>
                            <p>Jakarta Pusat, DKI Jakarta 10220</p>
                            <p>Telp: +62 21 1234 5678</p>
                            <p>Email: info@mdagang.com</p>
                            <p>NPWP: 01.234.567.8-901.000</p>
                        </div>
                    </div>

                    <!-- To -->
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-4 border-b border-gray-200 pb-2">Kepada:</h2>
                        <div class="text-gray-700 space-y-1">
                            <p class="text-xl font-bold text-gray-900" x-text="invoice.customer_name"></p>
                            <p x-text="invoice.customer_address || 'Alamat tidak tersedia'"></p>
                            <p x-text="invoice.customer_phone || ''"></p>
                            <p x-text="invoice.customer_email"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6 border-b border-gray-200 pb-2">Detail Item</h2>
                
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-gray-300">
                            <th class="text-left py-4 px-2 font-bold text-gray-900">Deskripsi</th>
                            <th class="text-center py-4 px-2 font-bold text-gray-900 w-20">Qty</th>
                            <th class="text-right py-4 px-2 font-bold text-gray-900 w-32">Harga Satuan</th>
                            <th class="text-right py-4 px-2 font-bold text-gray-900 w-20">Diskon</th>
                            <th class="text-right py-4 px-2 font-bold text-gray-900 w-32">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in invoice.items" :key="item.id">
                            <tr class="border-b border-gray-200">
                                <td class="py-4 px-2 text-gray-900" x-text="item.description"></td>
                                <td class="py-4 px-2 text-center text-gray-700" x-text="item.quantity"></td>
                                <td class="py-4 px-2 text-right text-gray-700" x-text="formatCurrency(item.unit_price)"></td>
                                <td class="py-4 px-2 text-right text-gray-700" x-text="item.discount + '%'"></td>
                                <td class="py-4 px-2 text-right font-semibold text-gray-900" x-text="formatCurrency(item.total)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Invoice Summary -->
            <div class="bg-gray-50 p-8">
                <div class="max-w-md ml-auto">
                    <div class="space-y-4">
                        <div class="flex justify-between text-lg">
                            <span class="text-gray-700">Subtotal:</span>
                            <span class="font-semibold text-gray-900" x-text="formatCurrency(invoice.subtotal)"></span>
                        </div>
                        <div class="flex justify-between text-lg" x-show="invoice.total_discount > 0">
                            <span class="text-gray-700">Total Diskon:</span>
                            <span class="font-semibold text-red-600" x-text="'-' + formatCurrency(invoice.total_discount)"></span>
                        </div>
                        <div class="flex justify-between text-lg">
                            <span class="text-gray-700">PPN (11%):</span>
                            <span class="font-semibold text-gray-900" x-text="formatCurrency(invoice.tax_amount)"></span>
                        </div>
                        <div class="border-t-2 border-gray-300 pt-4">
                            <div class="flex justify-between text-2xl font-bold">
                                <span class="text-gray-900">TOTAL:</span>
                                <span class="text-gray-900" x-text="formatCurrency(invoice.total_amount)"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div x-show="invoice.notes" class="p-8 border-t border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Catatan</h2>
                <p class="text-gray-700 leading-relaxed" x-text="invoice.notes"></p>
            </div>

            <!-- Payment Info -->
            <div class="bg-blue-50 p-8 border-t border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Informasi Pembayaran</h2>
                <div class="grid grid-cols-2 gap-8">
                    <div class="bg-white p-4 rounded-lg border">
                        <h3 class="font-bold text-gray-900 mb-2">Bank BCA</h3>
                        <p class="text-gray-700">No. Rekening: <span class="font-semibold">1234567890</span></p>
                        <p class="text-gray-700">A/N: <span class="font-semibold">PT. MDagang Indonesia</span></p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border">
                        <h3 class="font-bold text-gray-900 mb-2">Bank Mandiri</h3>
                        <p class="text-gray-700">No. Rekening: <span class="font-semibold">0987654321</span></p>
                        <p class="text-gray-700">A/N: <span class="font-semibold">PT. MDagang Indonesia</span></p>
                    </div>
                </div>
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Penting:</strong> Mohon sertakan nomor invoice <span class="font-semibold" x-text="invoice.invoice_number"></span> sebagai keterangan transfer untuk mempercepat proses verifikasi pembayaran.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-8 border-t border-gray-200 text-center">
                <p class="text-gray-600 mb-2">Terima kasih atas kepercayaan Anda!</p>
                <p class="text-sm text-gray-500">Invoice ini dibuat secara otomatis oleh sistem MDagang</p>
                <p class="text-xs text-gray-400 mt-4">Dicetak pada: <span x-text="new Date().toLocaleString('id-ID')"></span></p>
            </div>
        </div>

        <!-- Error State -->
        <div x-show="!loading && !invoice" class="text-center py-12">
            <i class="fas fa-exclamation-triangle text-red-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Invoice tidak ditemukan</h3>
            <p class="text-gray-600 mb-4">Invoice yang Anda cari tidak dapat ditemukan.</p>
            <button @click="window.close()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                <i class="fas fa-times mr-2"></i>
                Tutup
            </button>
        </div>
    </div>

    <script>
    function invoicePrint(invoiceId) {
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
</body>
</html>