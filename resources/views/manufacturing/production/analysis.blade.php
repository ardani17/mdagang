@extends('layouts.dashboard')

@section('title', 'Analisis Produksi')
@section('page-title', 'Analisis Produksi')

@section('content')
<div x-data="productionAnalysis()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Analisis Produksi</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed" x-text="'Order: ' + order.order_number">Analisis mendalam performa produksi</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.production.history') }}" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <button @click="viewDetail" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Lihat Detail
            </button>
            <button @click="exportAnalysis" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Analisis
            </button>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Efisiensi Produksi</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="order.efficiency + '%'">0%</p>
                    <p class="text-xs text-muted mt-1" x-text="getEfficiencyStatus(order.efficiency)">-</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                     :class="getEfficiencyBgColor(order.efficiency)">
                    <svg class="w-6 h-6" :class="getEfficiencyTextColor(order.efficiency)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Variance Biaya</p>
                    <p class="text-xl md:text-2xl font-bold leading-none"
                       :class="order.cost_variance >= 0 ? 'text-red-600' : 'text-green-600'"
                       x-text="formatCurrency(Math.abs(order.cost_variance))">Rp 0</p>
                    <p class="text-xs text-muted mt-1" x-text="order.cost_variance >= 0 ? 'Over Budget' : 'Under Budget'">-</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                     :class="order.cost_variance >= 0 ? 'bg-red-100 dark:bg-red-900/20' : 'bg-green-100 dark:bg-green-900/20'">
                    <svg class="w-6 h-6" :class="order.cost_variance >= 0 ? 'text-red-600' : 'text-green-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Variance Waktu</p>
                    <p class="text-xl md:text-2xl font-bold leading-none"
                       :class="order.time_variance >= 0 ? 'text-red-600' : 'text-green-600'"
                       x-text="Math.abs(order.time_variance) + ' jam'">0 jam</p>
                    <p class="text-xs text-muted mt-1" x-text="order.time_variance >= 0 ? 'Lebih Lama' : 'Lebih Cepat'">-</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                     :class="order.time_variance >= 0 ? 'bg-red-100 dark:bg-red-900/20' : 'bg-green-100 dark:bg-green-900/20'">
                    <svg class="w-6 h-6" :class="order.time_variance >= 0 ? 'text-red-600' : 'text-green-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Yield Rate</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="order.yield_rate + '%'">0%</p>
                    <p class="text-xs text-muted mt-1" x-text="getYieldStatus(order.yield_rate)">-</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                     :class="getYieldBgColor(order.yield_rate)">
                    <svg class="w-6 h-6" :class="getYieldTextColor(order.yield_rate)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Production Performance Chart -->
        <div class="card p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Performa vs Target</h3>
            <div class="space-y-4">
                <!-- Production Volume -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-muted">Volume Produksi</span>
                        <span class="text-sm font-medium text-foreground" x-text="order.actual_production + ' / ' + order.target_production + ' unit'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all duration-300"
                             :class="getProductionVolumeColor(order.production_achievement)"
                             :style="'width: ' + Math.min(order.production_achievement, 100) + '%'"></div>
                    </div>
                    <p class="text-xs text-muted mt-1" x-text="order.production_achievement + '% dari target'"></p>
                </div>

                <!-- Time Performance -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-muted">Waktu Produksi</span>
                        <span class="text-sm font-medium text-foreground" x-text="order.actual_duration + ' / ' + order.estimated_duration + ' jam'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all duration-300"
                             :class="getTimePerformanceColor(order.time_performance)"
                             :style="'width: ' + Math.min(order.time_performance, 100) + '%'"></div>
                    </div>
                    <p class="text-xs text-muted mt-1" x-text="order.time_performance + '% efisiensi waktu'"></p>
                </div>

                <!-- Cost Performance -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-muted">Biaya Produksi</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatCurrency(order.actual_cost) + ' / ' + formatCurrency(order.estimated_cost)"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all duration-300"
                             :class="getCostPerformanceColor(order.cost_performance)"
                             :style="'width: ' + Math.min(order.cost_performance, 100) + '%'"></div>
                    </div>
                    <p class="text-xs text-muted mt-1" x-text="order.cost_performance + '% dari budget'"></p>
                </div>
            </div>
        </div>

        <!-- Material Efficiency -->
        <div class="card p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Efisiensi Bahan</h3>
            <div class="space-y-3">
                <template x-for="material in order.material_analysis" :key="material.id">
                    <div class="p-3 bg-border/30 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="text-sm font-medium text-foreground" x-text="material.name"></p>
                                <p class="text-xs text-muted" x-text="material.supplier"></p>
                            </div>
                            <span class="text-xs font-medium px-2 py-1 rounded-full"
                                  :class="getMaterialEfficiencyColor(material.efficiency)"
                                  x-text="material.efficiency + '%'"></span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <div>
                                <span class="text-muted">Target:</span>
                                <p class="font-medium text-foreground" x-text="material.target_quantity + ' ' + material.unit"></p>
                            </div>
                            <div>
                                <span class="text-muted">Aktual:</span>
                                <p class="font-medium text-foreground" x-text="material.actual_quantity + ' ' + material.unit"></p>
                            </div>
                            <div>
                                <span class="text-muted">Waste:</span>
                                <p class="font-medium" 
                                   :class="material.waste_percentage > 5 ? 'text-red-600' : 'text-green-600'"
                                   x-text="material.waste_percentage + '%'"></p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Detailed Analysis -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Cost Breakdown -->
        <div class="card p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Breakdown Biaya</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted">Biaya Bahan</span>
                    <div class="text-right">
                        <p class="text-sm font-medium text-foreground" x-text="formatCurrency(order.material_cost)"></p>
                        <p class="text-xs text-muted" x-text="((order.material_cost / order.actual_cost) * 100).toFixed(1) + '%'"></p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted">Biaya Tenaga Kerja</span>
                    <div class="text-right">
                        <p class="text-sm font-medium text-foreground" x-text="formatCurrency(order.labor_cost)"></p>
                        <p class="text-xs text-muted" x-text="((order.labor_cost / order.actual_cost) * 100).toFixed(1) + '%'"></p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted">Biaya Overhead</span>
                    <div class="text-right">
                        <p class="text-sm font-medium text-foreground" x-text="formatCurrency(order.overhead_cost)"></p>
                        <p class="text-xs text-muted" x-text="((order.overhead_cost / order.actual_cost) * 100).toFixed(1) + '%'"></p>
                    </div>
                </div>
                <hr class="border-border">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-foreground">Total</span>
                    <p class="text-sm font-bold text-foreground" x-text="formatCurrency(order.actual_cost)"></p>
                </div>
            </div>
        </div>

        <!-- Quality Metrics -->
        <div class="card p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Metrik Kualitas</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted">First Pass Yield</span>
                    <span class="text-sm font-medium text-foreground" x-text="order.first_pass_yield + '%'">-</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted">Defect Rate</span>
                    <span class="text-sm font-medium" 
                          :class="order.defect_rate > 2 ? 'text-red-600' : 'text-green-600'"
                          x-text="order.defect_rate + '%'">-</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted">Rework Rate</span>
                    <span class="text-sm font-medium" 
                          :class="order.rework_rate > 5 ? 'text-red-600' : 'text-green-600'"
                          x-text="order.rework_rate + '%'">-</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted">QC Status</span>
                    <span :class="getQCStatusColor(order.qc_status)"
                          class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                          x-text="getQCStatusText(order.qc_status)">-</span>
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="card p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Rekomendasi</h3>
            <div class="space-y-3">
                <template x-for="recommendation in recommendations" :key="recommendation.id">
                    <div class="p-3 rounded-lg" :class="getRecommendationColor(recommendation.priority)">
                        <div class="flex items-start space-x-2">
                            <div class="flex-shrink-0 w-2 h-2 rounded-full mt-2" :class="getRecommendationDotColor(recommendation.priority)"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-foreground" x-text="recommendation.title"></p>
                                <p class="text-xs text-muted mt-1" x-text="recommendation.description"></p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Historical Comparison -->
    <div class="card p-6">
        <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Perbandingan Historis</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-border/50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Metrik</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Order Ini</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Rata-rata 3 Bulan</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Best Performance</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Trend</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-foreground">Efisiensi</td>
                        <td class="px-4 py-3 text-sm text-foreground" x-text="order.efficiency + '%'"></td>
                        <td class="px-4 py-3 text-sm text-foreground" x-text="historical.avg_efficiency + '%'"></td>
                        <td class="px-4 py-3 text-sm text-foreground" x-text="historical.best_efficiency + '%'"></td>
                        <td class="px-4 py-3">
                            <span :class="getTrendColor(historical.efficiency_trend)" class="text-sm font-medium" x-text="getTrendText(historical.efficiency_trend)"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-foreground">Biaya per Unit</td>
                        <td class="px-4 py-3 text-sm text-foreground" x-text="formatCurrency(order.cost_per_unit)"></td>
                        <td class="px-4 py-3 text-sm text-foreground" x-text="formatCurrency(historical.avg_cost_per_unit)"></td>
                        <td class="px-4 py-3 text-sm text-foreground" x-text="formatCurrency(historical.best_cost_per_unit)"></td>
                        <td class="px-4 py-3">
                            <span :class="getTrendColor(historical.cost_trend)" class="text-sm font-medium" x-text="getTrendText(historical.cost_trend)"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-foreground">Yield Rate</td>
                        <td class="px-4 py-3 text-sm text-foreground" x-text="order.yield_rate + '%'"></td>
                        <td class="px-4 py-3 text-sm text-foreground" x-text="historical.avg_yield_rate + '%'"></td>
                        <td class="px-4 py-3 text-sm text-foreground" x-text="historical.best_yield_rate + '%'"></td>
                        <td class="px-4 py-3">
                            <span :class="getTrendColor(historical.yield_trend)" class="text-sm font-medium" x-text="getTrendText(historical.yield_trend)"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function productionAnalysis() {
    return {
        order: {
            id: {{ $id ?? 1 }}, // This would come from the route parameter
            order_number: '',
            efficiency: 0,
            cost_variance: 0,
            time_variance: 0,
            yield_rate: 0,
            actual_production: 0,
            target_production: 0,
            actual_duration: 0,
            estimated_duration: 0,
            actual_cost: 0,
            estimated_cost: 0,
            material_cost: 0,
            labor_cost: 0,
            overhead_cost: 0,
            production_achievement: 0,
            time_performance: 0,
            cost_performance: 0,
            cost_per_unit: 0,
            first_pass_yield: 0,
            defect_rate: 0,
            rework_rate: 0,
            qc_status: '',
            material_analysis: []
        },
        historical: {
            avg_efficiency: 0,
            best_efficiency: 0,
            efficiency_trend: 0,
            avg_cost_per_unit: 0,
            best_cost_per_unit: 0,
            cost_trend: 0,
            avg_yield_rate: 0,
            best_yield_rate: 0,
            yield_trend: 0
        },
        recommendations: [],

        async init() {
            await this.loadData();
            this.generateRecommendations();
        },

        async loadData() {
            try {
                const response = await fetch(`/api/production-orders/${this.order.id}/analysis`);
                const result = await response.json();
                
                if (result.success) {
                    this.order = { ...this.order, ...result.data.order };
                    this.historical = { ...this.historical, ...result.data.historical };
                } else {
                    alert('Gagal memuat data: ' + result.message);
                }
            } catch (error) {
                console.error('Error loading data:', error);
                alert('Terjadi kesalahan saat memuat data');
            }
        },

        generateRecommendations() {
            this.recommendations = [];
            
            if (this.order.efficiency < 85) {
                this.recommendations.push({
                    id: 1,
                    priority: 'high',
                    title: 'Tingkatkan Efisiensi Produksi',
                    description: 'Efisiensi produksi di bawah target 85%. Evaluasi proses dan pelatihan operator.'
                });
            }
            
            if (this.order.cost_variance > 0) {
                this.recommendations.push({
                    id: 2,
                    priority: 'medium',
                    title: 'Kontrol Biaya Produksi',
                    description: 'Biaya produksi melebihi budget. Review penggunaan bahan dan efisiensi proses.'
                });
            }
            
            if (this.order.defect_rate > 2) {
                this.recommendations.push({
                    id: 3,
                    priority: 'high',
                    title: 'Perbaiki Kontrol Kualitas',
                    description: 'Tingkat defect tinggi. Perkuat quality control dan training operator.'
                });
            }
            
            if (this.order.yield_rate < 95) {
                this.recommendations.push({
                    id: 4,
                    priority: 'medium',
                    title: 'Optimasi Yield Rate',
                    description: 'Yield rate dapat ditingkatkan. Evaluasi resep dan proses produksi.'
                });
            }
        },

        getEfficiencyStatus(efficiency) {
            if (efficiency >= 95) return 'Excellent';
            if (efficiency >= 85) return 'Good';
            if (efficiency >= 75) return 'Fair';
            return 'Poor';
        },

        getEfficiencyBgColor(efficiency) {
            if (efficiency >= 95) return 'bg-green-100 dark:bg-green-900/20';
            if (efficiency >= 85) return 'bg-yellow-100 dark:bg-yellow-900/20';
            if (efficiency >= 75) return 'bg-orange-100 dark:bg-orange-900/20';
            return 'bg-red-100 dark:bg-red-900/20';
        },

        getEfficiencyTextColor(efficiency) {
            if (efficiency >= 95) return 'text-green-600';
            if (efficiency >= 85) return 'text-yellow-600';
            if (efficiency >= 75) return 'text-orange-600';
            return 'text-red-600';
        },

        getYieldStatus(yield_rate) {
            if (yield_rate >= 98) return 'Excellent';
            if (yield_rate >= 95) return 'Good';
            if (yield_rate >= 90) return 'Fair';
            return 'Poor';
        },

        getYieldBgColor(yield_rate) {
            if (yield_rate >= 98) return 'bg-green-100 dark:bg-green-900/20';
            if (yield_rate >= 95) return 'bg-yellow-100 dark:bg-yellow-900/20';
            if (yield_rate >= 90) return 'bg-orange-100 dark:bg-orange-900/20';
            return 'bg-red-100 dark:bg-red-900/20';
        },

        getYieldTextColor(yield_rate) {
            if (yield_rate >= 98) return 'text-green-600';
            if (yield_rate >= 95) return 'text-yellow-600';
            if (yield_rate >= 90) return 'text-orange-600';
            return 'text-red-600';
        },

        getProductionVolumeColor(achievement) {
            if (achievement >= 100) return 'bg-green-500';
            if (achievement >= 95) return 'bg-yellow-500';
            if (achievement >= 85) return 'bg-orange-500';
            return 'bg-red-500';
        },

        getTimePerformanceColor(performance) {
            if (performance >= 100) return 'bg-green-500';
            if (performance >= 90) return 'bg-yellow-500';
            if (performance >= 80) return 'bg-orange-500';
            return 'bg-red-500';
        },

        getCostPerformanceColor(performance) {
            if (performance <= 100) return 'bg-green-500';
            if (performance <= 110) return 'bg-yellow-500';
            if (performance <= 120) return 'bg-orange-500';
            return 'bg-red-500';
        },

        getMaterialEfficiencyColor(efficiency) {
            if (efficiency >= 95) return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
            if (efficiency >= 85) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400';
            return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400';
        },

        getQCStatusColor(status) {
            const colors = {
                'passed': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green
-400',
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

        getRecommendationColor(priority) {
            const colors = {
                'high': 'bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800',
                'medium': 'bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-800',
                'low': 'bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800'
            };
            return colors[priority] || 'bg-gray-50 dark:bg-gray-900/10 border border-gray-200 dark:border-gray-800';
        },

        getRecommendationDotColor(priority) {
            const colors = {
                'high': 'bg-red-500',
                'medium': 'bg-yellow-500',
                'low': 'bg-blue-500'
            };
            return colors[priority] || 'bg-gray-500';
        },

        getTrendColor(trend) {
            if (trend > 0) return 'text-green-600';
            if (trend < 0) return 'text-red-600';
            return 'text-gray-600';
        },

        getTrendText(trend) {
            if (trend > 0) return '↗ Membaik';
            if (trend < 0) return '↘ Menurun';
            return '→ Stabil';
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        },

        viewDetail() {
            window.location.href = `/production-orders/${this.order.id}`;
        },

        exportAnalysis() {
            window.open(`/production-orders/${this.order.id}/analysis/export`, '_blank');
        }
    }
}
</script>
@endpush