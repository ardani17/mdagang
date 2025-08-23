@extends('layouts.dashboard')

@section('title', 'Laporan Inventori')
@section('page-title')
<span class="text-base lg:text-2xl">Laporan Inventori</span>
@endsection

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-muted hover:text-foreground">
        <svg class="w-3 h-3 mr-2.5" fill="currentColor" viewBox="0 0 20 20">
            <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
        </svg>
        Dasbor
    </a>
</li>
<li class="inline-flex items-center">
    <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
    <a href="{{ route('inventory.index') }}" class="text-sm font-medium text-muted hover:text-foreground">Inventori</a>
</li>
<li>
    <div class="flex items-center">
        <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Laporan</span>
    </div>
</li>
@endsection

@section('content')
<div class="space-y-4 lg:space-y-6">
    <!-- Mobile Back Button -->
    <div class="sm:hidden mb-4">
        <a href="{{ route('inventory.index') }}"
           class="inline-flex items-center px-4 py-2 bg-background border border-border rounded-lg text-foreground hover:bg-surface transition-colors">
            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="text-sm font-medium">Kembali ke Inventori</span>
        </a>
    </div>

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Laporan Inventori</h2>
            <p class="text-sm text-muted">Analisis dan laporan stok inventori</p>
        </div>
        <div class="flex items-center space-x-3">
            <button class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor Laporan
            </button>
            <button class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H9.5a2 2 0 01-2-2V5a2 2 0 012-2H17"/>
                </svg>
                Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Report Filters -->
    <div class="card p-4 lg:p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Periode</label>
                <select class="input">
                    <option>Bulan Ini</option>
                    <option>3 Bulan Terakhir</option>
                    <option>6 Bulan Terakhir</option>
                    <option>Tahun Ini</option>
                    <option>Custom</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Kategori</label>
                <select class="input">
                    <option>Semua Kategori</option>
                    <option>Bahan Baku</option>
                    <option>Kemasan</option>
                    <option>Produk Jadi</option>
                    <option>Peralatan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Jenis Laporan</label>
                <select class="input">
                    <option>Stok Saat Ini</option>
                    <option>Pergerakan Stok</option>
                    <option>Stok Rendah</option>
                    <option>Nilai Inventori</option>
                    <option>Aging Analysis</option>
                </select>
            </div>
            <div class="flex items-end">
                <button class="btn-primary w-full flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Generate
                </button>
            </div>
        </div>
    </div>

    <!-- Report Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Total Nilai Inventori</p>
                    <p class="text-2xl lg:text-3xl font-bold text-blue-600">Rp 45.2M</p>
                    <p class="text-xs text-green-600 mt-1">+5.2% dari bulan lalu</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Turnover Ratio</p>
                    <p class="text-2xl lg:text-3xl font-bold text-green-600">8.5x</p>
                    <p class="text-xs text-green-600 mt-1">+0.3x dari bulan lalu</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Dead Stock</p>
                    <p class="text-2xl lg:text-3xl font-bold text-red-600">Rp 1.2M</p>
                    <p class="text-xs text-red-600 mt-1">2.7% dari total inventori</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Carrying Cost</p>
                    <p class="text-2xl lg:text-3xl font-bold text-primary">Rp 890K</p>
                    <p class="text-xs text-muted mt-1">Per bulan</p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Analysis Chart -->
    <div class="card p-4 lg:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h3 class="text-lg font-semibold text-foreground">Analisis Pergerakan Stok</h3>
            <div class="flex gap-2 mt-2 sm:mt-0">
                <button class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Stok Masuk</button>
                <button class="px-3 py-1 text-xs font-medium bg-surface text-muted rounded-full">Stok Keluar</button>
                <button class="px-3 py-1 text-xs font-medium bg-surface text-muted rounded-full">Penyesuaian</button>
            </div>
        </div>
        <div class="h-80">
            <canvas id="inventoryChart"></canvas>
        </div>
    </div>

    <!-- Top Products by Value -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <div class="card p-4 lg:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Produk Tertinggi Berdasarkan Nilai</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-surface rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4.5m8-4.5v10l-8 4.5m0-9L4 7m8 4.5v9"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-foreground">Tepung Terigu Premium</p>
                            <p class="text-sm text-muted">250 kg</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-foreground">Rp 3,000,000</p>
                        <p class="text-sm text-green-600">22.5%</p>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-surface rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4.5m8-4.5v10l-8 4.5m0-9L4 7m8 4.5v9"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-foreground">Kemasan Plastik Premium</p>
                            <p class="text-sm text-muted">1,500 pcs</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-foreground">Rp 2,250,000</p>
                        <p class="text-sm text-green-600">16.8%</p>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3 bg-surface rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4.5m8-4.5v10l-8 4.5m0-9L4 7m8 4.5v9"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-foreground">Gula Pasir</p>
                            <p class="text-sm text-muted">45 kg</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-foreground">Rp 675,000</p>
                        <p class="text-sm text-green-600">5.1%</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-4">Produk Stok Rendah</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-foreground">Gula Pasir</p>
                            <p class="text-sm text-red-600">45 kg (Min: 50 kg)</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                        Kritis
                    </span>
                </div>

                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-foreground">Mentega</p>
                            <p class="text-sm text-orange-600">8 kg (Min: 10 kg)</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 rounded-full">
                        Rendah
                    </span>
                </div>

                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-foreground">Vanilla Extract</p>
                            <p class="text-sm text-yellow-600">12 botol (Min: 15 botol)</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                        Peringatan
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inventory Movement Chart
    const ctx = document.getElementById('inventoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
            datasets: [{
                label: 'Stok Masuk',
                data: [120, 150, 180, 140, 200, 160, 190, 170],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }, {
                label: 'Stok Keluar',
                data: [100, 130, 160, 120, 180, 140, 170, 150],
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(156, 163, 175, 0.1)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(156, 163, 175, 0.1)'
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection