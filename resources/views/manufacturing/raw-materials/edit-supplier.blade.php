@extends('layouts.dashboard')

@section('title', 'Edit Pemasok')
@section('page-title', 'Edit Pemasok')

@section('content')
<div class="space-y-6">
    @php
        // Get supplier data
        $supplier = \App\Models\Supplier::find($id);
        if (!$supplier) {
            abort(404, 'Supplier not found');
        }
    @endphp

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-2">
                <a href="{{ route('manufacturing.raw-materials.suppliers.show', $supplier->id) }}" 
                   class="text-muted hover:text-foreground transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <nav class="text-sm text-muted">
                    <a href="{{ route('manufacturing.raw-materials.suppliers') }}" class="hover:text-foreground">Pemasok</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('manufacturing.raw-materials.suppliers.show', $supplier->id) }}" class="hover:text-foreground">{{ $supplier->name }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-foreground font-medium">Edit</span>
                </nav>
            </div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Edit Pemasok</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Perbarui informasi pemasok {{ $supplier->name }}</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.raw-materials.suppliers.show', $supplier->id) }}"
               class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Batal
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

    <!-- Edit Form -->
    <form id="supplier-form" action="{{ route('manufacturing.raw-materials.suppliers.update', $supplier->id) }}" method="POST" class="space-y-6" x-data="supplierEditForm">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="card">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-foreground mb-4">Informasi Dasar</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="code" class="block text-sm font-medium text-foreground mb-2">
                                    Kode Pemasok <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code', $supplier->code) }}"
                                       class="input w-full @error('code') border-red-500 @enderror" 
                                       placeholder="SUP-001"
                                       required>
                                @error('code')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="name" class="block text-sm font-medium text-foreground mb-2">
                                    Nama Pemasok <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $supplier->name) }}"
                                       class="input w-full @error('name') border-red-500 @enderror" 
                                       placeholder="PT. Supplier ABC"
                                       required>
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="contact_person" class="block text-sm font-medium text-foreground mb-2">
                                    Kontak Person
                                </label>
                                <input type="text" 
                                       id="contact_person" 
                                       name="contact_person" 
                                       value="{{ old('contact_person', $supplier->contact_person) }}"
                                       class="input w-full @error('contact_person') border-red-500 @enderror" 
                                       placeholder="John Doe">
                                @error('contact_person')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="is_active" class="block text-sm font-medium text-foreground mb-2">
                                    Status
                                </label>
                                <select id="is_active" 
                                        name="is_active" 
                                        class="input w-full @error('is_active') border-red-500 @enderror">
                                    <option value="1" {{ old('is_active', $supplier->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('is_active', $supplier->is_active) == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                                @error('is_active')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-foreground mb-4">Informasi Kontak</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-foreground mb-2">
                                    Nomor Telepon
                                </label>
                                <input type="tel" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $supplier->phone) }}"
                                       class="input w-full @error('phone') border-red-500 @enderror" 
                                       placeholder="081234567890">
                                @error('phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-foreground mb-2">
                                    Email
                                </label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $supplier->email) }}"
                                       class="input w-full @error('email') border-red-500 @enderror" 
                                       placeholder="supplier@example.com">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-foreground mb-2">
                                    Alamat
                                </label>
                                <textarea id="address" 
                                          name="address" 
                                          rows="3"
                                          class="input w-full @error('address') border-red-500 @enderror" 
                                          placeholder="Jl. Contoh No. 123">{{ old('address', $supplier->address) }}</textarea>
                                @error('address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-medium text-foreground mb-2">
                                    Kota
                                </label>
                                <input type="text" 
                                       id="city" 
                                       name="city" 
                                       value="{{ old('city', $supplier->city) }}"
                                       class="input w-full @error('city') border-red-500 @enderror" 
                                       placeholder="Jakarta">
                                @error('city')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-foreground mb-2">
                                    Kode Pos
                                </label>
                                <input type="text" 
                                       id="postal_code" 
                                       name="postal_code" 
                                       value="{{ old('postal_code', $supplier->postal_code) }}"
                                       class="input w-full @error('postal_code') border-red-500 @enderror" 
                                       placeholder="12345">
                                @error('postal_code')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Information -->
                <div class="card">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-foreground mb-4">Informasi Bisnis</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="payment_terms" class="block text-sm font-medium text-foreground mb-2">
                                    Syarat Pembayaran
                                </label>
                                <select id="payment_terms" 
                                        name="payment_terms" 
                                        class="input w-full @error('payment_terms') border-red-500 @enderror">
                                    <option value="">Pilih syarat pembayaran</option>
                                    <option value="Cash" {{ old('payment_terms', $supplier->payment_terms) == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Net 7" {{ old('payment_terms', $supplier->payment_terms) == 'Net 7' ? 'selected' : '' }}>Net 7 hari</option>
                                    <option value="Net 15" {{ old('payment_terms', $supplier->payment_terms) == 'Net 15' ? 'selected' : '' }}>Net 15 hari</option>
                                    <option value="Net 30" {{ old('payment_terms', $supplier->payment_terms) == 'Net 30' ? 'selected' : '' }}>Net 30 hari</option>
                                    <option value="Net 45" {{ old('payment_terms', $supplier->payment_terms) == 'Net 45' ? 'selected' : '' }}>Net 45 hari</option>
                                    <option value="Net 60" {{ old('payment_terms', $supplier->payment_terms) == 'Net 60' ? 'selected' : '' }}>Net 60 hari</option>
                                </select>
                                @error('payment_terms')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="lead_time_days" class="block text-sm font-medium text-foreground mb-2">
                                    Lead Time (Hari)
                                </label>
                                <input type="number" 
                                       id="lead_time_days" 
                                       name="lead_time_days" 
                                       value="{{ old('lead_time_days', $supplier->lead_time_days) }}"
                                       class="input w-full @error('lead_time_days') border-red-500 @enderror" 
                                       placeholder="7"
                                       min="0">
                                @error('lead_time_days')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="rating" class="block text-sm font-medium text-foreground mb-2">
                                    Rating (1-5)
                                </label>
                                <select id="rating" 
                                        name="rating" 
                                        class="input w-full @error('rating') border-red-500 @enderror">
                                    <option value="">Pilih rating</option>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('rating', $supplier->rating) == $i ? 'selected' : '' }}>
                                            {{ $i }} Bintang
                                        </option>
                                    @endfor
                                </select>
                                @error('rating')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-foreground mb-2">
                                    Catatan
                                </label>
                                <textarea id="notes" 
                                          name="notes" 
                                          rows="3"
                                          class="input w-full @error('notes') border-red-500 @enderror" 
                                          placeholder="Catatan tambahan tentang pemasok...">{{ old('notes', $supplier->notes) }}</textarea>
                                @error('notes')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Preview & Actions -->
            <div class="space-y-6">
                <!-- Preview Card -->
                <div class="card sticky top-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-foreground mb-4">Preview</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-muted">Kode:</span>
                                <span class="font-medium text-foreground ml-2" x-text="$el.closest('form').code.value || '{{ $supplier->code }}'">{{ $supplier->code }}</span>
                            </div>
                            <div>
                                <span class="text-muted">Nama:</span>
                                <span class="font-medium text-foreground ml-2" x-text="$el.closest('form').name.value || '{{ $supplier->name }}'">{{ $supplier->name }}</span>
                            </div>
                            <div>
                                <span class="text-muted">Kontak:</span>
                                <span class="font-medium text-foreground ml-2" x-text="$el.closest('form').contact_person.value || '{{ $supplier->contact_person }}'">{{ $supplier->contact_person }}</span>
                            </div>
                            <div>
                                <span class="text-muted">Telepon:</span>
                                <span class="font-medium text-foreground ml-2" x-text="$el.closest('form').phone.value || '{{ $supplier->phone }}'">{{ $supplier->phone }}</span>
                            </div>
                            <div>
                                <span class="text-muted">Status:</span>
                                <span class="ml-2">
                                    @if($supplier->is_active)
                                        <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400 rounded-full">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400 rounded-full">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-foreground mb-4">Aksi</h3>
                        <div class="space-y-3">
                            <button type="submit" class="w-full btn btn-primary touch-manipulation">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('manufacturing.raw-materials.suppliers.show', $supplier->id) }}" 
                               class="w-full btn btn-outline touch-manipulation">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Mobile Save Button (Fixed Bottom) -->
    <div class="lg:hidden fixed bottom-6 right-6 z-50">
        <button type="submit" form="supplier-form" 
                class="btn btn-primary rounded-full w-14 h-14 flex items-center justify-center shadow-lg touch-manipulation">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </button>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('supplierEditForm', () => ({
        init() {
            // Add form ID for mobile submit button
            this.$el.setAttribute('id', 'supplier-form');
        }
    }));
});
</script>
@endsection