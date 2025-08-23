@extends('layouts.dashboard')

@section('title', 'Detail Produk Jadi')
@section('page-title', 'Detail Produk Jadi')

@section('content')
<div x-data="finishedProductDetail()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight" x-text="product.name">Loading...</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Detail lengkap produk jadi dan informasi stok</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.finished-products.index') }}" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <button @click="adjustStock" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Sesuaikan Stok
            </button>
            <button @click="createProductionOrder" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Buat Order Produksi
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4 lg:space-y-6">
            <!-- Product Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Produk</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Nama Produk</label>
                        <p class="text-base font-medium text-foreground" x-text="product.name">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">SKU</label>
                        <p class="text-base text-foreground" x-text="product.sku">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Kategori</label>
                        <p class="text-base text-foreground" x-text="product.category">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Unit</label>
                        <p class="text-base text-foreground" x-text="product.unit">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Berat per Unit</label>
                        <p class="text-base text-foreground" x-text="product.weight + ' ' + product.weight_unit">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Dimensi</label>
                        <p class="text-base text-foreground" x-text="product.dimensions">-</p>
                    </div>
                </div>
                <div class="mt-4 md:mt-6">
                    <label class="block text-sm font-medium text-muted mb-2">Deskripsi</label>
                    <p class="text-sm text-foreground bg-border/30 p-3 rounded-lg" x-text="product.description || 'Tidak ada deskripsi'"></p>
                </div>
            </div>

            <!-- Stock Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Stok</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-foreground mb-1" x-text="product.current_stock">0</div>
                        <p class="text-sm text-muted">Stok Saat Ini</p>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600 mb-1" x-text="product.min_stock">0</div>
                        <p class="text-sm text-muted">Stok Minimum</p>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 mb-1" x-text="product.max_stock">0</div>
                        <p class="text-sm text-muted">Stok Maksimum</p>
                    </div>
                </div>

                <!-- Stock Level Indicator -->
                <div class="mt-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-muted">Level Stok</span>
                        <span class="text-sm font-medium text-foreground" x-text="getStockPercentage() + '%'">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all duration-300"
                             :class="getStockLevelColor()"
                             :style="'width: ' + getStockPercentage() + '%'"></div>
                    </div>
                    <p class="text-xs text-muted mt-1" x-text="getStockStatus()">-</p>
                </div>

                <!-- Stock Locations -->
                <div class="mt-6">
                    <h4 class="text-sm font-medium text-foreground mb-3">Lokasi Penyimpanan</h4>
                    <div class="space-y-2">
                        <template x-for="location in product.stock_locations" :key="location.id">
                            <div class="flex items-center justify-between p-3 bg-border/30 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-foreground" x-text="location.warehouse_name"></p>
                                    <p class="text-xs text-muted" x-text="location.zone + ' - ' + location.rack"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-foreground" x-text="location.quantity + ' unit'"></p>
                                    <p class="text-xs text-muted" x-text="'Exp: ' + formatDate(location.expiry_date)"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Production Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Produksi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Resep Terkait</label>
                        <p class="text-base font-medium text-foreground" x-text="product.recipe_name">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Batch Size Standard</label>
                        <p class="text-base text-foreground" x-text="product.standard_batch_size + ' unit'">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Produksi Terakhir</label>
                        <p class="text-base text-foreground" x-text="formatDate(product.last_production)">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Lead Time Produksi</label>
                        <p class="text-base text-foreground" x-text="product.production_lead_time + ' hari'">-</p>
                    </div>
                </div>
            </div>

            <!-- Stock Movement History -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Riwayat Pergerakan Stok</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-border/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Tanggal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Jenis</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Jumlah</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Saldo</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-muted uppercase">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <template x-for="movement in product.stock_movements" :key="movement.id">
                                <tr>
                                    <td class="px-4 py-3 text-sm text-foreground" x-text="formatDateTime(movement.date)"></td>
                                    <td class="px-4 py-3">
                                        <span :class="getMovementTypeColor(movement.type)"
                                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                              x-text="getMovementTypeText(movement.type)"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-sm font-medium"
                                              :class="movement.type === 'in' ? 'text-green-600' : 'text-red-600'"
                                              x-text="(movement.type === 'in' ? '+' : '-') + movement.quantity"></span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-foreground" x-text="movement.balance"></td>
                                    <td class="px-4 py-3 text-sm text-foreground" x-text="movement.notes"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 lg:space-y-6">
            <!-- Status & Pricing -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Status & Harga</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Status Produk</span>
                        <span :class="getStatusColor(product.status)"
                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              x-text="getStatusText(product.status)">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Biaya Produksi</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatCurrency(product.unit_cost)">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Harga Jual</span>
                        <span class="text-sm font-medium text-foreground" x-text="formatCurrency(product.selling_price)">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Margin</span>
                        <span class="text-sm font-medium text-foreground" x-text="product.profit_margin + '%'">0%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Nilai Stok</span>
                        <span class="text-sm font-bold text-foreground" x-text="formatCurrency(product.current_stock * product.unit_cost)">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Quality Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Kualitas</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-muted">Tanggal Kadaluarsa</span>
                        <p class="text-sm font-medium text-foreground" x-text="formatDate(product.expiry_date)">-</p>
                    </div>
                    <div>
                        <span class="text-sm text-muted">Shelf Life</span>
                        <p class="text-sm font-medium text-foreground" x-text="product.shelf_life + ' hari'">-</p>
                    </div>
                    <div>
                        <span class="text-sm text-muted">Kondisi Penyimpanan</span>
                        <p class="text-sm text-foreground" x-text="product.storage_conditions">-</p>
                    </div>
                    <div>
                        <span class="text-sm text-muted">QC Status</span>
                        <span :class="getQCStatusColor(product.qc_status)"
                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              x-text="getQCStatusText(product.qc_status)">-</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <button @click="updatePrice" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                        Update Harga
                    </button>
                    <button @click="viewRecipe" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Lihat Resep
                    </button>
                    <button @click="viewCatalog" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Lihat di Katalog
                    </button>
                    <button @click="exportData" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Data
                    </button>
                </div>
            </div>

            <!-- Alerts -->
            <div x-show="product.current_stock <= product.min_stock" class="card p-4 md:p-6 bg-orange-50 dark:bg-orange-900/10 border-orange-200 dark:border-orange-800">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-orange-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-orange-800 dark:text-orange-400">Stok Rendah</h4>
                        <p class="text-xs text-orange-700 dark:text-orange-500 mt-1">Stok produk sudah mencapai batas minimum. Pertimbangkan untuk melakukan produksi ulang.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Adjustment Modal -->
    <div x-show="showStockModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showStockModal = false"></div>
            
            <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-base md:text-lg font-medium text-foreground leading-tight">Sesuaikan Stok</h3>
                    <button @click="showStockModal = false" class="text-muted hover:text-foreground">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <form @submit.prevent="saveStockAdjustment" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Stok Saat Ini</label>
                        <div class="input bg-border/30 flex items-center" x-text="product.current_stock + ' unit'"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Jenis Penyesuaian *</label>
                        <select x-model="stockAdjustment.type" class="input" required>
                            <option value="">Pilih Jenis</option>
                            <option value="in">Penambahan Stok</option>
                            <option value="out">Pengurangan Stok</option>
                            <option value="correction">Koreksi Stok</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Jumlah *</label>
                        <input type="number" x-model="stockAdjustment.quantity" class="input" min="1" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Alasan *</label>
                        <textarea x-model="stockAdjustment.reason" class="input" rows="3" required placeholder="Jelaskan alasan penyesuaian stok"></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showStockModal = false" class="btn btn-outline touch-manipulation">Batal</button>
                        <button type="submit" class="btn btn-primary touch-manipulation">Simpan Penyesuaian</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function finishedProductDetail() {
    return {
        product: {
            id: {{ $id ?? 1 }}, // This would come from the route parameter
            name: '',
            sku: '',
            category: '',
            unit: '',
            weight: 0,
            weight_unit: '',
            dimensions: '',
            description: '',
            current_stock: 0,
            min_stock: 0,
            max_stock: 0,
            unit_cost: 0,
            selling_price: 0,
            profit_margin: 0,
            status: '',
            expiry_date: '',
            shelf_life: 0,
            storage_conditions: '',
            qc_status: '',
            recipe_name: '',
            standard_batch_size: 0,
            last_production: '',
            production_lead_time: 0,
            stock_locations: [],
            stock_movements: []
        },
        showStockModal: false,
        stockAdjustment: {
            type: '',
            quantity: 0,
            reason: ''
        },

        async init() {
            await this.loadData();
        },

        async loadData() {
            try {
                const response = await fetch(`/api/finished-products/${this.product.id}`);
                const result = await response.json();
                
                if (result.success) {
                    this.product = { ...this.product, ...result.data };
                } else {
                    alert('Gagal memuat data: ' + result.message);
                }
            } catch (error) {
                console.error('Error loading data:', error);
                alert('Terjadi kesalahan saat memuat data');
            }
        },

        getStockPercentage() {
            if (this.product.max_stock === 0) return 0;
            return Math.round((this.product.current_stock / this.product.max_stock) * 100);
        },

        getStockLevelColor() {
            const percentage = this.getStockPercentage();
            if (percentage >= 70) return 'bg-green-500';
            if (percentage >= 30) return 'bg-yellow-500';
            return 'bg-red-500';
        },

        getStockStatus() {
            if (this.product.current_stock <= 0) return 'Stok Habis';
            if (this.product.current_stock <= this.product.min_stock) return 'Stok Rendah';
            if (this.product.current_stock >= this.product.max_stock) return 'Stok Penuh';
            return 'Stok Normal';
        },

        getStatusColor(status) {
            const colors = {
                'available': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'low_stock': 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
                'out_of_stock': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getStatusText(status) {
            const texts = {
                'available': 'Tersedia',
                'low_stock': 'Stok Rendah',
                'out_of_stock': 'Habis'
            };
            return texts[status] || status;
        },

        getQCStatusColor(status) {
            const colors = {
                'passed': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                'failed': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getQCStatusText(status) {
            const texts = {
                'passed': 'Passed',
                'pending': 'Pending',
                'failed': 'Failed'
            };
            return texts[status] || status;
        },

        getMovementTypeColor(type) {
            const colors = {
                'in': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'out': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                'correction': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400'
            };
            return colors[
type] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getMovementTypeText(type) {
            const texts = {
                'in': 'Masuk',
                'out': 'Keluar',
                'correction': 'Koreksi'
            };
            return texts[type] || type;
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

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        },

        adjustStock() {
            this.showStockModal = true;
        },

        async saveStockAdjustment() {
            try {
                const response = await fetch(`/api/finished-products/${this.product.id}/stock-adjustment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify(this.stockAdjustment)
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('Penyesuaian stok berhasil disimpan!');
                    this.showStockModal = false;
                    this.resetStockAdjustment();
                    await this.loadData();
                } else {
                    alert('Gagal menyimpan penyesuaian stok: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan penyesuaian stok');
            }
        },

        resetStockAdjustment() {
            this.stockAdjustment = {
                type: '',
                quantity: 0,
                reason: ''
            };
        },

        createProductionOrder() {
            window.location.href = `/production/orders/create?product=${this.product.id}`;
        },

        updatePrice() {
            window.location.href = `/finished-products/pricing?product=${this.product.id}`;
        },

        viewRecipe() {
            window.location.href = `/recipes/${this.product.recipe_id}`;
        },

        viewCatalog() {
            window.location.href = `/finished-products/catalog?product=${this.product.id}`;
        },

        exportData() {
            window.open(`/finished-products/${this.product.id}/export`, '_blank');
        }
    }
}
</script>
@endpush