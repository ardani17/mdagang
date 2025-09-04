@extends('layouts.dashboard')

@section('title', 'Order Produksi')
@section('page-title', 'Manajemen Order Produksi')

@section('content')
<div x-data="productionOrders()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Order Produksi</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Kelola dan monitor order produksi</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ route('manufacturing.production.orders.create') }}"
               class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Buat Order Produksi
            </a>
            <!-- <button @click="exportData" class="btn btn-outline">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export
            </button> -->
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Total Order</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="orders.length">0</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Sedang Berjalan</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="inProgressOrders">0</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Selesai</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="completedOrders">0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Pending</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="pendingOrders">0</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative">
                    <input type="text" 
                           x-model="search" 
                           @input="filterData"
                           placeholder="Cari order..." 
                           class="input pl-10">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <select x-model="statusFilter" @change="filterData" class="input">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">Sedang Berjalan</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
                <input type="date" x-model="dateFilter" @change="filterData" class="input">
            </div>
        </div>
    </div>

    <!-- Production Orders Table/Cards -->
    <div class="card">
        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-border/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Resep</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Operator</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="order in filteredData" :key="order.id">
                        <tr class="hover:bg-border/30">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="order.order_number"></div>
                                    <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="'Batch: ' + order.batch_size"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="order.recipe_name"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm md:text-base text-foreground leading-tight" x-text="order.quantity_produced + ' / ' + order.quantity_planned"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-primary h-2 rounded-full transition-all duration-300"
                                         :style="'width: ' + order.progress + '%'"></div>
                                </div>
                                <div class="text-xs text-muted mt-1" x-text="order.progress + '%'"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-foreground" x-text="formatDate(order.start_date)"></div>
                                <div class="text-xs text-muted" x-text="'Target: ' + formatDate(order.target_date)"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="order.operator"></td>
                            <td class="px-6 py-4">
                                <span :class="getStatusColor(order.status)"
                                      class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                      x-text="getStatusText(order.status)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <button @click="viewOrder(order)"
                                            class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors touch-manipulation"
                                            title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click="updateProgress(order)"
                                            class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors touch-manipulation"
                                            title="Update Progress">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="printOrder(order)"
                                            class="p-2 text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-lg transition-colors touch-manipulation"
                                            title="Print">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden space-y-4 p-4">
            <template x-for="order in filteredData" :key="order.id">
                <div class="bg-background border border-border rounded-lg p-4 space-y-3">
                    <!-- Header with order number and status -->
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-base md:text-lg font-medium text-foreground leading-tight" x-text="order.order_number"></h3>
                            <p class="text-xs md:text-sm text-muted leading-relaxed" x-text="'Batch: ' + order.batch_size"></p>
                        </div>
                        <span :class="getStatusColor(order.status)"
                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              x-text="getStatusText(order.status)"></span>
                    </div>

                    <!-- Recipe and Operator -->
                    <div class="grid grid-cols-1 gap-2 text-sm">
                        <div>
                            <span class="text-muted">Resep:</span>
                            <p class="font-medium text-foreground" x-text="order.recipe_name"></p>
                        </div>
                        <div>
                            <span class="text-muted">Operator:</span>
                            <p class="font-medium text-foreground" x-text="order.operator"></p>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-muted">Progress:</span>
                            <span class="text-sm font-medium text-foreground" x-text="order.progress + '%'"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-primary h-2 rounded-full transition-all duration-300"
                                 :style="'width: ' + order.progress + '%'"></div>
                        </div>
                    </div>

                    <!-- Quantity and Dates -->
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-muted">Quantity:</span>
                            <p class="font-medium text-foreground" x-text="order.quantity_produced + ' / ' + order.quantity_planned"></p>
                        </div>
                        <div>
                            <span class="text-muted">Mulai:</span>
                            <p class="font-medium text-foreground" x-text="formatDate(order.start_date)"></p>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs md:text-sm text-muted">Target Selesai:</span>
                        <p class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="formatDate(order.target_date)"></p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-2 border-t border-border">
                        <button @click="viewOrder(order)"
                                class="flex items-center px-3 py-2 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Lihat
                        </button>
                        <button @click="updateProgress(order)"
                                class="flex items-center px-3 py-2 text-sm text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Update
                        </button>
                        <button @click="printOrder(order)"
                                class="flex items-center px-3 py-2 text-sm text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print
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
function productionOrders() {
    return {
        orders: [],
        filteredData: [],
        search: '',
        statusFilter: '',
        dateFilter: '',

        init() {
            this.loadData();
        },

        async loadData() {
            try {
                const response = await fetch('/api/production/orders');
                const data = await response.json();
                this.orders = data.data.data;
                this.filteredData = this.orders;
            } catch (error) {
                console.error('Error loading data:', error);
            }
        },

        filterData() {
            this.filteredData = this.orders.filter(order => {
                const matchesSearch = order.order_number.toLowerCase().includes(this.search.toLowerCase()) ||
                                    order.recipe_name.toLowerCase().includes(this.search.toLowerCase()) ||
                                    order.operator.toLowerCase().includes(this.search.toLowerCase());
                const matchesStatus = !this.statusFilter || order.status === this.statusFilter;
                const matchesDate = !this.dateFilter || order.start_date === this.dateFilter;
                
                return matchesSearch && matchesStatus && matchesDate;
            });
        },

        get inProgressOrders() {
            return this.orders.filter(order => order.status === 'in_progress').length;
        },

        get completedOrders() {
            return this.orders.filter(order => order.status === 'completed').length;
        },

        get pendingOrders() {
            return this.orders.filter(order => order.status === 'pending').length;
        },

        getStatusColor(status) {
            const colors = {
                'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                'completed': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getStatusText(status) {
            const texts = {
                'pending': 'Pending',
                'in_progress': 'Sedang Berjalan',
                'completed': 'Selesai',
                'cancelled': 'Dibatalkan'
            };
            return texts[status] || status;
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
            console.log('View order:', order);
        },

        updateProgress(order) {
            console.log('Update progress:', order);
        },

        printOrder(order) {
            console.log('Print order:', order);
        },

        exportData() {
            console.log('Export production orders data');
        }
    }
}
</script>
@endpush