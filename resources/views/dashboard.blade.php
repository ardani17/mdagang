@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div x-data="dashboard()" class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-primary to-primary-dark rounded-lg p-6 text-primary-foreground">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Selamat datang kembali, {{ auth()->user()->name ?? 'Pengguna' }}!</h2>
                <p class="mt-1 opacity-90">Berikut yang terjadi dengan bisnis Anda hari ini.</p>
            </div>
            <div class="hidden md:block">
                <svg class="w-16 h-16 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Sales -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Total Penjualan</p>
                    <p class="text-2xl font-bold text-foreground" x-text="formatCurrency(stats.totalSales)">Rp 0</p>
                    <p class="text-xs text-green-600 mt-1">
                        <span class="inline-flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            +12.5% dari bulan lalu
                        </span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Order Produksi Aktif</p>
                    <p class="text-2xl font-bold text-foreground" x-text="stats.activeProductionOrders">0</p>
                    <p class="text-xs text-blue-600 mt-1">
                        <span class="inline-flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            +5 order baru minggu ini
                        </span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Products -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Produk Siap Jual</p>
                    <p class="text-2xl font-bold text-foreground" x-text="stats.readyProducts">0</p>
                    <p class="text-xs text-purple-600 mt-1">
                        <span class="inline-flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            +150 unit diproduksi
                        </span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Bahan Baku Tersedia</p>
                    <p class="text-2xl font-bold text-foreground" x-text="stats.availableRawMaterials">0</p>
                    <p class="text-xs text-orange-600 mt-1">
                        <span class="inline-flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            3 bahan perlu restok
                        </span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales Chart -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-foreground">Ringkasan Produksi</h3>
                <div class="flex space-x-2">
                    <button @click="chartPeriod = '7d'" 
                            :class="chartPeriod === '7d' ? 'bg-primary text-primary-foreground' : 'bg-border text-muted'"
                            class="px-3 py-1 text-xs rounded-md transition-colors">7D</button>
                    <button @click="chartPeriod = '30d'" 
                            :class="chartPeriod === '30d' ? 'bg-primary text-primary-foreground' : 'bg-border text-muted'"
                            class="px-3 py-1 text-xs rounded-md transition-colors">30D</button>
                    <button @click="chartPeriod = '90d'" 
                            :class="chartPeriod === '90d' ? 'bg-primary text-primary-foreground' : 'bg-border text-muted'"
                            class="px-3 py-1 text-xs rounded-md transition-colors">90D</button>
                </div>
            </div>
            <div class="h-64">
                <canvas id="salesChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Top Products -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Produk Terlaris</h3>
            <div class="space-y-4">
                <template x-for="product in topProducts" :key="product.id">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                <span class="text-sm font-medium text-primary" x-text="product.name.charAt(0)"></span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-foreground" x-text="product.name"></p>
                                <p class="text-xs text-muted" x-text="product.category"></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-foreground" x-text="formatCurrency(product.revenue)"></p>
                            <p class="text-xs text-muted" x-text="product.sales + ' terjual'"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Orders -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-foreground">Order Terbaru</h3>
                <a href="{{ route('orders.index') }}" class="text-sm text-primary hover:text-primary/80">Lihat semua</a>
            </div>
            <div class="space-y-3">
                <template x-for="order in recentOrders" :key="order.id">
                    <div class="flex items-center justify-between p-3 bg-border/30 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-foreground" x-text="'#' + order.number"></p>
                            <p class="text-xs text-muted" x-text="order.customer"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-foreground" x-text="formatCurrency(order.total)"></p>
                            <span :class="getStatusColor(order.status)" 
                                  class="inline-flex px-2 py-1 text-xs font-medium rounded-full" 
                                  x-text="order.status"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-foreground">Bahan Baku Stok Rendah</h3>
                <a href="{{ route('inventory.index') }}" class="text-sm text-primary hover:text-primary/80">Lihat semua</a>
            </div>
            <div class="space-y-3">
                <template x-for="item in lowStockProducts" :key="item.id">
                    <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                        <div>
                            <p class="text-sm font-medium text-foreground" x-text="item.name"></p>
                            <p class="text-xs text-muted" x-text="'Saat ini: ' + item.current_stock + ' ' + item.unit"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-orange-600" x-text="'Min: ' + item.min_stock"></p>
                            <button class="text-xs text-primary hover:text-primary/80">Restok</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Aksi Cepat</h3>
            <div class="space-y-3">
                <a href="{{ route('orders.create') }}" 
                   class="flex items-center p-3 bg-border/30 rounded-lg hover:bg-border/50 transition-colors">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-foreground">Order Produksi Baru</p>
                        <p class="text-xs text-muted">Buat order produksi baru</p>
                    </div>
                </a>

                <a href="{{ route('products.create') }}" 
                   class="flex items-center p-3 bg-border/30 rounded-lg hover:bg-border/50 transition-colors">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-foreground">Beli Bahan Baku</p>
                        <p class="text-xs text-muted">Buat pembelian bahan baku</p>
                    </div>
                </a>

                <a href="{{ route('customers.create') }}" 
                   class="flex items-center p-3 bg-border/30 rounded-lg hover:bg-border/50 transition-colors">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-foreground">Tambah Pelanggan</p>
                        <p class="text-xs text-muted">Daftarkan pelanggan baru</p>
                    </div>
                </a>

                <a href="{{ route('financial.reports.index') }}"
                   class="flex items-center p-3 bg-border/30 rounded-lg hover:bg-border/50 transition-colors">
                    <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-foreground">Lihat Laporan</p>
                        <p class="text-xs text-muted">Laporan keuangan dan penjualan</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function dashboard() {
    return {
        chartPeriod: '30d',
        salesChart: null,
        stats: {
            totalSales: 125000000,
            activeProductionOrders: 12,
            readyProducts: 450,
            availableRawMaterials: 8
        },
        topProducts: [
            { id: 1, name: 'Minuman Temulawak 250ml', category: 'Minuman Herbal', revenue: 18500000, sales: 1850 },
            { id: 2, name: 'Krupuk Bro Merah 250g', category: 'Makanan Ringan', revenue: 12000000, sales: 2400 },
            { id: 3, name: 'Krupuk Bro Putih 5kg', category: 'Makanan Ringan', revenue: 15000000, sales: 300 },
            { id: 4, name: 'Krupuk Bro Kuning 250g', category: 'Makanan Ringan', revenue: 8500000, sales: 1700 },
            { id: 5, name: 'Minuman Temulawak 500ml', category: 'Minuman Herbal', revenue: 9200000, sales: 920 }
        ],
        recentOrders: [
            { id: 1, number: 'PRD-001', customer: 'Order Produksi', total: 500, status: 'processing' },
            { id: 2, number: 'SO-001', customer: 'Toko Berkah', total: 2500000, status: 'confirmed' },
            { id: 3, number: 'PRD-002', customer: 'Order Produksi', total: 300, status: 'completed' },
            { id: 4, number: 'SO-002', customer: 'Warung Maju', total: 1800000, status: 'shipped' },
            { id: 5, number: 'PRD-003', customer: 'Order Produksi', total: 750, status: 'processing' }
        ],
        lowStockProducts: [
            { id: 1, name: 'Gula Cair', current_stock: 15, min_stock: 50, unit: 'liter' },
            { id: 2, name: 'Temulawak Bubuk', current_stock: 3, min_stock: 15, unit: 'kg' },
            { id: 3, name: 'Botol 250ml', current_stock: 200, min_stock: 1000, unit: 'pcs' },
            { id: 4, name: 'Plastik Kemasan 250g', current_stock: 50, min_stock: 500, unit: 'pcs' }
        ],

        init() {
            this.$nextTick(() => {
                this.initSalesChart();
            });
        },

        initSalesChart() {
            const ctx = document.getElementById('salesChart');
            if (!ctx) return;

            this.salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Produksi (Unit)',
                        data: [1200, 1900, 1500, 2500, 2200, 3000, 2800, 3500, 3200, 4000, 3800, 4500],
                        borderColor: 'rgb(255, 193, 7)',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + ' unit';
                                }
                            }
                        }
                    }
                }
            });
        },

        getStatusColor(status) {
            const colors = {
                'confirmed': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                'processing': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                'shipped': 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
                'delivered': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'completed': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }
    }
}
</script>
@endpush