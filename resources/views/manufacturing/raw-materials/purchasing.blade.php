@extends('layouts.dashboard')

@section('title', 'Pembelian Bahan Baku')
@section('page-title', 'Pembelian Bahan Baku')

@section('content')
<div x-data="purchasingIndex()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Pembelian Bahan Baku</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Kelola purchase order dan pembelian bahan baku</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.raw-materials.index') }}"
               class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Bahan Baku
            </a>
            <a href="{{ route('manufacturing.raw-materials.purchasing.create') }}" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Buat Purchase Order
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Total PO Bulan Ini</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="monthlyPOs">0</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">PO Pending</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="pendingPOs">0</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Total Pembelian</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="formatCurrency(totalPurchases)">Rp 0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-muted leading-tight">Rata-rata Lead Time</p>
                    <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="averageLeadTime + ' hari'">0 hari</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative">
                    <input type="text" 
                           x-model="search" 
                           @input="filterData"
                           placeholder="Cari PO..." 
                           class="input pl-10">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <select x-model="statusFilter" @change="filterData" class="input">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="sent">Terkirim</option>
                    <option value="received">Diterima</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
                <select x-model="supplierFilter" @change="filterData" class="input">
                    <option value="">Semua Pemasok</option>
                    <template x-for="supplier in suppliers" :key="supplier.id">
                        <option :value="supplier.id" x-text="supplier.name"></option>
                    </template>
                </select>
                <input type="date" x-model="dateFilter" @change="filterData" class="input">
            </div>
        </div>
    </div>

    <!-- Purchase Orders Table/Cards -->
    <div class="card">
        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-border/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">No. PO</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Pemasok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Expected Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-for="po in filteredData" :key="po.id || Math.random()">
                        <tr class="hover:bg-border/30">
                            <td class="px-6 py-4">
                                <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="po.po_number || '-'"></div>
                                <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="'Ref: ' + (po.reference || '-')"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="po.supplier_name || '-'"></div>
                                <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="po.supplier_contact || '-'"></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="formatDate(po.order_date)"></td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-foreground" x-text="(po.total_items || 0) + ' item'"></div>
                                <div class="text-sm text-muted" x-text="(po.total_quantity || 0) + ' unit'"></div>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-foreground" x-text="formatCurrency(po.total_amount)"></td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="formatDate(po.expected_date)"></td>
                            <td class="px-6 py-4">
                                <span :class="getStatusColor(po.status || '')"
                                      class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                      x-text="getStatusText(po.status || '')"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <button @click="viewPO(po)" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg touch-manipulation" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button @click="editPO(po)"
                                            x-show="po.status === 'draft'"
                                            class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg touch-manipulation"
                                            title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="sendPO(po)"
                                            x-show="po.status === 'draft'"
                                            class="p-2 text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-lg touch-manipulation"
                                            title="Kirim PO">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                    </button>
                                    <button @click="receiveGoods(po)"
                                            x-show="po.status === 'confirmed' || po.status === 'partial'"
                                            class="p-2 text-orange-600 hover:text-orange-800 hover:bg-orange-50 rounded-lg touch-manipulation"
                                            title="Terima Barang">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </button>
                                    <button @click="printPO(po)" class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg touch-manipulation" title="Print">
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

        <!-- Mobile Card View -->
        <div class="lg:hidden space-y-4 p-4">
            <template x-for="po in filteredData" :key="po.id || Math.random()">
                <div class="bg-background border border-border rounded-lg p-4 space-y-3">
                    <!-- Header with PO number and status -->
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-base md:text-lg font-medium text-foreground leading-tight" x-text="po.po_number || '-'"></h3>
                            <p class="text-xs md:text-sm text-muted leading-relaxed" x-text="'Ref: ' + (po.reference || '-')"></p>
                        </div>
                        <span :class="getStatusColor(po.status || '')"
                              class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                              x-text="getStatusText(po.status || '')"></span>
                    </div>

                    <!-- Supplier Info -->
                    <div>
                        <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="po.supplier_name || '-'"></div>
                        <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="po.supplier_contact || '-'"></div>
                    </div>

                    <!-- Order Details -->
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-muted">Tanggal Order:</span>
                            <p class="font-medium text-foreground" x-text="formatDate(po.order_date)"></p>
                        </div>
                        <div>
                            <span class="text-muted">Expected Date:</span>
                            <p class="font-medium text-foreground" x-text="formatDate(po.expected_date)"></p>
                        </div>
                        <div>
                            <span class="text-muted">Total Items:</span>
                            <p class="font-medium text-foreground" x-text="(po.total_items || 0) + ' item'"></p>
                        </div>
                        <div>
                            <span class="text-muted">Total Quantity:</span>
                            <p class="font-medium text-foreground" x-text="(po.total_quantity || 0) + ' unit'"></p>
                        </div>
                    </div>

                    <!-- Total Amount -->
                    <div class="flex items-center justify-between p-3 bg-border/30 rounded-lg">
                        <span class="text-sm font-medium text-muted">Total Amount:</span>
                        <span class="text-lg font-bold text-foreground" x-text="formatCurrency(po.total_amount)"></span>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-2 pt-2 border-t border-border">
                        <button @click="viewPO(po)"
                                class="flex items-center px-2 py-1 text-xs text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Lihat
                        </button>
                        <button @click="editPO(po)"
                                x-show="po.status === 'draft'"
                                class="flex items-center px-2 py-1 text-xs text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                        <button @click="sendPO(po)"
                                x-show="po.status === 'draft'"
                                class="flex items-center px-2 py-1 text-xs text-purple-600 hover:text-purple-800 hover:bg-purple-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Kirim
                        </button>
                        <button @click="receiveGoods(po)"
                                x-show="po.status === 'confirmed' || po.status === 'partial'"
                                class="flex items-center px-2 py-1 text-xs text-orange-600 hover:text-orange-800 hover:bg-orange-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Terima
                        </button>
                        <button @click="printPO(po)"
                                class="flex items-center px-2 py-1 text-xs text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-colors touch-manipulation">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Define the function globally before Alpine initializes
