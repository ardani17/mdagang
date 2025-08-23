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

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Terjadi kesalahan!</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Simple Form -->
    <form method="POST" action="{{ route('manufacturing.raw-materials.suppliers.store') }}" class="space-y-6">
        @csrf
        
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
                    <label for="name" class="block text-sm font-medium text-foreground mb-2">
                        Nama Perusahaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                           value="{{ old('name') }}"
                           class="form-input w-full @error('name') border-red-500 @enderror" 
                           placeholder="Contoh: CV. Supplier Terpercaya">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-foreground mb-2">
                        Kode Supplier
                    </label>
                    <input type="text" id="code" name="code"
                           value="{{ old('code', 'SUP' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)) }}"
                           class="form-input w-full @error('code') border-red-500 @enderror" 
                           placeholder="SUP0001">
                    @error('code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tax_id" class="block text-sm font-medium text-foreground mb-2">
                        NPWP
                    </label>
                    <input type="text" id="tax_id" name="tax_id"
                           value="{{ old('tax_id') }}"
                           class="form-input w-full @error('tax_id') border-red-500 @enderror" 
                           placeholder="00.000.000.0-000.000">
                    @error('tax_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-foreground mb-2">
                        Alamat Lengkap <span class="text-red-500">*</span>
                    </label>
                    <textarea id="address" name="address" rows="3" required
                              class="form-textarea w-full @error('address') border-red-500 @enderror" 
                              placeholder="Masukkan alamat lengkap">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-foreground mb-2">
                        Kota <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="city" name="city" required
                           value="{{ old('city', 'Jakarta') }}"
                           class="form-input w-full @error('city') border-red-500 @enderror" 
                           placeholder="Jakarta">
                    @error('city')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
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
                    <label for="contact_person" class="block text-sm font-medium text-foreground mb-2">
                        Nama Kontak Utama <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="contact_person" name="contact_person" required
                           value="{{ old('contact_person') }}"
                           class="form-input w-full @error('contact_person') border-red-500 @enderror" 
                           placeholder="Nama lengkap kontak person">
                    @error('contact_person')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-foreground mb-2">
                        Nomor Telepon <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" id="phone" name="phone" required
                           value="{{ old('phone') }}"
                           class="form-input w-full @error('phone') border-red-500 @enderror" 
                           placeholder="+62 812 3456 7890">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-foreground mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" required
                           value="{{ old('email') }}"
                           class="form-input w-full @error('email') border-red-500 @enderror" 
                           placeholder="email@perusahaan.com">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
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
                    <p class="text-sm text-muted">Informasi syarat bisnis</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="payment_terms" class="block text-sm font-medium text-foreground mb-2">
                        Syarat Pembayaran (hari)
                    </label>
                    <input type="number" id="payment_terms" name="payment_terms"
                           value="{{ old('payment_terms', 30) }}"
                           class="form-input w-full @error('payment_terms') border-red-500 @enderror" 
                           placeholder="30" min="0">
                    @error('payment_terms')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="lead_time_days" class="block text-sm font-medium text-foreground mb-2">
                        Lead Time (hari)
                    </label>
                    <input type="number" id="lead_time_days" name="lead_time_days"
                           value="{{ old('lead_time_days', 7) }}"
                           class="form-input w-full @error('lead_time_days') border-red-500 @enderror" 
                           placeholder="7" min="1">
                    @error('lead_time_days')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="minimum_order_value" class="block text-sm font-medium text-foreground mb-2">
                        Minimum Order (Rp)
                    </label>
                    <input type="number" id="minimum_order_value" name="minimum_order_value"
                           value="{{ old('minimum_order_value', 0) }}"
                           class="form-input w-full @error('minimum_order_value') border-red-500 @enderror" 
                           placeholder="0" min="0" step="1000">
                    @error('minimum_order_value')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="rating" class="block text-sm font-medium text-foreground mb-2">
                        Rating Awal
                    </label>
                    <select id="rating" name="rating" class="form-select w-full @error('rating') border-red-500 @enderror">
                        <option value="1" {{ old('rating') == 1 ? 'selected' : '' }}>1 - Sangat Buruk</option>
                        <option value="2" {{ old('rating') == 2 ? 'selected' : '' }}>2 - Buruk</option>
                        <option value="3" {{ old('rating', 3) == 3 ? 'selected' : '' }}>3 - Cukup</option>
                        <option value="4" {{ old('rating') == 4 ? 'selected' : '' }}>4 - Baik</option>
                        <option value="5" {{ old('rating') == 5 ? 'selected' : '' }}>5 - Sangat Baik</option>
                    </select>
                    @error('rating')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="is_active" class="block text-sm font-medium text-foreground mb-2">
                        Status
                    </label>
                    <select id="is_active" name="is_active" class="form-select w-full @error('is_active') border-red-500 @enderror">
                        <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    @error('is_active')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-3">
                    <label for="notes" class="block text-sm font-medium text-foreground mb-2">
                        Catatan
                    </label>
                    <textarea id="notes" name="notes" rows="3"
                              class="form-textarea w-full @error('notes') border-red-500 @enderror" 
                              placeholder="Catatan tambahan tentang pemasok (opsional)">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col sm:flex-row gap-3 justify-end">
            <button type="reset" class="btn btn-outline">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Bersihkan Form
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
@endsection