@extends('layouts.dashboard')

@section('title', 'Tambah Resep Baru')
@section('page-title', 'Tambah Resep Produk Baru')

@section('content')
<div x-data="recipeForm()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Tambah Resep Produk Baru</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Isi formulir di bawah untuk membuat resep produksi baru</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.recipes.index') }}"
               class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Form Section -->
    <div class="card p-6">
        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column - Basic Information -->
                <div class="space-y-6">
                    <h2 class="text-lg font-semibold text-foreground border-b pb-2">Informasi Dasar</h2>
                    
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Nama Resep *</label>
                        <input type="text" x-model="formData.name" required
                               class="input w-full" placeholder="Masukkan nama resep">
                        <p class="text-red-500 text-xs mt-1" x-show="errors.name" x-text="errors.name"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Kode Resep *</label>
                        <input type="text" x-model="formData.code" required
                               class="input w-full" placeholder="Masukkan kode unik resep">
                        <p class="text-red-500 text-xs mt-1" x-show="errors.code" x-text="errors.code"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Deskripsi</label>
                        <textarea x-model="formData.description" rows="3"
                                  class="input w-full" placeholder="Deskripsi singkat tentang resep"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Produk *</label>
                        <select x-model="formData.product_id" required class="input w-full">
                            <option value="">Pilih Produk</option>
                            <template x-for="product in products" :key="product.id">
                                <option :value="product.id" x-text="product.name"></option>
                            </template>
                        </select>
                        <p class="text-red-500 text-xs mt-1" x-show="errors.product_id" x-text="errors.product_id"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Kategori *</label>
                        <select x-model="formData.category" required class="input w-full">
                            <option value="">Pilih Kategori</option>
                            <option value="Minuman Herbal">Minuman Herbal</option>
                            <option value="Makanan Ringan">Makanan Ringan</option>
                            <option value="Kue dan Roti">Kue dan Roti</option>
                            <option value="Makanan Utama">Makanan Utama</option>
                        </select>
                        <p class="text-red-500 text-xs mt-1" x-show="errors.category" x-text="errors.category"></p>
                    </div>
                </div>

                <!-- Right Column - Production Details -->
                <div class="space-y-6">
                    <h2 class="text-lg font-semibold text-foreground border-b pb-2">Detail Produksi</h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Ukuran Batch *</label>
                            <input type="number" x-model="formData.batch_size" step="0.001" min="1" required
                                   class="input w-full" placeholder="0.000">
                            <p class="text-red-500 text-xs mt-1" x-show="errors.batch_size" x-text="errors.batch_size"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Satuan *</label>
                            <select x-model="formData.unit" required class="input w-full">
                                <option value="">Pilih Satuan</option>
                                <option value="pcs">Pcs</option>
                                <option value="kg">Kg</option>
                                <option value="gram">Gram</option>
                                <option value="liter">Liter</option>
                                <option value="ml">ML</option>
                                <option value="pack">Pack</option>
                            </select>
                            <p class="text-red-500 text-xs mt-1" x-show="errors.unit" x-text="errors.unit"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Waktu Produksi (menit) *</label>
                        <input type="number" x-model="formData.production_time" min="1" required
                               class="input w-full" placeholder="Dalam menit">
                        <p class="text-red-500 text-xs mt-1" x-show="errors.production_time" x-text="errors.production_time"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Instruksi Produksi</label>
                        <textarea x-model="formData.instructions" rows="3"
                                  class="input w-full" placeholder="Langkah-langkah produksi"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Catatan</label>
                        <textarea x-model="formData.notes" rows="2"
                                  class="input w-full" placeholder="Catatan tambahan"></textarea>
                    </div>
                </div>
            </div>

            <!-- Ingredients Section -->
            <div class="mt-8 pt-6 border-t">
                <h2 class="text-lg font-semibold text-foreground mb-4">Bahan Baku</h2>
                
                <div class="space-y-4" x-data="{ ingredients: [] }">
                    <template x-for="(ingredient, index) in formData.ingredients" :key="index">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 border rounded-lg">
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-2">Bahan Baku *</label>
                                <select x-model="ingredient.raw_material_id" required class="input w-full">
                                    <option value="">Pilih Bahan Baku</option>
                                    <template x-for="material in rawMaterials" :key="material.id">
                                        <option :value="material.id" x-text="material.name"></option>
                                    </template>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-2">Jumlah *</label>
                                <input type="number" x-model="ingredient.quantity" step="0.001" min="0" required
                                       class="input w-full" placeholder="0.000">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-foreground mb-2">Satuan *</label>
                                <select x-model="ingredient.unit" required class="input w-full">
                                    <option value="">Pilih Satuan</option>
                                    <option value="kg">Kg</option>
                                    <option value="gram">Gram</option>
                                    <option value="liter">Liter</option>
                                    <option value="ml">ML</option>
                                    <option value="pcs">Pcs</option>
                                </select>
                            </div>
                            
                            <div class="flex items-end">
                                <button type="button" @click="removeIngredient(index)" 
                                        class="btn btn-outline btn-danger w-full">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </template>
                    
                    <button type="button" @click="addIngredient" 
                            class="btn btn-outline mt-4">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Tambah Bahan
                    </button>
                    
                    <p class="text-red-500 text-xs mt-1" x-show="errors.ingredients" x-text="errors.ingredients"></p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t">
                <a href="{{ route('manufacturing.recipes.index') }}" class="btn btn-outline">
                    Batal
                </a>
                <button type="submit" :disabled="isSubmitting" 
                        class="btn btn-primary" :class="{'opacity-50 cursor-not-allowed': isSubmitting}">
                    <span x-show="isSubmitting" class="inline-flex">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                    <span x-show="!isSubmitting">Simpan Resep</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function recipeForm() {
    return {
        formData: {
            name: '',
            code: '',
            description: '',
            product_id: '',
            category: '',
            batch_size: 1,
            unit: '',
            production_time: 0,
            instructions: '',
            notes: '',
            ingredients: [
                {
                    raw_material_id: '',
                    quantity: 0,
                    unit: ''
                }
            ]
        },
        products: [],
        rawMaterials: [],
        errors: {},
        isSubmitting: false,

        async init() {
            await this.loadProducts();
            await this.loadRawMaterials();
        },

        async loadProducts() {
            try {
                const response = await fetch('/api/products?is_manufactured=true&is_active=true');
                const data = await response.json();
                this.products = data.data || [];
            } catch (error) {
                console.error('Error loading products:', error);
            }
        },

        async loadRawMaterials() {
            try {
                const response = await fetch('/api/raw-materials?is_active=true');
                const data = await response.json();
                this.rawMaterials = data.data || [];
            } catch (error) {
                console.error('Error loading raw materials:', error);
            }
        },

        addIngredient() {
            this.formData.ingredients.push({
                raw_material_id: '',
                quantity: 0,
                unit: ''
            });
        },

        removeIngredient(index) {
            if (this.formData.ingredients.length > 1) {
                this.formData.ingredients.splice(index, 1);
            }
        },

        async submitForm() {
            this.isSubmitting = true;
            this.errors = {};

            try {
                const response = await fetch('/api/recipes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();

                if (response.ok) {
                    // Success
                    window.location.href = '/manufacturing/recipes?success=Resep berhasil dibuat';
                } else {
                    // Validation errors
                    if (data.errors) {
                        this.errors = data.errors;
                    } else {
                        alert(data.message || 'Terjadi kesalahan saat menyimpan resep');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
            } finally {
                this.isSubmitting = false;
            }
        }
    }
}
</script>
@endpush