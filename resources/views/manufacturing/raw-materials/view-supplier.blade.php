@extends('layouts.dashboard')

@section('title', 'Detail Pemasok')
@section('page-title', 'Detail Pemasok')

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
                <a href="{{ route('manufacturing.raw-materials.suppliers') }}" 
                   class="text-muted hover:text-foreground transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <nav class="text-sm text-muted">
                    <span>Pemasok</span>
                    <span class="mx-2">/</span>
                    <span class="text-foreground font-medium">{{ $supplier->name }}</span>
                </nav>
            </div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">{{ $supplier->name }}</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">{{ $supplier->code }} • {{ $supplier->contact_person }}</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.raw-materials.suppliers.edit', $supplier->id) }}"
               class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Pemasok
            </a>
        </div>
    </div>

    <!-- Status Alert -->
    @if(!$supplier->is_active)
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <span class="text-red-800 dark:text-red-200 font-medium">Pemasok ini sedang tidak aktif</span>
            </div>
        </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information Card -->
            <div class="card">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-foreground mb-4">Informasi Dasar</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-muted mb-1">Kode Pemasok</label>
                            <p class="text-foreground font-medium">{{ $supplier->code }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted mb-1">Nama Pemasok</label>
                            <p class="text-foreground font-medium">{{ $supplier->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted mb-1">Kontak Person</label>
                            <p class="text-foreground font-medium">{{ $supplier->contact_person ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted mb-1">Status</label>
                            @if($supplier->is_active)
                                <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400 rounded-full">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400 rounded-full">
                                    Tidak Aktif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Card -->
            <div class="card">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-foreground mb-4">Informasi Kontak</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-muted mb-1">Nomor Telepon</label>
                            <p class="text-foreground font-medium">{{ $supplier->phone ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted mb-1">Email</label>
                            <p class="text-foreground font-medium">{{ $supplier->email ?: '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-muted mb-1">Alamat</label>
                            <p class="text-foreground font-medium">{{ $supplier->address ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted mb-1">Kota</label>
                            <p class="text-foreground font-medium">{{ $supplier->city ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted mb-1">Kode Pos</label>
                            <p class="text-foreground font-medium">{{ $supplier->postal_code ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Information Card -->
            <div class="card">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-foreground mb-4">Informasi Bisnis</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-muted mb-1">Syarat Pembayaran</label>
                            <p class="text-foreground font-medium">{{ $supplier->payment_terms ?: '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted mb-1">Lead Time (Hari)</label>
                            <p class="text-foreground font-medium">{{ $supplier->lead_time_days ?: '-' }} hari</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-muted mb-1">Catatan</label>
                            <p class="text-foreground font-medium">{{ $supplier->notes ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Stats & Actions -->
        <div class="space-y-6">
            <!-- Rating Card -->
            <div class="card">
                <div class="p-6 text-center">
                    <h3 class="text-lg font-semibold text-foreground mb-2">Rating Pemasok</h3>
                    <div class="flex items-center justify-center mb-2">
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-6 h-6 {{ $i <= $supplier->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-foreground">{{ $supplier->rating }}/5</p>
                    <p class="text-sm text-muted">Rating Kualitas</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Statistik</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-muted">Total Pesanan</span>
                            <span class="font-semibold text-foreground">0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-muted">Total Pembelian</span>
                            <span class="font-semibold text-foreground">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-muted">Pesanan Tertunda</span>
                            <span class="font-semibold text-foreground">0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-muted">Bergabung Sejak</span>
                            <span class="font-semibold text-foreground">{{ $supplier->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Aksi Cepat</h3>
                    <div class="space-y-3">
                        <a href="{{ route('manufacturing.raw-materials.suppliers.edit', $supplier->id) }}" 
                           class="w-full btn btn-outline touch-manipulation">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Pemasok
                        </a>
                        <a href="{{ route('manufacturing.raw-materials.purchasing.create') }}" 
                           class="w-full btn btn-outline touch-manipulation">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Buat Pesanan
                        </a>
                        <button class="w-full btn btn-outline text-red-600 hover:bg-red-50 hover:border-red-300 touch-manipulation">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus Pemasok
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Back Button (Fixed Bottom) -->
    <div class="lg:hidden fixed bottom-6 right-6 z-50">
        <a href="{{ route('manufacturing.raw-materials.suppliers') }}" 
           class="btn btn-primary rounded-full w-14 h-14 flex items-center justify-center shadow-lg touch-manipulation">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
    </div>
</div>
@endsection