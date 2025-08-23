@extends('layouts.dashboard')

@section('title', 'Produksi')
@section('page-title', 'Dashboard Produksi')

@section('content')
<div x-data="productionDashboard()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Dashboard Produksi</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Monitor dan kelola seluruh aktivitas produksi</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.production.orders.create') }}"
               class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Buat Order Produksi
            </a>
            <a href="{{ route('manufacturing.production.quality-control') }}"
               class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Quality Control
            </a>
            <button @click="refreshData" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Production Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Order Aktif</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="activeOrders">0</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Produksi Hari Ini</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="todayProduction">0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Efisiensi Rata-rata</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="averageEfficiency + '%'">0%</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">QC Pending</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="pendingQC">0</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('manufacturing.production.orders') }}" class="card p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm md:text-base font-medium text-foreground leading-tight">Order Produksi</h3>
                    <p class="text-xs md:text-sm text-muted leading-relaxed">Kelola order produksi</p>
                </div>
            </div>
        </a>

        <a href="{{ route('manufacturing.recipes.index') }}" class="card p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm md:text-base font-medium text-foreground leading-tight">Resep Produk</h3>
                    <p class="text-xs md:text-sm text-muted leading-relaxed">Kelola resep & BOM</p>
                </div>
            </div>
        </a>

        <a href="{{ route('manufacturing.production.quality-control') }}" class="card p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm md:text-base font-medium text-foreground leading-tight">Quality Control</h3>
                    <p class="text-xs md:text-sm text-muted leading-relaxed">Kontrol kualitas</p>
                </div>
            </div>
        </a>

        <a href="{{ route('manufacturing.production.history') }}" class="card p-4 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm md:text-base font-medium text-foreground leading-tight">Riwayat Produksi</h3>
                    <p class="text-xs md:text-sm text-muted leading-relaxed">Lihat riwayat</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Production Overview Chart -->
    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight">Overview Produksi</h3>
            <select x-model="chartPeriod" @change="updateChart" class="input w-32">
                <option value="week">7 Hari</option>
                <option value="month">30 Hari</option>
                <option value="quarter">3 Bulan</option>
            </select>
        </div>
        <div class="h-64">
            <canvas id="productionChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Active Production Orders -->
    <div class="card">
        <div class="p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight">Order Produksi Aktif</h3>
                <a href="{{ route('manufacturing.production.orders') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    Lihat Semua →
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-border/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Target</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="order in activeProductionOrders" :key="order.id">
                        <tr class="hover:bg-border/30">
                            <td class="px-6 py-4">
                                <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="order.order_number"></div>
                                <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="'Operator: ' + order.operator_name"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="order.product_name"></div>
                                <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="order.batch_count + ' batch'"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="h-2 rounded-full bg-blue-600" :style="'width: ' + order.progress + '%'"></div>
                                    </div>
                                    <span class="text-sm text-foreground" x-text="order.progress + '%'"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="formatDate(order.target_date)"></td>
                            <td class="px-6 py-4">
                                <span :class="getStatusColor(order.status)" 
                                      class="inline-flex px-2 py-1 text-xs font-medium rounded-full" 
                                      x-text="getStatusText(order.status)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <button @click="viewOrder(order)" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg touch-manipulation" title="Lihat">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click="updateProgress(order)" class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg touch-manipulation" title="Update Progress">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Production Alerts -->
    <div x-show="alerts.length > 0" class="card p-6">
        <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Peringatan Produksi</h3>
        <div class="space-y-3">
            <template x-for="alert in alerts" :key="alert.id">
                <div :class="getAlertColor(alert.type)" class="p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <div>
                                <p class="text-sm md:text-base font-medium leading-tight" x-text="alert.title"></p>
                                <p class="text-xs md:text-sm opacity-90 leading-relaxed" x-text="alert.message"></p>
                            </div>
                        </div>
                        <button @click="dismissAlert(alert.id)" class="p-2 opacity-70 hover:opacity-100 touch-manipulation">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function productionDashboard() {
    return {
        activeOrders: 0,
        todayProduction: 0,
        averageEfficiency: 0,
        pendingQC: 0,
        activeProductionOrders: [],
        alerts: [],
        chartPeriod: 'week',

        init() {
            this.loadDashboardData();
            this.loadActiveOrders();
            this.loadAlerts();
            this.initChart();
        },

        async loadDashboardData() {
            try {
                const response = await fetch('/api/production/dashboard');
                const data = await response.json();
                
                this.activeOrders = data.active_orders;
                this.todayProduction = data.today_production;
                this.averageEfficiency = data.average_efficiency;
                this.pendingQC = data.pending_qc;
            } catch (error) {
                console.error('Error loading dashboard data:', error);
            }
        },

        async loadActiveOrders() {
            try {
                const response = await fetch('/api/production-orders?status=in_progress&limit=5');
                const data = await response.json();
                this.activeProductionOrders = data.data;
            } catch (error) {
                console.error('Error loading active orders:', error);
            }
        },

        async loadAlerts() {
            try {
                const response = await fetch('/api/production/alerts');
                const data = await response.json();
                this.alerts = data.data;
            } catch (error) {
                console.error('Error loading alerts:', error);
            }
        },

        initChart() {
            // Initialize production chart
            const ctx = document.getElementById('productionChart');
            if (ctx) {
                // Chart implementation would go here
                console.log('Initialize production chart');
            }
        },

        updateChart() {
            console.log('Update chart for period:', this.chartPeriod);
        },

        refreshData() {
            this.loadDashboardData();
            this.loadActiveOrders();
            this.loadAlerts();
        },

        getStatusColor(status) {
            const colors = {
                'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                'completed': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'on_hold': 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getStatusText(status) {
            const texts = {
                'pending': 'Pending',
                'in_progress': 'In Progress',
                'completed': 'Selesai',
                'on_hold': 'Ditahan'
            };
            return texts[status] || status;
        },

        getAlertColor(type) {
            const colors = {
                'warning': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                'error': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                'info': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400'
            };
            return colors[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        },

        viewOrder(order) {
            window.location.href = `/production/orders/${order.id}`;
        },

        updateProgress(order) {
            console.log('Update progress for order:', order);
        },

        dismissAlert(alertId) {
            this.alerts = this.alerts.filter(alert => alert.id !== alertId);
        }
    }
}
</script>
@endpush