@extends('layouts.dashboard')

@section('title', 'Edit Transaksi')
@section('page-title')
<span class="text-base lg:text-2xl">Edit Transaksi</span>
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
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Edit</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="transactionEdit()" class="space-y-4 lg:space-y-6 p-4 lg:p-0">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('financial.transactions.index') }}" class="p-2 text-muted hover:text-foreground rounded-lg border border-border hover:border-primary/20 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl lg:text-2xl font-bold text-foreground">Edit Transaksi</h2>
                <p class="text-sm text-muted" x-text="'Referensi: ' + (form.reference || 'Tidak ada')"></p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('financial.transactions.index') }}" class="btn-secondary">
                Batal
            </a>
            <button @click="updateTransaction()" 
                    :disabled="loading"
                    class="btn-primary flex items-center">
                <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="loading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
            </button>
        </div>
    </div>

    <!-- Transaction Form -->
    <form @submit.prevent="updateTransaction()" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Transaction Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="card p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Detail Transaksi</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Transaction Type -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-foreground mb-2">Tipe Transaksi *</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="relative">
                                    <input type="radio" 
                                           x-model="form.type" 
                                           value="income" 
                                           class="sr-only peer">
                                    <div class="flex items-center justify-center p-4 border-2 border-border rounded-lg cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300 transition-colors">
                                        <div class="text-center">
                                            <svg class="w-6 h-6 mx-auto mb-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                            </svg>
                                            <span class="text-sm font-medium text-foreground">Pemasukan</span>
                                        </div>
                                    </div>
                                </label>
                                
                                <label class="relative">
                                    <input type="radio" 
                                           x-model="form.type" 
                                           value="expense" 
                                           class="sr-only peer">
                                    <div class="flex items-center justify-center p-4 border-2 border-border rounded-lg cursor-pointer peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-300 transition-colors">
                                        <div class="text-center">
                                            <svg class="w-6 h-6 mx-auto mb-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                            </svg>
                                            <span class="text-sm font-medium text-foreground">Pengeluaran</span>
                                        </div>
                                    </div>
                                </label>
                                
                                <label class="relative">
                                    <input type="radio" 
                                           x-model="form.type" 
                                           value="transfer" 
                                           class="sr-only peer">
                                    <div class="flex items-center justify-center p-4 border-2 border-border rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300 transition-colors">
                                        <div class="text-center">
                                            <svg class="w-6 h-6 mx-auto mb-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                            </svg>
                                            <span class="text-sm font-medium text-foreground">Transfer</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div x-show="errors.type" class="mt-1 text-sm text-red-600" x-text="errors.type"></div>
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-foreground mb-2">Deskripsi *</label>
                            <input type="text" 
                                   id="description"
                                   x-model="form.description"
                                   class="input"
                                   :class="errors.description ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                   placeholder="Masukkan deskripsi transaksi">
                            <div x-show="errors.description" class="mt-1 text-sm text-red-600" x-text="errors.description"></div>
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-foreground mb-2">Jumlah *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-muted text-sm">Rp</span>
                                </div>
                                <input type="number" 
                                       id="amount"
                                       x-model="form.amount"
                                       class="input pl-12"
                                       :class="errors.amount ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                       placeholder="0"
                                       step="0.01"
                                       min="0">
                            </div>
                            <div x-show="errors.amount" class="mt-1 text-sm text-red-600" x-text="errors.amount"></div>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-foreground mb-2">Kategori *</label>
                            <select id="category"
                                    x-model="form.category"
                                    class="input"
                                    :class="errors.category ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''">
                                <option value="">Pilih Kategori</option>
                                <template x-for="category in getAvailableCategories()" :key="category.value">
                                    <option :value="category.value" x-text="category.label"></option>
                                </template>
                            </select>
                            <div x-show="errors.category" class="mt-1 text-sm text-red-600" x-text="errors.category"></div>
                        </div>

                        <!-- Date -->
                        <div>
                            <label for="date" class="block text-sm font-medium text-foreground mb-2">Tanggal *</label>
                            <input type="date" 
                                   id="date"
                                   x-model="form.date"
                                   class="input"
                                   :class="errors.date ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''">
                            <div x-show="errors.date" class="mt-1 text-sm text-red-600" x-text="errors.date"></div>
                        </div>

                        <!-- Time -->
                        <div>
                            <label for="time" class="block text-sm font-medium text-foreground mb-2">Waktu</label>
                            <input type="time" 
                                   id="time"
                                   x-model="form.time"
                                   class="input">
                        </div>

                        <!-- Reference -->
                        <div class="md:col-span-2">
                            <label for="reference" class="block text-sm font-medium text-foreground mb-2">Nomor Referensi</label>
                            <input type="text" 
                                   id="reference"
                                   x-model="form.reference"
                                   class="input"
                                   placeholder="Nomor invoice, nomor kwitansi, dll.">
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-foreground mb-2">Catatan</label>
                            <textarea id="notes"
                                      x-model="form.notes"
                                      rows="3"
                                      class="input"
                                      placeholder="Catatan atau komentar tambahan"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="card p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Informasi Pembayaran</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Payment Method -->
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-foreground mb-2">Metode Pembayaran</label>
                            <select id="payment_method"
                                    x-model="form.payment_method"
                                    class="input">
                                <option value="">Pilih Metode Pembayaran</option>
                                <option value="cash">Tunai</option>
                                <option value="bank_transfer">Transfer Bank</option>
                                <option value="credit_card">Kartu Kredit</option>
                                <option value="debit_card">Kartu Debit</option>
                                <option value="digital_wallet">Dompet Digital</option>
                                <option value="check">Cek</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>

                        <!-- Account -->
                        <div x-show="form.payment_method && form.payment_method !== 'cash'">
                            <label for="account" class="block text-sm font-medium text-foreground mb-2">Akun</label>
                            <select id="account"
                                    x-model="form.account_id"
                                    class="input">
                                <option value="">Pilih Akun</option>
                                <option value="1">Akun Bisnis Utama</option>
                                <option value="2">Akun Tabungan</option>
                                <option value="3">Kas Kecil</option>
                            </select>
                        </div>

                        <!-- Tax Information -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="form.is_taxable"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Transaksi ini kena pajak</span>
                            </label>
                        </div>

                        <div x-show="form.is_taxable">
                            <label for="tax_rate" class="block text-sm font-medium text-foreground mb-2">Tarif Pajak (%)</label>
                            <input type="number" 
                                   id="tax_rate"
                                   x-model="form.tax_rate"
                                   class="input"
                                   placeholder="0"
                                   step="0.01"
                                   min="0"
                                   max="100">
                        </div>
                    </div>
                </div>

                <!-- Status Update -->
                <div class="card p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Status Transaksi</h3>
                    
                    <div class="space-y-3">
                        <label class="relative">
                            <input type="radio" 
                                   x-model="form.status" 
                                   value="pending" 
                                   class="sr-only peer">
                            <div class="flex items-center p-3 border border-border rounded-lg cursor-pointer peer-checked:border-yellow-500 peer-checked:bg-yellow-50">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                                <div>
                                    <div class="text-sm font-medium text-foreground">Tertunda</div>
                                    <div class="text-xs text-muted">Menunggu persetujuan</div>
                                </div>
                            </div>
                        </label>
                        
                        <label class="relative">
                            <input type="radio" 
                                   x-model="form.status" 
                                   value="approved" 
                                   class="sr-only peer">
                            <div class="flex items-center p-3 border border-border rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                <div>
                                    <div class="text-sm font-medium text-foreground">Disetujui</div>
                                    <div class="text-xs text-muted">Transaksi disetujui</div>
                                </div>
                            </div>
                        </label>
                        
                        <label class="relative">
                            <input type="radio" 
                                   x-model="form.status" 
                                   value="completed" 
                                   class="sr-only peer">
                            <div class="flex items-center p-3 border border-border rounded-lg cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                <div>
                                    <div class="text-sm font-medium text-foreground">Selesai</div>
                                    <div class="text-xs text-muted">Transaksi diproses</div>
                                </div>
                            </div>
                        </label>

                        <label class="relative">
                            <input type="radio" 
                                   x-model="form.status" 
                                   value="rejected" 
                                   class="sr-only peer">
                            <div class="flex items-center p-3 border border-border rounded-lg cursor-pointer peer-checked:border-red-500 peer-checked:bg-red-50">
                                <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                                <div>
                                    <div class="text-sm font-medium text-foreground">Ditolak</div>
                                    <div class="text-xs text-muted">Transaksi ditolak</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Transaction Summary -->
                <div class="card p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Ringkasan Transaksi</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Subtotal:</span>
                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(form.amount || 0)"></span>
                        </div>
                        
                        <div x-show="form.is_taxable && form.tax_rate" class="flex justify-between items-center">
                            <span class="text-sm text-muted">Pajak (<span x-text="form.tax_rate"></span>%):</span>
                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(calculateTax())"></span>
                        </div>
                        
                        <div class="border-t border-border pt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-base font-semibold text-foreground">Total:</span>
                                <span class="text-base font-bold" 
                                      :class="form.type === 'income' ? 'text-green-600' : form.type === 'expense' ? 'text-red-600' : 'text-blue-600'"
                                      x-text="formatCurrency(calculateTotal())"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Aksi Cepat</h3>
                    
                    <div class="space-y-3">
                        <button type="button" 
                                @click="previewTransaction()"
                                class="w-full btn-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Pratinjau
                        </button>
                        
                        <button type="button" 
                                @click="duplicateTransaction()"
                                class="w-full btn-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Duplikasi
                        </button>
                        
                        <button type="button" 
                                @click="resetForm()"
                                class="w-full btn-secondary text-sm text-red-600 hover:text-red-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset ke Asli
                        </button>
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="card p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Riwayat Perubahan</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <p class="font-medium text-foreground">Transaksi Dibuat</p>
                                <p class="text-xs text-muted" x-text="formatDateTime(originalTransaction.created_at)"></p>
                            </div>
                        </div>
                        <div x-show="originalTransaction.updated_at && originalTransaction.updated_at !== originalTransaction.created_at" class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2 flex-shrink-0"></div>
                            <div>
                                <p class="font-medium text-foreground">Terakhir Diubah</p>
                                <p class="text-xs text-muted" x-text="formatDateTime(originalTransaction.updated_at)"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6 border-t border-border">
            <div class="flex items-center space-x-4">
                <label class="flex items-center">
                    <input type="checkbox" 
                           x-model="form.send_notification"
                           class="rounded border-border text-primary focus:ring-primary">
                    <span class="ml-2 text-sm text-foreground">Kirim notifikasi perubahan</span>
                </label>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('financial.transactions.index') }}" class="btn-secondary">
                    Batal
                </a>
                
                <button type="submit" 
                        :disabled="loading"
                        class="btn-primary flex items-center"
                        :class="loading ? 'opacity-50 cursor-not-allowed' : ''">
                    <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function transactionEdit() {
    return {
        loading: false,
        errors: {},
        originalTransaction: {},
        form: {
            type: 'expense',
            description: '',
            amount: '',
            category: '',
            date: '',
            time: '',
            reference: '',
            notes: '',
            payment_method: '',
            account_id: '',
            is_taxable: false,
            tax_rate: 0,
            status: 'completed',
            send_notification: true
        },

        categories: {
            income: [
                { value: 'sales', label: 'Pendapatan Penjualan' },
                { value: 'services', label: 'Pendapatan Jasa' },
                { value: 'interest', label: 'Pendapatan Bunga' },
                { value: 'investment', label: 'Hasil Investasi' },
                { value: 'rental', label: 'Pendapatan Sewa' },
                { value: 'other_income', label: 'Pendapatan Lain' }
            ],
            expense: [
                { value: 'operations', label: 'Operasional' },
                { value: 'marketing', label: 'Pemasaran & Iklan' },
                { value: 'equipment', label: 'Peralatan & Perlengkapan' },
                { value: 'utilities', label: 'Utilitas' },
                { value: 'salaries', label: 'Gaji & Tunjangan' },
                { value: 'rent', label: 'Sewa & Fasilitas' },
                { value: 'travel', label: 'Perjalanan & Transportasi' },
                { value: 'professional', label: 'Layanan Profesional' },
                { value: 'insurance', label: 'Asuransi' },
                { value: 'taxes', label: 'Pajak & Biaya' },
                { value: 'other_expense', label: 'Pengeluaran Lain' }
            ],
            transfer: [
                { value: 'bank_transfer', label: 'Transfer Bank' },
                { value: 'account_transfer', label: 'Transfer Akun' },
                { value: 'investment_transfer', label: 'Transfer Investasi' }
            ]
        },

        async init() {
            await this.loadTransaction();
        },

        async loadTransaction() {
            try {
                // Load existing transaction data
                // In real implementation, get transaction ID from URL
                const transactionId = window.location.pathname.split('/').slice(-2, -1)[0];
                
                // Dummy data for demo
                this.originalTransaction = {
                    id: 1,
                    type: 'income',
                    description: 'Pendapatan Penjualan Produk',
                    amount: 15000000,
                    category: 'sales',
                    date: '2024-01-15',
                    time: '10:30',
                    reference: 'INV-2024-001',
                    notes: 'Penjualan produk bulanan kepada PT. Maju Jaya',
                    payment_method: 'bank_transfer',
                    account_id: '1',
                    is_taxable: true,
                    tax_rate: 11,
                    status: 'completed',
                    created_at: '2024-01-15T10:30:00Z',
                    updated_at: '2024-01-15T10:30:00Z'
                };

                // Copy to form
                this.form = { ...this.form, ...this.originalTransaction };
                
                // Format date properly
                if (this.form.date) {
                    this.form.date = new Date(this.form.date).toISOString().split('T')[0];
                }
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal memuat data transaksi'
                });
            }
        },

        getAvailableCategories() {
            return this.categories[this.form.type] || [];
        },

        calculateTax() {
            if (!this.form.is_taxable || !this.form.tax_rate || !this.form.amount) {
                return 0;
            }
            return (parseFloat(this.form.amount) * parseFloat(this.form.tax_rate)) / 100;
        },

        calculateTotal() {
            const amount = parseFloat(this.form.amount) || 0;
            const tax = this.calculateTax();
            return amount + tax;
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },

        formatDateTime(dateString) {
            return new Date(dateString).toLocaleString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        validateForm() {
            this.errors = {};

            if (!this.form.type) {
                this.errors.type = 'Tipe transaksi wajib diisi';
            }

            if (!this.form.description.trim()) {
                this.errors.description = 'Deskripsi wajib diisi';
            }

            if (!this.form.amount || parseFloat(this.form.amount) <= 0) {
                this.errors.amount = 'Jumlah harus lebih besar dari 0';
            }

            if (!this.form.category) {
                this.errors.category = 'Kategori wajib diisi';
            }

            if (!this.form.date) {
                this.errors.date = 'Tanggal wajib diisi';
            }

            return Object.keys(this.errors).length === 0;
        },

        async updateTransaction() {
            if (!this.validateForm()) {
                return;
            }

            this.loading = true;

            try {
                // Simulate API call
                await new Promise(resolve => setTimeout(resolve, 1000));

                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: 'Transaksi berhasil diperbarui.'
                });

                // Redirect to transaction list or detail
                window.location.href = '/financial/transactions';
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal memperbarui transaksi'
                });
            } finally {
                this.loading = false;
            }
        },

        previewTransaction() {
            console.log('Preview transaction:', this.form);
        },

        duplicateTransaction() {
            window.location.href = `/financial/transactions/create?duplicate=${this.originalTransaction.id}`;
        },

        resetForm() {
            if (confirm('Apakah Anda yakin ingin mengembalikan ke data asli? Semua perubahan akan hilang.')) {
                this.form = { ...this.form, ...this.originalTransaction };
                this.errors = {};
            }
        }
    }
}
</script>
@endsection