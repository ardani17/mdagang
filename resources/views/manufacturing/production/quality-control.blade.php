@extends('layouts.dashboard')

@section('title', 'Quality Control')
@section('page-title', 'Quality Control Produksi')

@section('content')
<div x-data="qualityControl()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Quality Control</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Monitor dan kontrol kualitas produksi</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.production.index') }}"
               class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Produksi
            </a>
            <button @click="showCreateInspection = true" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Buat Inspeksi
            </button>
            <button @click="exportQCReport" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Laporan
            </button>
        </div>
    </div>

    <!-- QC Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Inspeksi Hari Ini</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="todayInspections">0</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Pass Rate</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="passRate + '%'">0%</p>
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
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Reject Rate</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="rejectRate + '%'">0%</p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Pending Review</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="pendingReview">0</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative">
                    <input type="text" 
                           x-model="search" 
                           @input="filterData"
                           placeholder="Cari inspeksi..." 
                           class="input pl-10">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <select x-model="statusFilter" @change="filterData" class="input">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="passed">Passed</option>
                    <option value="failed">Failed</option>
                    <option value="rework">Rework</option>
                </select>
                <select x-model="productFilter" @change="filterData" class="input">
                    <option value="">Semua Produk</option>
                    <option value="Minuman Temulawak">Minuman Temulawak</option>
                    <option value="Krupuk Bro">Krupuk Bro</option>
                </select>
                <input type="date" x-model="dateFilter" @change="filterData" class="input">
            </div>
        </div>
    </div>

    <!-- QC Inspections Table -->
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-border/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">ID Inspeksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Order Produksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Batch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Inspector</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="inspection in filteredData" :key="inspection.id">
                        <tr class="hover:bg-border/30">
                            <td class="px-6 py-4">
                                <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="inspection.inspection_id"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="inspection.production_order"></div>
                                <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="'Batch #' + inspection.batch_number"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="inspection.product_name"></div>
                                <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="inspection.product_sku"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="inspection.batch_size + ' unit'"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="inspection.inspector_name"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="formatDate(inspection.inspection_date)"></td>
                            <td class="px-6 py-4">
                                <span :class="getStatusColor(inspection.status)" 
                                      class="inline-flex px-2 py-1 text-xs font-medium rounded-full" 
                                      x-text="getStatusText(inspection.status)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-foreground" x-text="inspection.score + '/100'"></span>
                                    <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full" 
                                             :class="getScoreColor(inspection.score)"
                                             :style="'width: ' + inspection.score + '%'"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <button @click="viewInspection(inspection)" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg touch-manipulation" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click="editInspection(inspection)"
                                            x-show="inspection.status === 'pending' || inspection.status === 'in_progress'"
                                            class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg touch-manipulation"
                                            title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="approveInspection(inspection)"
                                            x-show="inspection.status === 'passed'"
                                            class="p-2 text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-lg touch-manipulation"
                                            title="Approve">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                    <button @click="printCertificate(inspection)"
                                            x-show="inspection.status === 'passed'"
                                            class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg touch-manipulation"
                                            title="Print Certificate">
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
    </div>

    <!-- Create Inspection Modal -->
    <div x-show="showCreateInspection" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showCreateInspection = false"></div>
            
            <div class="inline-block w-full max-w-3xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-base md:text-lg font-medium text-foreground leading-tight">Buat Inspeksi QC Baru</h3>
                    <button @click="showCreateInspection = false" class="text-muted hover:text-foreground">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form @submit.prevent="saveInspection" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Order Produksi *</label>
                            <select x-model="newInspection.production_order_id" class="input" required>
                                <option value="">Pilih Order</option>
                                <template x-for="order in productionOrders" :key="order.id">
                                    <option :value="order.id" x-text="order.order_number + ' - ' + order.product_name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Batch Number *</label>
                            <input type="text" x-model="newInspection.batch_number" class="input" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Inspector *</label>
                            <select x-model="newInspection.inspector_id" class="input" required>
                                <option value="">Pilih Inspector</option>
                                <template x-for="inspector in inspectors" :key="inspector.id">
                                    <option :value="inspector.id" x-text="inspector.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Tanggal Inspeksi *</label>
                            <input type="date" x-model="newInspection.inspection_date" class="input" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Jenis Inspeksi</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" x-model="newInspection.visual_check" class="mr-2">
                                <span class="text-sm">Visual Check</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" x-model="newInspection.weight_check" class="mr-2">
                                <span class="text-sm">Weight Check</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" x-model="newInspection.taste_test" class="mr-2">
                                <span class="text-sm">Taste Test</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" x-model="newInspection.packaging_check" class="mr-2">
                                <span class="text-sm">Packaging Check</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Catatan</label>
                        <textarea x-model="newInspection.notes" class="input" rows="3" placeholder="Catatan inspeksi"></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showCreateInspection = false" class="btn btn-outline touch-manipulation">Batal</button>
                        <button type="submit" class="btn btn-primary touch-manipulation">Buat Inspeksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function qualityControl() {
    return {
        inspections: [],
        filteredData: [],
        productionOrders: [],
        inspectors: [],
        search: '',
        statusFilter: '',
        productFilter: '',
        dateFilter: '',
        showCreateInspection: false,
        newInspection: {
            production_order_id: '',
            batch_number: '',
            inspector_id: '',
            inspection_date: new Date().toISOString().split('T')[0],
            visual_check: false,
            weight_check: false,
            taste_test: false,
            packaging_check: false,
            notes: ''
        },

        init() {
            this.loadData();
            this.loadProductionOrders();
            this.loadInspectors();
        },

        async loadData() {
            try {
                const response = await fetch('/api/quality-inspections');
                const data = await response.json();
                this.inspections = data.data;
                this.filteredData = this.inspections;
            } catch (error) {
                console.error('Error loading data:', error);
            }
        },

        async loadProductionOrders() {
            try {
                const response = await fetch('/api/production-orders?status=in_progress');
                const data = await response.json();
                this.productionOrders = data.data;
            } catch (error) {
                console.error('Error loading production orders:', error);
            }
        },

        async loadInspectors() {
            try {
                const response = await fetch('/api/inspectors');
                const data = await response.json();
                this.inspectors = data.data;
            } catch (error) {
                console.error('Error loading inspectors:', error);
            }
        },

        filterData() {
            this.filteredData = this.inspections.filter(inspection => {
                const matchesSearch = inspection.inspection_id.toLowerCase().includes(this.search.toLowerCase()) ||
                                    inspection.product_name.toLowerCase().includes(this.search.toLowerCase()) ||
                                    inspection.production_order.toLowerCase().includes(this.search.toLowerCase());
                const matchesStatus = !this.statusFilter || inspection.status === this.statusFilter;
                const matchesProduct = !this.productFilter || inspection.product_name.includes(this.productFilter);
                const matchesDate = !this.dateFilter || inspection.inspection_date === this.dateFilter;
                
                return matchesSearch && matchesStatus && matchesProduct && matchesDate;
            });
        },

        get todayInspections() {
            const today = new Date().toISOString().split('T')[0];
            return this.inspections.filter(inspection => inspection.inspection_date === today).length;
        },

        get passRate() {
            const total = this.inspections.length;
            if (total === 0) return 0;
            const passed = this.inspections.filter(inspection => inspection.status === 'passed').length;
            return Math.round((passed / total) * 100);
        },

        get rejectRate() {
            const total = this.inspections.length;
            if (total === 0) return 0;
            const failed = this.inspections.filter(inspection => inspection.status === 'failed').length;
            return Math.round((failed / total) * 100);
        },

        get pendingReview() {
            return this.inspections.filter(inspection => inspection.status === 'pending').length;
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

        getScoreColor(score) {
            if (score >= 90) return 'bg-green-500';
            if (score >= 80) return 'bg-yellow-500';
            if (score >= 70) return 'bg-orange-500';
            return 'bg-red-500';
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        },

        viewInspection(inspection) {
            window.location.href = `/quality-inspections/${inspection.id}`;
        },

        editInspection(inspection) {
            window.location.href = `/quality-inspections/${inspection.id}/edit`;
        },

        async approveInspection(inspection) {
            if (!confirm('Apakah Anda yakin ingin menyetujui inspeksi ini?')) {
                return;
            }

            try {
                const response = await fetch(`/api/quality-inspections/${inspection.id}/approve`, {
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

        printCertificate(inspection) {
            window.open(`/quality-inspections/${inspection.id}/certificate`, '_blank');
        },

        async saveInspection() {
            try {
                const response = await fetch('/api/quality-inspections', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newInspection)
                });

                if (response.ok) {
                    this.showCreateInspection = false;
                    this.resetForm();
                    this.loadData();
                } else {
                    console.error('Failed to save inspection');
                }
            } catch (error) {
                console.error('Error saving inspection:', error);
            }
        },

        resetForm() {
            this.newInspection = {
                production_order_id: '',
                batch_number: '',
                inspector_id: '',
                inspection_date: new Date().toISOString().split('T')[0],
                visual_check: false,
                weight_check: false,
                taste_test: false,
                packaging_check: false,
                notes: ''
            };
        },

        exportQCReport() {
            console.log('Export QC report');
        }
    }
}
</script>
@endpush