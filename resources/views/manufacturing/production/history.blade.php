@extends('layouts.dashboard')

@section('title', 'Riwayat Produksi')
@section('page-title', 'Riwayat Produksi')

@section('content')
<div x-data="productionHistory()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Riwayat Produksi</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Lihat riwayat dan analisis produksi yang telah selesai</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.production.index') }}"
               class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Produksi
            </a>
            <!-- <button @click="exportHistory" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export
            </button>
            <button @click="generateReport" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 3H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Generate Report
            </button> -->
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Total Produksi Bulan Ini</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="monthlyProduction">0</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Total Biaya Produksi</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="formatCurrency(totalProductionCost)">Rp 0</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Waktu Produksi Rata-rata</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="averageProductionTime + ' jam'">0 jam</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Date Range -->
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
                <select x-model="productFilter" @change="filterData" class="input">
                    <option value="">Semua Produk</option>
                    <option value="Minuman Temulawak">Minuman Temulawak</option>
                    <option value="Krupuk Bro">Krupuk Bro</option>
                </select>
                <select x-model="periodFilter" @change="filterData" class="input">
                    <option value="">Semua Periode</option>
                    <option value="today">Hari Ini</option>
                    <option value="week">Minggu Ini</option>
                    <option value="month">Bulan Ini</option>
                    <option value="quarter">Kuartal Ini</option>
                </select>
            </div>
            <div class="flex gap-2">
                <input type="date" x-model="startDate" @change="filterData" class="input">
                <span class="flex items-center text-muted">s/d</span>
                <input type="date" x-model="endDate" @change="filterData" class="input">
            </div>
        </div>
    </div>

    <!-- Production History Table/Cards -->
    <div class="card">
        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-border/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Batch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Jumlah Produksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Tanggal Selesai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Durasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Efisiensi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Biaya</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">QC Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="order in filteredData" :key="order.id">
                        <tr class="hover:bg-border/30">
                            <td class="px-6 py-4">
                                <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="order.order_number"></div>
                                <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="'Operator: ' + order.operator_name"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="order.product_name"></div>
                                <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="order.product_sku"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="order.batch_count + ' batch'"></td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-foreground" x-text="order.actual_production + ' unit'"></div>
                                <div class="text-sm text-muted" x-text="'Target: ' + order.target_production + ' unit'"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="formatDate(order.completed_date)"></td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-foreground" x-text="order.actual_duration + ' jam'"></div>
                                <div class="text-sm text-muted" x-text="'Est: ' + order.estimated_duration + ' jam'"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-foreground" x-text="order.efficiency + '%'"></span>
                                    <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full"
                                             :class="getEfficiencyColor(order.efficiency)"
                                             :style="'width: ' + Math.min(order.efficiency, 100) + '%'"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-foreground" x-text="formatCurrency(order.actual_cost)"></div>
                                <div class="text-sm text-muted" x-text="'Budget: ' + formatCurrency(order.estimated_cost)"></div>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="getQCStatusColor(order.qc_status)"
                                      class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                      x-text="getQCStatusText(order.qc_status)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <!-- <button @click="viewDetails(order)" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg touch-manipulation" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button> -->
                                    <!-- <button @click="viewAnalysis(order)" class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg touch-manipulation" title="Analisis">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </button>
                                    <button @click="printReport(order)" class="p-2 text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-lg touch-manipulation" title="Print Report">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                    </button> -->
                                    <button @click="reorderProduction(order)" class="p-2 text-orange-600 hover:text-orange-800 hover:bg-orange-50 rounded-lg touch-manipulation" title="Reorder">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
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
                    <!-- Header with order number and QC status -->
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-base md:text-lg font-medium text-foreground leading-tight" x-text="order.order_number"></h3>
                            <p class="text-xs md:text-sm text-muted leading-relaxed" x-text="'Operator: ' + order.operator_name"></p>
                        </div>
                        <span :class="getQCStatusColor(order.qc_status)"
                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              x-text="getQCStatusText(order.qc_status)"></span>
                    </div>

                    <!-- Product Info -->
                    <div>
                        <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="order.product_name"></div>
                        <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="order.product_sku + ' • ' + order.batch_count + ' batch'"></div>
                    </div>

                    <!-- Production Details -->
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-muted">Produksi Aktual:</span>
                            <p class="font-medium text-foreground" x-text="order.actual_production + ' unit'"></p>
                        </div>
                        <div>
                            <span class="text-muted">Target:</span>
                            <p class="font-medium text-foreground" x-text="order.target_production + ' unit'"></p>
                        </div>
                        <div>
                            <span class="text-muted">Durasi Aktual:</span>
                            <p class="font-medium text-foreground" x-text="order.actual_duration + ' jam'"></p>
                        </div>
                        <div>
                            <span class="text-muted">Estimasi:</span>
                            <p class="font-medium text-foreground" x-text="order.estimated_duration + ' jam'"></p>
                        </div>
                    </div>

                    <!-- Efficiency Bar -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-muted">Efisiensi:</span>
                            <span class="text-sm font-medium text-foreground" x-text="order.efficiency + '%'"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full"
                                 :class="getEfficiencyColor(order.efficiency)"
                                 :style="'width: ' + Math.min(order.efficiency, 100) + '%'"></div>
                        </div>
                    </div>

                    <!-- Cost Information -->
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-muted">Biaya Aktual:</span>
                            <p class="font-medium text-foreground" x-text="formatCurrency(order.actual_cost)"></p>
                        </div>
                        <div>
                            <span class="text-muted">Budget:</span>
                            <p class="font-medium text-foreground" x-text="formatCurrency(order.estimated_cost)"></p>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs md:text-sm text-muted">Tanggal Selesai:</span>
                        <p class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="formatDate(order.completed_date)"></p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-2 pt-2 border-t border-border">
                        <button @click="viewDetails(order)"
                                class="flex items-center px-2 py-1 text-xs text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Detail
                        </button>
                        <button @click="viewAnalysis(order)"
                                class="flex items-center px-2 py-1 text-xs text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Analisis
                        </button>
                        <button @click="printReport(order)"
                                class="flex items-center px-2 py-1 text-xs text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print
                        </button>
                        <button @click="reorderProduction(order)"
                                class="flex items-center px-2 py-1 text-xs text-orange-600 hover:text-orange-800 hover:bg-orange-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reorder
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Production Analytics -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Efficiency Trend Chart -->
        <div class="card p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Tren Efisiensi Produksi</h3>
            <div class="h-64 flex items-center justify-center bg-border/30 rounded-lg">
                <p class="text-muted">Chart akan ditampilkan di sini</p>
            </div>
        </div>

        <!-- Cost Analysis -->
        <div class="card p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Analisis Biaya vs Target</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-muted">Total Budget</span>
                    <span class="text-sm font-medium text-foreground" x-text="formatCurrency(totalBudget)"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-muted">Total Actual</span>
                    <span class="text-sm font-medium text-foreground" x-text="formatCurrency(totalActualCost)"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-muted">Variance</span>
                    <span class="text-sm font-medium" 
                          :class="costVariance >= 0 ? 'text-red-600' : 'text-green-600'" 
                          x-text="formatCurrency(Math.abs(costVariance)) + (costVariance >= 0 ? ' Over' : ' Under')"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full" 
                         :class="costVariancePercentage <= 5 ? 'bg-green-500' : costVariancePercentage <= 10 ? 'bg-yellow-500' : 'bg-red-500'"
                         :style="'width: ' + Math.min(Math.abs(costVariancePercentage), 100) + '%'"></div>
                </div>
                <p class="text-xs text-muted" x-text="'Variance: ' + costVariancePercentage.toFixed(1) + '%'"></p>
            </div>
        </div>
    </div>

    <!-- Top Products by Production -->
    <div class="card p-6">
        <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Produk Terbanyak Diproduksi</h3>
        <div class="space-y-3">
            <template x-for="product in topProducts" :key="product.name">
                <div class="flex items-center justify-between p-3 bg-border/30 rounded-lg">
                    <div>
                        <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="product.name"></div>
                        <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="product.total_batches + ' batch • ' + product.total_units + ' unit'"></div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium text-foreground" x-text="formatCurrency(product.total_cost)"></div>
                        <div class="text-sm text-muted" x-text="product.avg_efficiency + '% efisiensi'"></div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function productionHistory() {
    return {
        orders: [],
        filteredData: [],
        search: '',
        productFilter: '',
        periodFilter: '',
        startDate: '',
        endDate: '',

        init() {
            this.loadData();
            this.setDefaultDateRange();
        },

        setDefaultDateRange() {
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            
            this.startDate = firstDay.toISOString().split('T')[0];
            this.endDate = today.toISOString().split('T')[0];
        },

        async loadData() {
            try {
                const response = await fetch('/api/production/orders/get/history');
                const data = await response.json();
                this.orders = data.data;
                this.filteredData = this.orders;
            } catch (error) {
                console.error('Error loading data:', error);
            }
        },

        filterData() {
            this.filteredData = this.orders.filter(order => {
                const matchesSearch = order.order_number.toLowerCase().includes(this.search.toLowerCase()) ||
                                    order.product_name.toLowerCase().includes(this.search.toLowerCase());
                const matchesProduct = !this.productFilter || order.product_name.includes(this.productFilter);
                const matchesPeriod = this.matchesPeriodFilter(order);
                const matchesDateRange = this.matchesDateRange(order);
                
                return matchesSearch && matchesProduct && matchesPeriod && matchesDateRange;
            });
        },

        matchesPeriodFilter(order) {
            if (!this.periodFilter) return true;
            
            const orderDate = new Date(order.completed_date);
            const today = new Date();
            
            switch (this.periodFilter) {
                case 'today':
                    return orderDate.toDateString() === today.toDateString();
                case 'week':
                    const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                    return orderDate >= weekAgo;
                case 'month':
                    return orderDate.getMonth() === today.getMonth() && orderDate.getFullYear() === today.getFullYear();
                case 'quarter':
                    const quarter = Math.floor(today.getMonth() / 3);
                    const orderQuarter = Math.floor(orderDate.getMonth() / 3);
                    return orderQuarter === quarter && orderDate.getFullYear() === today.getFullYear();
                default:
                    return true;
            }
        },

        matchesDateRange(order) {
            if (!this.startDate && !this.endDate) return true;
            
            const orderDate = new Date(order.completed_date);
            const start = this.startDate ? new Date(this.startDate) : null;
            const end = this.endDate ? new Date(this.endDate) : null;
            
            if (start && orderDate < start) return false;
            if (end && orderDate > end) return false;
            
            return true;
        },

        get monthlyProduction() {
            const currentMonth = new Date().getMonth();
            const currentYear = new Date().getFullYear();
            
            return this.orders
                .filter(order => {
                    const orderDate = new Date(order.completed_date);
                    return orderDate.getMonth() === currentMonth && orderDate.getFullYear() === currentYear;
                })
                .reduce((sum, order) => sum + order.actual_production, 0);
        },

        get averageEfficiency() {
            if (this.orders.length === 0) return 0;
            const totalEfficiency = this.orders.reduce((sum, order) => sum + order.efficiency, 0);
            return Math.round(totalEfficiency / this.orders.length);
        },

        get totalProductionCost() {
            return this.orders.reduce((sum, order) => sum + order.actual_cost, 0);
        },

        get averageProductionTime() {
            if (this.orders.length === 0) return 0;
            const totalTime = this.orders.reduce((sum, order) => sum + order.actual_duration, 0);
            return Math.round(totalTime / this.orders.length);
        },

        get totalBudget() {
            return this.orders.reduce((sum, order) => sum + order.estimated_cost, 0);
        },

        get totalActualCost() {
            return this.orders.reduce((sum, order) => sum + order.actual_cost, 0);
        },

        get costVariance() {
            return this.totalActualCost - this.totalBudget;
        },

        get costVariancePercentage() {
            if (this.totalBudget === 0) return 0;
            return (this.costVariance / this.totalBudget) * 100;
        },

        get topProducts() {
            const productStats = {};
            
            this.orders.forEach(order => {
                if (!productStats[order.product_name]) {
                    productStats[order.product_name] = {
                        name: order.product_name,
                        total_batches: 0,
                        total_units: 0,
                        total_cost: 0,
                        total_efficiency: 0,
                        count: 0
                    };
                }
                
                const stats = productStats[order.product_name];
                stats.total_batches += order.batch_count;
                stats.total_units += order.actual_production;
                stats.total_cost += order.actual_cost;
                stats.total_efficiency += order.efficiency;
                stats.count += 1;
            });
            
            return Object.values(productStats)
                .map(stats => ({
                    ...stats,
                    avg_efficiency: Math.round(stats.total_efficiency / stats.count)
                }))
                .sort((a, b) => b.total_units - a.total_units)
                .slice(0, 5);
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

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        },

        viewDetails(order) {
            window.location.href = `/manufacturing/production-orders/${order.id}`;
        },

        viewAnalysis(order) {
            window.location.href = `/manufacturing/production-orders/${order.id}/analysis`;
        },

        printReport(order) {
            window.open(`/manufacturing/production-orders/${order.id}/report`, '_blank');
        },

        reorderProduction(order) {
            window.location.href = `/manufacturing/production/orders/create?reorder=${order.id}`;
        },

        exportHistory() {
            console.log('Export history');
        },

        generateReport() {
            console.log('Generate report');
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