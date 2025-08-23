@extends('layouts.dashboard')

@section('title', 'Detail Transaksi')
@section('page-title')
<span class="text-base lg:text-2xl">Detail Transaksi</span>
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
        <a href="{{ route('financial.dashboard') }}" class="ml-1 text-sm font-medium text-muted hover:text-foreground md:ml-2">Keuangan</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('financial.transactions.index') }}" class="ml-1 text-sm font-medium text-muted hover:text-foreground md:ml-2">Transaksi</a>
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
<div x-data="transactionDetail()" class="space-y-4 lg:space-y-6 p-4 lg:p-0">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('financial.transactions.index') }}" class="p-2 text-muted hover:text-foreground rounded-lg border border-border hover:border-primary/20 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl lg:text-2xl font-bold text-foreground" x-text="transaction.description"></h2>
                <p class="text-sm text-muted" x-text="'Referensi: ' + (transaction.reference || 'Tidak ada')"></p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="duplicateTransaction()" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                Duplikasi
            </button>
            <a :href="`/financial/transactions/${transaction.id}/edit`" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Transaksi
            </a>
        </div>
    </div>

    <!-- Transaction Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Transaction Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="card p-4 lg:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-foreground">Informasi Transaksi</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                          :class="getStatusClass(transaction.status)"
                          x-text="getStatusText(transaction.status)">
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-2">Deskripsi</label>
                        <p class="text-foreground font-medium" x-text="transaction.description"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-2">Jumlah</label>
                        <p class="text-2xl font-bold"
                           :class="transaction.type === 'income' ? 'text-green-600' : transaction.type === 'expense' ? 'text-red-600' : 'text-blue-600'"
                           x-text="(transaction.type === 'income' ? '+' : transaction.type === 'expense' ? '-' : '') + formatCurrency(transaction.amount)">
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-2">Tipe Transaksi</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium"
                              :class="getTypeClass(transaction.type)"
                              x-text="getTypeText(transaction.type)">
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-2">Kategori</label>
                        <p class="text-foreground" x-text="transaction.category_label || transaction.category"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-2">Tanggal & Waktu</label>
                        <p class="text-foreground" x-text="formatDateTime(transaction.date)"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-2">Metode Pembayaran</label>
                        <p class="text-foreground" x-text="getPaymentMethodText(transaction.payment_method)"></p>
                    </div>
                </div>

                <div x-show="transaction.reference" class="mt-6 pt-6 border-t border-border">
                    <label class="block text-sm font-medium text-muted mb-2">Nomor Referensi</label>
                    <p class="text-foreground font-mono" x-text="transaction.reference"></p>
                </div>

                <div x-show="transaction.notes" class="mt-6 pt-6 border-t border-border">
                    <label class="block text-sm font-medium text-muted mb-2">Catatan</label>
                    <p class="text-foreground whitespace-pre-wrap" x-text="transaction.notes"></p>
                </div>
            </div>

            <!-- Tax Information -->
            <div x-show="transaction.is_taxable" class="card p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Informasi Pajak</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-2">Subtotal</label>
                        <p class="text-foreground font-medium" x-text="formatCurrency(transaction.subtotal || transaction.amount)"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-2">Pajak (<span x-text="transaction.tax_rate"></span>%)</label>
                        <p class="text-foreground font-medium" x-text="formatCurrency(transaction.tax_amount || 0)"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-2">Total</label>
                        <p class="text-foreground font-bold text-lg" x-text="formatCurrency(transaction.total_amount || transaction.amount)"></p>
                    </div>
                </div>
            </div>

            <!-- Recurring Information -->
            <div x-show="transaction.is_recurring" class="card p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Informasi Berulang</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-2">Frekuensi</label>
                        <p class="text-foreground" x-text="getRecurringFrequencyText(transaction.recurring_frequency)"></p>
                    </div>
                    <div x-show="transaction.recurring_end_date">
                        <label class="block text-sm font-medium text-muted mb-2">Berakhir Pada</label>
                        <p class="text-foreground" x-text="formatDate(transaction.recurring_end_date)"></p>
                    </div>
                    <div x-show="transaction.recurring_count">
                        <label class="block text-sm font-medium text-muted mb-2">Jumlah Kejadian</label>
                        <p class="text-foreground" x-text="transaction.recurring_count + ' kali'"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Aksi Cepat</h3>
                <div class="space-y-3">
                    <button @click="approveTransaction()" 
                            x-show="transaction.status === 'pending'"
                            class="w-full btn-primary text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Setujui Transaksi
                    </button>
                    
                    <button @click="rejectTransaction()" 
                            x-show="transaction.status === 'pending'"
                            class="w-full btn-secondary text-sm text-red-600 hover:text-red-800">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Tolak Transaksi
                    </button>
                    
                    <button @click="printTransaction()" class="w-full btn-secondary text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Cetak
                    </button>
                    
                    <button @click="exportTransaction()" class="w-full btn-secondary text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Ekspor
                    </button>
                </div>
            </div>

            <!-- Account Information -->
            <div x-show="transaction.account_name" class="card p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Informasi Akun</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Akun</label>
                        <p class="text-foreground font-medium" x-text="transaction.account_name"></p>
                    </div>
                    <div x-show="transaction.account_number">
                        <label class="block text-sm font-medium text-muted mb-1">Nomor Akun</label>
                        <p class="text-foreground font-mono" x-text="transaction.account_number"></p>
                    </div>
                </div>
            </div>

            <!-- Transaction Timeline -->
            <div class="card p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Timeline</h3>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                        <div>
                            <p class="text-sm font-medium text-foreground">Transaksi Dibuat</p>
                            <p class="text-xs text-muted" x-text="formatDateTime(transaction.created_at)"></p>
                        </div>
                    </div>
                    <div x-show="transaction.approved_at" class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                        <div>
                            <p class="text-sm font-medium text-foreground">Disetujui</p>
                            <p class="text-xs text-muted" x-text="formatDateTime(transaction.approved_at)"></p>
                            <p x-show="transaction.approved_by" class="text-xs text-muted" x-text="'oleh ' + transaction.approved_by"></p>
                        </div>
                    </div>
                    <div x-show="transaction.completed_at" class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-green-600 rounded-full mt-2 flex-shrink-0"></div>
                        <div>
                            <p class="text-sm font-medium text-foreground">Selesai</p>
                            <p class="text-xs text-muted" x-text="formatDateTime(transaction.completed_at)"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Transactions -->
            <div x-show="relatedTransactions.length > 0" class="card p-4 lg:p-6">
                <h3 class="text-lg font-semibold text-foreground mb-4">Transaksi Terkait</h3>
                <div class="space-y-3">
                    <template x-for="related in relatedTransactions" :key="related.id">
                        <div class="flex items-center justify-between p-3 bg-surface rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-foreground" x-text="related.description"></p>
                                <p class="text-xs text-muted" x-text="formatDate(related.date)"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium" 
                                   :class="related.type === 'income' ? 'text-green-600' : 'text-red-600'"
                                   x-text="formatCurrency(related.amount)"></p>
                                <a :href="`/financial/transactions/${related.id}`" class="text-xs text-primary hover:text-primary/80">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function transactionDetail() {
    return {
        transaction: {
            id: 1,
            description: 'Pendapatan Penjualan Produk',
            amount: 15000000,
            type: 'income',
            category: 'sales',
            category_label: 'Pendapatan Penjualan',
            status: 'completed',
            date: '2024-01-15T10:30:00Z',
            reference: 'INV-2024-001',
            notes: 'Penjualan produk bulanan kepada PT. Maju Jaya untuk periode Januari 2024.',
            payment_method: 'bank_transfer',
            account_name: 'Akun Bisnis Utama',
            account_number: '1234567890',
            is_taxable: true,
            tax_rate: 11,
            tax_amount: 1650000,
            subtotal: 15000000,
            total_amount: 16650000,
            is_recurring: false,
            recurring_frequency: null,
            recurring_end_date: null,
            recurring_count: null,
            created_at: '2024-01-15T10:30:00Z',
            approved_at: '2024-01-15T11:00:00Z',
            approved_by: 'Manager Keuangan',
            completed_at: '2024-01-15T11:30:00Z'
        },
        relatedTransactions: [
            {
                id: 2,
                description: 'Biaya Pengiriman',
                amount: 150000,
                type: 'expense',
                date: '2024-01-15T12:00:00Z'
            }
        ],
        loading: false,

        async init() {
            await this.loadTransaction();
            await this.loadRelatedTransactions();
        },

        async loadTransaction() {
            try {
                // In real implementation, load from API
                // const response = await fetch(`/api/financial/transactions/${transactionId}`);
                // const transaction = await response.json();
                // this.transaction = transaction;
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal memuat detail transaksi'
                });
            }
        },

        async loadRelatedTransactions() {
            try {
                // In real implementation, load from API
                // const response = await fetch(`/api/financial/transactions/${this.transaction.id}/related`);
                // const related = await response.json();
                // this.relatedTransactions = related;
            } catch (error) {
                console.error('Error loading related transactions:', error);
            }
        },

        async approveTransaction() {
            if (!confirm('Apakah Anda yakin ingin menyetujui transaksi ini?')) {
                return;
            }

            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: 'Transaksi berhasil disetujui.'
                });
                this.transaction.status = 'approved';
                this.transaction.approved_at = new Date().toISOString();
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal menyetujui transaksi'
                });
            }
        },

        async rejectTransaction() {
            if (!confirm('Apakah Anda yakin ingin menolak transaksi ini?')) {
                return;
            }

            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: 'Transaksi berhasil ditolak.'
                });
                this.transaction.status = 'rejected';
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal menolak transaksi'
                });
            }
        },

        duplicateTransaction() {
            window.location.href = `/financial/transactions/create?duplicate=${this.transaction.id}`;
        },

        printTransaction() {
            window.print();
        },

        async exportTransaction() {
            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: 'Transaksi berhasil diekspor.'
                });
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal mengekspor transaksi'
                });
            }
        },

        getStatusClass(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'approved': 'bg-blue-100 text-blue-800',
                'rejected': 'bg-red-100 text-red-800',
                'completed': 'bg-green-100 text-green-800',
                'draft': 'bg-gray-100 text-gray-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },

        getStatusText(status) {
            const texts = {
                'pending': 'Tertunda',
                'approved': 'Disetujui',
                'rejected': 'Ditolak',
                'completed': 'Selesai',
                'draft': 'Draft'
            };
            return texts[status] || status;
        },

        getTypeClass(type) {
            const classes = {
                'income': 'bg-green-100 text-green-800',
                'expense': 'bg-red-100 text-red-800',
                'transfer': 'bg-blue-100 text-blue-800'
            };
            return classes[type] || 'bg-gray-100 text-gray-800';
        },

        getTypeText(type) {
            const texts = {
                'income': 'Pemasukan',
                'expense': 'Pengeluaran',
                'transfer': 'Transfer'
            };
            return texts[type] || type;
        },

        getPaymentMethodText(method) {
            const methods = {
                'cash': 'Tunai',
                'bank_transfer': 'Transfer Bank',
                'credit_card': 'Kartu Kredit',
                'debit_card': 'Kartu Debit',
                'digital_wallet': 'Dompet Digital',
                'check': 'Cek',
                'other': 'Lainnya'
            };
            return methods[method] || method || 'Tidak ditentukan';
        },

        getRecurringFrequencyText(frequency) {
            const frequencies = {
                'daily': 'Harian',
                'weekly': 'Mingguan',
                'monthly': 'Bulanan',
                'quarterly': 'Kuartalan',
                'yearly': 'Tahunan'
            };
            return frequencies[frequency] || frequency;
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
                month: 'long',
                day: 'numeric'
            });
        },

        formatDateTime(dateString) {
            return new Date(dateString).toLocaleString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
}
</script>
@endpush