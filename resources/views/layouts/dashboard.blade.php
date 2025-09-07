<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ theme: $store.theme.current }" :class="{ 'dark': $store.theme.isDark() }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BRO Manajemen') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Additional Head Content -->
    @stack('head')
</head>
<body class="bg-background text-foreground antialiased">
    <!-- Loading Overlay -->
    <div x-show="$store.loading.isLoading" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
         style="display: none;">
        <div class="bg-surface rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary"></div>
                <span x-text="$store.loading.message" class="text-foreground font-medium"></span>
            </div>
        </div>
    </div>

    <!-- Notification Container - Temporarily Disabled -->
    {{-- <div class="fixed top-4 right-4 z-40 space-y-2" x-data>
        <template x-for="notification in $store.notifications.items" :key="notification.id">
            <div x-show="true"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-x-full opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="translate-x-full opacity-0"
                 class="max-w-sm w-full bg-surface border border-border rounded-lg shadow-lg overflow-hidden">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <!-- Success Icon -->
                            <svg x-show="notification.type === 'success'" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <!-- Error Icon -->
                            <svg x-show="notification.type === 'error'" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <!-- Warning Icon -->
                            <svg x-show="notification.type === 'warning'" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <!-- Info Icon -->
                            <svg x-show="notification.type === 'info'" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p x-text="notification.title" class="text-sm font-medium text-foreground"></p>
                            <p x-text="notification.message" class="mt-1 text-sm text-muted"></p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="$store.notifications.remove(notification.id)"
                                    class="bg-surface rounded-md inline-flex text-muted hover:text-foreground focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div> --}}

    <!-- Financial Notifications - Temporarily Disabled -->
    {{-- @include('components.financial-notifications') --}}

    <!-- Confirmation Dialogs -->
    @include('components.confirmation-dialogs')

    <!-- Accessibility Features - Disabled -->
    {{-- @include('components.accessibility-features') --}}

    <!-- Modal Container -->
    <div x-show="$store.modal.isOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="$store.modal.close()"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div x-show="$store.modal.isOpen"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-surface rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div x-html="$store.modal.component"></div>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="flex h-screen bg-background">
        <!-- Sidebar -->
        <div x-show="$store.sidebar.isOpen || !$store.sidebar.isMobile"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             :class="$store.sidebar.isMobile ? 'fixed inset-y-0 left-0 z-50' : 'relative'"
             class="w-64 bg-surface border-r border-border">
            
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-border">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-foreground">BRO Manajemen</span>
                </div>
                
                <!-- Close button for mobile -->
                <button x-show="$store.sidebar.isMobile"
                        @click="$store.sidebar.close()"
                        class="p-1 rounded-md text-muted hover:text-foreground hover:bg-border">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
                <!-- Dasbor -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary text-primary-foreground' : 'text-foreground hover:bg-border' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />
                    </svg>
                    Dasbor
                </a>

                <!-- Bahan Baku -->
                <div x-data="{ open: {{ request()->routeIs('manufacturing.raw-materials.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-foreground rounded-lg hover:bg-border transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Bahan Baku
                        </div>
                        <svg :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="ml-6 mt-2 space-y-1">
                        <a href="{{ route('manufacturing.raw-materials.index') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('manufacturing.raw-materials.index') ? 'text-primary bg-primary/10' : '' }}">
                            Semua Bahan Baku
                        </a>
                        <a href="{{ route('manufacturing.raw-materials.create') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('manufacturing.raw-materials.create') ? 'text-primary bg-primary/10' : '' }}">
                            Tambah Bahan Baku
                        </a>
                        <a href="{{ route('manufacturing.raw-materials.purchasing') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('manufacturing.raw-materials.purchasing') ? 'text-primary bg-primary/10' : '' }}">
                            Pembelian Bahan
                        </a>
                        <a href="{{ route('manufacturing.raw-materials.suppliers') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('manufacturing.raw-materials.suppliers') ? 'text-primary bg-primary/10' : '' }}">
                            Pemasok
                        </a>
                    </div>
                </div>

                <!-- Resep Produk -->
                <div x-data="{ open: {{ request()->routeIs('bom.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-foreground rounded-lg hover:bg-border transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Resep Produk
                        </div>
                        <svg :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="ml-6 mt-2 space-y-1">
                        <a href="{{ route('manufacturing.recipes.index') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('manufacturing.recipes.index') ? 'text-primary bg-primary/10' : '' }}">
                            Semua Resep
                        </a>
                        <a href="{{ route('manufacturing.recipes.create') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('manufacturing.recipes.create') ? 'text-primary bg-primary/10' : '' }}">
                            Buat Resep Baru
                        </a>
                        <a href="{{ route('manufacturing.recipes.cost-calculation') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('manufacturing.recipes.cost-calculation') ? 'text-primary bg-primary/10' : '' }}">
                            Kalkulasi Biaya
                        </a>
                    </div>
                </div>

                <!-- Produksi -->
                <div x-data="{ open: {{ request()->routeIs('production.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-foreground rounded-lg hover:bg-border transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            </svg>
                            Produksi
                        </div>
                        <svg :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="ml-6 mt-2 space-y-1">
                        <a href="{{ route('manufacturing.production.orders') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('manufacturing.production.orders') ? 'text-primary bg-primary/10' : '' }}">
                            Order Produksi
                        </a>
                        <a href="{{ route('manufacturing.production.orders.create') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('manufacturing.production.orders.create') ? 'text-primary bg-primary/10' : '' }}">
                            Buat Produksi Baru
                        </a>
                        <a href="{{ route('manufacturing.production.history') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('manufacturing.production.history') ? 'text-primary bg-primary/10' : '' }}">
                            Riwayat Produksi
                        </a>
                        <!-- <a href="{{ route('manufacturing.production.quality-control') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('manufacturing.production.quality-control') ? 'text-primary bg-primary/10' : '' }}">
                            Quality Control
                        </a> -->
                    </div>
                </div>

                <!-- Produk Jadi -->
                <div x-data="{ open: {{ request()->routeIs('finished-products.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-foreground rounded-lg hover:bg-border transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Produk Jadi
                        </div>
                        <svg :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="ml-6 mt-2 space-y-1">
                        <a href="{{ route('manufacturing.finished-products.index') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('manufacturing.finished-products.index') ? 'text-primary bg-primary/10' : '' }}">
                            Stok Produk Jadi
                        </a>
                        <a href="{{ route('manufacturing.finished-products.catalog') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('manufacturing.finished-products.catalog') ? 'text-primary bg-primary/10' : '' }}">
                            Katalog Produk
                        </a>
                        <a href="{{ route('manufacturing.finished-products.pricing') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('manufacturing.finished-products.pricing') ? 'text-primary bg-primary/10' : '' }}">
                            Harga & Margin
                        </a>
                    </div>
                </div>

                <!-- Penjualan & Pesanan -->
                <div x-data="{ open: {{ request()->routeIs('orders.*') || request()->routeIs('sales.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-foreground rounded-lg hover:bg-border transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Penjualan & Pesanan
                        </div>
                        <svg :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="ml-6 mt-2 space-y-1">
                        <a href="{{ route('orders.index') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('orders.index') ? 'text-primary bg-primary/10' : '' }}">
                            Semua Pesanan
                        </a>
                        <a href="{{ route('orders.create') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('orders.create') ? 'text-primary bg-primary/10' : '' }}">
                            Pesanan Baru
                        </a>
                        <a href="{{ route('sales.reports') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('sales.reports') ? 'text-primary bg-primary/10' : '' }}">
                            Laporan Penjualan
                        </a>
                    </div>
                </div>

                <!-- Pelanggan -->
                <a href="{{ route('customers.index') }}"
                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('customers.*') ? 'bg-primary text-primary-foreground' : 'text-foreground hover:bg-border' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    Pelanggan
                </a>

                <!-- Financial -->
                <!-- <div x-data="{ open: {{ request()->routeIs('financial.*') || request()->routeIs('reports.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-foreground rounded-lg hover:bg-border transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            Keuangan
                        </div>
                        <svg :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="ml-6 mt-2 space-y-1">
                        <a href="{{ route('financial.journal.index') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('financial.journal.index') ? 'text-primary bg-primary/10' : '' }}">
                            Jurnal Entri
                        </a>
                        <a href="{{ route('financial.cashflow.index') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('financial.cashflow.index') ? 'text-primary bg-primary/10' : '' }}">
                            Arus Kas
                        </a>
                        <a href="{{ route('financial.reports.index') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('financial.reports.index') ? 'text-primary bg-primary/10' : '' }}">
                            Laporan Keuangan
                        </a>
                        <a href="{{ route('expenses.index') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('expenses.*') ? 'text-primary bg-primary/10' : '' }}">
                            Pengeluaran
                        </a>
                        <a href="{{ route('invoices.index') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('invoices.*') ? 'text-primary bg-primary/10' : '' }}">
                            Invoice
                        </a>
                        <a href="{{ route('payments.index') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('payments.*') ? 'text-primary bg-primary/10' : '' }}">
                            Pembayaran
                        </a>
                        <a href="{{ route('receivables.index') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('receivables.*') ? 'text-primary bg-primary/10' : '' }}">
                            Piutang Dagang
                        </a>
                        <a href="{{ route('payables.index') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('payables.*') ? 'text-primary bg-primary/10' : '' }}">
                            Hutang Dagang
                        </a>
                        <a href="{{ route('taxes.index') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('taxes.*') ? 'text-primary bg-primary/10' : '' }}">
                            Manajemen Pajak
                        </a>
                        <a href="{{ route('audit.index') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors {{ request()->routeIs('audit.*') ? 'text-primary bg-primary/10' : '' }}">
                            Audit Trail
                        </a>
                    </div>
                </div> -->

                <!-- Settings -->
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-foreground rounded-lg hover:bg-border transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Pengaturan
                        </div>
                        <svg :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div x-show="open" x-transition class="ml-6 mt-2 space-y-1">
                        <a href="{{ route('users.index') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors">
                            Manajemen Pengguna
                        </a>
                        <!-- <a href="{{ route('settings.general') }}"
                           class="block px-3 py-2 text-sm text-muted hover:text-foreground hover:bg-border rounded-lg transition-colors">
                            Pengaturan Umum
                        </a> -->
                    </div>
                </div>
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-border">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium text-primary-foreground">D</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-foreground truncate">Demo User</p>
                        <p class="text-xs text-muted truncate">demo@mdagang.com</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Sidebar Overlay -->
        <div x-show="$store.sidebar.isOpen && $store.sidebar.isMobile"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="$store.sidebar.close()"
             class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
             style="display: none;"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-surface border-b border-border">
                <div class="flex items-center justify-between h-16 px-4">
                    <!-- Left Side -->
                    <div class="flex items-center space-x-4">
                        <!-- Mobile Menu Button -->
                        <button @click="$store.sidebar.toggle()"
                                class="p-2 rounded-md text-muted hover:text-foreground hover:bg-border lg:hidden">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Page Title -->
                        <h1 class="text-xl font-semibold text-foreground">
                            @yield('page-title', 'Dashboard')
                        </h1>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center space-x-4">
                        <!-- Search -->
                        <div class="relative hidden md:block">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" 
                                   placeholder="Cari..."
                                   class="input pl-10 pr-4 py-2 w-64">
                        </div>

                        <!-- Notifications - Temporarily Disabled -->
                        {{-- <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    class="p-2 rounded-md text-muted hover:text-foreground hover:bg-border relative">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <!-- Notification Badge -->
                                <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-surface"></span>
                            </button>

                            <!-- Notifications Dropdown -->
                            <div x-show="open"
                                 x-transition
                                 @click.away="open = false"
                                 class="absolute right-0 mt-2 w-80 bg-surface rounded-lg shadow-lg border border-border z-50"
                                 style="display: none;">
                                <div class="p-4 border-b border-border">
                                    <h3 class="text-lg font-medium text-foreground">Notifikasi</h3>
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    <!-- Sample Notifications -->
                                    <div class="p-4 border-b border-border hover:bg-border/50">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-foreground">Pesanan baru diterima</p>
                                                <p class="text-xs text-muted">Pesanan #12345 dari John Doe</p>
                                                <p class="text-xs text-muted">2 menit yang lalu</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-4 border-b border-border hover:bg-border/50">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-foreground">Peringatan stok rendah</p>
                                                <p class="text-xs text-muted">Produk "Nasi Goreng" stoknya menipis</p>
                                                <p class="text-xs text-muted">1 jam yang lalu</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4 border-t border-border">
                                    <a href="#" class="text-sm text-primary hover:text-primary/80">Lihat semua notifikasi</a>
                                </div>
                            </div>
                        </div> --}}


                        <!-- Theme Switcher -->
                        <button @click="$store.theme.toggle()"
                                class="p-2 rounded-md text-muted hover:text-foreground hover:bg-border">
                            <!-- Sun Icon (Light Mode) -->
                            <svg x-show="!$store.theme.isDark()" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <!-- Moon Icon (Dark Mode) -->
                            <svg x-show="$store.theme.isDark()" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        <!-- User Menu -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" 
                                    class="flex items-center space-x-2 p-2 rounded-md text-muted hover:text-foreground hover:bg-border">
                                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-primary-foreground">
                                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                                    </span>
                                </div>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- User Dropdown -->
                            <div x-show="open" 
                                 x-transition
                                 @click.away="open = false"
                                 class="absolute right-0 mt-2 w-48 bg-surface rounded-lg shadow-lg border border-border z-50"
                                 style="display: none;">
                                <div class="p-4 border-b border-border">
                                    <p class="text-sm font-medium text-foreground">{{ auth()->user()->name ?? 'User' }}</p>
                                    <p class="text-xs text-muted">{{ auth()->user()->email ?? 'user@example.com' }}</p>
                                </div>
                                <div class="py-2">
                                    <a href="{{ route('profile.edit') }}" 
                                       class="flex items-center px-4 py-2 text-sm text-foreground hover:bg-border">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Profil
                                    </a>
                                    <a href="{{ route('settings.account') }}" 
                                       class="flex items-center px-4 py-2 text-sm text-foreground hover:bg-border">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Pengaturan
                                    </a>
                                    <div class="border-t border-border my-2"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" 
                                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-border">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-background">
                <div class="p-6">
                    <!-- Breadcrumb -->
                    @hasSection('breadcrumb')
                    <nav class="flex mb-6" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                    @endif

                    <!-- Page Content -->
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>