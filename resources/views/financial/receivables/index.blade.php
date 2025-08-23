@extends('layouts.dashboard')

@section('title', 'Piutang Dagang')

@section('content')
<div x-data="receivableManager()" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-foreground">Piutang Dagang</h1>
            <p class="text-muted">Kelola piutang dari pelanggan</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button @click="exportData" class="btn-secondary">
                <i class="fas fa-download mr-2"></i>
                Ekspor Data
            </button>
            <button @click="openCreateModal" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Tambah Piutang
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-file-invoice-dollar text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted">Total Piutang</p>
                    <p class="text-2xl font-bold text-foreground" x-text="formatCurrency(stats.total_receivables)"></p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted">Sudah Dibayar</p>
                    <p class="text-2xl font-bold text-foreground" x-text="formatCurrency(stats.paid_receivables)"></p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted">Belum Jatuh Tempo</p>
                    <p class="text-2xl font-bold text-foreground" x-text="formatCurrency(stats.pending_receivables)"></p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted">Overdue</p>
                    <p class="text-2xl font-bold text-foreground" x-text="formatCurrency(stats.overdue_receivables)"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Cari Piutang</label>
                <input type="text" x-model="filters.search" @input="filterReceivables"
                       placeholder="Nomor invoice, pelanggan..."
                       class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                <select x-model="filters.status" @change="filterReceivables"
                        class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                    <option value="">Semua Status</option>
                    <option value="outstanding">Outstanding</option>
                    <option value="partial">Sebagian Dibayar</option>
                    <option value="paid">Lunas</option>
                    <option value="overdue">Overdue</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Pelanggan</label>
                <select x-model="filters.customer" @change="filterReceivables"
                        class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                    <option value="">Semua Pelanggan</option>
                    <option value="PT. Maju Jaya">PT. Maju Jaya</option>
                    <option value="CV. Berkah Sejahtera">CV. Berkah Sejahtera</option>
                    <option value="Toko Sumber Rezeki">Toko Sumber Rezeki</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Dari Tanggal</label>
                <input type="date" x-model="filters.date_from" @change="filterReceivables"
                       class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Sampai Tanggal</label>
                <input type="date" x-model="filters.date_to" @change="filterReceivables"
                       class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
            </div>
        </div>
    </div>

    <!-- Receivables List -->
    <div class="card">
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-surface">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Invoice
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Pelanggan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Tanggal Invoice
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Jatuh Tempo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Jumlah
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Sisa
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-border">
                    <template x-for="receivable in filteredReceivables" :key="receivable.id">
                        <tr class="hover:bg-surface">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-foreground" x-text="receivable.invoice_number"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-foreground" x-text="receivable.customer_name"></div>
                                <div class="text-sm text-muted" x-text="receivable.customer_email"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground" x-text="formatDate(receivable.invoice_date)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground" x-text="formatDate(receivable.due_date)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground" x-text="formatCurrency(receivable.total_amount)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground" x-text="formatCurrency(receivable.outstanding_amount)"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getStatusClass(receivable.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" x-text="getStatusText(receivable.status)"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button @click="viewReceivable(receivable)" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button @click="recordPayment(receivable)" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-money-bill"></i>
                                    </button>
                                    <button @click="sendReminder(receivable)" class="text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-bell"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden">
            <template x-for="receivable in filteredReceivables" :key="receivable.id">
                <div class="p-4 border-b border-border">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="text-lg font-medium text-foreground" x-text="receivable.invoice_number"></h3>
                            <p class="text-sm text-muted" x-text="receivable.customer_name"></p>
                        </div>
                        <span :class="getStatusClass(receivable.status)" class="px-2 py-1 text-xs font-semibold rounded-full" x-text="getStatusText(receivable.status)"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                        <div>
                            <span class="text-muted">Tanggal:</span>
                            <span class="text-foreground ml-1" x-text="formatDate(receivable.invoice_date)"></span>
                        </div>
                        <div>
                            <span class="text-muted">Jatuh Tempo:</span>
                            <span class="text-foreground ml-1" x-text="formatDate(receivable.due_date)"></span>
                        </div>
                        <div>
                            <span class="text-muted">Total:</span>
                            <span class="text-foreground ml-1" x-text="formatCurrency(receivable.total_amount)"></span>
                        </div>
                        <div>
                            <span class="text-muted">Sisa:</span>
                            <span class="font-bold text-foreground ml-1" x-text="formatCurrency(receivable.outstanding_amount)"></span>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button @click="viewReceivable(receivable)" class="p-2 text-blue-600 hover:bg-blue-50 rounded">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button @click="recordPayment(receivable)" class="p-2 text-green-600 hover:bg-green-50 rounded">
                            <i class="fas fa-money-bill"></i>
                        </button>
                        <button @click="sendReminder(receivable)" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded">
                            <i class="fas fa-bell"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="filteredReceivables.length === 0" class="text-center py-12">
            <i class="fas fa-file-invoice-dollar text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-foreground mb-2">Tidak ada piutang</h3>
            <p class="text-muted mb-4">Belum ada piutang yang tercatat atau sesuai dengan filter.</p>
            <button @click="openCreateModal" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Tambah Piutang Pertama
            </button>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between">
        <div class="text-sm text-muted">
            Menampilkan <span x-text="((currentPage - 1) * perPage) + 1"></span> sampai 
            <span x-text="Math.min(currentPage * perPage, totalReceivables)"></span> dari 
            <span x-text="totalReceivables"></span> piutang
        </div>
        <div class="flex space-x-2">
            <button @click="previousPage" :disabled="currentPage === 1" 
                    class="px-3 py-2 text-sm font-medium text-muted bg-surface border border-border rounded-md hover:bg-surface disabled:opacity-50 disabled:cursor-not-allowed">
                Sebelumnya
            </button>
            <button @click="nextPage" :disabled="currentPage === lastPage" 
                    class="px-3 py-2 text-sm font-medium text-muted bg-surface border border-border rounded-md hover:bg-surface disabled:opacity-50 disabled:cursor-not-allowed">
                Selanjutnya
            </button>
        </div>
    </div>

    <!-- Payment Modal -->
    <div x-show="showPaymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-cloak>
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-lg shadow-lg rounded-md card">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-foreground">Catat Pembayaran</h3>
                <button @click="closePaymentModal" class="text-muted hover:text-foreground">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form @submit.prevent="savePayment" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Invoice</label>
                    <input type="text" :value="selectedReceivable?.invoice_number" readonly
                           class="w-full px-3 py-2 border border-border rounded-md shadow-sm bg-surface text-foreground">
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Sisa Piutang</label>
                    <input type="text" :value="formatCurrency(selectedReceivable?.outstanding_amount)" readonly
                           class="w-full px-3 py-2 border border-border rounded-md shadow-sm bg-surface text-foreground">
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Jumlah Pembayaran *</label>
                    <input type="number" x-model="paymentForm.amount" required min="0" step="0.01" :max="selectedReceivable?.outstanding_amount"
                           class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Tanggal Pembayaran *</label>
                    <input type="date" x-model="paymentForm.payment_date" required
                           class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Metode Pembayaran *</label>
                    <select x-model="paymentForm.method" required
                            class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                        <option value="">Pilih Metode</option>
                        <option value="cash">Tunai</option>
                        <option value="bank_transfer">Transfer Bank</option>
                        <option value="credit_card">Kartu Kredit</option>
                        <option value="e_wallet">E-Wallet</option>
                        <option value="check">Cek</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Catatan</label>
                    <textarea x-model="paymentForm.notes" rows="3"
                              class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground"
                              placeholder="Catatan pembayaran..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="closePaymentModal" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary" :disabled="loading">
                        <span x-show="loading" class="mr-2">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function receivableManager() {
    return {
        receivables: [],
        filteredReceivables: [],
        stats: {
            total_receivables: 0,
            paid_receivables: 0,
            pending_receivables: 0,
            overdue_receivables: 0
        },
        filters: {
            search: '',
            status: '',
            customer: '',
            date_from: '',
            date_to: ''
        },
        showPaymentModal: false,
        selectedReceivable: null,
        loading: false,
        currentPage: 1,
        perPage: 25,
        totalReceivables: 0,
        lastPage: 1,
        paymentForm: {
            amount: '',
            payment_date: '',
            method: '',
            notes: ''
        },

        init() {
            this.loadReceivables();
            this.loadStats();
        },

        async loadReceivables() {
            this.loading = true;
            try {
                const response = await fetch('/api/receivables');
                const data = await response.json();
                this.receivables = data.data;
                this.filteredReceivables = [...this.receivables];
                this.totalReceivables = data.total;
                this.lastPage = data.last_page;
                this.currentPage = data.current_page;
            } catch (error) {
                console.error('Error loading receivables:', error);
                this.$store.notifications.add('error', 'Gagal memuat data piutang');
            } finally {
                this.loading = false;
            }
        },

        async loadStats() {
            try {
                const response = await fetch('/api/receivables/stats');
                const data = await response.json();
                this.stats = data;
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        },

        filterReceivables() {
            let filtered = [...this.receivables];

            if (this.filters.search) {
                const search = this.filters.search.toLowerCase();
                filtered = filtered.filter(receivable => 
                    receivable.invoice_number.toLowerCase().includes(search) ||
                    receivable.customer_name.toLowerCase().includes(search)
                );
            }

            if (this.filters.status) {
                filtered = filtered.filter(receivable => receivable.status === this.filters.status);
            }

            if (this.filters.customer) {
                filtered = filtered.filter(receivable => receivable.customer_name === this.filters.customer);
            }

            if (this.filters.date_from) {
                filtered = filtered.filter(receivable => receivable.invoice_date >= this.filters.date_from);
            }

            if (this.filters.date_to) {
                filtered = filtered.filter(receivable => receivable.invoice_date <= this.filters.date_to);
            }

            this.filteredReceivables = filtered;
        },

        recordPayment(receivable) {
            this.selectedReceivable = receivable;
            this.paymentForm = {
                amount: receivable.outstanding_amount,
                payment_date: new Date().toISOString().split('T')[0],
                method: '',
                notes: ''
            };
            this.showPaymentModal = true;
        },

        closePaymentModal() {
            this.showPaymentModal = false;
            this.selectedReceivable = null;
        },

        async savePayment() {
            this.loading = true;
            try {
                const response = await fetch(`/api/receivables/${this.selectedReceivable.id}/payment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.paymentForm)
                });

                const data = await response.json();
                
                if (data.success) {
                    this.$store.notifications.add('success', data.message);
                    this.closePaymentModal();
                    this.loadReceivables();
                    this.loadStats();
                } else {
                    this.$store.notifications.add('error', data.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error('Error saving payment:', error);
                this.$store.notifications.add('error', 'Gagal menyimpan pembayaran');
            } finally {
                this.loading = false;
            }
        },

        viewReceivable(receivable) {
            window.open(`/invoices/${receivable.invoice_id}/view`, '_blank');
        },

        async sendReminder(receivable) {
            try {
                const response = await fetch(`/api/receivables/${receivable.id}/reminder`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    this.$store.notifications.add('success', 'Pengingat berhasil dikirim');
                } else {
                    this.$store.notifications.add('error', data.message || 'Gagal mengirim pengingat');
                }
            } catch (error) {
                console.error('Error sending reminder:', error);
                this.$store.notifications.add('error', 'Gagal mengirim pengingat');
            }
        },

        async exportData() {
            try {
                const response = await fetch('/api/receivables/export');
                const data = await response.json();
                
                if (data.success) {
                    this.$store.notifications.add('success', 'Data berhasil diekspor');
                } else {
                    this.$store.notifications.add('error', 'Gagal mengekspor data');
                }
            } catch (error) {
                console.error('Error exporting data:', error);
                this.$store.notifications.add('error', 'Gagal mengekspor data');
            }
        },

        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.loadReceivables();
            }
        },

        nextPage() {
            if (this.currentPage < this.lastPage) {
                this.currentPage++;
                this.loadReceivables();
            }
        },

        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('id-ID');
        },

        formatCurrency(amount) {
            if (!amount) return 'Rp 0';
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        },

        getStatusClass(status) {
            const classes = {
                'outstanding': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                'partial': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                'paid': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                'overdue': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
            };
            return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
        },

        getStatusText(status) {
            const texts = {
                'outstanding': 'Outstanding',
                'partial': 'Sebagian',
                'paid': 'Lunas',
                'overdue': 'Overdue'
            };
            return texts[status] || status;
        },

        openCreateModal() {
            // Redirect to invoice creation page
            window.location.href = '/invoices/create';
        }
    }
}
</script>
@endsection