@extends('layouts.dashboard')

@section('title', 'Detail Bahan Baku')
@section('page-title', 'Detail Bahan Baku')

@section('content')
<div x-data="rawMaterialsShow()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight" x-text="material.name">Detail Bahan Baku</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Informasi lengkap bahan baku</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.raw-materials.index') }}" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <button @click="editMaterial" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </button>
            <button @click="printDetails" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </button>
        </div>
    </div>

    <!-- Status Alert -->
    <div x-show="material.current_stock <= material.min_stock" class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <div>
                <h4 class="text-sm font-medium text-orange-800 dark:text-orange-200">Stok Rendah</h4>
                <p class="text-sm text-orange-700 dark:text-orange-300">Stok saat ini sudah mencapai batas minimum. Pertimbangkan untuk melakukan pemesanan ulang.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-4 lg:space-y-6">
            <!-- Basic Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Dasar</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Nama Bahan Baku</label>
                        <p class="text-base font-medium text-foreground" x-text="material.name">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">SKU</label>
                        <p class="text-base font-medium text-foreground" x-text="material.sku">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Kategori</label>
                        <span class="inline-flex px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 rounded-full" x-text="material.category">-</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Unit</label>
                        <p class="text-base font-medium text-foreground" x-text="material.unit">-</p>
                    </div>
                </div>
                <div class="mt-4" x-show="material.description">
                    <label class="block text-sm font-medium text-muted mb-1">Deskripsi</label>
                    <p class="text-base text-foreground" x-text="material.description">-</p>
                </div>
            </div>

            <!-- Stock Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Stok</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                    <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-2xl font-bold text-blue-600" x-text="material.current_stock">0</p>
                        <p class="text-sm text-blue-600" x-text="material.unit">Unit</p>
                        <p class="text-xs text-muted mt-1">Stok Saat Ini</p>
                    </div>
                    <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                        <p class="text-2xl font-bold text-orange-600" x-text="material.min_stock">0</p>
                        <p class="text-sm text-orange-600" x-text="material.unit">Unit</p>
                        <p class="text-xs text-muted mt-1">Stok Minimum</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <p class="text-2xl font-bold text-green-600" x-text="material.max_stock || '-'">0</p>
                        <p class="text-sm text-green-600" x-text="material.unit">Unit</p>
                        <p class="text-xs text-muted mt-1">Stok Maksimum</p>
                    </div>
                </div>
                
                <!-- Stock Status Bar -->
                <div class="mt-6">
                    <div class="flex justify-between text-sm text-muted mb-2">
                        <span>Status Stok</span>
                        <span x-text="getStockPercentage() + '%'">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-300" 
                             :class="getStockStatusColor()"
                             :style="`width: ${Math.min(getStockPercentage(), 100)}%`"></div>
                    </div>
                    <div class="flex justify-between text-xs text-muted mt-1">
                        <span>0</span>
                        <span x-text="material.max_stock || 'Tidak terbatas'">Max</span>
                    </div>
                </div>
            </div>

            <!-- Pricing Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Harga</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Harga per Unit</label>
                        <p class="text-lg font-bold text-foreground" x-text="formatCurrency(material.unit_cost)">Rp 0</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Total Nilai Stok</label>
                        <p class="text-lg font-bold text-primary" x-text="formatCurrency(material.current_stock * material.unit_cost)">Rp 0</p>
                    </div>
                </div>
            </div>

            <!-- Stock Movement History -->
            <div class="card p-4 md:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight">Riwayat Pergerakan Stok</h3>
                    <button @click="loadStockHistory" class="btn btn-sm btn-outline">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh
                    </button>
                </div>
                
                <div class="space-y-3">
                    <template x-for="movement in stockMovements" :key="movement.id">
                        <div class="flex items-center justify-between p-3 bg-border/30 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3"
                                     :class="movement.type === 'in' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path x-show="movement.type === 'in'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8l-8-8-8 8" />
                                        <path x-show="movement.type === 'out'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20V4m-8 8l8 8 8-8" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-foreground" x-text="movement.description">-</p>
                                    <p class="text-xs text-muted" x-text="formatDate(movement.created_at)">-</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium" 
                                   :class="movement.type === 'in' ? 'text-green-600' : 'text-red-600'"
                                   x-text="(movement.type === 'in' ? '+' : '-') + movement.quantity + ' ' + material.unit">0</p>
                                <p class="text-xs text-muted" x-text="'Oleh: ' + movement.user_name">-</p>
                            </div>
                        </div>
                    </template>
                    
                    <div x-show="stockMovements.length === 0" class="text-center py-8 text-muted">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p>Belum ada riwayat pergerakan stok</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="space-y-4 lg:space-y-6">
            <!-- Status Card -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Status</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Status Aktif</span>
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              :class="material.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'"
                              x-text="material.is_active ? 'Aktif' : 'Tidak Aktif'">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">Status Stok</span>
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              :class="getStockStatusBadge()"
                              x-text="getStockStatusText()">-</span>
                    </div>
                </div>
            </div>

            <!-- Supplier Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Supplier</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Supplier Utama</label>
                        <p class="text-base font-medium text-foreground" x-text="material.supplier_name || 'Belum ditentukan'">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Lead Time</label>
                        <p class="text-base font-medium text-foreground" x-text="(material.lead_time || 0) + ' hari'">0 hari</p>
                    </div>
                    <div x-show="material.supplier_name">
                        <button @click="contactSupplier" class="btn btn-sm btn-outline w-full">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Hubungi Supplier
                        </button>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Tambahan</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Lokasi Penyimpanan</label>
                        <p class="text-base font-medium text-foreground" x-text="material.storage_location || 'Belum ditentukan'">-</p>
                    </div>
                    <div x-show="material.expiry_date">
                        <label class="block text-sm font-medium text-muted mb-1">Tanggal Kadaluarsa</label>
                        <p class="text-base font-medium text-foreground" x-text="formatDate(material.expiry_date)">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted mb-1">Terakhir Diperbarui</label>
                        <p class="text-sm text-foreground" x-text="formatDate(material.updated_at)">-</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Aksi Cepat</h3>
                <div class="space-y-2">
                    <button @click="createPurchaseOrder" class="btn btn-sm btn-primary w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Buat Purchase Order
                    </button>
                    <button @click="adjustStock" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Sesuaikan Stok
                    </button>
                    <button @click="viewUsageReport" class="btn btn-sm btn-outline w-full">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Laporan Penggunaan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function rawMaterialsShow() {
    return {
        material: {
            id: {{ $id ?? 1 }}, // This would come from the route parameter
            name: '',
            sku: '',
            category: '',
            unit: '',
            description: '',
            current_stock: 0,
            min_stock: 0,
            max_stock: 0,
            unit_cost: 0,
            currency: 'IDR',
            supplier_name: '',
            lead_time: 0,
            storage_location: '',
            expiry_date: '',
            is_active: true,
            created_at: '',
            updated_at: ''
        },
        stockMovements: [],

        async init() {
            await this.loadData();
            await this.loadStockHistory();
        },

        async loadData() {
            try {
                const response = await fetch(`/api/raw-materials/${this.material.id}`);
                const result = await response.json();
                
                if (result.success) {
                    this.material = { ...this.material, ...result.data };
                } else {
                    alert('Gagal memuat data: ' + result.message);
                }
            } catch (error) {
                console.error('Error loading data:', error);
                alert('Terjadi kesalahan saat memuat data');
            }
        },

        async loadStockHistory() {
            try {
                const response = await fetch(`/api/raw-materials/${this.material.id}/stock-movements`);
                const result = await response.json();
                
                if (result.success) {
                    this.stockMovements = result.data;
                }
            } catch (error) {
                console.error('Error loading stock history:', error);
            }
        },

        getStockPercentage() {
            if (!this.material.max_stock) return 0;
            return Math.round((this.material.current_stock / this.material.max_stock) * 100);
        },

        getStockStatusColor() {
            if (this.material.current_stock <= this.material.min_stock) {
                return 'bg-red-500';
            } else if (this.material.current_stock <= this.material.min_stock * 1.5) {
                return 'bg-orange-500';
            } else {
                return 'bg-green-500';
            }
        },

        getStockStatusBadge() {
            if (this.material.current_stock <= this.material.min_stock) {
                return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400';
            } else if (this.material.current_stock <= this.material.min_stock * 1.5) {
                return 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400';
            } else {
                return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
            }
        },

        getStockStatusText() {
            if (this.material.current_stock <= this.material.min_stock) {
                return 'Stok Kritis';
            } else if (this.material.current_stock <= this.material.min_stock * 1.5) {
                return 'Stok Rendah';
            } else {
                return 'Stok Baik';
            }
        },

        editMaterial() {
            window.location.href = `/raw-materials/${this.material.id}/edit`;
        },

        createPurchaseOrder() {
            window.location.href = `/raw-materials/purchasing?material=${this.material.id}`;
        },

        adjustStock() {
            window.location.href = `/raw-materials/${this.material.id}/edit#stock-adjustment`;
        },

        contactSupplier() {
            if (this.material.supplier_phone) {
                window.open(`tel:${this.material.supplier_phone}`);
            } else {
                alert('Nomor telepon supplier tidak tersedia');
            }
        },

        viewUsageReport() {
            window.location.href = `/raw-materials/${this.material.id}/usage-report`;
        },

        printDetails() {
            window.print();
        },

        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
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
        }
    }
}
</script>
@endpush