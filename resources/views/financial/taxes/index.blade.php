@extends('layouts.dashboard')

@section('title', 'Manajemen Pajak')

@section('content')
<div x-data="taxManager()" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-foreground">Manajemen Pajak</h1>
            <p class="text-muted">Kelola perhitungan dan pelaporan pajak</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button @click="generateReport" class="btn-secondary">
                <i class="fas fa-file-alt mr-2"></i>
                Laporan Pajak
            </button>
            <button @click="openCalculatorModal" class="btn-primary">
                <i class="fas fa-calculator mr-2"></i>
                Kalkulator Pajak
            </button>
        </div>
    </div>

    <!-- Tax Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-percentage text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted">PPN Terutang</p>
                    <p class="text-2xl font-bold text-foreground" x-text="formatCurrency(taxSummary.vat_payable)"></p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-money-bill-wave text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted">PPh Terutang</p>
                    <p class="text-2xl font-bold text-foreground" x-text="formatCurrency(taxSummary.income_tax_payable)"></p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-receipt text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted">Total Pajak Bulan Ini</p>
                    <p class="text-2xl font-bold text-foreground" x-text="formatCurrency(taxSummary.monthly_total)"></p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-calendar-alt text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted">Jatuh Tempo Terdekat</p>
                    <p class="text-lg font-bold text-foreground" x-text="taxSummary.next_due_date"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tax Configuration -->
    <div class="card p-6">
        <h2 class="text-lg font-semibold text-foreground mb-4">Konfigurasi Tarif Pajak</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">PPN (%)</label>
                <input type="number" x-model="taxRates.vat" @change="updateTaxRates" step="0.01" min="0" max="100"
                       class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">PPh Pasal 21 (%)</label>
                <input type="number" x-model="taxRates.income_tax_21" @change="updateTaxRates" step="0.01" min="0" max="100"
                       class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">PPh Pasal 23 (%)</label>
                <input type="number" x-model="taxRates.income_tax_23" @change="updateTaxRates" step="0.01" min="0" max="100"
                       class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
            </div>
        </div>
    </div>

    <!-- Tax Transactions -->
    <div class="card">
        <div class="px-6 py-4 border-b border-border">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-lg font-semibold text-foreground">Transaksi Pajak</h2>
                <div class="flex flex-col sm:flex-row gap-3">
                    <select x-model="filters.period" @change="filterTransactions"
                            class="px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                        <option value="">Semua Periode</option>
                        <option value="2024-01">Januari 2024</option>
                        <option value="2024-02">Februari 2024</option>
                        <option value="2024-03">Maret 2024</option>
                    </select>
                    <select x-model="filters.type" @change="filterTransactions"
                            class="px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                        <option value="">Semua Jenis</option>
                        <option value="vat">PPN</option>
                        <option value="income_tax_21">PPh 21</option>
                        <option value="income_tax_23">PPh 23</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-surface">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Jenis Pajak
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Deskripsi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Dasar Pengenaan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Tarif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Jumlah Pajak
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-border">
                    <template x-for="transaction in filteredTransactions" :key="transaction.id">
                        <tr class="hover:bg-surface">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground" x-text="formatDate(transaction.date)"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getTaxTypeClass(transaction.type)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" x-text="getTaxTypeText(transaction.type)"></span>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="transaction.description"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground" x-text="formatCurrency(transaction.tax_base)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground" x-text="transaction.tax_rate + '%'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground" x-text="formatCurrency(transaction.tax_amount)"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getStatusClass(transaction.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" x-text="getStatusText(transaction.status)"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden">
            <template x-for="transaction in filteredTransactions" :key="transaction.id">
                <div class="p-4 border-b border-border">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span :class="getTaxTypeClass(transaction.type)" class="px-2 py-1 text-xs font-semibold rounded-full" x-text="getTaxTypeText(transaction.type)"></span>
                            <p class="text-sm text-muted mt-1" x-text="formatDate(transaction.date)"></p>
                        </div>
                        <span :class="getStatusClass(transaction.status)" class="px-2 py-1 text-xs font-semibold rounded-full" x-text="getStatusText(transaction.status)"></span>
                    </div>
                    <h3 class="text-sm font-medium text-foreground mb-2" x-text="transaction.description"></h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-muted">Dasar Pengenaan:</span>
                            <span class="text-foreground ml-1" x-text="formatCurrency(transaction.tax_base)"></span>
                        </div>
                        <div>
                            <span class="text-muted">Tarif:</span>
                            <span class="text-foreground ml-1" x-text="transaction.tax_rate + '%'"></span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-muted">Jumlah Pajak:</span>
                            <span class="font-bold text-foreground ml-1" x-text="formatCurrency(transaction.tax_amount)"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="filteredTransactions.length === 0" class="text-center py-12">
            <i class="fas fa-receipt text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-foreground mb-2">Tidak ada transaksi pajak</h3>
            <p class="text-muted mb-4">Belum ada transaksi pajak yang tercatat atau sesuai dengan filter.</p>
        </div>
    </div>

    <!-- Tax Calculator Modal -->
    <div x-show="showCalculatorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-cloak>
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md card">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-foreground">Kalkulator Pajak</h3>
                <button @click="closeCalculatorModal" class="text-muted hover:text-foreground">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Jenis Pajak</label>
                        <select x-model="calculator.taxType" @change="calculateTax"
                                class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                            <option value="">Pilih Jenis Pajak</option>
                            <option value="vat">PPN</option>
                            <option value="income_tax_21">PPh Pasal 21</option>
                            <option value="income_tax_23">PPh Pasal 23</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Dasar Pengenaan Pajak</label>
                        <input type="number" x-model="calculator.taxBase" @input="calculateTax" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground"
                               placeholder="Masukkan jumlah dasar pengenaan">
                    </div>
                </div>

                <div class="bg-surface rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                        <div>
                            <p class="text-sm text-muted">Dasar Pengenaan</p>
                            <p class="text-lg font-bold text-foreground" x-text="formatCurrency(calculator.taxBase || 0)"></p>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Tarif Pajak</p>
                            <p class="text-lg font-bold text-foreground" x-text="(calculator.taxRate || 0) + '%'"></p>
                        </div>
                        <div>
                            <p class="text-sm text-muted">Jumlah Pajak</p>
                            <p class="text-xl font-bold text-blue-600" x-text="formatCurrency(calculator.taxAmount || 0)"></p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button @click="closeCalculatorModal" class="btn-secondary">Tutup</button>
                    <button @click="saveTaxCalculation" class="btn-primary" :disabled="!calculator.taxType || !calculator.taxBase">
                        Simpan Perhitungan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function taxManager() {
    return {
        taxSummary: {
            vat_payable: 0,
            income_tax_payable: 0,
            monthly_total: 0,
            next_due_date: ''
        },
        taxRates: {
            vat: 11,
            income_tax_21: 5,
            income_tax_23: 2
        },
        transactions: [],
        filteredTransactions: [],
        filters: {
            period: '',
            type: ''
        },
        showCalculatorModal: false,
        calculator: {
            taxType: '',
            taxBase: '',
            taxRate: 0,
            taxAmount: 0
        },
        loading: false,

        init() {
            this.loadTaxSummary();
            this.loadTaxTransactions();
            this.loadTaxRates();
        },

        async loadTaxSummary() {
            try {
                const response = await fetch('/api/taxes/summary');
                const data = await response.json();
                this.taxSummary = data;
            } catch (error) {
                console.error('Error loading tax summary:', error);
            }
        },

        async loadTaxTransactions() {
            this.loading = true;
            try {
                const response = await fetch('/api/taxes/transactions');
                const data = await response.json();
                this.transactions = data.data;
                this.filteredTransactions = [...this.transactions];
            } catch (error) {
                console.error('Error loading tax transactions:', error);
                this.$store.notifications.add('error', 'Gagal memuat data transaksi pajak');
            } finally {
                this.loading = false;
            }
        },

        async loadTaxRates() {
            try {
                const response = await fetch('/api/taxes/rates');
                const data = await response.json();
                this.taxRates = data;
            } catch (error) {
                console.error('Error loading tax rates:', error);
            }
        },

        async updateTaxRates() {
            try {
                const response = await fetch('/api/taxes/rates', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.taxRates)
                });

                const data = await response.json();
                
                if (data.success) {
                    this.$store.notifications.add('success', 'Tarif pajak berhasil diperbarui');
                } else {
                    this.$store.notifications.add('error', 'Gagal memperbarui tarif pajak');
                }
            } catch (error) {
                console.error('Error updating tax rates:', error);
                this.$store.notifications.add('error', 'Gagal memperbarui tarif pajak');
            }
        },

        filterTransactions() {
            let filtered = [...this.transactions];

            if (this.filters.period) {
                filtered = filtered.filter(transaction => 
                    transaction.date.startsWith(this.filters.period)
                );
            }

            if (this.filters.type) {
                filtered = filtered.filter(transaction => transaction.type === this.filters.type);
            }

            this.filteredTransactions = filtered;
        },

        openCalculatorModal() {
            this.calculator = {
                taxType: '',
                taxBase: '',
                taxRate: 0,
                taxAmount: 0
            };
            this.showCalculatorModal = true;
        },

        closeCalculatorModal() {
            this.showCalculatorModal = false;
        },

        calculateTax() {
            if (!this.calculator.taxType || !this.calculator.taxBase) {
                this.calculator.taxRate = 0;
                this.calculator.taxAmount = 0;
                return;
            }

            const rates = {
                'vat': this.taxRates.vat,
                'income_tax_21': this.taxRates.income_tax_21,
                'income_tax_23': this.taxRates.income_tax_23
            };

            this.calculator.taxRate = rates[this.calculator.taxType] || 0;
            this.calculator.taxAmount = (this.calculator.taxBase * this.calculator.taxRate) / 100;
        },

        async saveTaxCalculation() {
            this.loading = true;
            try {
                const response = await fetch('/api/taxes/calculate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.calculator)
                });

                const data = await response.json();
                
                if (data.success) {
                    this.$store.notifications.add('success', 'Perhitungan pajak berhasil disimpan');
                    this.closeCalculatorModal();
                    this.loadTaxTransactions();
                    this.loadTaxSummary();
                } else {
                    this.$store.notifications.add('error', data.message || 'Gagal menyimpan perhitungan');
                }
            } catch (error) {
                console.error('Error saving tax calculation:', error);
                this.$store.notifications.add('error', 'Gagal menyimpan perhitungan pajak');
            } finally {
                this.loading = false;
            }
        },

        async generateReport() {
            try {
                const response = await fetch('/api/taxes/report');
                const data = await response.json();
                
                if (data.success) {
                    this.$store.notifications.add('success', 'Laporan pajak berhasil dibuat');
                } else {
                    this.$store.notifications.add('error', 'Gagal membuat laporan pajak');
                }
            } catch (error) {
                console.error('Error generating report:', error);
                this.$store.notifications.add('error', 'Gagal membuat laporan pajak');
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

        getTaxTypeClass(type) {
            const classes = {
                'vat': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                'income_tax_21': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                'income_tax_23': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'
            };
            return classes[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
        },

        getTaxTypeText(type) {
            const texts = {
                'vat': 'PPN',
                'income_tax_21': 'PPh 21',
                'income_tax_23': 'PPh 23'
            };
            return texts[type] || type;
        },

        getStatusClass(status) {
            const classes = {
                'calculated': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                'paid': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                'overdue': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
            };
            return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
        },

        getStatusText(status) {
            const texts = {
                'calculated': 'Dihitung',
                'paid': 'Dibayar',
                'overdue': 'Terlambat'
            };
            return texts[status] || status;
        }
    }
}
</script>
@endsection