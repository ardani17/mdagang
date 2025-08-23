@extends('layouts.dashboard')

@section('title', 'Detail Order Produksi')
@section('page-title', 'Detail Order Produksi')

@section('content')
<div x-data="productionOrderDetail()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight" x-text="order.order_number">Loading...</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Detail lengkap order produksi yang telah selesai</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.production.history') }}" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <button @click="printReport" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Report
            </button>
            <button @click="viewAnalysis" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Lihat Analisis
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4 lg:space-y-6">
            <!-- Order Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Order</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Nomor Order</label>
                        <p class="text-base font-medium text-foreground" x-text="order.order_number">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Status</label>
                        <span :class="getStatusColor(order.status)"
                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              x-text="getStatusText(order.status)">-</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Tanggal Mulai</label>
                        <p class="text-base text-foreground" x-text="formatDate(order.start_date)">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Tanggal Selesai</label>
                        <p class="text-base text-foreground" x-text="formatDate(order.completed_date)">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Operator</label>
                        <p class="text-base text-foreground" x-text="order.operator_name">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Shift</label>
                        <p class="text-base text-foreground" x-text="order.shift">-</p>
                    </div>
                </div>
            </div>

            <!-- Product Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Produk</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Nama Produk</label>
                        <p class="text-base font-medium text-foreground" x-text="order.product_name">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">SKU</label>
                        <p class="text-base text-foreground" x-text="order.product_sku">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Resep</label>
                        <p class="text-base text-foreground" x-text="order.recipe_name">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Versi Resep</label>
                        <p class="text-base text-foreground" x-text="order.recipe_version">-</p>
                    </div>
                </div>
            </div>

            <!-- Production Details -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Detail Produksi</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Target Produksi</label>
                        <p class="text-xl font-bold text-foreground" x-text="order.target_production + ' unit'">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Produksi Aktual</label>
                        <p class="text-xl font-bold text-foreground" x-text="order.actual_production + ' unit'">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Jumlah Batch</label>
                        <p class="text-xl font-bold text-foreground" x-text="order.batch_count + ' batch'">-</p>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Durasi Estimasi</label>
                        <p class="text-base text-foreground" x-text="order.estimated_duration + ' jam'">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Durasi Aktual</label>
                        <p class="text-base text-foreground" x-text="order.actual_duration + ' jam'">-</p>
                    </div>
                </div>

                <!-- Efficiency Bar -->
                <div class="mt-6">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-muted">Efisiensi Produksi</label>
                        <span class="text-lg font-bold text-foreground" x-text="order.efficiency + '%'">-</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all duration-300"
                             :class="getEfficiencyColor(order.efficiency)"
                             :style="'width: ' + Math.min(order.efficiency, 100) + '%'"></div>
                    </div>
                </div>
            </div>

            <!-- Material Usage -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Penggunaan Bahan</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-border/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Bahan</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Target</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Aktual</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Variance</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <template x-for="material in order.materials" :key="material.id">
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-foreground" x-text="material.name"></div>
                                        <div class="text-xs text-muted" x-text="material.supplier"></div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-foreground" x-text="material.target_quantity + ' ' + material.unit"></td>
                                    <td class="px-4 py-3 text-sm text-foreground" x-text="material.actual_quantity + ' ' + material.unit"></td>
                                    <td class="px-4 py-3">
                                        <span :class="material.variance >= 0 ? 'text-red-600' : 'text-green-600'"
                                              class="text-sm font-medium"
                                              x-text="(material.variance >= 0 ? '+' : '') + material.variance + ' ' + material.unit"></span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-foreground" x-text="formatCurrency(material.actual_cost)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Production Timeline -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Timeline Produksi</h3>
                <div class="space-y-4">
                    <template x-for="event in order.timeline" :key="event.id">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-3 h-3 rounded-full mt-2"
                                 :class="getTimelineColor(event.type)"></div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-foreground" x-text="event.title"></p>
                                    <span class="text-xs text-muted" x-text="formatDateTime(event.timestamp)"></span>
                                </div>
                                <p class="text-sm text-muted mt-1" x-text="event.description"></p>
                                <p class="text-xs text-muted mt-1" x-text="'Oleh: ' + event.user_name"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 lg:space-y-6">
            <!-- Cost Summary -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Ringkasan Biaya</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Biaya Bahan</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatCurrency(order.material_cost)">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Biaya Tenaga Kerja</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatCurrency(order.labor_cost)">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Biaya Overhead</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatCurrency(order.overhead_cost)">Rp 0</span>
                    </div>
                    <hr class="border-border">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-foreground">Total Estimasi</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatCurrency(order.estimated_cost)">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-foreground">Total Aktual</span>
                        <span class="text-sm font-bold text-foreground" x-text="formatCurrency(order.actual_cost)">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Variance</span>
                        <span class="text-sm font-medium"
                              :class="order.cost_variance >= 0 ? 'text-red-600' : 'text-green-600'"
                              x-text="formatCurrency(Math.abs(order.cost_variance)) + (order.cost_variance >= 0 ? ' Over' : ' Under')">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Quality Control -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Quality Control</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Status QC</span>
                        <span :class="getQCStatusColor(order.qc_status)"
                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              x-text="getQCStatusText(order.qc_status)">-</span>
                    </div>
                    <div x-show="order.qc_inspector">
                        <span class="text-sm text-muted">Inspector</span>
                        <p class="text-sm font-medium text-foreground" x-text="order.qc_inspector">-</p>
                    </div>
                    <div x-show="order.qc_date">
                        <span class="text-sm text-muted">Tanggal QC</span>
                        <p class="text-sm font-medium text-foreground" x-text="formatDate(order.qc_date)">-</p>
                    </div>
                    <div x-show="order.qc_notes">
                        <span class="text-sm text-muted">Catatan QC</span>
                        <p class="text-sm text-foreground" x-text="order.qc_notes">-</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <button @click="reorderProduction" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reorder Produksi
                    </button>
                    <button @click="exportData" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Data
                    </button>
                    <button @click="viewRecipe" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Lihat Resep
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function productionOrderDetail() {
    return {
        order: {
            id: {{ $id ?? 1 }}, // This would come from the route parameter
            order_number: '',
            status: '',
            start_date: '',
            completed_date: '',
            operator_name: '',
            shift: '',
            product_name: '',
            product_sku: '',
            recipe_name: '',
            recipe_version: '',
            target_production: 0,
            actual_production: 0,
            batch_count: 0,
            estimated_duration: 0,
            actual_duration: 0,
            efficiency: 0,
            material_cost: 0,
            labor_cost: 0,
            overhead_cost: 0,
            estimated_cost: 0,
            actual_cost: 0,
            cost_variance: 0,
            qc_status: '',
            qc_inspector: '',
            qc_date: '',
            qc_notes: '',
            materials: [],
            timeline: []
        },

        async init() {
            await this.loadData();
        },

        async loadData() {
            try {
                const response = await fetch(`/api/production-orders/${this.order.id}`);
                const result = await response.json();
                
                if (result.success) {
                    this.order = { ...this.order, ...result.data };
                } else {
                    alert('Gagal memuat data: ' + result.message);
                }
            } catch (error) {
                console.error('Error loading data:', error);
                alert('Terjadi kesalahan saat memuat data');
            }
        },

        getStatusColor(status) {
            const colors = {
                'completed': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getStatusText(status) {
            const texts = {
                'completed': 'Selesai',
                'in_progress': 'Dalam Proses',
                'cancelled': 'Dibatalkan',
                'pending': 'Menunggu'
            };
            return texts[status] || status;
        },

        getEfficiencyColor(efficiency) {
            if (efficiency >= 95) return 'bg-green-500';
            if (efficiency >= 85) return 'bg-yellow-500';
            if (efficiency >= 75) return 'bg-orange-500';
            return 'bg-red-500';
        },

        getQCStatusColor(status) {
            const colors = {
                'passed': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'failed': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                'not_required': 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getQCStatusText(status) {
            const texts = {
                'passed': 'Passed',
                'failed': 'Failed',
                'pending': 'Pending',
                'not_required': 'N/A'
            };
            return texts[status] || status;
        },

        getTimelineColor(type) {
            const colors = {
                'start': 'bg-blue-500',
                'material': 'bg-orange-500',
                'production': 'bg-green-500',
                'qc': 'bg-purple-500',
                'complete': 'bg-green-600',
                'issue': 'bg-red-500'
            };
            return colors[type] || 'bg-gray-500';
        },

        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        },

        formatDateTime(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        },

        printReport() {
            window.open(`/production-orders/${this.order.id}/report`, '_blank');
        },

        viewAnalysis() {
            window.location.href = `/production-orders/${this.order.id}/analysis`;
        },

        reorderProduction() {
            if (confirm('Apakah Anda yakin ingin membuat order produksi baru berdasarkan order ini?')) {
                window.location.href = `/production/orders/create?reorder=${this.order.id}`;
            }
        },

        exportData() {
            window.open(`/production-orders/${this.order.id}/export`, '_blank');
        },

        viewRecipe() {
            window.location.href = `/recipes/${this.order.recipe_id}`;
        }
    }
}
</script>
@endpush