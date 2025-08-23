@extends('layouts.dashboard')

@section('title', 'Buat Purchase Order')
@section('page-title', 'Buat Purchase Order')

@section('content')
<div x-data="createPurchaseOrder()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Buat Purchase Order</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Buat purchase order baru untuk pembelian bahan baku</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.raw-materials.purchasing') }}"
               class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Purchasing
            </a>
        </div>
    </div>

    <!-- Main Form -->
    <div class="card">
        <div class="p-6">
            <form @submit.prevent="savePO" class="space-y-6">
                <!-- PO Header Information -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Pemasok *</label>
                            <select x-model="po.supplier_id" @change="onSupplierChange" class="input" required>
                                <option value="">Pilih Pemasok</option>
                                <template x-for="supplier in suppliers" :key="supplier.id">
                                    <option :value="supplier.id" x-text="supplier.name"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Tanggal Order *</label>
                            <input type="date" x-model="po.order_date" class="input" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Tanggal Diharapkan *</label>
                            <input type="date" x-model="po.expected_date" class="input" required>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Nomor Referensi</label>
                            <input type="text" x-model="po.reference" class="input" placeholder="Referensi internal (opsional)">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                            <select x-model="po.status" class="input">
                                <option value="draft">Draft</option>
                                <option value="sent">Terkirim</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Prioritas</label>
                            <select x-model="po.priority" class="input">
                                <option value="normal">Normal</option>
                                <option value="urgent">Mendesak</option>
                                <option value="low">Rendah</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- PO Items Section -->
                <div class="border-t border-border pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-foreground">Item Purchase Order</h3>
                        <button type="button" @click="addItem" class="btn btn-primary btn-sm touch-manipulation">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Tambah Item
                        </button>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-4">
                        <template x-for="(item, index) in po.items" :key="index">
                            <div class="border border-border rounded-lg p-4">
                                <!-- Desktop Layout -->
                                <div class="hidden lg:grid lg:grid-cols-6 gap-4 items-end">
                                    <div class="lg:col-span-2">
                                        <label class="block text-sm font-medium text-foreground mb-2">Bahan Baku *</label>
                                        <select x-model="item.material_id" @change="updateItemPrice(index)" class="input" required>
                                            <option value="">Pilih Bahan Baku</option>
                                            <template x-for="material in rawMaterials" :key="material.id">
                                                <option :value="material.id" x-text="material.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Jumlah *</label>
                                        <input type="number" x-model="item.quantity" @input="calculateItemTotal(index)" class="input" placeholder="0" min="1" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Satuan *</label>
                                        <input type="text" x-model="item.unit" class="input" placeholder="kg, pcs, dll" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Harga Satuan *</label>
                                        <input type="number" x-model="item.unit_price" @input="calculateItemTotal(index)" class="input" placeholder="0" min="0" required>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="block text-sm font-medium text-foreground mb-2">Total</label>
                                            <div class="text-lg font-semibold text-foreground" x-text="formatCurrency(item.total || 0)"></div>
                                        </div>
                                        <button type="button" @click="removeItem(index)" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg touch-manipulation">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Mobile Layout -->
                                <div class="lg:hidden space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-base font-medium text-foreground">Item #<span x-text="index + 1"></span></h4>
                                        <button type="button" @click="removeItem(index)" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg touch-manipulation">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Bahan Baku *</label>
                                        <select x-model="item.material_id" @change="updateItemPrice(index)" class="input" required>
                                            <option value="">Pilih Bahan Baku</option>
                                            <template x-for="material in rawMaterials" :key="material.id">
                                                <option :value="material.id" x-text="material.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-foreground mb-2">Jumlah *</label>
                                            <input type="number" x-model="item.quantity" @input="calculateItemTotal(index)" class="input" placeholder="0" min="1" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-foreground mb-2">Satuan *</label>
                                            <input type="text" x-model="item.unit" class="input" placeholder="kg, pcs" required>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Harga Satuan *</label>
                                        <input type="number" x-model="item.unit_price" @input="calculateItemTotal(index)" class="input" placeholder="0" min="0" required>
                                    </div>
                                    
                                    <div class="p-3 bg-border/30 rounded-lg">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-muted">Total Item:</span>
                                            <span class="text-lg font-semibold text-foreground" x-text="formatCurrency(item.total || 0)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Empty State -->
                        <div x-show="po.items.length === 0" class="text-center py-8 text-muted">
                            <svg class="w-12 h-12 mx-auto mb-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p>Belum ada item yang ditambahkan</p>
                            <p class="text-sm">Klik "Tambah Item" untuk menambahkan bahan baku</p>
                        </div>
                    </div>

                    <!-- PO Summary -->
                    <div x-show="po.items.length > 0" class="mt-6 p-4 bg-border/30 rounded-lg">
                        <div class="space-y-4">
                            <!-- Items Summary -->
                            <div class="space-y-2">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-muted">Total Item:</span>
                                    <span class="font-medium text-foreground" x-text="po.items.length + ' item'"></span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-muted">Total Quantity:</span>
                                    <span class="font-medium text-foreground" x-text="getTotalQuantity() + ' unit'"></span>
                                </div>
                            </div>

                            <!-- Tax and Shipping Inputs -->
                            <div class="border-t border-border pt-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Pajak (%)</label>
                                        <input type="number" x-model="po.tax_percentage" @input="calculateTotal()" class="input" placeholder="0" min="0" max="100" step="0.1">
                                        <p class="text-xs text-muted mt-1">Masukkan persentase pajak (misal: 10 untuk 10%)</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Ongkos Kirim (Rp)</label>
                                        <input type="number" x-model="po.shipping_cost" @input="calculateTotal()" class="input" placeholder="0" min="0">
                                        <p class="text-xs text-muted mt-1">Kosongkan jika tidak ada ongkir</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Calculation -->
                            <div class="border-t border-border pt-4">
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-muted">Subtotal:</span>
                                        <span class="font-medium text-foreground" x-text="formatCurrency(getSubtotal())"></span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-muted">Pajak:</span>
                                        <span class="font-medium text-foreground" x-text="formatCurrency(getTaxAmount())"></span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-muted">Ongkos Kirim:</span>
                                        <span class="font-medium text-foreground" x-text="formatCurrency(po.shipping_cost || 0)"></span>
                                    </div>
                                    <div class="flex justify-between items-center border-t border-border pt-2">
                                        <span class="text-lg font-medium text-foreground">Total Purchase Order:</span>
                                        <span class="text-xl font-bold text-foreground" x-text="formatCurrency(getTotalAmount())"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="border-t border-border pt-6">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Catatan</label>
                        <textarea x-model="po.notes" class="input" rows="4" placeholder="Catatan tambahan untuk purchase order ini..."></textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="border-t border-border pt-6">
                    <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                        <a href="{{ route('manufacturing.raw-materials.purchasing') }}" class="btn btn-outline touch-manipulation">
                            Batal
                        </a>
                        <button type="button" @click="saveDraft" class="btn btn-secondary touch-manipulation">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Simpan Draft
                        </button>
                        <button type="submit" class="btn btn-primary touch-manipulation">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Buat & Kirim PO
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createPurchaseOrder() {
    return {
        po: {
            supplier_id: '',
            order_date: new Date().toISOString().split('T')[0],
            expected_date: '',
            reference: '',
            status: 'draft',
            priority: 'normal',
            notes: '',
            tax_percentage: 0,
            shipping_cost: 0,
            items: []
        },
        suppliers: [],
        rawMaterials: [],
        isLoading: false,

        init() {
            this.loadSuppliers();
            // Don't load raw materials initially - wait for supplier selection
            this.addItem(); // Add first item by default
        },

        async loadSuppliers() {
            console.log('🔍 [DEBUG PO] Starting to load suppliers...');
            try {
                // Use AJAX route instead of API route to avoid authentication issues
                const response = await fetch('/ajax/suppliers', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    credentials: 'same-origin' // Include cookies for session-based auth
                });
                
                console.log('📡 [DEBUG PO] Response status:', response.status);
                const result = await response.json();
                console.log('📦 [DEBUG PO] API Response:', result);
                
                if (result.success && Array.isArray(result.data)) {
                    this.suppliers = result.data;
                    console.log('✅ [DEBUG PO] Loaded suppliers:', this.suppliers);
                    
                    console.log(`📊 [DEBUG PO] Total suppliers loaded: ${this.suppliers.length}`);
                    if (this.suppliers.length > 0) {
                        console.log('🔍 [DEBUG PO] First supplier:', this.suppliers[0]);
                    }
                } else {
                    console.error('❌ [DEBUG PO] Failed to load suppliers:', result.message);
                    this.suppliers = [];
                }
            } catch (error) {
                console.error('❌ [DEBUG PO] Error loading suppliers:', error);
                console.error('Stack trace:', error.stack);
                this.suppliers = [];
            }
        },

        async loadRawMaterials(supplierId = null) {
            try {
                let url = '/ajax/raw-materials';
                if (supplierId) {
                    url += `?supplier_id=${supplierId}`;
                }
                
                // Use AJAX route instead of API route to avoid authentication issues
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    credentials: 'same-origin'
                });
                const result = await response.json();
                
                if (result.success && Array.isArray(result.data)) {
                    this.rawMaterials = result.data;
                    console.log('✅ [DEBUG PO] Loaded raw materials for supplier:', supplierId, this.rawMaterials);
                } else {
                    console.error('Failed to load raw materials:', result.message || 'Unknown error');
                    this.rawMaterials = [];
                }
            } catch (error) {
                console.error('Error loading raw materials:', error);
                this.rawMaterials = [];
            }
        },

        onSupplierChange() {
            console.log('🔄 [DEBUG PO] Supplier changed to:', this.po.supplier_id);
            
            // Clear existing raw materials selection in items
            this.po.items.forEach(item => {
                item.material_id = '';
                item.unit = '';
                item.unit_price = 0;
                item.total = 0;
            });
            
            // Reload raw materials for the selected supplier
            if (this.po.supplier_id) {
                this.loadRawMaterials(this.po.supplier_id);
            } else {
                this.rawMaterials = [];
            }
        },

        addItem() {
            this.po.items.push({
                material_id: '',
                quantity: 1,
                unit: '',
                unit_price: 0,
                total: 0
            });
        },

        removeItem(index) {
            this.po.items.splice(index, 1);
        },

        updateItemPrice(index) {
            const item = this.po.items[index];
            const material = this.rawMaterials.find(m => m.id == item.material_id);
            if (material) {
                item.unit = material.unit;
                item.unit_price = material.last_purchase_price || material.average_price || 0;
                this.calculateItemTotal(index);
            }
        },

        calculateItemTotal(index) {
            const item = this.po.items[index];
            item.total = (item.quantity || 0) * (item.unit_price || 0);
        },

        getTotalQuantity() {
            return this.po.items.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
        },

        getSubtotal() {
            return this.po.items.reduce((sum, item) => sum + (item.total || 0), 0);
        },

        getTaxAmount() {
            const subtotal = this.getSubtotal();
            const taxPercentage = parseFloat(this.po.tax_percentage) || 0;
            return subtotal * (taxPercentage / 100);
        },

        getTotalAmount() {
            const subtotal = this.getSubtotal();
            const tax = this.getTaxAmount();
            const shipping = parseFloat(this.po.shipping_cost) || 0;
            return subtotal + tax + shipping;
        },

        calculateTotal() {
            // This function is called when tax percentage or shipping changes
            // The total is automatically calculated by getTotalAmount()
        },

        async saveDraft() {
            this.po.status = 'draft';
            await this.savePO();
        },

        async savePO() {
            if (this.po.items.length === 0) {
                alert('Harap tambahkan minimal satu item');
                return;
            }

            // Validate required fields
            if (!this.po.supplier_id || !this.po.order_date || !this.po.expected_date) {
                alert('Harap lengkapi semua field yang wajib diisi');
                return;
            }

            // Validate items
            for (let i = 0; i < this.po.items.length; i++) {
                const item = this.po.items[i];
                if (!item.material_id || !item.quantity || !item.unit || !item.unit_price) {
                    alert(`Item #${i + 1} belum lengkap. Harap lengkapi semua field.`);
                    return;
                }
            }

            this.isLoading = true;

            try {
                // Calculate values properly
                const subtotal = this.getSubtotal();
                const taxPercentage = parseFloat(this.po.tax_percentage) || 0;
                const taxAmount = this.getTaxAmount();
                const shippingCost = parseFloat(this.po.shipping_cost) || 0;
                const totalAmount = this.getTotalAmount();
                
                // Ensure tax_amount is properly calculated
                const finalTaxAmount = subtotal * (taxPercentage / 100);
                
                const requestData = {
                    ...this.po,
                    items: this.po.items.map(item => ({
                        raw_material_id: item.material_id,
                        quantity: item.quantity,
                        unit_price: item.unit_price
                    })),
                    tax_percentage: taxPercentage,
                    tax_amount: finalTaxAmount,
                    shipping_cost: shippingCost,
                    total_amount: subtotal + finalTaxAmount + shippingCost,
                    total_items: this.po.items.length,
                    total_quantity: this.getTotalQuantity()
                };
                
                const response = await fetch('/ajax/purchase-orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(requestData)
                });

                if (response.ok) {
                    const result = await response.json();
                    alert('Purchase Order berhasil dibuat!');
                    window.location.href = '{{ route("manufacturing.raw-materials.purchasing") }}';
                } else {
                    const error = await response.json();
                    alert('Gagal menyimpan Purchase Order: ' + (error.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error saving PO:', error);
                alert('Terjadi kesalahan saat menyimpan Purchase Order');
            } finally {
                this.isLoading = false;
            }
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount || 0);
        }
    }
}
</script>
@endpush