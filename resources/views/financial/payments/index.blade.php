@extends('layouts.dashboard')

@section('title', 'Manajemen Pembayaran')

@section('content')
<div x-data="paymentManager()" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-foreground">Manajemen Pembayaran</h1>
            <p class="text-muted">Kelola pembayaran masuk dan keluar</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button @click="exportData" class="btn-secondary">
                <i class="fas fa-download mr-2"></i>
                Ekspor Data
            </button>
            <button @click="openCreateModal" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Catat Pembayaran
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-arrow-down text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted">Pembayaran Masuk</p>
                    <p class="text-2xl font-bold text-foreground" x-text="formatCurrency(stats.total_received)"></p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-arrow-up text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted">Pembayaran Keluar</p>
                    <p class="text-2xl font-bold text-foreground" x-text="formatCurrency(stats.total_paid)"></p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-clock text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted">Pending</p>
                    <p class="text-2xl font-bold text-foreground" x-text="stats.pending_payments"></p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted">Saldo Bersih</p>
                    <p class="text-2xl font-bold text-foreground" x-text="formatCurrency(stats.net_balance)"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Cari Pembayaran</label>
                <input type="text" x-model="filters.search" @input="filterPayments"
                       placeholder="Nomor referensi, deskripsi..."
                       class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Tipe</label>
                <select x-model="filters.type" @change="filterPayments"
                        class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                    <option value="">Semua Tipe</option>
                    <option value="received">Pembayaran Masuk</option>
                    <option value="paid">Pembayaran Keluar</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                <select x-model="filters.status" @change="filterPayments"
                        class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                    <option value="">Semua Status</option>
                    <option value="completed">Selesai</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Gagal</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Dari Tanggal</label>
                <input type="date" x-model="filters.date_from" @change="filterPayments"
                       class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Sampai Tanggal</label>
                <input type="date" x-model="filters.date_to" @change="filterPayments"
                       class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
            </div>
        </div>
    </div>

    <!-- Payment List -->
    <div class="card">
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-surface">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Referensi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Tipe
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Deskripsi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Jumlah
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
                    <template x-for="payment in filteredPayments" :key="payment.id">
                        <tr class="hover:bg-surface">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-foreground" x-text="payment.reference_number"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getTypeClass(payment.type)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                    <i :class="payment.type === 'received' ? 'fas fa-arrow-down mr-1' : 'fas fa-arrow-up mr-1'"></i>
                                    <span x-text="getTypeText(payment.type)"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-foreground" x-text="payment.description"></div>
                                <div class="text-sm text-muted" x-text="payment.method"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground" x-text="formatDate(payment.payment_date)"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium" :class="payment.type === 'received' ? 'text-green-600' : 'text-red-600'" x-text="formatCurrency(payment.amount)"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getStatusClass(payment.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" x-text="getStatusText(payment.status)"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button @click="viewPayment(payment)" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button @click="editPayment(payment)" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="printReceipt(payment)" class="text-purple-600 hover:text-purple-900">
                                        <i class="fas fa-print"></i>
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
            <template x-for="payment in filteredPayments" :key="payment.id">
                <div class="p-4 border-b border-border">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="text-lg font-medium text-foreground" x-text="payment.reference_number"></h3>
                            <p class="text-sm text-muted" x-text="payment.description"></p>
                        </div>
                        <div class="flex flex-col items-end space-y-1">
                            <span :class="getStatusClass(payment.status)" class="px-2 py-1 text-xs font-semibold rounded-full" x-text="getStatusText(payment.status)"></span>
                            <span :class="getTypeClass(payment.type)" class="px-2 py-1 text-xs font-semibold rounded-full" x-text="getTypeText(payment.type)"></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                        <div>
                            <span class="text-muted">Tanggal:</span>
                            <span class="text-foreground ml-1" x-text="formatDate(payment.payment_date)"></span>
                        </div>
                        <div>
                            <span class="text-muted">Metode:</span>
                            <span class="text-foreground ml-1" x-text="payment.method"></span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-muted">Jumlah:</span>
                            <span class="text-lg font-bold ml-1" :class="payment.type === 'received' ? 'text-green-600' : 'text-red-600'" x-text="formatCurrency(payment.amount)"></span>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button @click="viewPayment(payment)" class="p-2 text-blue-600 hover:bg-blue-50 rounded">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button @click="editPayment(payment)" class="p-2 text-green-600 hover:bg-green-50 rounded">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button @click="printReceipt(payment)" class="p-2 text-purple-600 hover:bg-purple-50 rounded">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="filteredPayments.length === 0" class="text-center py-12">
            <i class="fas fa-credit-card text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-foreground mb-2">Tidak ada pembayaran</h3>
            <p class="text-muted mb-4">Belum ada pembayaran yang dicatat atau sesuai dengan filter.</p>
            <button @click="openCreateModal" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Catat Pembayaran Pertama
            </button>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between">
        <div class="text-sm text-muted">
            Menampilkan <span x-text="((currentPage - 1) * perPage) + 1"></span> sampai
            <span x-text="Math.min(currentPage * perPage, totalPayments)"></span> dari
            <span x-text="totalPayments"></span> pembayaran
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

    <!-- Create/Edit Modal -->
    <div x-show="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-cloak>
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md card">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-foreground" x-text="editingPayment ? 'Edit Pembayaran' : 'Catat Pembayaran Baru'"></h3>
                <button @click="closeModal" class="text-muted hover:text-foreground">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form @submit.prevent="savePayment" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Tipe Pembayaran *</label>
                        <select x-model="form.type" required
                                class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                            <option value="">Pilih Tipe</option>
                            <option value="received">Pembayaran Masuk</option>
                            <option value="paid">Pembayaran Keluar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Nomor Referensi *</label>
                        <input type="text" x-model="form.reference_number" required
                               class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Deskripsi *</label>
                    <input type="text" x-model="form.description" required
                           class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Jumlah *</label>
                        <input type="number" x-model="form.amount" required min="0" step="0.01"
                               class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Tanggal Pembayaran *</label>
                        <input type="date" x-model="form.payment_date" required
                               class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Metode Pembayaran *</label>
                        <select x-model="form.method" required
                                class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                            <option value="">Pilih Metode</option>
                            <option value="cash">Tunai</option>
                            <option value="bank_transfer">Transfer Bank</option>
                            <option value="credit_card">Kartu Kredit</option>
                            <option value="debit_card">Kartu Debit</option>
                            <option value="e_wallet">E-Wallet</option>
                            <option value="check">Cek</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Status *</label>
                        <select x-model="form.status" required
                                class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                            <option value="">Pilih Status</option>
                            <option value="completed">Selesai</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Gagal</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Catatan</label>
                    <textarea x-model="form.notes" rows="3"
                              class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground"
                              placeholder="Catatan tambahan untuk pembayaran..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="closeModal" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary" :disabled="loading">
                        <span x-show="loading" class="mr-2">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                        <span x-text="editingPayment ? 'Perbarui Pembayaran' : 'Simpan Pembayaran'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function paymentManager() {
    return {
        payments: [],
        filteredPayments: [],
        stats: {
            total_received: 0,
            total_paid: 0,
            pending_payments: 0,
            net_balance: 0
        },
        filters: {
            search: '',
            type: '',
            status: '',
            date_from: '',
            date_to: ''
        },
        showModal: false,
        editingPayment: null,
        loading: false,
        currentPage: 1,
        perPage: 25,
        totalPayments: 0,
        lastPage: 1,
        form: {
            type: '',
            reference_number: '',
            description: '',
            amount: '',
            payment_date: '',
            method: '',
            status: '',
            notes: ''
        },

        init() {
            this.loadPayments();
            this.loadStats();
        },

        async loadPayments() {
            this.loading = true;
            try {
                const response = await fetch('/api/payments');
                const data = await response.json();
                this.payments = data.data;
                this.filteredPayments = [...this.payments];
                this.totalPayments = data.total;
                this.lastPage = data.last_page;
                this.currentPage = data.current_page;
            } catch (error) {
                console.error('Error loading payments:', error);
                this.$store.notifications.add('error', 'Gagal memuat data pembayaran');
            } finally {
                this.loading = false;
            }
        },

        async loadStats() {
            try {
                const response = await fetch('/api/payments/stats');
                const data = await response.json();
                this.stats = data;
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        },

        filterPayments() {
            let filtered = [...this.payments];

            if (this.filters.search) {
                const search = this.filters.search.toLowerCase();
                filtered = filtered.filter(payment => 
                    payment.reference_number.toLowerCase().includes(search) ||
                    payment.description.toLowerCase().includes(search)
                );
            }

            if (this.filters.type) {
                filtered = filtered.filter(payment => payment.type === this.filters.type);
            }

            if (this.filters.status) {
                filtered = filtered.filter(payment => payment.status === this.filters.status);
            }

            if (this.filters.date_from) {
                filtered = filtered.filter(payment => payment.payment_date >= this.filters.date_from);
            }

            if (this.filters.date_to) {
                filtered = filtered.filter(payment => payment.payment_date <= this.filters.date_to);
            }

            this.filteredPayments = filtered;
        },

        openCreateModal() {
            this.editingPayment = null;
            this.resetForm();
            this.showModal = true;
        },

        editPayment(payment) {
            this.editingPayment = payment;
            this.form = {
                type: payment.type,
                reference_number: payment.reference_number,
                description: payment.description,
                amount: payment.amount,
                payment_date: payment.payment_date,
                method: payment.method,
                status: payment.status,
                notes: payment.notes
            };
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.editingPayment = null;
            this.resetForm();
        },

        resetForm() {
            this.form = {
                type: '',
                reference_number: this.generateReferenceNumber(),
                description: '',
                amount: '',
                payment_date: new Date().toISOString().split('T')[0],
                method: '',
                status: 'completed',
                notes: ''
            };
        },

        async savePayment() {
            this.loading = true;
            try {
                const url = this.editingPayment ? `/api/payments/${this.editingPayment.id}` : '/api/payments';
                const method = this.editingPayment ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();
                
                if (data.success) {
                    this.$store.notifications.add('success', data.message);
                    this.closeModal();
                    this.loadPayments();
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

        viewPayment(payment) {
            // Implement view payment details
            this.$store.notifications.add('info', 'Fitur detail pembayaran akan segera tersedia');
        },

        printReceipt(payment) {
            window.open(`/payments/${payment.id}/receipt`, '_blank');
        },

        async exportData() {
            try {
                const response = await fetch('/api/payments/export');
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
                this.loadPayments();
            }
        },

        nextPage() {
            if (this.currentPage < this.lastPage) {
                this.currentPage++;
                this.loadPayments();
            }
        },

        generateReferenceNumber() {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            return `PAY-${year}${month}${day}-${random}`;
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
                minimumFractionDigits: 0
            }).format(amount);
        },

        getTypeClass(type) {
            const classes = {
                'received': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                'paid': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
            };
            return classes[type] || classes['received'];
        },

        getTypeText(type) {
            const texts = {
                'received': 'Masuk',
                'paid': 'Keluar'
            };
            return texts[type] || 'Masuk';
        },

        getStatusClass(status) {
            const classes = {
                'completed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                'failed': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
            };
            return classes[status] || classes['completed'];
        },

        getStatusText(status) {
            const texts = {
                'completed': 'Selesai',
                'pending': 'Pending',
                'failed': 'Gagal'
            };
            return texts[status] || 'Selesai';
        }
    }
}
</script>
@endsection