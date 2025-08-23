@extends('layouts.dashboard')

@section('title', 'Tambah Bahan Baku')
@section('page-title', 'Tambah Bahan Baku')

@section('content')
<div x-data="rawMaterialsCreate()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Tambah Bahan Baku</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Tambahkan bahan baku baru ke dalam sistem</p>
        </div>
        <a href="{{ route('manufacturing.raw-materials.index') }}" class="btn btn-outline touch-manipulation">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
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
                            <label class="block text-sm font-medium text-foreground mb-2">Kode</label>
                            <input type="text"
                                   x-model="form.code"
                                   class="input w-full h-12 text-base"
                                   placeholder="Auto-generate jika kosong">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Kategori</label>
                            <select x-model="form.category_id" class="input w-full h-12 text-base">
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
                    <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Informasi Stok</h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 md:gap-6">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Stok Awal *</label>
                            <input type="number"
                                   x-model="form.current_stock"
                                   class="input w-full h-12 text-base"
                                   placeholder="0"
                                   min="0"
                                   step="0.01"
                                   required>
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
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Titik Pemesanan Ulang</label>
                            <input type="number"
                                   x-model="form.reorder_point"
                                   class="input w-full h-12 text-base"
                                   placeholder="Auto dari stok minimum"
                                   min="0"
                                   step="0.01">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Jumlah Pemesanan Ulang</label>
                            <input type="number"
                                   x-model="form.reorder_quantity"
                                   class="input w-full h-12 text-base"
                                   placeholder="Auto kalkulasi"
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
                                   placeholder="Auto dari harga pembelian"
                                   min="0"
                                   step="1">
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
                            <span x-show="!loading">Simpan Bahan Baku</span>
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
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function rawMaterialsCreate() {
    return {
        loading: false,
        suppliers: [],
        categories: [],
        units: [],
        selectedSupplier: null,
        form: {
            name: '',
            code: '',
            category_id: '',
            unit: '',
            description: '',
            current_stock: 0,
            minimum_stock: 0,
            maximum_stock: 0,
            reorder_point: '',
            reorder_quantity: '',
            last_purchase_price: 0,
            average_price: '',
            supplier_id: '',
            lead_time_days: 7,
            storage_location: '',
            expiry_date: '',
            is_active: true
        },

        init() {
            this.loadFormData();
        },

        async loadFormData() {
            try {
                const response = await fetch('/manufacturing/raw-materials/form-data', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    credentials: 'same-origin'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.categories = result.data.categories || [];
                    this.units = result.data.units || [];
                    this.suppliers = result.data.suppliers || [];
                    console.log('Form data loaded successfully');
                } else {
                    console.error('Failed to load form data:', result.message);
                }
            } catch (error) {
                console.error('Error loading form data:', error);
            }
        },

        updateSupplierInfo() {
            if (this.form.supplier_id) {
                this.selectedSupplier = this.suppliers.find(s => s.id == this.form.supplier_id);
                // Auto-fill lead time if supplier has it
                if (this.selectedSupplier && this.selectedSupplier.lead_time_days) {
                    this.form.lead_time_days = this.selectedSupplier.lead_time_days;
                }
            } else {
                this.selectedSupplier = null;
                this.form.lead_time_days = 7; // Reset to default
            }
        },

        async submitForm() {
            this.loading = true;
            
            try {
                // Clean empty values
                const formData = {};
                for (const [key, value] of Object.entries(this.form)) {
                    if (value !== '' && value !== null) {
                        formData[key] = value;
                    }
                }
                
                const response = await fetch('/manufacturing/raw-materials', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(formData)
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('Bahan baku berhasil ditambahkan!');
                    window.location.href = '/manufacturing/raw-materials';
                } else {
                    let errorMessage = result.message || 'Gagal menambahkan bahan baku';
                    
                    if (result.errors) {
                        const errorList = [];
                        for (const [field, messages] of Object.entries(result.errors)) {
                            errorList.push(`${field}: ${messages.join(', ')}`);
                        }
                        if (errorList.length > 0) {
                            errorMessage += '\n\n' + errorList.join('\n');
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

        resetForm() {
            this.form = {
                name: '',
                code: '',
                category_id: '',
                unit: '',
                description: '',
                current_stock: 0,
                minimum_stock: 0,
                maximum_stock: 0,
                reorder_point: '',
                reorder_quantity: '',
                last_purchase_price: 0,
                average_price: '',
                supplier_id: '',
                lead_time_days: 7,
                storage_location: '',
                expiry_date: '',
                is_active: true
            };
            this.selectedSupplier = null;
        }
    }
}
</script>
@endpush