window.purchasingIndex = function() {
    return {
        purchaseOrders: [],
        filteredData: [],
        suppliers: [],
        search: '',
        statusFilter: '',
        supplierFilter: '',
        dateFilter: '',

        init() {
            // Initialize with empty arrays to prevent undefined errors
            this.purchaseOrders = [];
            this.filteredData = [];
            this.suppliers = [];
            
            // Load data after initialization
            this.loadData();
            this.loadSuppliers();
        },

        async loadData() {
            console.log('🔄 Loading purchase orders...');
            try {
                const response = await fetch('/ajax/purchase-orders', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });
                
                console.log('📡 Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('📦 API Response:', result);
                
                if (result.success) {
                    // The API returns data in result.data.data array
                    const data = result.data?.data || [];
                    console.log('📋 Extracted data:', data);
                    console.log('📊 Data is array:', Array.isArray(data));
                    console.log('📈 Data length:', data.length);
                    
                    this.purchaseOrders = Array.isArray(data) ? data : [];
                    this.filteredData = [...this.purchaseOrders];
                    
                    console.log('✅ Purchase orders loaded:', this.purchaseOrders.length);
                    console.log('🔍 Filtered data:', this.filteredData.length);
                } else {
                    console.error('❌ Failed to load purchase orders:', result.message);
                    this.purchaseOrders = [];
                    this.filteredData = [];
                }
            } catch (error) {
                console.error('💥 Error loading purchase orders:', error);
                this.purchaseOrders = [];
                this.filteredData = [];
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
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    // Ensure we always get an array
                    const data = result.data?.data || result.data || [];
                    this.suppliers = Array.isArray(data) ? data : [];
                } else {
                    console.error('Failed to load suppliers:', result.message);
                    this.suppliers = [];
                }
            } catch (error) {
                console.error('Error loading suppliers:', error);
                this.suppliers = [];
            }
        },

        filterData() {
            // Ensure purchaseOrders is an array before filtering
            if (!Array.isArray(this.purchaseOrders)) {
                this.purchaseOrders = [];
                this.filteredData = [];
                return;
            }
            
            this.filteredData = this.purchaseOrders.filter(po => {
                // Safe property access with fallbacks
                const poNumber = po.po_number || '';
                const supplierName = po.supplier_name || '';
                const status = po.status || '';
                const supplierId = po.supplier_id || '';
                const orderDate = po.order_date || '';
                
                const matchesSearch = poNumber.toLowerCase().includes(this.search.toLowerCase()) ||
                                    supplierName.toLowerCase().includes(this.search.toLowerCase());
                const matchesStatus = !this.statusFilter || status === this.statusFilter;
                const matchesSupplier = !this.supplierFilter || supplierId == this.supplierFilter;
                const matchesDate = !this.dateFilter || orderDate === this.dateFilter;
                
                return matchesSearch && matchesStatus && matchesSupplier && matchesDate;
            });
        },

        get monthlyPOs() {
            if (!Array.isArray(this.purchaseOrders)) return 0;
            try {
                const currentMonth = new Date().getMonth();
                return this.purchaseOrders.filter(po => {
                    if (!po.order_date) return false;
                    const orderDate = new Date(po.order_date);
                    return !isNaN(orderDate.getTime()) && orderDate.getMonth() === currentMonth;
                }).length;
            } catch (error) {
                console.error('Error calculating monthly POs:', error);
                return 0;
            }
        },

        get pendingPOs() {
            if (!Array.isArray(this.purchaseOrders)) return 0;
            try {
                return this.purchaseOrders.filter(po => {
                    const status = po.status || '';
                    return ['draft', 'sent', 'confirmed'].includes(status);
                }).length;
            } catch (error) {
                console.error('Error calculating pending POs:', error);
                return 0;
            }
        },

        get totalPurchases() {
            if (!Array.isArray(this.purchaseOrders)) return 0;
            try {
                return this.purchaseOrders.reduce((sum, po) => {
                    const amount = parseFloat(po.total_amount) || 0;
                    return sum + amount;
                }, 0);
            } catch (error) {
                console.error('Error calculating total purchases:', error);
                return 0;
            }
        },

        get averageLeadTime() {
            if (!Array.isArray(this.purchaseOrders)) return 0;
            try {
                const completedPOs = this.purchaseOrders.filter(po => po.status === 'completed' && po.order_date && po.completed_date);
                if (completedPOs.length === 0) return 0;
                
                const totalLeadTime = completedPOs.reduce((sum, po) => {
                    const orderDate = new Date(po.order_date);
                    const completedDate = new Date(po.completed_date);
                    
                    if (isNaN(orderDate.getTime()) || isNaN(completedDate.getTime())) return sum;
                    
                    const leadTime = Math.ceil((completedDate - orderDate) / (1000 * 60 * 60 * 24));
                    return sum + (leadTime > 0 ? leadTime : 0);
                }, 0);
                
                return Math.round(totalLeadTime / completedPOs.length);
            } catch (error) {
                console.error('Error calculating average lead time:', error);
                return 0;
            }
        },

        getStatusColor(status) {
            const colors = {
                'draft': 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
                'sent': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
                'confirmed': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                'partial': 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400',
                'received': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
            };
            return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400';
        },

        getStatusText(status) {
            const texts = {
                'draft': 'Draft',
                'sent': 'Terkirim',
                'confirmed': 'Dikonfirmasi',
                'partial': 'Sebagian Diterima',
                'received': 'Diterima',
                'cancelled': 'Dibatalkan'
            };
            return texts[status] || status;
        },

        formatDate(dateString) {
            if (!dateString) return '-';
            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '-';
                
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            } catch (error) {
                console.error('Error formatting date:', error);
                return '-';
            }
        },

        viewPO(po) {
            if (!po || !po.id) {
                console.error('Invalid PO data for view');
                return;
            }
            window.location.href = `/manufacturing/raw-materials/purchase-order-detail?id=${po.id}`;
        },

        editPO(po) {
            if (!po || !po.id) {
                console.error('Invalid PO data for edit');
                return;
            }
            window.location.href = `/manufacturing/raw-materials/purchasing/edit?id=${po.id}`;
        },

        async sendPO(po) {
            if (confirm(`Apakah Anda yakin ingin mengirim PO ${po.po_number}?`)) {
                try {
                    const response = await fetch(`/ajax/purchase-orders/${po.id}/send`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        credentials: 'same-origin'
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Purchase order berhasil dikirim');
                        this.loadData();
                    } else {
                        alert('Gagal mengirim purchase order: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error sending PO:', error);
                    alert('Terjadi kesalahan saat mengirim purchase order');
                }
            }
        },

        receiveGoods(po) {
            if (!po || !po.id) {
                console.error('Invalid PO data for receive goods');
                return;
            }
            window.location.href = `/manufacturing/raw-materials/purchasing/receive?id=${po.id}`;
        },

        printPO(po) {
            if (!po || !po.id) {
                console.error('Invalid PO data for print');
                return;
            }
            window.open(`/ajax/purchase-orders/${po.id}/print`, '_blank');
        },

        formatCurrency(amount) {
            if (amount === null || amount === undefined || isNaN(amount)) return 'Rp 0';
            try {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(amount);
            } catch (error) {
                console.error('Error formatting currency:', error);
                return 'Rp 0';
            }
        }
    };
};
</script>
@endpush
                    