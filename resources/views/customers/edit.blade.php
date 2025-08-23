@extends('layouts.dashboard')

@section('title', 'Edit Pelanggan')
@section('page-title')
<span class="text-base lg:text-2xl">Edit Pelanggan</span>
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
        <a href="/customers" class="ml-1 text-sm font-medium text-muted hover:text-foreground md:ml-2">Pelanggan</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Edit</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="customerEdit()" class="space-y-4 lg:space-y-6 p-4 lg:p-0">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="/customers" class="p-2 text-muted hover:text-foreground rounded-lg border border-border hover:border-primary/20 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-xl lg:text-2xl font-bold text-foreground">Edit Pelanggan</h2>
                <p class="text-sm text-muted" x-text="'ID: ' + form.customer_id"></p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="/customers" class="btn-secondary">
                Batal
            </a>
            <button @click="saveCustomer()" 
                    :disabled="loading"
                    class="btn-primary flex items-center">
                <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="loading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
            </button>
        </div>
    </div>

    <form @submit.prevent="saveCustomer()" class="space-y-6">
        <!-- Basic Information -->
        <div class="card p-4 lg:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-6">Informasi Dasar</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Customer ID -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        ID Pelanggan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           x-model="form.customer_id"
                           class="input"
                           placeholder="Contoh: CUST-001"
                           required>
                    <p class="text-xs text-muted mt-1">ID unik untuk pelanggan ini</p>
                </div>

                <!-- Customer Name -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Nama Pelanggan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           x-model="form.name"
                           class="input"
                           placeholder="Masukkan nama lengkap pelanggan"
                           required>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Nomor Telepon <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           x-model="form.phone"
                           class="input"
                           placeholder="Contoh: 081234567890"
                           required>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Email
                    </label>
                    <input type="email" 
                           x-model="form.email"
                           class="input"
                           placeholder="Contoh: customer@email.com">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select x-model="form.status" class="input" required>
                        <option value="">Pilih Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                        <option value="vip">VIP</option>
                    </select>
                </div>

                <!-- Customer Type -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Tipe Pelanggan
                    </label>
                    <select x-model="form.customer_type" class="input">
                        <option value="">Pilih Tipe</option>
                        <option value="individual">Individu</option>
                        <option value="company">Perusahaan</option>
                        <option value="reseller">Reseller</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="card p-4 lg:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-6">Informasi Alamat</h3>
            
            <div class="space-y-4">
                <!-- Full Address -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Alamat Lengkap
                    </label>
                    <textarea x-model="form.address" 
                              class="input min-h-[100px]"
                              placeholder="Masukkan alamat lengkap pelanggan..."></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- City -->
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">
                            Kota
                        </label>
                        <input type="text" 
                               x-model="form.city"
                               class="input"
                               placeholder="Contoh: Jakarta">
                    </div>

                    <!-- Province -->
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">
                            Provinsi
                        </label>
                        <input type="text" 
                               x-model="form.province"
                               class="input"
                               placeholder="Contoh: DKI Jakarta">
                    </div>

                    <!-- Postal Code -->
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">
                            Kode Pos
                        </label>
                        <input type="text" 
                               x-model="form.postal_code"
                               class="input"
                               placeholder="Contoh: 12345">
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="card p-4 lg:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-6">Informasi Tambahan</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Birth Date -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Tanggal Lahir
                    </label>
                    <input type="date" 
                           x-model="form.birth_date"
                           class="input">
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Jenis Kelamin
                    </label>
                    <select x-model="form.gender" class="input">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="male">Laki-laki</option>
                        <option value="female">Perempuan</option>
                    </select>
                </div>

                <!-- Discount Percentage -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Diskon Default (%)
                    </label>
                    <input type="number" 
                           x-model="form.default_discount"
                           class="input"
                           min="0"
                           max="100"
                           step="0.1"
                           placeholder="0">
                    <p class="text-xs text-muted mt-1">Diskon otomatis untuk pelanggan ini</p>
                </div>

                <!-- Credit Limit -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Limit Kredit
                    </label>
                    <input type="number" 
                           x-model="form.credit_limit"
                           class="input"
                           min="0"
                           step="1000"
                           placeholder="0">
                    <p class="text-xs text-muted mt-1">Batas maksimal kredit dalam Rupiah</p>
                </div>
            </div>
        </div>

        <!-- Customer Notes -->
        <div class="card p-4 lg:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-6">Catatan Pelanggan</h3>
            
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">
                    Catatan Internal
                </label>
                <textarea x-model="form.notes" 
                          class="input min-h-[120px]"
                          placeholder="Tambahkan catatan khusus tentang pelanggan ini..."></textarea>
                <p class="text-xs text-muted mt-1">Catatan ini hanya untuk internal dan tidak akan dilihat pelanggan</p>
            </div>
        </div>

        <!-- Preferences -->
        <div class="card p-4 lg:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-6">Preferensi</h3>
            
            <div class="space-y-4">
                <!-- Communication Preferences -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-3">
                        Preferensi Komunikasi
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   x-model="form.preferences.email_notifications"
                                   class="rounded border-border text-primary focus:ring-primary">
                            <span class="ml-2 text-sm text-foreground">Notifikasi Email</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   x-model="form.preferences.sms_notifications"
                                   class="rounded border-border text-primary focus:ring-primary">
                            <span class="ml-2 text-sm text-foreground">Notifikasi SMS</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   x-model="form.preferences.promotional_emails"
                                   class="rounded border-border text-primary focus:ring-primary">
                            <span class="ml-2 text-sm text-foreground">Email Promosi</span>
                        </label>
                    </div>
                </div>

                <!-- Preferred Contact Time -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">
                            Waktu Kontak Terbaik
                        </label>
                        <select x-model="form.preferred_contact_time" class="input">
                            <option value="">Pilih Waktu</option>
                            <option value="morning">Pagi (08:00 - 12:00)</option>
                            <option value="afternoon">Siang (12:00 - 17:00)</option>
                            <option value="evening">Sore (17:00 - 21:00)</option>
                        </select>
                    </div>

                    <!-- Preferred Payment Method -->
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">
                            Metode Pembayaran Favorit
                        </label>
                        <select x-model="form.preferred_payment_method" class="input">
                            <option value="">Pilih Metode</option>
                            <option value="cash">Tunai</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="credit_card">Kartu Kredit</option>
                            <option value="e_wallet">E-Wallet</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col sm:flex-row sm:justify-end gap-3">
            <a href="/customers" class="btn-secondary text-center">
                Batal
            </a>
            <button type="submit" 
                    :disabled="loading"
                    class="btn-primary flex items-center justify-center">
                <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="loading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function customerEdit() {
    return {
        loading: false,
        form: {
            customer_id: 'CUST-001',
            name: 'Ahmad Wijaya',
            phone: '081234567890',
            email: 'ahmad.wijaya@email.com',
            status: 'active',
            customer_type: 'individual',
            address: 'Jl. Merdeka No. 123, Jakarta Pusat',
            city: 'Jakarta',
            province: 'DKI Jakarta',
            postal_code: '10110',
            birth_date: '1985-06-15',
            gender: 'male',
            default_discount: 0,
            credit_limit: 5000000,
            notes: 'Pelanggan setia yang selalu membeli produk premium. Suka dengan kemasan khusus.',
            preferred_contact_time: 'morning',
            preferred_payment_method: 'transfer',
            preferences: {
                email_notifications: true,
                sms_notifications: false,
                promotional_emails: true
            }
        },

        async init() {
            // Load customer data from API
            await this.loadCustomer();
        },

        async loadCustomer() {
            try {
                // In real implementation, load from API
                // const response = await fetch(`/api/customers/${customerId}`);
                // const customer = await response.json();
                // this.form = { ...this.form, ...customer };
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal memuat data pelanggan'
                });
            }
        },

        async saveCustomer() {
            if (!this.validateForm()) {
                return;
            }

            this.loading = true;

            try {
                // Simulate API call
                await new Promise(resolve => setTimeout(resolve, 1000));

                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: 'Data pelanggan berhasil diperbarui.'
                });

                // Redirect to customer detail or list
                window.location.href = '/customers';
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal menyimpan data pelanggan'
                });
            } finally {
                this.loading = false;
            }
        },

        validateForm() {
            const requiredFields = ['customer_id', 'name', 'phone', 'status'];
            
            for (const field of requiredFields) {
                if (!this.form[field] || this.form[field].toString().trim() === '') {
                    this.$store.notifications.add({
                        type: 'error',
                        title: 'Validasi Gagal!',
                        message: `Field ${this.getFieldLabel(field)} harus diisi.`
                    });
                    return false;
                }
            }

            // Validate email format if provided
            if (this.form.email && !this.isValidEmail(this.form.email)) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Validasi Gagal!',
                    message: 'Format email tidak valid.'
                });
                return false;
            }

            // Validate phone format
            if (this.form.phone && !this.isValidPhone(this.form.phone)) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Validasi Gagal!',
                    message: 'Format nomor telepon tidak valid.'
                });
                return false;
            }

            return true;
        },

        getFieldLabel(field) {
            const labels = {
                'customer_id': 'ID Pelanggan',
                'name': 'Nama Pelanggan',
                'phone': 'Nomor Telepon',
                'status': 'Status'
            };
            return labels[field] || field;
        },

        isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },

        isValidPhone(phone) {
            const phoneRegex = /^(\+62|62|0)[0-9]{9,13}$/;
            return phoneRegex.test(phone.replace(/\s+/g, ''));
        }
    }
}
</script>
@endpush