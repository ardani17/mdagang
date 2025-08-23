@extends('layouts.dashboard')

@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-muted hover:text-foreground">
        <svg class="w-3 h-3 mr-2.5" fill="currentColor" viewBox="0 0 20 20">
            <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
        </svg>
        Dasbor
    </a>
</li>
<li>
    <div class="flex items-center">
        <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('products.index') }}" class="ml-1 text-sm font-medium text-muted hover:text-foreground md:ml-2">Produk</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Tambah Produk</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="productForm()" class="max-w-4xl mx-auto space-y-6">
    <form @submit.prevent="submitForm()" class="space-y-6">
        <!-- Basic Information -->
        <div class="card">
            <div class="p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-foreground">Informasi Dasar</h3>
                <p class="text-sm text-muted">Masukkan informasi dasar produk Anda</p>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Produk -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-foreground mb-2">
                            Nama Produk <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               x-model="form.name"
                               @input="generateSKU()"
                               class="input"
                               placeholder="Masukkan nama produk"
                               required>
                        <p x-show="errors.name" x-text="errors.name" class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <!-- SKU -->
                    <div>
                        <label for="sku" class="block text-sm font-medium text-foreground mb-2">
                            SKU <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="sku" 
                               name="sku" 
                               x-model="form.sku"
                               class="input"
                               placeholder="SKU akan dibuat otomatis"
                               required>
                        <p x-show="errors.sku" x-text="errors.sku" class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-foreground mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select id="type" 
                                name="type" 
                                x-model="form.type"
                                class="input"
                                required>
                            <option value="">Pilih Kategori</option>
                            <option value="food">Makanan</option>
                            <option value="beverage">Minuman</option>
                        </select>
                        <p x-show="errors.type" x-text="errors.type" class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <!-- Unit -->
                    <div>
                        <label for="unit" class="block text-sm font-medium text-foreground mb-2">
                            Satuan <span class="text-red-500">*</span>
                        </label>
                        <select id="unit" 
                                name="unit" 
                                x-model="form.unit"
                                class="input"
                                required>
                            <option value="">Pilih Satuan</option>
                            <option value="pcs">Pcs</option>
                            <option value="kg">Kg</option>
                            <option value="gram">Gram</option>
                            <option value="liter">Liter</option>
                            <option value="ml">ML</option>
                            <option value="porsi">Porsi</option>
                            <option value="pack">Pack</option>
                            <option value="box">Box</option>
                        </select>
                        <p x-show="errors.unit" x-text="errors.unit" class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   x-model="form.is_active"
                                   class="rounded border-border text-primary focus:ring-primary">
                            <label for="is_active" class="ml-2 text-sm text-foreground">Produk Aktif</label>
                        </div>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="description" class="block text-sm font-medium text-foreground mb-2">
                        Deskripsi Produk
                    </label>
                    <textarea id="description" 
                              name="description" 
                              x-model="form.description"
                              rows="3" 
                              class="input"
                              placeholder="Masukkan deskripsi produk (opsional)"></textarea>
                    <p x-show="errors.description" x-text="errors.description" class="mt-1 text-sm text-red-600"></p>
                </div>

                <!-- Upload Gambar -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Gambar Produk</label>
                    <div class="flex items-center space-x-4">
                        <div class="w-24 h-24 bg-surface border-2 border-dashed border-border rounded-lg flex items-center justify-center overflow-hidden">
                            <img x-show="imagePreview" 
                                 :src="imagePreview" 
                                 alt="Preview"
                                 class="w-full h-full object-cover">
                            <div x-show="!imagePreview" class="text-center">
                                <svg class="w-8 h-8 text-muted mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-xs text-muted">Gambar</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <input type="file" 
                                   id="image" 
                                   name="image" 
                                   @change="handleImageUpload($event)"
                                   accept="image/*"
                                   class="hidden">
                            <label for="image" class="btn-secondary cursor-pointer inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Pilih Gambar
                            </label>
                            <p class="text-xs text-muted mt-1">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                        </div>
                    </div>
                    <p x-show="errors.image" x-text="errors.image" class="mt-1 text-sm text-red-600"></p>
                </div>
            </div>
        </div>

        <!-- Pricing & Stock -->
        <div class="card">
            <div class="p-6 border-b border-border">
                <h3 class="text-lg font-semibold text-foreground">Harga & Stok</h3>
                <p class="text-sm text-muted">Atur harga jual dan informasi stok produk</p>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Harga Dasar -->
                    <div>
                        <label for="base_price" class="block text-sm font-medium text-foreground mb-2">
                            Harga Dasar <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted">Rp</span>
                            <input type="number" 
                                   id="base_price" 
                                   name="base_price" 
                                   x-model="form.base_price"
                                   @input="calculateSellingPrice()"
                                   class="input pl-10"
                                   placeholder="0"
                                   min="0"
                                   step="100"
                                   required>
                        </div>
                        <p x-show="errors.base_price" x-text="errors.base_price" class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <!-- Margin Keuntungan -->
                    <div>
                        <label for="profit_margin" class="block text-sm font-medium text-foreground mb-2">
                            Margin Keuntungan (%)
                        </label>
                        <input type="number" 
                               id="profit_margin" 
                               name="profit_margin" 
                               x-model="profitMargin"
                               @input="calculateSellingPrice()"
                               class="input"
                               placeholder="30"
                               min="0"
                               max="1000"
                               step="1">
                    </div>

                    <!-- Harga Jual -->
                    <div>
                        <label for="selling_price" class="block text-sm font-medium text-foreground mb-2">
                            Harga Jual <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted">Rp</span>
                            <input type="number" 
                                   id="selling_price" 
                                   name="selling_price" 
                                   x-model="form.selling_price"
                                   class="input pl-10"
                                   placeholder="0"
                                   min="0"
                                   step="100"
                                   required>
                        </div>
                        <p x-show="errors.selling_price" x-text="errors.selling_price" class="mt-1 text-sm text-red-600"></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Stok Awal -->
                    <div>
                        <label for="current_stock" class="block text-sm font-medium text-foreground mb-2">
                            Stok Awal
                        </label>
                        <input type="number" 
                               id="current_stock" 
                               name="current_stock" 
                               x-model="form.current_stock"
                               class="input"
                               placeholder="0"
                               min="0"
                               step="1">
                        <p x-show="errors.current_stock" x-text="errors.current_stock" class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <!-- Stok Minimum -->
                    <div>
                        <label for="min_stock" class="block text-sm font-medium text-foreground mb-2">
                            Stok Minimum
                        </label>
                        <input type="number" 
                               id="min_stock" 
                               name="min_stock" 
                               x-model="form.min_stock"
                               class="input"
                               placeholder="5"
                               min="0"
                               step="1">
                        <p x-show="errors.min_stock" x-text="errors.min_stock" class="mt-1 text-sm text-red-600"></p>
                    </div>
                </div>

                <!-- Profit Preview -->
                <div x-show="form.base_price > 0 && form.selling_price > 0" class="bg-surface p-4 rounded-lg">
                    <h4 class="font-medium text-foreground mb-2">Ringkasan Keuntungan</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-muted">Harga Dasar:</span>
                            <div class="font-medium" x-text="formatCurrency(form.base_price)"></div>
                        </div>
                        <div>
                            <span class="text-muted">Harga Jual:</span>
                            <div class="font-medium" x-text="formatCurrency(form.selling_price)"></div>
                        </div>
                        <div>
                            <span class="text-muted">Keuntungan per Unit:</span>
                            <div class="font-medium text-green-600" x-text="formatCurrency(form.selling_price - form.base_price)"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('products.index') }}" class="btn-secondary">
                Batal
            </a>
            <button type="submit" 
                    class="btn-primary"
                    :disabled="loading">
                <span x-show="!loading">Simpan Produk</span>
                <span x-show="loading" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menyimpan...
                </span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function productForm() {
    return {
        loading: false,
        imagePreview: null,
        profitMargin: 30,
        form: {
            name: '',
            sku: '',
            description: '',
            type: '',
            unit: '',
            image: null,
            base_price: 0,
            selling_price: 0,
            min_stock: 5,
            current_stock: 0,
            is_active: true
        },
        errors: {},

        generateSKU() {
            if (this.form.name.length >= 3) {
                const prefix = this.form.name.substring(0, 3).toUpperCase();
                const timestamp = Date.now().toString().slice(-6);
                this.form.sku = `${prefix}${timestamp}`;
            }
        },

        calculateSellingPrice() {
            if (this.form.base_price > 0 && this.profitMargin > 0) {
                const margin = this.profitMargin / 100;
                this.form.selling_price = Math.round(this.form.base_price * (1 + margin));
            }
        },

        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    this.$store.notifications.add({
                        type: 'error',
                        title: 'Gagal!',
                        message: 'Ukuran file terlalu besar. Maksimal 2MB.'
                    });
                    return;
                }

                // Validate file type
                if (!file.type.startsWith('image/')) {
                    this.$store.notifications.add({
                        type: 'error',
                        title: 'Gagal!',
                        message: 'File harus berupa gambar.'
                    });
                    return;
                }

                this.form.image = file;
                
                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        async submitForm() {
            this.loading = true;
            this.errors = {};

            try {
                const formData = new FormData();
                
                // Append form data
                Object.keys(this.form).forEach(key => {
                    if (this.form[key] !== null && this.form[key] !== '') {
                        if (key === 'is_active') {
                            formData.append(key, this.form[key] ? '1' : '0');
                        } else {
                            formData.append(key, this.form[key]);
                        }
                    }
                });

                const response = await fetch('{{ route("products.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    this.$store.notifications.add({
                        type: 'success',
                        title: 'Berhasil!',
                        message: 'Produk berhasil ditambahkan.'
                    });
                    
                    // Redirect to products index
                    setTimeout(() => {
                        window.location.href = '{{ route("products.index") }}';
                    }, 1000);
                } else {
                    if (data.errors) {
                        this.errors = data.errors;
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan');
                    }
                }
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: error.message || 'Terjadi kesalahan saat menyimpan produk.'
                });
            } finally {
                this.loading = false;
            }
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount || 0);
        }
    }
}
</script>
@endpush