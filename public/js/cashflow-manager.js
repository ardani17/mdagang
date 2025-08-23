function journalManager() {
    return {
        loading: false,
        showTransactionForm: false,
        filterPeriod: 'month',
        cashflowTransactions: [],
        alerts: [],
        summary: {
            cash_inflow: 0,
            cash_outflow: 0,
            net_cashflow: 0,
            current_balance: 0,
            inflow_change: 0,
            outflow_change: 0,
            health_status: 'safe'
        },
        transactionForm: {
            type: 'inflow',
            category_id: '',
            amount: '',
            description: '',
            date: new Date().toISOString().split('T')[0],
            reference: ''
        },
        categories: {
            inflow: [
                { id: 1, name: 'Penjualan Produk', icon: '💰' },
                { id: 2, name: 'Piutang Tertagih', icon: '💳' },
                { id: 3, name: 'Pendapatan Lain', icon: '💎' },
                { id: 4, name: 'Modal/Pinjaman', icon: '🏦' }
            ],
            outflow: [
                { id: 5, name: 'Bahan Baku', icon: '📦' },
                { id: 6, name: 'Gaji & Upah', icon: '👥' },
                { id: 7, name: 'Operasional', icon: '⚙️' },
                { id: 8, name: 'Pembayaran Hutang', icon: '💸' }
            ]
        },

        init() {
            this.loadTransactions();
            this.loadSummary();
            this.loadAlerts();
        },

        async loadTransactions() {
            this.loading = true;
            try {
                const response = await fetch(`/api/financial/cashflow/transactions?period=${this.filterPeriod}`);
                const data = await response.json();
                this.cashflowTransactions = data.data || [];
            } catch (error) {
                console.error('Error loading transactions:', error);
                this.cashflowTransactions = this.getDemoTransactions();
            } finally {
                this.loading = false;
            }
        },

        async loadSummary() {
            try {
                const response = await fetch('/api/financial/cashflow/summary');
                const data = await response.json();
                this.summary = data;
            } catch (error) {
                console.error('Error loading summary:', error);
                this.summary = {
                    cash_inflow: 150000000,
                    cash_outflow: 125000000,
                    net_cashflow: 25000000,
                    current_balance: 75000000,
                    inflow_change: 12,
                    outflow_change: 8,
                    health_status: 'safe'
                };
            }
        },

        async loadAlerts() {
            try {
                const response = await fetch('/api/financial/cashflow/alerts');
                const data = await response.json();
                this.alerts = data.alerts || [];
            } catch (error) {
                console.error('Error loading alerts:', error);
                this.alerts = this.getDemoAlerts();
            }
        },

        getDemoTransactions() {
            return [
                {
                    id: 1,
                    date: '2025-01-17',
                    description: 'Penjualan Minuman Temulawak ke Toko ABC',
                    category_name: 'Penjualan Produk',
                    type: 'inflow',
                    amount: 15000000,
                    running_balance: 75000000,
                    reference: 'INV-2025-001'
                },
                {
                    id: 2,
                    date: '2025-01-16',
                    description: 'Pembayaran Gaji Karyawan Januari',
                    category_name: 'Gaji & Upah',
                    type: 'outflow',
                    amount: 10000000,
                    running_balance: 60000000,
                    reference: 'PAY-2025-001'
                },
                {
                    id: 3,
                    date: '2025-01-16',
                    description: 'Piutang PT. Maju Jaya',
                    category_name: 'Piutang Tertagih',
                    type: 'inflow',
                    amount: 25000000,
                    running_balance: 70000000,
                    reference: 'REC-2025-001'
                },
                {
                    id: 4,
                    date: '2025-01-15',
                    description: 'Pembelian Bahan Baku Temulawak',
                    category_name: 'Bahan Baku',
                    type: 'outflow',
                    amount: 8000000,
                    running_balance: 45000000,
                    reference: 'PO-2025-001'
                },
                {
                    id: 5,
                    date: '2025-01-15',
                    description: 'Penjualan Krupuk Bro ke Distributor',
                    category_name: 'Penjualan Produk',
                    type: 'inflow',
                    amount: 12000000,
                    running_balance: 53000000,
                    reference: 'INV-2025-002'
                }
            ];
        },

        getDemoAlerts() {
            return [
                {
                    id: 1,
                    level: 'warning',
                    title: 'Tagihan Supplier Jatuh Tempo',
                    message: 'Tagihan supplier Rp 10.000.000 akan jatuh tempo dalam 3 hari (20 Jan 2025)'
                },
                {
                    id: 2,
                    level: 'info',
                    title: 'Target Penjualan Tercapai',
                    message: 'Penjualan bulan ini sudah mencapai 85% dari target. Excellent progress! 🎉'
                },
                {
                    id: 3,
                    level: 'info',
                    title: 'Tips Keuangan',
                    message: 'Kas Anda cukup untuk 2.4 bulan. Pertimbangkan investasi untuk pertumbuhan bisnis.'
                }
            ];
        },

        getAvailableCategories() {
            return this.transactionForm.type === 'inflow' ? this.categories.inflow : this.categories.outflow;
        },

        closeTransactionForm() {
            this.showTransactionForm = false;
            this.resetTransactionForm();
        },

        resetTransactionForm() {
            this.transactionForm = {
                type: 'inflow',
                category_id: '',
                amount: '',
                description: '',
                date: new Date().toISOString().split('T')[0],
                reference: ''
            };
        },

        calculateNewBalance() {
            const currentBalance = this.summary.current_balance || 0;
            const amount = parseFloat(this.transactionForm.amount) || 0;
            
            if (this.transactionForm.type === 'inflow') {
                return currentBalance + amount;
            } else {
                return currentBalance - amount;
            }
        },

        isFormValid() {
            return this.transactionForm.type && 
                   this.transactionForm.category_id && 
                   this.transactionForm.amount > 0 && 
                   this.transactionForm.description && 
                   this.transactionForm.date;
        },

        async saveTransaction() {
            if (!this.isFormValid()) return;

            try {
                const response = await fetch('/api/financial/cashflow/transactions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify(this.transactionForm)
                });

                if (response.ok) {
                    this.closeTransactionForm();
                    this.loadTransactions();
                    this.loadSummary();
                    this.showSuccessMessage('Transaksi berhasil disimpan!');
                } else {
                    throw new Error('Failed to save transaction');
                }
            } catch (error) {
                console.error('Error saving transaction:', error);
                this.showErrorMessage('Gagal menyimpan transaksi. Silakan coba lagi.');
            }
        },

        dismissAlert(alertId) {
            this.alerts = this.alerts.filter(alert => alert.id !== alertId);
        },

        dismissAllAlerts() {
            this.alerts = [];
        },

        showSuccessMessage(message) {
            // Implementation for success notification
            console.log('Success:', message);
            // You can integrate with your notification system here
        },

        showErrorMessage(message) {
            // Implementation for error notification
            console.error('Error:', message);
            // You can integrate with your notification system here
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount || 0);
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        },

        async exportCashflow() {
            try {
                const response = await fetch('/api/financial/cashflow/export');
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'cashflow_report.xlsx';
                a.click();
                this.showSuccessMessage('Laporan arus kas berhasil diekspor!');
            } catch (error) {
                console.error('Error exporting cashflow:', error);
                this.showErrorMessage('Gagal mengekspor laporan. Silakan coba lagi.');
            }
        }
    }
}