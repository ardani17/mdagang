@extends('layouts.dashboard')

@section('title', 'Hutang Dagang')

@section('content')
<div x-data="payableManager()" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-foreground">Hutang Dagang</h1>
            <p class="text-muted">Kelola hutang kepada supplier</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button @click="exportData" class="btn-secondary">
                <i class="fas fa-download mr-2"></i>
                Ekspor Data
            </button>
            <button @click="openCreateModal" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Tambah Hutang
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-file-invoice text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted">Total Hutang</p>
                    <p class="text-2xl font-bold text-foreground" x-text="formatCurrency(stats.total_payables)"></p>
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
                    <p class="text-2xl font-bold text-foreground" x-text="formatCurrency(stats.paid_payables)"></p>
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
                    <p class="text-2xl font-bold text-foreground" x-text="formatCurrency(stats.pending_payables)"></p>
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
                    <p class="text-2xl font-bold text-foreground" x-text="formatCurrency(stats.overdue_payables)"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Cari Hutang</label>
                <input type="text" x-model="filters.search" @input="filterPayables"
                       placeholder="Nomor invoice, supplier..."
                       class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                <select x-model="filters.status" @change="filterPayables"
                        class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                    <option value="">Semua Status</option>
                    <option value="outstanding">Outstanding</option>
                    <option value="partial">Sebagian Dibayar</option>
                    <option value="paid">Lunas</option>
                    <option value="overdue">Overdue</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Supplier</label>
                <select x-model="filters.supplier" @change="filterPayables"
                        class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                    <option value="">Semua Supplier</option>
                    <option value="PT. Supplier Utama">PT. Supplier Utama</option>
                    <option value="CV. Mitra Sejahtera">CV. Mitra Sejahtera</option>
                    <option value="Toko Grosir Makmur">Toko Grosir Makmur</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Dari Tanggal</label>
                <input type="date" x-model="filters.date_from" @change="filterPayables"
                       class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Sampai Tanggal</label>
                <input type="date" x-model="filters.date_to" @change="filterPayables"
                       class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
            </div>
        </div>
    </div>

    <!-- Payables List -->
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
                            Supplier
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
                    <template x-for="payable in filteredPayables" :key="payable.id">
                        <tr class="hover:bg-surface">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-foreground" x-text="payable.invoice_number"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-foreground" x-text="payable.supplier_name"></div>
                                <div class="text-sm text-muted" x-text="payable.supplier_email"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground" x-text="formatDate(payable.invoice_date)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground" x-text="formatDate(payable.due_date)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground" x-text="formatCurrency(payable.total_amount)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground" x-text="formatCurrency(payable.outstanding_amount)"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getStatusClass(payable.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" x-text="getStatusText(payable.status)"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button @click="viewPayable(payable)" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button @click="recordPayment(payable)" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-money-bill"></i>
                                    </button>
                                    <button @click="editPayable(payable)" class="text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-edit"></i>
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
            <template x-for="payable in filteredPayables" :key="payable.id">
                <div class="p-4 border-b border-border">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="text-lg font-medium text-foreground" x-text="payable.invoice_number"></h3>
                            <p class="text-sm text-muted" x-text="payable.supplier_name"></p>
                        </div>
                        <span :class="getStatusClass(payable.status)" class="px-2 py-1 text-xs font-semibold rounded-full" x-text="getStatusText(payable.status)"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                        <div>
                            <span class="text-muted">Tanggal:</span>
                            <span class="text-foreground ml-1" x-text="formatDate(payable.invoice_date)"></span>
                        </div>
                        <div>
                            <span class="text-muted">Jatuh Tempo:</span>
                            <span class="text-foreground ml-1" x-text="formatDate(payable.due_date)"></span>
                        </div>
                        <div>
                            <span class="text-muted">Total:</span>
                            <span class="text-foreground ml-1" x-text="formatCurrency(payable.total_amount)"></span>
                        </div>
                        <div>
                            <span class="text-muted">Sisa:</span>
                            <span class="font-bold text-foreground ml-1" x-text="formatCurrency(payable.outstanding_amount)"></span>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button @click="viewPayable(payable)" class="p-2 text-blue-600 hover:bg-blue-50 rounded">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button @click="recordPayment(payable)" class="p-2 text-green-600 hover:bg-green-50 rounded">
                            <i class="fas fa-money-bill"></i>
                        </button>
                        <button @click="editPayable(payable)" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="filteredPayables.length === 0" class="text-center py-12">
            <i class="fas fa-file-invoice text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-foreground mb-2">Tidak ada hutang</h3>
            <p class="text-muted mb-4">Belum ada hutang yang tercatat atau sesuai dengan filter.</p>
            <button @click="openCreateModal" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Tambah Hutang Pertama
            </button>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between">
        <div class="text-sm text-muted">
            Menampilkan <span x-text="((currentPage - 1) * perPage) + 1"></span> sampai 
            <span x-text="Math.min(currentPage * perPage, totalPayables)"></span> dari 
            <span x-text="totalPayables"></span> hutang
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
                <h3 class="text-lg font-bold text-foreground">Catat Pembayaran Hutang</h3>
                <button @click="closePaymentModal" class="text-muted hover:text-foreground">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form @submit.prevent="savePayment" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Invoice</label>
                    <input type="text" :value="selectedPayable?.invoice_number" readonly
                           class="w-full px-3 py-2 border border-border rounded-md shadow-sm bg-surface text-foreground">
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Sisa Hutang</label>
                    <input type="text" :value="formatCurrency(selectedPayable?.outstanding_amount)" readonly
                           class="w-full px-3 py-2 border border-border rounded-md shadow-sm bg-surface text-foreground">
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Jumlah Pembayaran *</label>
                    <input type="number" x-model="paymentForm.amount" required min="0" step="0.01" :max="selectedPayable?.outstanding_amount"
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

    <!-- Create/Edit Modal -->
    <div x-show="showCreateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-cloak>
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md card">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-foreground" x-text="editMode ? 'Edit Hutang' : 'Tambah Hutang'"></h3>
                <button @click="closeCreateModal" class="text-muted hover:text-foreground">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form @submit.prevent="savePayable" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Nomor Invoice *</label>
                        <input type="text" x-model="payableForm.invoice_number" required
                               class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Supplier *</label>
                        <select x-model="payableForm.supplier_name" required
                                class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                            <option value="">Pilih Supplier</option>
                            <option value="PT. Supplier Utama">PT. Supplier Utama</option>
                            <option value="CV. Mitra Sejahtera">CV. Mitra Sejahtera</option>
                            <option value="Toko Grosir Makmur">Toko Grosir Makmur</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Email Supplier</label>
                        <input type="email" x-model="payableForm.supplier_email"
                               class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Tanggal Invoice *</label>
                        <input type="date" x-model="payableForm.invoice_date" required
                               class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Jatuh Tempo *</label>
                        <input type="date" x-model="payableForm.due_date" required
                               class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Total Amount *</label>
                        <input type="number" x-model="payableForm.total_amount" required min="0" step="0.01"
                               class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Deskripsi</label>
                    <textarea x-model="payableForm.description" rows="3"
                              class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground"
                              placeholder="Deskripsi hutang..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="closeCreateModal" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary" :disabled="loading">
                        <span x-show="loading" class="mr-2">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                        <span x-text="editMode ? 'Update Hutang' : 'Simpan Hutang'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function payableManager() {
    return {
        payables: [],
        filteredPayables: [],
        stats: {
            total_payables: 0,
            paid_payables: 0,
            pending_payables: 0,
            overdue_payables: 0
        },
        filters: {
            search: '',
            status: '',
            supplier: '',
            date_from: '',
            date_to: ''
        },
        showPaymentModal: false,
        showCreateModal: false,
        selectedPayable: null,
        editMode: false,
        loading: false,
        currentPage: 1,
        perPage: 25,
        totalPayables: 0,
        lastPage: 1,
        paymentForm: {
            amount: '',
            payment_date: '',
            method: '',
            notes: ''
        },
        payableForm: {
            invoice_number: '',
            supplier_name: '',
            supplier_email: '',
            invoice_date: '',
            due_date: '',
            total_amount: '',
            description: ''
        },

        init() {
            this.loadPayables();
            this.loadStats();
        },

        async loadPayables() {
            this.loading = true;
            try {
                const response = await fetch('/api/payables');
                const data = await response.json();
                this.payables = data.data;
                this.filteredPayables = [...this.payables];
                this.totalPayables = data.total;
                this.lastPage = data.last_page;
                this.currentPage = data.current_page;
            } catch (error) {
                console.error('Error loading payables:', error);
                this.$store.notifications.add('error', 'Gagal memuat data hutang');
            } finally {
                this.loading = false;
            }
        },

        async loadStats() {
            try {
                const response = await fetch('/api/payables/stats');
                const data = await response.json();
                this.stats = data;
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        },

        filterPayables() {
            let filtered = [...this.payables];

            if (this.filters.search) {
                const search = this.filters.search.toLowerCase();
                filtered = filtered.filter(payable =>
                    payable.invoice_number.toLowerCase().includes(search) ||
                    payable.supplier_name.toLowerCase().includes(search)
                );
            }

            if (this.filters.status) {
                filtered = filtered.filter(payable => payable.status === this.filters.status);
            }

            if (this.filters.supplier) {
                filtered = filtered.filter(payable => payable.supplier_name === this.filters.supplier);
            }

            if (this.filters.date_from) {
                filtered = filtered.filter(payable => payable.invoice_date >= this.filters.date_from);
            }

            if (this.filters.date_to) {
                filtered = filtered.filter(payable => payable.invoice_date <= this.filters.date_to);
            }

            this.filteredPayables = filtered;
        },

        recordPayment(payable) {
            this.selectedPayable = payable;
            this.paymentForm = {
                amount: payable.outstanding_amount,
                payment_date: new Date().toISOString().split('T')[0],
                method: '',
                notes: ''
            };
            this.showPaymentModal = true;
        },

        closePaymentModal() {
            this.showPaymentModal = false;
            this.selectedPayable = null;
        },

        async savePayment() {
            this.loading = true;
            try {
                const response = await fetch(`/api/payables/${this.selectedPayable.id}/payment`, {
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
                    this.loadPayables();
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

        openCreateModal() {
            this.editMode = false;
            this.payableForm = {
                invoice_number: '',
                supplier_name: '',
                supplier_email: '',
                invoice_date: '',
                due_date: '',
                total_amount: '',
                description: ''
            };
            this.showCreateModal = true;
        },

        editPayable(payable) {
            this.editMode = true;
            this.selectedPayable = payable;
            this.payableForm = {
                invoice_number: payable.invoice_number,
                supplier_name: payable.supplier_name,
                supplier_email: payable.supplier_email,
                invoice_date: payable.invoice_date,
                due_date: payable.due_date,
                total_amount: payable.total_amount,
                description: payable.description
            };
            this.showCreateModal = true;
        },

        closeCreateModal() {
            this.showCreateModal = false;
            this.selectedPayable = null;
            this.editMode = false;
        },

        async savePayable() {
            this.loading = true;
            try {
                const url = this.editMode ? `/api/payables/${this.selectedPayable.id}` : '/api/payables';
                const method = this.editMode ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.payableForm)
                });

                const data = await response.json();
                
                if (data.success) {
                    this.$store.notifications.add('success', data.message);
                    this.closeCreateModal();
                    this.loadPayables();
                    this.loadStats();
                } else {
                    this.$store.notifications.add('error', data.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                console.error('Error saving payable:', error);
                this.$store.notifications.add('error', 'Gagal menyimpan hutang');
            } finally {
                this.loading = false;
            }
        },

        viewPayable(payable) {
            // Open payable details in new tab
            window.open(`/payables/${payable.id}/view`, '_blank');
        },

        async exportData() {
            try {
                const response = await fetch('/api/payables/export');
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
                this.loadPayables();
            }
        },

        nextPage() {
            if (this.currentPage < this.lastPage) {
                this.currentPage++;
                this.loadPayables();
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
        }
    }
}
</script>
@endsection