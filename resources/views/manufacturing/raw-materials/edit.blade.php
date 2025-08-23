@extends('layouts.dashboard')

@section('title', 'Edit Bahan Baku')
@section('page-title', 'Edit Bahan Baku')

@section('content')
<div x-data="rawMaterialsEdit()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Edit Bahan Baku</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Ubah informasi bahan baku yang sudah ada</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.raw-materials.index') }}" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <button @click="showHistory = !showHistory" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Riwayat
            </button>
        </div>
    </div>

    <!-- Current Stock Alert -->
    <div x-show="form.current_stock <= form.minimum_stock" class="card p-4 border-l-4 border-l-yellow-500">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-yellow-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <div>
                <h4 class="text-sm font-semibold text-foreground">⚠️ Stok Rendah</h4>
                <p class="text-sm text-muted mt-1">Stok saat ini sudah mencapai batas minimum. Pertimbangkan untuk melakukan pemesanan ulang.</p>
            </div>
        </div>
    </div>

    <!-- History Panel -->
    <div x-show="showHistory" x-transition class="card p-4 md:p-6">
        <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Riwayat Perubahan</h3>
        <div class="space-y-3">
            <template x-for="history in stockHistory" :key="history.id">
                <div class="flex items-center justify-between p-3 bg-border/30 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-foreground" x-text="history.action"></p>
                        <p class="text-xs text-muted" x-text="formatDate(history.date)"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium" :class="history.type === 'in' ? 'text-green-600' : 'text-red-600'" x-text="(history.type === 'in' ? '+' : '-') + history.quantity + ' ' + form.unit"></p>
                        <p class="text-xs text-muted" x-text="'Oleh: ' + history.user"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Form -->
    <form @submit.prevent="submitForm" class="space-y-4 md:space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-4 lg:space-y-6">
                <div class="card p-4 md:p-6">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Nama Bahan Baku *</label>
                            <input type="text"
                                   x-model="form.name"
                                   class="input w-full h-12 text-base"
                                   placeholder="Contoh: Gula Cair"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Kode *</label>
                            <input type="text"
                                   x-model="form.code"
                                   class="input w-full h-12 text-base"
                                   placeholder="Contoh: RM-GC-001"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Kategori *</label>
                            <select x-model="form.category_id" class="input w-full h-12 text-base" required>
                                <option value="">Pilih Kategori</option>
                                <template x-for="category in categories" :key="category.id">
                                    <option :value="category.id" x-text="category.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Unit *</label>
                            <select x-model="form.unit" class="input w-full h-12 text-base" required>
                                <option value="">Pilih Unit</option>
                                <template x-for="unit in units" :key="unit.value">
                                    <option :value="unit.value" x-text="unit.label"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-6">
                        <label class="block text-sm font-medium text-foreground mb-2">Deskripsi</label>
                        <textarea x-model="form.description"
                                  class="input w-full text-base min-h-[80px]"
                                  rows="3"
                                  placeholder="Deskripsi bahan baku (opsional)"></textarea>
                    </div>
                </div>

                <!-- Stock Information -->
                <div class="card p-4 md:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight">Informasi Stok</h3>
                        <button type="button" @click="showStockAdjustment = !showStockAdjustment" class="btn btn-sm btn-outline">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Sesuaikan Stok
                        </button>
                    </div>
                    
                    <!-- Stock Adjustment Panel -->
                    <div x-show="showStockAdjustment" x-transition class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-3">Penyesuaian Stok</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-blue-700 dark:text-blue-300 mb-1">Tipe</label>
                                <select x-model="stockAdjustment.type" class="input w-full h-10 text-sm">
                                    <option value="in">Stok Masuk</option>
                                    <option value="out">Stok Keluar</option>
                                    <option value="adjustment">Penyesuaian</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-blue-700 dark:text-blue-300 mb-1">Jumlah</label>
                                <input type="number" x-model="stockAdjustment.quantity" class="input w-full h-10 text-sm" min="0" step="0.01">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-blue-700 dark:text-blue-300 mb-1">Alasan</label>
                                <input type="text" x-model="stockAdjustment.reason" class="input w-full h-10 text-sm" placeholder="Alasan penyesuaian">
                            </div>
                        </div>
                        <div class="mt-3 flex justify-end">
                            <button type="button" @click="applyStockAdjustment" class="btn btn-sm btn-primary">
                                Terapkan
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 md:gap-6">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Stok Saat Ini</label>
                            <div class="relative">
                                <input type="number"
                                       x-model="form.current_stock"
                                       class="input w-full h-12 text-base pr-16"
                                       min="0"
                                       step="0.01"
                                       readonly>
                                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm text-muted" x-text="form.unit"></span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Stok Minimum *</label>
                            <input type="number"
                                   x-model="form.minimum_stock"
                                   class="input w-full h-12 text-base"
                                   placeholder="0"
                                   min="0"
                                   step="0.01"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Stok Maksimum</label>
                            <input type="number"
                                   x-model="form.maximum_stock"
                                   class="input w-full h-12 text-base"
                                   placeholder="0"
                                   min="0"
                                   step="0.01">
                        </div>
                    </div>
                </div>

                <!-- Pricing Information -->
                <div class="card p-4 md:p-6">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Harga</h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Harga Pembelian Terakhir *</label>
                            <input type="number"
                                   x-model="form.last_purchase_price"
                                   class="input w-full h-12 text-base"
                                   placeholder="0"
                                   min="0"
                                   step="1"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Harga Rata-rata</label>
                            <input type="number"
                                   x-model="form.average_price"
                                   class="input w-full h-12 text-base"
                                   placeholder="0"
                                   min="0"
                                   step="1">
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-border/30 rounded-lg">
                        <div class="flex justify-between text-sm">
                            <span class="text-muted">Total Nilai Stok:</span>
                            <span class="font-medium text-foreground" x-text="formatCurrency(form.current_stock * form.average_price)"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Information -->
            <div class="space-y-4 lg:space-y-6">
                <!-- Supplier Information -->
                <div class="card p-4 md:p-6">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Supplier</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Supplier Utama</label>
                            <select x-model="form.supplier_id"
                                    @change="updateSupplierInfo()"
                                    class="input w-full h-12 text-base">
                                <option value="">Pilih Supplier</option>
                                <template x-for="supplier in suppliers" :key="supplier.id">
                                    <option :value="supplier.id" x-text="supplier.name"></option>
                                </template>
                            </select>
                        </div>
                        
                        <!-- Display supplier details when selected -->
                        <template x-if="selectedSupplier">
                            <div class="space-y-3 pt-2 border-t border-border">
                                <div class="text-sm">
                                    <span class="font-medium text-muted">Kontak:</span>
                                    <span class="text-foreground ml-1" x-text="selectedSupplier.contact_person || '-'"></span>
                                </div>
                                <div class="text-sm">
                                    <span class="font-medium text-muted">Telepon:</span>
                                    <span class="text-foreground ml-1" x-text="selectedSupplier.phone || '-'"></span>
                                </div>
                                <div class="text-sm">
                                    <span class="font-medium text-muted">Email:</span>
                                    <span class="text-foreground ml-1" x-text="selectedSupplier.email || '-'"></span>
                                </div>
                                <div class="text-sm">
                                    <span class="font-medium text-muted">Alamat:</span>
                                    <span class="text-foreground ml-1" x-text="selectedSupplier.address || '-'"></span>
                                </div>
                                <div class="text-sm">
                                    <span class="font-medium text-muted">Rating:</span>
                                    <span class="text-foreground ml-1">
                                        <template x-for="i in 5" :key="i">
                                            <span :class="i <= Math.floor(selectedSupplier.rating || 0) ? 'text-yellow-500' : 'text-gray-300'">★</span>
                                        </template>
                                        <span class="ml-1" x-text="`(${selectedSupplier.rating || 0}/5)`"></span>
                                    </span>
                                </div>
                            </div>
                        </template>
                        
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Lead Time (hari)</label>
                            <input type="number"
                                   x-model="form.lead_time_days"
                                   class="input w-full h-12 text-base"
                                   placeholder="7"
                                   min="0">
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="card p-4 md:p-6">
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Tambahan</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Lokasi Penyimpanan</label>
                            <input type="text"
                                   x-model="form.storage_location"
                                   class="input w-full h-12 text-base"
                                   placeholder="Contoh: Gudang A - Rak 1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Tanggal Kadaluarsa</label>
                            <input type="date"
                                   x-model="form.expiry_date"
                                   class="input w-full h-12 text-base">
                        </div>
                        <div class="pt-2">
                            <label class="flex items-center">
                                <input type="checkbox"
                                       x-model="form.is_active"
                                       class="w-5 h-5 rounded border-border text-primary focus:ring-primary touch-manipulation">
                                <span class="ml-3 text-base text-foreground">Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card p-4 md:p-6">
                    <div class="space-y-3">
                        <button type="submit"
                                class="btn btn-primary w-full h-12 text-base touch-manipulation"
                                :disabled="loading">
                            <span x-show="!loading">Update Bahan Baku</span>
                            <span x-show="loading" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                        <button type="button"
                                @click="resetForm"
                                class="btn btn-outline w-full h-12 text-base touch-manipulation">
                            Reset Form
                        </button>
                        <button type="button"
                                @click="deleteItem"
                                class="btn btn-danger w-full h-12 text-base touch-manipulation">
                            Hapus Bahan Baku
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function rawMaterialsEdit() {
    return {
        loading: false,
        showHistory: false,
        showStockAdjustment: false,
        originalData: {},
        suppliers: [],
        categories: [],
        units: [],
        selectedSupplier: null,
        form: {
            id: {{ isset($id) && $id ? $id : 1 }}, // This would come from the route parameter
            name: '',
            code: '',
            category: '',
            category_id: '',
            unit: '',
            description: '',
            current_stock: 0,
            minimum_stock: 0,
            maximum_stock: 0,
            last_purchase_price: 0,
            average_price: 0,
            supplier_id: '',
            lead_time_days: 7,
            storage_location: '',
            expiry_date: '',
            is_active: true
        },
        stockAdjustment: {
            type: 'in',
            quantity: 0,
            reason: ''
        },
        stockHistory: [
            {
                id: 1,
                action: 'Stok awal ditambahkan',
                type: 'in',
                quantity: 100,
                date: '2025-01-15T10:00:00Z',
                user: 'Admin'
            },
            {
                id: 2,
                action: 'Digunakan untuk produksi',
                type: 'out',
                quantity: 25,
                date: '2025-01-16T14:30:00Z',
                user: 'Operator'
            }
        ],

        async init() {
            await this.loadFormData();
            await this.loadData();
        },

        async loadFormData() {
            try {
                const response = await fetch('/manufacturing/raw-materials/form-data', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.categories = result.data.categories || [];
                    this.units = result.data.units || [];
                    this.suppliers = result.data.suppliers || [];
                    console.log('Form data loaded:', result.data);
                } else {
                    console.error('Failed to load form data:', result.message);
                    // Fallback to individual loading
                    await this.loadSuppliers();
                }
            } catch (error) {
                console.error('Error loading form data:', error);
                // Fallback to individual loading
                await this.loadSuppliers();
            }
        },

        async loadSuppliers() {
            try {
                const response = await fetch('/ajax/suppliers', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.suppliers = result.data?.data || result.data || [];
                } else {
                    console.error('Failed to load suppliers:', result.message);
                    this.suppliers = [];
                }
            } catch (error) {
                console.error('Error loading suppliers:', error);
                this.suppliers = [];
            }
        },

        async loadData() {
            try {
                const response = await fetch(`/manufacturing/raw-materials/${this.form.id}/show`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });
                const result = await response.json();
                
                if (result.success && result.data) {
                    // Safely merge data with null checks
                    const data = result.data;
                    this.form = {
                        ...this.form,
                        id: data.id || this.form.id,
                        name: data.name || '',
                        code: data.code || '',
                        category: data.category || '',
                        category_id: data.category_id || '',
                        unit: data.unit || '',
                        description: data.description || '',
                        current_stock: parseFloat(data.current_stock) || 0,
                        minimum_stock: parseFloat(data.minimum_stock) || 0,
                        maximum_stock: parseFloat(data.maximum_stock) || 0,
                        last_purchase_price: parseFloat(data.last_purchase_price) || 0,
                        average_price: parseFloat(data.average_price) || 0,
                        supplier_id: data.supplier_id || '',
                        lead_time_days: parseInt(data.lead_time_days) || 7,
                        storage_location: data.storage_location || '',
                        expiry_date: this.formatDateForInput(data.expiry_date),
                        is_active: data.is_active !== undefined ? data.is_active : true
                    };
                    this.originalData = { ...this.form };
                    
                    // Update supplier info after loading data
                    this.updateSupplierInfo();
                    
                    // Load stock history
                    await this.loadStockHistory();
                    
                    console.log('Raw material data loaded:', this.form);
                } else {
                    alert('Gagal memuat data: ' + (result.message || 'Data tidak ditemukan'));
                }
            } catch (error) {
                console.error('Error loading data:', error);
                alert('Terjadi kesalahan saat memuat data');
            }
        },

        async loadStockHistory() {
            try {
                const response = await fetch(`/manufacturing/raw-materials/${this.form.id}/stock-movements`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });
                const result = await response.json();
                
                if (result.success && result.data) {
                    // Handle both paginated and direct array responses
                    this.stockHistory = Array.isArray(result.data) ? result.data : (result.data.data || []);
                }
            } catch (error) {
                console.error('Error loading stock history:', error);
            }
        },

        async submitForm() {
            this.loading = true;
            
            try {
                // Clean form data before sending
                const formData = { ...this.form };
                
                // Remove empty or null values that might cause validation issues
                Object.keys(formData).forEach(key => {
                    if (formData[key] === '' || formData[key] === null || formData[key] === undefined) {
                        delete formData[key];
                    }
                });
                
                // Ensure category_id is sent instead of category if available
                // Also remove category object if it exists
                if (formData.category_id) {
                    delete formData.category;
                } else if (typeof formData.category === 'object' && formData.category !== null) {
                    // If category is an object (from previous data load), remove it
                    delete formData.category;
                }
                
                console.log('Submitting cleaned form data:', formData);
                
                const response = await fetch(`/manufacturing/raw-materials/${this.form.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(formData)
                });

                const result = await response.json();
                console.log('Server response:', result);
                
                if (result.success) {
                    alert('Bahan baku berhasil diperbarui!');
                    this.originalData = { ...this.form };
                } else {
                    console.error('Server error response:', result);
                    let errorMessage = result.message || 'Gagal memperbarui bahan baku';
                    
                    if (result.data || result.errors) {
                        const errors = result.data || result.errors;
                        console.log('Detailed validation errors:', errors);
                        
                        // Format error messages
                        let errorDetails = [];
                        for (const [field, messages] of Object.entries(errors)) {
                            if (Array.isArray(messages)) {
                                errorDetails.push(`${field}: ${messages.join(', ')}`);
                            } else {
                                errorDetails.push(`${field}: ${messages}`);
                            }
                        }
                        
                        if (errorDetails.length > 0) {
                            errorMessage += '\n\nDetail errors:\n' + errorDetails.join('\n');
                        }
                    }
                    
                    alert(errorMessage);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
            } finally {
                this.loading = false;
            }
        },

        async applyStockAdjustment() {
            if (!this.stockAdjustment.quantity || !this.stockAdjustment.reason) {
                alert('Harap isi jumlah dan alasan penyesuaian');
                return;
            }

            try {
                const response = await fetch(`/manufacturing/raw-materials/${this.form.id}/adjust-stock`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(this.stockAdjustment)
                });

                const result = await response.json();
                
                if (result.success) {
                    // Update current stock
                    if (this.stockAdjustment.type === 'in') {
                        this.form.current_stock += parseFloat(this.stockAdjustment.quantity);
                    } else if (this.stockAdjustment.type === 'out') {
                        this.form.current_stock -= parseFloat(this.stockAdjustment.quantity);
                    } else {
                        this.form.current_stock = parseFloat(this.stockAdjustment.quantity);
                    }

                    // Reset adjustment form
                    this.stockAdjustment = {
                        type: 'in',
                        quantity: 0,
                        reason: ''
                    };
                    this.showStockAdjustment = false;

                    // Reload stock history
                    await this.loadStockHistory();
                    
                    alert('Penyesuaian stok berhasil diterapkan!');
                } else {
                    alert('Gagal menerapkan penyesuaian stok: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menerapkan penyesuaian stok');
            }
        },

        updateSupplierInfo() {
            if (this.form.supplier_id && Array.isArray(this.suppliers)) {
                this.selectedSupplier = this.suppliers.find(s => s && s.id == this.form.supplier_id) || null;
                // Auto-fill lead time if supplier has it and current is default
                if (this.selectedSupplier && this.selectedSupplier.lead_time_days && this.form.lead_time_days == 7) {
                    this.form.lead_time_days = this.selectedSupplier.lead_time_days;
                }
            } else {
                this.selectedSupplier = null;
            }
        },

        resetForm() {
            if (confirm('Apakah Anda yakin ingin mereset form ke data asli?')) {
                this.form = { ...this.originalData };
                this.updateSupplierInfo();
            }
        },

        async deleteItem() {
            if (!confirm('Apakah Anda yakin ingin menghapus bahan baku ini? Tindakan ini tidak dapat dibatalkan.')) {
                return;
            }

            try {
                const response = await fetch(`/manufacturing/raw-materials/${this.form.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('Bahan baku berhasil dihapus!');
                    window.location.href = '/manufacturing/raw-materials';
                } else {
                    alert('Gagal menghapus bahan baku: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus data');
            }
        },

        formatDate(dateString) {
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
            const numAmount = parseFloat(amount) || 0;
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(numAmount);
        },

        formatDateForInput(dateString) {
            if (!dateString) return '';
            
            try {
                // Handle different date formats from database
                const date = new Date(dateString);
                
                // Check if date is valid
                if (isNaN(date.getTime())) {
                    return '';
                }
                
                // Format as YYYY-MM-DD for HTML date input
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                
                return `${year}-${month}-${day}`;
            } catch (error) {
                console.error('Error formatting date:', error);
                return '';
            }
        }
    }
}
</script>
@endpush