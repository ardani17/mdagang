@extends('layouts.dashboard')

@section('title', 'Pengaturan Umum')
@section('page-title')
<span class="text-base lg:text-2xl">Pengaturan Umum</span>
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
<li>
    <div class="flex items-center">
        <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Pengaturan Umum</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="generalSettings()" class="space-y-4 lg:space-y-6 p-4 lg:p-0">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Pengaturan Umum</h2>
            <p class="text-sm text-muted">Kelola pengaturan aplikasi dan konfigurasi sistem</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="resetToDefaults()" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Reset ke Default
            </button>
            
            <button @click="saveSettings()" 
                    :disabled="loading"
                    class="btn-primary flex items-center"
                    :class="loading ? 'opacity-50 cursor-not-allowed' : ''">
                <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="loading ? 'Menyimpan...' : 'Simpan Pengaturan'"></span>
            </button>
        </div>
    </div>

    <!-- Settings Tabs -->
    <div class="card">
        <div class="border-b border-border">
            <nav class="flex space-x-8 px-4 lg:px-6" aria-label="Tabs">
                <button @click="activeTab = 'general'" 
                        :class="activeTab === 'general' ? 'border-primary text-primary' : 'border-transparent text-muted hover:text-foreground hover:border-border'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Umum
                </button>
                <button @click="activeTab = 'company'" 
                        :class="activeTab === 'company' ? 'border-primary text-primary' : 'border-transparent text-muted hover:text-foreground hover:border-border'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Informasi Perusahaan
                </button>
                <button @click="activeTab = 'financial'" 
                        :class="activeTab === 'financial' ? 'border-primary text-primary' : 'border-transparent text-muted hover:text-foreground hover:border-border'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Keuangan
                </button>
                <button @click="activeTab = 'notifications'" 
                        :class="activeTab === 'notifications' ? 'border-primary text-primary' : 'border-transparent text-muted hover:text-foreground hover:border-border'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Notifikasi
                </button>
                <button @click="activeTab = 'system'" 
                        :class="activeTab === 'system' ? 'border-primary text-primary' : 'border-transparent text-muted hover:text-foreground hover:border-border'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Sistem
                </button>
            </nav>
        </div>

        <!-- General Settings Tab -->
        <div x-show="activeTab === 'general'" class="p-4 lg:p-6">
            <form @submit.prevent="saveSettings()" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Application Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-foreground">Pengaturan Aplikasi</h3>
                        
                        <div>
                            <label for="app_name" class="block text-sm font-medium text-foreground mb-2">Nama Aplikasi</label>
                            <input type="text" 
                                   id="app_name"
                                   x-model="settings.app_name"
                                   class="input"
                                   placeholder="MDagang">
                        </div>

                        <div>
                            <label for="app_description" class="block text-sm font-medium text-foreground mb-2">Deskripsi Aplikasi</label>
                            <textarea id="app_description"
                                      x-model="settings.app_description"
                                      rows="3"
                                      class="input"
                                      placeholder="Sistem manajemen dagang terintegrasi"></textarea>
                        </div>

                        <div>
                            <label for="timezone" class="block text-sm font-medium text-foreground mb-2">Zona Waktu</label>
                            <select id="timezone" x-model="settings.timezone" class="input">
                                <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                                <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                            </select>
                        </div>

                        <div>
                            <label for="language" class="block text-sm font-medium text-foreground mb-2">Bahasa Default</label>
                            <select id="language" x-model="settings.language" class="input">
                                <option value="id">Bahasa Indonesia</option>
                                <option value="en">English</option>
                            </select>
                        </div>
                    </div>

                    <!-- Display Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-foreground">Pengaturan Tampilan</h3>
                        
                        <div>
                            <label for="date_format" class="block text-sm font-medium text-foreground mb-2">Format Tanggal</label>
                            <select id="date_format" x-model="settings.date_format" class="input">
                                <option value="d/m/Y">DD/MM/YYYY</option>
                                <option value="m/d/Y">MM/DD/YYYY</option>
                                <option value="Y-m-d">YYYY-MM-DD</option>
                                <option value="d-m-Y">DD-MM-YYYY</option>
                            </select>
                        </div>

                        <div>
                            <label for="time_format" class="block text-sm font-medium text-foreground mb-2">Format Waktu</label>
                            <select id="time_format" x-model="settings.time_format" class="input">
                                <option value="24">24 Jam (HH:MM)</option>
                                <option value="12">12 Jam (HH:MM AM/PM)</option>
                            </select>
                        </div>

                        <div>
                            <label for="items_per_page" class="block text-sm font-medium text-foreground mb-2">Item per Halaman</label>
                            <select id="items_per_page" x-model="settings.items_per_page" class="input">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="settings.dark_mode_enabled"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Aktifkan Mode Gelap</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="settings.sidebar_collapsed"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Sidebar Tertutup Default</span>
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Company Information Tab -->
        <div x-show="activeTab === 'company'" class="p-4 lg:p-6">
            <form @submit.prevent="saveSettings()" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Company Details -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-foreground">Detail Perusahaan</h3>
                        
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-foreground mb-2">Nama Perusahaan</label>
                            <input type="text" 
                                   id="company_name"
                                   x-model="settings.company_name"
                                   class="input"
                                   placeholder="PT. Contoh Perusahaan">
                        </div>

                        <div>
                            <label for="company_address" class="block text-sm font-medium text-foreground mb-2">Alamat Perusahaan</label>
                            <textarea id="company_address"
                                      x-model="settings.company_address"
                                      rows="3"
                                      class="input"
                                      placeholder="Jl. Contoh No. 123, Jakarta"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="company_phone" class="block text-sm font-medium text-foreground mb-2">Telepon</label>
                                <input type="tel" 
                                       id="company_phone"
                                       x-model="settings.company_phone"
                                       class="input"
                                       placeholder="+62 21 1234567">
                            </div>
                            
                            <div>
                                <label for="company_email" class="block text-sm font-medium text-foreground mb-2">Email</label>
                                <input type="email" 
                                       id="company_email"
                                       x-model="settings.company_email"
                                       class="input"
                                       placeholder="info@perusahaan.com">
                            </div>
                        </div>

                        <div>
                            <label for="company_website" class="block text-sm font-medium text-foreground mb-2">Website</label>
                            <input type="url" 
                                   id="company_website"
                                   x-model="settings.company_website"
                                   class="input"
                                   placeholder="https://www.perusahaan.com">
                        </div>
                    </div>

                    <!-- Legal Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-foreground">Informasi Legal</h3>
                        
                        <div>
                            <label for="tax_id" class="block text-sm font-medium text-foreground mb-2">NPWP</label>
                            <input type="text" 
                                   id="tax_id"
                                   x-model="settings.tax_id"
                                   class="input"
                                   placeholder="12.345.678.9-012.345">
                        </div>

                        <div>
                            <label for="business_license" class="block text-sm font-medium text-foreground mb-2">Nomor Izin Usaha</label>
                            <input type="text" 
                                   id="business_license"
                                   x-model="settings.business_license"
                                   class="input"
                                   placeholder="1234567890123">
                        </div>

                        <div>
                            <label for="business_type" class="block text-sm font-medium text-foreground mb-2">Jenis Usaha</label>
                            <select id="business_type" x-model="settings.business_type" class="input">
                                <option value="">Pilih Jenis Usaha</option>
                                <option value="retail">Retail</option>
                                <option value="wholesale">Grosir</option>
                                <option value="manufacturing">Manufaktur</option>
                                <option value="service">Jasa</option>
                                <option value="restaurant">Restoran</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>

                        <!-- Company Logo Upload -->
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-2">Logo Perusahaan</label>
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-surface border border-border rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <button type="button" class="btn-secondary text-sm">
                                        Upload Logo
                                    </button>
                                    <p class="text-xs text-muted mt-1">PNG, JPG hingga 2MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Financial Settings Tab -->
        <div x-show="activeTab === 'financial'" class="p-4 lg:p-6">
            <form @submit.prevent="saveSettings()" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Currency Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-foreground">Pengaturan Mata Uang</h3>
                        
                        <div>
                            <label for="default_currency" class="block text-sm font-medium text-foreground mb-2">Mata Uang Default</label>
                            <select id="default_currency" x-model="settings.default_currency" class="input">
                                <option value="IDR">Rupiah Indonesia (IDR)</option>
                                <option value="USD">US Dollar (USD)</option>
                                <option value="EUR">Euro (EUR)</option>
                                <option value="SGD">Singapore Dollar (SGD)</option>
                            </select>
                        </div>

                        <div>
                            <label for="currency_position" class="block text-sm font-medium text-foreground mb-2">Posisi Simbol Mata Uang</label>
                            <select id="currency_position" x-model="settings.currency_position" class="input">
                                <option value="before">Sebelum Angka (Rp 1.000)</option>
                                <option value="after">Setelah Angka (1.000 Rp)</option>
                            </select>
                        </div>

                        <div>
                            <label for="decimal_places" class="block text-sm font-medium text-foreground mb-2">Jumlah Desimal</label>
                            <select id="decimal_places" x-model="settings.decimal_places" class="input">
                                <option value="0">0 (1000)</option>
                                <option value="2">2 (1000.00)</option>
                            </select>
                        </div>

                        <div>
                            <label for="thousand_separator" class="block text-sm font-medium text-foreground mb-2">Pemisah Ribuan</label>
                            <select id="thousand_separator" x-model="settings.thousand_separator" class="input">
                                <option value=",">Koma (1,000)</option>
                                <option value=".">Titik (1.000)</option>
                                <option value=" ">Spasi (1 000)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tax Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-foreground">Pengaturan Pajak</h3>
                        
                        <div>
                            <label for="default_tax_rate" class="block text-sm font-medium text-foreground mb-2">Tarif Pajak Default (%)</label>
                            <input type="number" 
                                   id="default_tax_rate"
                                   x-model="settings.default_tax_rate"
                                   class="input"
                                   placeholder="11"
                                   step="0.01"
                                   min="0"
                                   max="100">
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="settings.tax_inclusive"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Harga Sudah Termasuk Pajak</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="settings.auto_calculate_tax"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Hitung Pajak Otomatis</span>
                            </label>
                        </div>

                        <div>
                            <label for="fiscal_year_start" class="block text-sm font-medium text-foreground mb-2">Awal Tahun Fiskal</label>
                            <select id="fiscal_year_start" x-model="settings.fiscal_year_start" class="input">
                                <option value="1">Januari</option>
                                <option value="4">April</option>
                                <option value="7">Juli</option>
                                <option value="10">Oktober</option>
                            </select>
                        </div>

                        <!-- Invoice Settings -->
                        <h4 class="text-md font-semibold text-foreground mt-6">Pengaturan Invoice</h4>
                        
                        <div>
                            <label for="invoice_prefix" class="block text-sm font-medium text-foreground mb-2">Prefix Invoice</label>
                            <input type="text" 
                                   id="invoice_prefix"
                                   x-model="settings.invoice_prefix"
                                   class="input"
                                   placeholder="INV">
                        </div>

                        <div>
                            <label for="invoice_number_length" class="block text-sm font-medium text-foreground mb-2">Panjang Nomor Invoice</label>
                            <select id="invoice_number_length" x-model="settings.invoice_number_length" class="input">
                                <option value="4">4 Digit (0001)</option>
                                <option value="5">5 Digit (00001)</option>
                                <option value="6">6 Digit (000001)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Notifications Tab -->
        <div x-show="activeTab === 'notifications'" class="p-4 lg:p-6">
            <form @submit.prevent="saveSettings()" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Email Notifications -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-foreground">Notifikasi Email</h3>
                        
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="settings.email_notifications.low_stock"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Stok Menipis</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="settings.email_notifications.new_order"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Pesanan Baru</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="settings.email_notifications.payment_received"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Pembayaran Diterima</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="settings.email_notifications.budget_exceeded"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Anggaran Terlampaui</span>
                            </label>
                        </div>

                        <div>
                            <label for="notification_email" class="block text-sm font-medium text-foreground mb-2">Email Notifikasi</label>
                            <input type="email" 
                                   id="notification_email"
                                   x-model="settings.notification_email"
                                   class="input"
                                   placeholder="admin@perusahaan.com">
                        </div>
                    </div>

                    <!-- System Notifications -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-foreground">Notifikasi Sistem</h3>
                        
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="settings.system_notifications.browser"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Notifikasi Browser</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="settings.system_notifications.sound"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Suara Notifikasi</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="settings.system_notifications.desktop"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Notifikasi Desktop</span>
                            </label>
                        </div>

                        <div>
                            <label for="notification_frequency" class="block text-sm font-medium text-foreground mb-2">Frekuensi Notifikasi</label>
                            <select id="notification_frequency" x-model="settings.notification_frequency" class="input">
                                <option value="realtime">Real-time</option>
                                <option value="hourly">Setiap Jam</option>
                                <option value="daily">Harian</option>
                                <option value="weekly">Mingguan</option>
                            </select>
                        </div>

                        <!-- Alert Thresholds -->
                        <h4 class="text-md font-semibold text-foreground mt-6">Batas Peringatan</h4>
                        
                        <div>
                            <label for="low_stock_threshold" class="block text-sm font-medium text-foreground mb-2">Batas Stok Minimum</label>
                            <input type="number" 
                                   id="low_stock_threshold"
                                   x-model="settings.low_stock_threshold"
                                   class="input"
                                   placeholder="10"
                                   min="1">
                        </div>

                        <div>
                            <label for="budget_alert_threshold" class="block text-sm font-medium text-foreground mb-2">Batas Peringatan Anggaran (%)</label>
                            <input type="number" 
                                   id="budget_alert_threshold"
                                   x-model="settings.budget_alert_threshold"
                                   class="input"
                                   placeholder="80"
                                   min="1"
                                   max="100">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- System Settings Tab -->
        <div x-show="activeTab === 'system'" class="p-4 lg:p-6">
            <form @submit.prevent="saveSettings()" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Backup Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-foreground">Pengaturan Backup</h3>
                        
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="settings.auto_backup"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Backup Otomatis</span>
                            </label>
                        </div>

                        <div x-show="settings.auto_backup">
                            <label for="backup_frequency" class="block text-sm font-medium text-foreground mb-2">Frekuensi Backup</label>
                            <select id="backup_frequency" x-model="settings.backup_frequency" class="input">
                                <option value="daily">Harian</option>
                                <option value="weekly">Mingguan</option>
                                <option value="monthly">Bulanan</option>
                            </select>
                        </div>

                        <div>
                            <label for="backup_retention" class="block text-sm font-medium text-foreground mb-2">Simpan Backup (hari)</label>
                            <input type="number" 
                                   id="backup_retention"
                                   x-model="settings.backup_retention"
                                   class="input"
                                   placeholder="30"
                                   min="1
