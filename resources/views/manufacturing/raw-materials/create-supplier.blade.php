@extends('layouts.dashboard')

@section('title', 'Tambah Pemasok Baru')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-muted hover:text-foreground">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-muted mx-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('manufacturing.raw-materials.suppliers') }}" class="text-muted hover:text-foreground">
                                Pemasok
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-muted mx-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-foreground font-medium">Tambah Pemasok</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-foreground">Tambah Pemasok Baru</h1>
            <p class="text-muted mt-1">Tambahkan informasi pemasok bahan baku untuk sistem manufaktur</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.raw-materials.suppliers') }}" 
               class="btn btn-outline touch-manipulation">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Form -->
    <form id="supplierForm" class="space-y-6">
        <!-- Progress Indicator -->
        <div class="card p-4">
            <div class="flex items-center justify-between text-sm text-muted mb-2">
                <span>Progress Pengisian</span>
                <span id="progressText">0%</span>
            </div>
            <div class="w-full bg-border rounded-full h-2">
                <div id="progressBar" class="bg-primary h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>

        <!-- Company Information -->
        <div class="card p-6">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-foreground">Informasi Perusahaan</h2>
                    <p class="text-sm text-muted">Data dasar perusahaan pemasok</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="company_name" class="block text-sm font-medium text-foreground mb-2">
                        Nama Perusahaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="company_name" name="company_name" required
                           class="form-input w-full" placeholder="Contoh: CV. Supplier Terpercaya">
                    <div class="error-message hidden text-red-500 text-sm mt-1"></div>
                </div>

                <div>
                    <label for="company_type" class="block text-sm font-medium text-foreground mb-2">
                        Jenis Perusahaan
                    </label>
                    <select id="company_type" name="company_type" class="form-select w-full">
                        <option value="">Pilih Jenis Perusahaan</option>
                        <option value="PT">PT (Perseroan Terbatas)</option>
                        <option value="CV">CV (Commanditaire Vennootschap)</option>
                        <option value="UD">UD (Usaha Dagang)</option>
                        <option value="Toko">Toko</option>
                        <option value="Perorangan">Perorangan</option>
                    </select>
                </div>

                <div>
                    <label for="tax_id" class="block text-sm font-medium text-foreground mb-2">
                        NPWP
                    </label>
                    <input type="text" id="tax_id" name="tax_id"
                           class="form-input w-full" placeholder="00.000.000.0-000.000">
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-foreground mb-2">
                        Alamat Lengkap <span class="text-red-500">*</span>
                    </label>
                    <textarea id="address" name="address" rows="3" required
                              class="form-textarea w-full" 
                              placeholder="Masukkan alamat lengkap termasuk kota dan kode pos"></textarea>
                    <div class="error-message hidden text-red-500 text-sm mt-1"></div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card p-6">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-foreground">Informasi Kontak</h2>
                    <p class="text-sm text-muted">Data kontak person perusahaan</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="contact_name" class="block text-sm font-medium text-foreground mb-2">
                        Nama Kontak Utama <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="contact_name" name="contact_name" required
                           class="form-input w-full" placeholder="Nama lengkap kontak person">
                    <div class="error-message hidden text-red-500 text-sm mt-1"></div>
                </div>

                <div>
                    <label for="position" class="block text-sm font-medium text-foreground mb-2">
                        Jabatan
                    </label>
                    <input type="text" id="position" name="position"
                           class="form-input w-full" placeholder="Contoh: Manager Penjualan">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-foreground mb-2">
                        Nomor Telepon <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" id="phone" name="phone" required
                           class="form-input w-full" placeholder="+62 812 3456 7890">
                    <div class="error-message hidden text-red-500 text-sm mt-1"></div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-foreground mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" required
                           class="form-input w-full" placeholder="email@perusahaan.com">
                    <div class="error-message hidden text-red-500 text-sm mt-1"></div>
                </div>

                <div class="md:col-span-2">
                    <label for="whatsapp" class="block text-sm font-medium text-foreground mb-2">
                        WhatsApp
                    </label>
                    <input type="tel" id="whatsapp" name="whatsapp"
                           class="form-input w-full" placeholder="+62 812 3456 7890">
                </div>
            </div>
        </div>

        <!-- Business Details -->
        <div class="card p-6">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-foreground">Detail Bisnis</h2>
                    <p class="text-sm text-muted">Informasi produk dan syarat bisnis</p>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-3">
                        Kategori Produk <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <label class="flex items-center p-3 border border-border rounded-lg hover:bg-accent cursor-pointer">
                            <input type="checkbox" name="product_categories[]" value="bahan_utama" class="form-checkbox mr-3">
                            <span class="text-sm">Bahan Utama</span>
                        </label>
                        <label class="flex items-center p-3 border border-border rounded-lg hover:bg-accent cursor-pointer">
                            <input type="checkbox" name="product_categories[]" value="pemanis" class="form-checkbox mr-3">
                            <span class="text-sm">Pemanis</span>
                        </label>
                        <label class="flex items-center p-3 border border-border rounded-lg hover:bg-accent cursor-pointer">
                            <input type="checkbox" name="product_categories[]" value="herbal" class="form-checkbox mr-3">
                            <span class="text-sm">Herbal</span>
                        </label>
                        <label class="flex items-center p-3 border border-border rounded-lg hover:bg-accent cursor-pointer">
                            <input type="checkbox" name="product_categories[]" value="kemasan" class="form-checkbox mr-3">
                            <span class="text-sm">Kemasan</span>
                        </label>
                        <label class="flex items-center p-3 border border-border rounded-lg hover:bg-accent cursor-pointer">
                            <input type="checkbox" name="product_categories[]" value="bahan_tambahan" class="form-checkbox mr-3">
                            <span class="text-sm">Bahan Tambahan</span>
                        </label>
                        <label class="flex items-center p-3 border border-border rounded-lg hover:bg-accent cursor-pointer">
                            <input type="checkbox" name="product_categories[]" value="lainnya" class="form-checkbox mr-3">
                            <span class="text-sm">Lainnya</span>
                        </label>
                    </div>
                    <div class="error-message hidden text-red-500 text-sm mt-1"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="minimum_order" class="block text-sm font-medium text-foreground mb-2">
                            Minimum Order
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted">Rp</span>
                            <input type="number" id="minimum_order" name="minimum_order"
                                   class="form-input w-full pl-10" placeholder="0" min="0">
                        </div>
                    </div>

                    <div>
                        <label for="lead_time" class="block text-sm font-medium text-foreground mb-2">
                            Lead Time (hari)
                        </label>
                        <input type="number" id="lead_time" name="lead_time"
                               class="form-input w-full" placeholder="7" min="1" max="365">
                    </div>

                    <div>
                        <label for="payment_terms" class="block text-sm font-medium text-foreground mb-2">
                            Syarat Pembayaran
                        </label>
                        <select id="payment_terms" name="payment_terms" class="form-select w-full">
                            <option value="">Pilih Syarat Pembayaran</option>
                            <option value="cash">Cash</option>
                            <option value="7_days">7 Hari</option>
                            <option value="14_days">14 Hari</option>
                            <option value="30_days">30 Hari</option>
                            <option value="60_days">60 Hari</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rating & Status -->
        <div class="card p-6">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-foreground">Rating & Status</h2>
                    <p class="text-sm text-muted">Penilaian dan status pemasok</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-3">
                        Rating Awal
                    </label>
                    <div class="flex items-center space-x-2">
                        <div class="flex space-x-1" id="rating-stars">
                            <button type="button" class="star-btn text-2xl text-gray-300 hover:text-yellow-400 focus:outline-none" data-rating="1">★</button>
                            <button type="button" class="star-btn text-2xl text-gray-300 hover:text-yellow-400 focus:outline-none" data-rating="2">★</button>
                            <button type="button" class="star-btn text-2xl text-yellow-400 hover:text-yellow-400 focus:outline-none" data-rating="3">★</button>
                            <button type="button" class="star-btn text-2xl text-gray-300 hover:text-yellow-400 focus:outline-none" data-rating="4">★</button>
                            <button type="button" class="star-btn text-2xl text-gray-300 hover:text-yellow-400 focus:outline-none" data-rating="5">★</button>
                        </div>
                        <span class="text-sm text-muted" id="rating-text">(3.0/5)</span>
                    </div>
                    <input type="hidden" id="rating" name="rating" value="3">
                </div>

                <div>
                    <label class="block text-sm font-medium text-foreground mb-3">
                        Status
                    </label>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="status" value="active" checked class="form-radio mr-2">
                            <span class="text-sm">Aktif</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="inactive" class="form-radio mr-2">
                            <span class="text-sm">Tidak Aktif</span>
                        </label>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-foreground mb-2">
                        Catatan
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                              class="form-textarea w-full" 
                              placeholder="Catatan tambahan tentang pemasok (opsional)"></textarea>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col sm:flex-row gap-3 justify-end">
            <button type="button" id="clearForm" class="btn btn-outline">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Bersihkan Form
            </button>
            <button type="button" id="saveDraft" class="btn btn-outline">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Simpan Draft
            </button>
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Pemasok
            </button>
        </div>
    </form>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
        <div class="flex items-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mr-4"></div>
            <span class="text-foreground">Menyimpan data pemasok...</span>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('supplierForm');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const loadingModal = document.getElementById('loadingModal');
    
    // Required fields for progress calculation
    const requiredFields = ['company_name', 'address', 'contact_name', 'phone', 'email'];
    const categoryCheckboxes = document.querySelectorAll('input[name="product_categories[]"]');
    
    // Star rating functionality
    const starButtons = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rating');
    const ratingText = document.getElementById('rating-text');
    
    starButtons.forEach(button => {
        button.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            ratingInput.value = rating;
            ratingText.textContent = `(${rating}.0/5)`;
            
            starButtons.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
            
            updateProgress();
        });
    });
    
    // Progress calculation
    function updateProgress() {
        let filledFields = 0;
        let totalFields = requiredFields.length + 1; // +1 for categories
        
        // Check required fields
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && field.value.trim() !== '') {
                filledFields++;
            }
        });
        
        // Check if at least one category is selected
        const hasCategory = Array.from(categoryCheckboxes).some(cb => cb.checked);
        if (hasCategory) {
            filledFields++;
        }
        
        const progress = Math.round((filledFields / totalFields) * 100);
        progressBar.style.width = progress + '%';
        progressText.textContent = progress + '%';
    }
    
    // Add event listeners for progress tracking
    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', updateProgress);
        }
    });
    
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateProgress);
    });
    
    // Form validation
    function validateForm() {
        let isValid = true;
        const errors = {};
        
        // Validate required fields
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            const errorDiv = field.parentNode.querySelector('.error-message');
            
            if (!field.value.trim()) {
                errors[fieldId] = 'Field ini wajib diisi';
                isValid = false;
                errorDiv.textContent = errors[fieldId];
                errorDiv.classList.remove('hidden');
                field.classList.add('border-red-500');
            } else {
                errorDiv.classList.add('hidden');
                field.classList.remove('border-red-500');
            }
        });
        
        // Validate email format
        const emailField = document.getElementById('email');
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailField.value && !emailPattern.test(emailField.value)) {
            const errorDiv = emailField.parentNode.querySelector('.error-message');
            errorDiv.textContent = 'Format email tidak valid';
            errorDiv.classList.remove('hidden');
            emailField.classList.add('border-red-500');
            isValid = false;
        }
        
        // Validate phone format (Indonesian)
        const phoneField = document.getElementById('phone');
        const phonePattern = /^(\+62|62|0)[0-9]{9,13}$/;
        if (phoneField.value && !phonePattern.test(phoneField.value.replace(/\s/g, ''))) {
            const errorDiv = phoneField.parentNode.querySelector('.error-message');
            errorDiv.textContent = 'Format nomor telepon tidak valid';
            errorDiv.classList.remove('hidden');
            phoneField.classList.add('border-red-500');
            isValid = false;
        }
        
        // Validate categories
        const hasCategory = Array.from(categoryCheckboxes).some(cb => cb.checked);
        const categoryError = document.querySelector('input[name="product_categories[]"]').closest('div').querySelector('.error-message');
        if (!hasCategory) {
            categoryError.textContent = 'Pilih minimal satu kategori produk';
            categoryError.classList.remove('hidden');
            isValid = false;
        } else {
            categoryError.classList.add('hidden');
        }
        
        return isValid;
    }
    
    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('Form submission started');
        
        if (!validateForm()) {
            console.log('Form validation failed');
            return;
        }
        
        // Show loading modal
        loadingModal.classList.remove('hidden');
        loadingModal.classList.add('flex');
        
        // Collect form data
        const formData = new FormData(form);
        const rawData = {};
        
        // Convert FormData to regular object
        for (let [key, value] of formData.entries()) {
            if (key === 'product_categories[]') {
                if (!rawData.product_categories) {
                    rawData.product_categories = [];
                }
                rawData.product_categories.push(value);
            } else {
                rawData[key] = value;
            }
        }
        
        console.log('Raw form data:', rawData);
        
        // Map form fields to database fields
        const data = {
            name: rawData.company_name || '',
            contact_person: rawData.contact_name || '',
            email: rawData.email || '',
            phone: rawData.phone || '',
            address: rawData.address || '',
            city: 'Jakarta', // Add default city since it's not in the form
            tax_id: rawData.tax_id || null,
            payment_terms: rawData.payment_terms === 'cash' ? 0 : parseInt(rawData.payment_terms?.replace('_days', '')) || null,
            lead_time_days: parseInt(rawData.lead_time) || null,
            minimum_order_value: parseFloat(rawData.minimum_order) || null,
            rating: parseFloat(rawData.rating) || 3.0,
            notes: rawData.notes || null,
            is_active: rawData.status === 'active'
        };
        
        // Generate a code if not provided
        if (!data.code) {
            data.code = 'SUP' + Date.now().toString().slice(-5);
        }
        
        console.log('Data to be sent:', data);
        console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.content);
        
        // Make actual web API call
        try {
            const url = '/manufacturing/raw-materials/suppliers';
            console.log('Sending POST request to:', url);
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin', // Include cookies for session authentication
                body: JSON.stringify(data)
            });
            
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            // Check if response is JSON
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                const result = await response.json();
                console.log('Response data:', result);
                
                // Hide loading modal
                loadingModal.classList.add('hidden');
                loadingModal.classList.remove('flex');
                
                if (response.ok && result.success) {
                    // Clear draft from localStorage
                    localStorage.removeItem('supplierDraft');
                    
                    // Show success message
                    alert(result.message || 'Pemasok berhasil ditambahkan!');
                    
                    // Redirect to suppliers list
                    window.location.href = '/manufacturing/raw-materials/suppliers';
                } else {
                    // Show error message
                    console.error('Server returned error:', result);
                    if (result.errors) {
                        // Display validation errors
                        let errorMessage = 'Terjadi kesalahan validasi:\n';
                        for (let field in result.errors) {
                            errorMessage += '- ' + field + ': ' + result.errors[field].join(', ') + '\n';
                        }
                        alert(errorMessage);
                    } else {
                        alert(result.message || 'Gagal menambahkan pemasok. Silakan coba lagi.');
                    }
                }
            } else {
                // Response is not JSON, might be HTML error page
                const text = await response.text();
                console.error('Non-JSON response:', text);
                
                // Hide loading modal
                loadingModal.classList.add('hidden');
                loadingModal.classList.remove('flex');
                
                if (response.status === 419) {
                    alert('Sesi Anda telah berakhir. Silakan muat ulang halaman dan coba lagi.');
                } else if (response.status === 404) {
                    alert('Endpoint tidak ditemukan. Silakan hubungi administrator.');
                } else if (response.status === 500) {
                    alert('Terjadi kesalahan server. Silakan coba lagi nanti.');
                } else {
                    alert('Terjadi kesalahan. Status: ' + response.status);
                }
            }
        } catch (error) {
            // Hide loading modal
            loadingModal.classList.add('hidden');
            loadingModal.classList.remove('flex');
            
            console.error('Error submitting form:', error);
            alert('Terjadi kesalahan: ' + error.message);
        }
    });
    
    // Clear form
    document.getElementById('clearForm').addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin menghapus semua data yang telah diisi?')) {
            form.reset();
            
            // Reset star rating to 3
            ratingInput.value = 3;
            ratingText.textContent = '(3.0/5)';
            starButtons.forEach((star, index) => {
                if (index < 3) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
            
            // Clear error messages
            document.querySelectorAll('.error-message').forEach(error => {
                error.classList.add('hidden');
            });
            
            document.querySelectorAll('.border-red-500').forEach(field => {
                field.classList.remove('border-red-500');
            });
            
            updateProgress();
        }
    });
    
    // Save draft functionality
    document.getElementById('saveDraft').addEventListener('click', function() {
        const formData = new FormData(form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            if (key === 'product_categories[]') {
                if (!data.product_categories) {
                    data.product_categories = [];
                }
                data.product_categories.push(value);
            } else {
                data[key] = value;
            }
        }
        
        // Save to localStorage
        localStorage.setItem('supplierDraft', JSON.stringify(data));
        alert('Draft berhasil disimpan!');
    });
    
    // Load draft on page load
    const savedDraft = localStorage.getItem('supplierDraft');
    if (savedDraft) {
        const draftData = JSON.parse(savedDraft);
        
        // Fill form fields
        Object.keys(draftData).forEach(key => {
            if (key === 'product_categories') {
                draftData[key].forEach(category => {
                    const checkbox = document.querySelector(`input[name="product_categories[]"][value="${category}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            } else {
                const field = document.querySelector(`[name="${key}"]`);
                if (field) {
                    if (field.type === 'radio') {
                        const radioButton = document.querySelector(`input[name="${key}"][value="${draftData[key]}"]`);
                        if (radioButton) radioButton.checked = true;
                    } else {
                        field.value = draftData[key];
                    }
                }
            }
        });
        
        // Update rating stars if saved
        if (draftData.rating) {
            const rating = parseInt(draftData.rating);
            ratingInput.value = rating;
            ratingText.textContent = `(${rating}.0/5)`;
            
            starButtons.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
        }
        
        updateProgress();
        
        // Ask if user wants to continue with draft
        if (confirm('Ditemukan draft yang tersimpan. Apakah Anda ingin melanjutkan mengisi form dengan data draft?')) {
            // Draft is already loaded
        } else {
            // Clear the form and remove draft
            form.reset();
            localStorage.removeItem('supplierDraft');
            updateProgress();
        }
    }
    
    // Initial progress calculation
    updateProgress();
});
</script>
@endsection