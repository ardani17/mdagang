@extends('layouts.dashboard')

@section('title', 'Detail Inspeksi QC')
@section('page-title', 'Detail Inspeksi Quality Control')

@section('content')
<div x-data="inspectionDetail()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight" x-text="inspection.inspection_id">Loading...</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Detail lengkap inspeksi quality control</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.production.quality-control') }}" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <button @click="editInspection" 
                    x-show="inspection.status === 'pending' || inspection.status === 'in_progress'"
                    class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Inspeksi
            </button>
            <button @click="printCertificate" 
                    x-show="inspection.status === 'passed'"
                    class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Certificate
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4 lg:space-y-6">
            <!-- Inspection Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Inspeksi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">ID Inspeksi</label>
                        <p class="text-base font-medium text-foreground" x-text="inspection.inspection_id">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Status</label>
                        <span :class="getStatusColor(inspection.status)"
                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              x-text="getStatusText(inspection.status)">-</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Tanggal Inspeksi</label>
                        <p class="text-base text-foreground" x-text="formatDate(inspection.inspection_date)">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Waktu Mulai</label>
                        <p class="text-base text-foreground" x-text="formatTime(inspection.start_time)">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Waktu Selesai</label>
                        <p class="text-base text-foreground" x-text="formatTime(inspection.end_time)">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Durasi</label>
                        <p class="text-base text-foreground" x-text="inspection.duration + ' menit'">-</p>
                    </div>
                </div>
            </div>

            <!-- Production Order Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Order Produksi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Nomor Order</label>
                        <p class="text-base font-medium text-foreground" x-text="inspection.production_order">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Batch Number</label>
                        <p class="text-base text-foreground" x-text="inspection.batch_number">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Nama Produk</label>
                        <p class="text-base font-medium text-foreground" x-text="inspection.product_name">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">SKU</label>
                        <p class="text-base text-foreground" x-text="inspection.product_sku">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Batch Size</label>
                        <p class="text-base text-foreground" x-text="inspection.batch_size + ' unit'">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Tanggal Produksi</label>
                        <p class="text-base text-foreground" x-text="formatDate(inspection.production_date)">-</p>
                    </div>
                </div>
            </div>

            <!-- Inspection Checklist -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Checklist Inspeksi</h3>
                <div class="space-y-4">
                    <template x-for="check in inspection.checklist" :key="check.id">
                        <div class="p-4 border border-border rounded-lg">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-foreground" x-text="check.category"></h4>
                                    <p class="text-xs text-muted mt-1" x-text="check.description"></p>
                                </div>
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                      :class="getCheckStatusColor(check.status)"
                                      x-text="getCheckStatusText(check.status)"></span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-muted mb-1">Standard</label>
                                    <p class="text-sm text-foreground" x-text="check.standard_value + ' ' + check.unit">-</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-muted mb-1">Hasil Aktual</label>
                                    <p class="text-sm font-medium text-foreground" x-text="check.actual_value + ' ' + check.unit">-</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-muted mb-1">Variance</label>
                                    <p class="text-sm font-medium"
                                       :class="Math.abs(check.variance) > check.tolerance ? 'text-red-600' : 'text-green-600'"
                                       x-text="(check.variance >= 0 ? '+' : '') + check.variance + ' ' + check.unit">-</p>
                                </div>
                            </div>
                            
                            <div x-show="check.notes" class="mt-3">
                                <label class="block text-xs font-medium text-muted mb-1">Catatan</label>
                                <p class="text-sm text-foreground" x-text="check.notes">-</p>
                            </div>
                            
                            <div x-show="check.images && check.images.length > 0" class="mt-3">
                                <label class="block text-xs font-medium text-muted mb-2">Foto Dokumentasi</label>
                                <div class="flex gap-2">
                                    <template x-for="image in check.images" :key="image.id">
                                        <img :src="image.url" 
                                             :alt="image.description"
                                             class="w-16 h-16 object-cover rounded-lg cursor-pointer hover:opacity-80"
                                             @click="viewImage(image)">
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Test Results -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Hasil Test</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-border/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Parameter</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Standard</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Hasil</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Metode</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <template x-for="test in inspection.test_results" :key="test.id">
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-foreground" x-text="test.parameter"></div>
                                        <div class="text-xs text-muted" x-text="test.description"></div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-foreground" x-text="test.standard_range"></td>
                                    <td class="px-4 py-3 text-sm font-medium text-foreground" x-text="test.result_value + ' ' + test.unit"></td>
                                    <td class="px-4 py-3">
                                        <span :class="getTestStatusColor(test.status)"
                                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                              x-text="getTestStatusText(test.status)"></span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-foreground" x-text="test.test_method"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Inspector Notes -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Catatan Inspector</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-2">Observasi Umum</label>
                        <p class="text-sm text-foreground bg-border/30 p-3 rounded-lg" x-text="inspection.general_notes || 'Tidak ada catatan khusus'"></p>
                    </div>
                    <div x-show="inspection.issues && inspection.issues.length > 0">
                        <label class="block text-sm font-medium text-muted mb-2">Issues Ditemukan</label>
                        <div class="space-y-2">
                            <template x-for="issue in inspection.issues" :key="issue.id">
                                <div class="p-3 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 rounded-lg">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-red-800 dark:text-red-400" x-text="issue.title"></p>
                                            <p class="text-xs text-red-600 dark:text-red-500 mt-1" x-text="issue.description"></p>
                                        </div>
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                              :class="getIssueSeverityColor(issue.severity)"
                                              x-text="issue.severity"></span>
                                    </div>
                                    <div x-show="issue.corrective_action" class="mt-2">
                                        <p class="text-xs font-medium text-red-700 dark:text-red-400">Tindakan Korektif:</p>
                                        <p class="text-xs text-red-600 dark:text-red-500" x-text="issue.corrective_action"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div x-show="inspection.recommendations">
                        <label class="block text-sm font-medium text-muted mb-2">Rekomendasi</label>
                        <p class="text-sm text-foreground bg-blue-50 dark:bg-blue-900/10 p-3 rounded-lg border border-blue-200 dark:border-blue-800" x-text="inspection.recommendations"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 lg:space-y-6">
            <!-- Inspector Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Inspector</h3>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-foreground" x-text="inspection.inspector_name">-</p>
                            <p class="text-xs text-muted" x-text="inspection.inspector_title">-</p>
                        </div>
                    </div>
                    <div>
                        <span class="text-sm text-muted">Sertifikasi:</span>
                        <p class="text-sm font-medium text-foreground" x-text="inspection.inspector_certification">-</p>
                    </div>
                    <div>
                        <span class="text-sm text-muted">Pengalaman:</span>
                        <p class="text-sm font-medium text-foreground" x-text="inspection.inspector_experience + ' tahun'">-</p>
                    </div>
                </div>
            </div>

            <!-- Score Summary -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Skor Inspeksi</h3>
                <div class="space-y-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-foreground mb-2" x-text="inspection.score + '/100'">-</div>
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                            <div class="h-3 rounded-full transition-all duration-300"
                                 :class="getScoreColor(inspection.score)"
                                 :style="'width: ' + inspection.score + '%'"></div>
                        </div>
                        <p class="text-sm text-muted" x-text="getScoreGrade(inspection.score)">-</p>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Visual Check</span>
                            <span class="text-sm font-medium text-foreground" x-text="inspection.visual_score + '/25'">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Weight Check</span>
                            <span class="text-sm font-medium text-foreground" x-text="inspection.weight_score + '/25'">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Taste Test</span>
                            <span class="text-sm font-medium text-foreground" x-text="inspection.taste_score + '/25'">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Packaging</span>
                            <span class="text-sm font-medium text-foreground" x-text="inspection.packaging_score + '/25'">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <button @click="approveInspection" 
                            x-show="inspection.status === 'passed' && !inspection.approved"
                            class="btn btn-sm btn-primary w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Approve Inspeksi
                    </button>
                    <button @click="requestRework" 
                            x-show="inspection.status === 'failed'"
                            class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Request Rework
                    </button>
                    <button @click="exportReport" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Report
                    </button>
                    <button @click="viewProductionOrder" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Lihat Order Produksi
                    </button>
                </div>
            </div>

            <!-- Inspection History -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Riwayat Inspeksi</h3>
                <div class="space-y-3">
                    <template x-for="history in inspection.history" :key="history.id">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-2 h-2 rounded-full mt-2" :class="getHistoryColor(history.action)"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-foreground" x-text="history.action_text"></p>
                                <p class="text-xs text-muted" x-text="formatDateTime(history.timestamp) + ' • ' + history.user_name"></p>
                                <p x-show="history.notes" class="text-xs text-muted mt-1" x-text="history.notes"></p>
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
function inspectionDetail() {
    return {
        inspection: {
            id: {{ $id ?? 1 }}, // This would come from the route parameter
            inspection_id: '',
            status: '',
            inspection_date: '',
            start_time: '',
            end_time: '',
            duration: 0,
            production_order: '',
            batch_number: '',
            product_name: '',
            product_sku: '',
            batch_size: 0,
            production_date: '',
            inspector_name: '',
            inspector_title: '',
            inspector_certification: '',
            inspector_experience: 0,
            score: 0,
            visual_score: 0,
            weight_score: 0,
            taste_score: 0,
            packaging_score: 0,
            general_notes: '',
            recommendations: '',
            approved: false,
            checklist: [],
            test_results: [],
            issues: [],
            history: []
        },

        async init() {
            await this.loadData();
        },

        async loadData() {
            try {
                const response = await fetch(`/api/quality-inspections/${this.inspection.id}`);
                const result = await response.json();
                
                if (result.success) {
                    this.inspection = { ...this.inspection, ...result.data };
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
                'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                'passed': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'failed': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                'rework': 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getStatusText(status) {
            const texts = {
                'pending': 'Pending',
                'in_progress': 'In Progress',
                'passed': 'Passed',
                'failed': 'Failed',
                'rework': 'Rework'
            };
            return texts[status] || status;
        },

        getCheckStatusColor(status) {
            const colors = {
                'pass': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'fail': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                'warning': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getCheckStatusText(status) {
            const texts = {
                'pass': 'Pass',
                'fail': 'Fail',
                'warning': 'Warning'
            };
            return texts[status] || status;
        },

        getTestStatusColor(status) {
            const colors = {
                'within_range': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'out_of_range': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                'borderline': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getTestStatusText(status) {
            const texts = {
                'within_range': 'Within Range',
                'out_of_range': 'Out of Range',
                'borderline': 'Borderline'
            };
            return texts[status] || status;
        },

        getIssueSeverityColor(severity) {
            const colors = {
                'Critical': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                'Major': 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
                'Minor': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400'
            };
            return colors[severity] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getScoreColor(score) {
            if (score >= 90) return 'bg-green-500';
            if (score >= 80) return 'bg-yellow-500';
            if (score >= 70) return 'bg-orange-500';
return 'bg-red-500';
        },

        getScoreGrade(score) {
            if (score >= 90) return 'Excellent (A)';
            if (score >= 80) return 'Good (B)';
            if (score >= 70) return 'Fair (C)';
            if (score >= 60) return 'Poor (D)';
            return 'Failed (F)';
        },

        getHistoryColor(action) {
            const colors = {
                'created': 'bg-blue-500',
                'started': 'bg-green-500',
                'completed': 'bg-green-600',
                'approved': 'bg-purple-500',
                'rejected': 'bg-red-500',
                'rework': 'bg-orange-500'
            };
            return colors[action] || 'bg-gray-500';
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

        formatTime(timeString) {
            if (!timeString) return '-';
            const time = new Date('2000-01-01 ' + timeString);
            return time.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        formatDateTime(dateTimeString) {
            if (!dateTimeString) return '-';
            const date = new Date(dateTimeString);
            return date.toLocaleString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        editInspection() {
            window.location.href = `/quality-inspections/${this.inspection.id}/edit`;
        },

        printCertificate() {
            window.open(`/quality-inspections/${this.inspection.id}/certificate`, '_blank');
        },

        async approveInspection() {
            if (!confirm('Apakah Anda yakin ingin menyetujui inspeksi ini?')) {
                return;
            }

            try {
                const response = await fetch(`/api/quality-inspections/${this.inspection.id}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('Inspeksi berhasil disetujui!');
                    await this.loadData();
                } else {
                    alert('Gagal menyetujui inspeksi: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyetujui inspeksi');
            }
        },

        async requestRework() {
            const reason = prompt('Masukkan alasan rework:');
            if (!reason) return;

            try {
                const response = await fetch(`/api/quality-inspections/${this.inspection.id}/rework`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify({ reason })
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('Request rework berhasil dikirim!');
                    await this.loadData();
                } else {
                    alert('Gagal mengirim request rework: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengirim request rework');
            }
        },

        exportReport() {
            window.open(`/quality-inspections/${this.inspection.id}/export`, '_blank');
        },

        viewProductionOrder() {
            window.location.href = `/production-orders/${this.inspection.production_order_id}`;
        },

        viewImage(image) {
            // Open image in modal or new window
            window.open(image.url, '_blank');
        }
    }
}
</script>
@endpush