<!-- System Settings Tab -->
        <div x-show="activeTab === 'system'" class="p-4 lg:p-6">
            <form @submit.prevent="saveSettings()" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Backup Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-foreground">Pengaturan Backup</h3>
                        
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="settings.auto_backup"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Backup Otomatis</span>
                            </label>
                        </div>

                        <div x-show="settings.auto_backup">
                            <label for="backup_frequency" class="block text-sm font-medium text-foreground mb-2">Frekuensi Backup</label>
                            <select id="backup_frequency" x-model="settings.backup_frequency" class="input">
                                <option value="daily">Harian</option>
                                <option value="weekly">Mingguan</option>
                                <option value="monthly">Bulanan</option>
                            </select>
                        </div>

                        <div>
                            <label for="backup_retention" class="block text-sm font-medium text-foreground mb-2">Simpan Backup (hari)</label>
                            <input type="number" 
                                   id="backup_retention"
                                   x-model="settings.backup_retention"
                                   class="input"
                                   placeholder="30"
                                   min="1">
                        </div>

                        <div class="mt-4">
                            <button type="button" @click="createBackup()" class="btn-secondary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 12l3 3m0 0l3-3m-3 3V9"/>
                                </svg>
                                Buat Backup Sekarang
                            </button>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-foreground">Pengaturan Keamanan</h3>
                        
                        <div>
                            <label for="session_timeout" class="block text-sm font-medium text-foreground mb-2">Timeout Sesi (menit)</label>
                            <input type="number" 
                                   id="session_timeout"
                                   x-model="settings.session_timeout"
                                   class="input"
                                   placeholder="120"
                                   min="5">
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="settings.force_https"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Paksa HTTPS</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="settings.two_factor_auth"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Autentikasi Dua Faktor</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="settings.audit_log"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Log Audit</span>
                            </label>
                        </div>

                        <!-- Maintenance Mode -->
                        <h4 class="text-md font-semibold text-foreground mt-6">Mode Maintenance</h4>
                        
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="settings.maintenance_mode"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Aktifkan Mode Maintenance</span>
                            </label>
                        </div>

                        <div x-show="settings.maintenance_mode">
                            <label for="maintenance_message" class="block text-sm font-medium text-foreground mb-2">Pesan Maintenance</label>
                            <textarea id="maintenance_message"
                                      x-model="settings.maintenance_message"
                                      rows="3"
                                      class="input"
                                      placeholder="Sistem sedang dalam pemeliharaan. Silakan coba lagi nanti."></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function generalSettings() {
    return {
        loading: false,
        activeTab: 'general',
        settings: {
            // General Settings
            app_name: 'MDagang',
            app_description: 'Sistem manajemen dagang terintegrasi',
            timezone: 'Asia/Jakarta',
            language: 'id',
            date_format: 'd/m/Y',
            time_format: '24',
            items_per_page: '25',
            dark_mode_enabled: false,
            sidebar_collapsed: false,

            // Company Information
            company_name: '',
            company_address: '',
            company_phone: '',
            company_email: '',
            company_website: '',
            tax_id: '',
            business_license: '',
            business_type: '',

            // Financial Settings
            default_currency: 'IDR',
            currency_position: 'before',
            decimal_places: '0',
            thousand_separator: '.',
            default_tax_rate: '11',
            tax_inclusive: false,
            auto_calculate_tax: true,
            fiscal_year_start: '1',
            invoice_prefix: 'INV',
            invoice_number_length: '5',

            // Notification Settings
            email_notifications: {
                low_stock: true,
                new_order: true,
                payment_received: true,
                budget_exceeded: true
            },
            system_notifications: {
                browser: true,
                sound: true,
                desktop: false
            },
            notification_email: '',
            notification_frequency: 'realtime',
            low_stock_threshold: '10',
            budget_alert_threshold: '80',

            // System Settings
            auto_backup: true,
            backup_frequency: 'daily',
            backup_retention: '30',
            session_timeout: '120',
            force_https: false,
            two_factor_auth: false,
            audit_log: true,
            maintenance_mode: false,
            maintenance_message: 'Sistem sedang dalam pemeliharaan. Silakan coba lagi nanti.'
        },

        init() {
            this.loadSettings();
        },

        async loadSettings() {
            try {
                const response = await fetch('/api/settings/general');
                if (response.ok) {
                    const data = await response.json();
                    this.settings = { ...this.settings, ...data };
                }
            } catch (error) {
                console.error('Error loading settings:', error);
            }
        },

        async saveSettings() {
            this.loading = true;
            
            try {
                const response = await fetch('/api/settings/general', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.settings)
                });

                const data = await response.json();

                if (response.ok) {
                    this.showNotification('Pengaturan berhasil disimpan!', 'success');
                } else {
                    this.showNotification(data.message || 'Gagal menyimpan pengaturan', 'error');
                }
            } catch (error) {
                console.error('Error saving settings:', error);
                this.showNotification('Kesalahan jaringan. Silakan coba lagi.', 'error');
            } finally {
                this.loading = false;
            }
        },

        async resetToDefaults() {
            if (confirm('Apakah Anda yakin ingin mereset semua pengaturan ke default?')) {
                try {
                    const response = await fetch('/api/settings/reset', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (response.ok) {
                        this.loadSettings();
                        this.showNotification('Pengaturan berhasil direset ke default!', 'success');
                    }
                } catch (error) {
                    console.error('Error resetting settings:', error);
                    this.showNotification('Gagal mereset pengaturan', 'error');
                }
            }
        },

        async createBackup() {
            try {
                const response = await fetch('/api/system/backup', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    this.showNotification('Backup berhasil dibuat!', 'success');
                } else {
                    this.showNotification('Gagal membuat backup', 'error');
                }
            } catch (error) {
                console.error('Error creating backup:', error);
                this.showNotification('Kesalahan jaringan. Silakan coba lagi.', 'error');
            }
        },

        showNotification(message, type) {
            // Integration with existing notification system
            if (window.Alpine && window.Alpine.store('notifications')) {
                window.Alpine.store('notifications').add({
                    message: message,
                    type: type,
                    duration: 5000
                });
            } else {
                alert(message);
            }
        }
    }
}
</script>
@endsection