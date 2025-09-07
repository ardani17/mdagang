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
<div x-data="customerEdit({{ Js::from($customer) }})" class="space-y-4 lg:space-y-6 p-4 lg:p-0">
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
                <p class="text-sm text-muted" x-text="'ID: ' + form.code"></p>
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
                <!-- Customer Code -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Kode Pelanggan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           x-model="form.code"
                           class="input"
                           placeholder="Contoh: CUST-001"
                           required
                           readonly>
                    <p class="text-xs text-muted mt-1">Kode unik untuk pelanggan ini (tidak dapat diubah)</p>
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
                    <select x-model="form.is_active" class="input" required>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>

                <!-- Customer Type -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Tipe Pelanggan <span class="text-red-500">*</span>
                    </label>
                    <select x-model="form.type" class="input" required>
                        <option value="individual">Individu</option>
                        <option value="business">Bisnis</option>
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
                        Alamat Lengkap <span class="text-red-500">*</span>
                    </label>
                    <textarea x-model="form.address" 
                              class="input min-h-[100px]"
                              placeholder="Masukkan alamat lengkap pelanggan..."
                              required></textarea>
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

                    <!-- Tax ID -->
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">
                            NPWP
                        </label>
                        <input type="text" 
                               x-model="form.tax_id"
                               class="input"
                               placeholder="Contoh: 01.234.567.8-912.345">
                        <p class="text-xs text-muted mt-1">Hanya untuk pelanggan bisnis</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Information -->
        <div class="card p-4 lg:p-6">
            <h3 class="text-lg font-semibold text-foreground mb-6">Informasi Keuangan</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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

                <!-- Outstanding Balance -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Saldo Tertunggak
                    </label>
                    <input type="number" 
                           x-model="form.outstanding_balance"
                           class="input"
                           min="0"
                           step="1000"
                           placeholder="0"
                           readonly>
                    <p class="text-xs text-muted mt-1">Saldo yang masih tertunggak (hanya baca)</p>
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
function customerEdit(customerData) {
    return {
        loading: false,
        customerId: customerData.id,
        form: {
            code: customerData.code || '',
            name: customerData.name || '',
            email: customerData.email || '',
            phone: customerData.phone || '',
            address: customerData.address || '',
            city: customerData.city || '',
            postal_code: customerData.postal_code || '',
            type: customerData.type || 'individual',
            tax_id: customerData.tax_id || '',
            credit_limit: parseFloat(customerData.credit_limit) || 0,
            outstanding_balance: parseFloat(customerData.outstanding_balance) || 0,
            notes: customerData.notes || '',
            is_active: customerData.is_active ? 1 : 0
        },

        async saveCustomer() {
            // if (!this.validateForm()) {
            //     return;
            // }

            this.loading = true;

            try {
                const response = await fetch(`/api/customers/${this.customerId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (response.ok) {
                    this.showNotification('Data pelanggan berhasil diperbarui', 'success');
                    // Redirect after successful update
                    setTimeout(() => {
                        window.location.href = '/customers';
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Gagal menyimpan data');
                }
            } catch (error) {
                console.error('Error saving customer:', error);
                this.showNotification(error.message, 'error');
            } finally {
                this.loading = false;
            }
        },

        validateForm() {
            const requiredFields = ['code', 'name', 'phone', 'address'];
            
            for (const field of requiredFields) {
                if (!this.form[field] || this.form[field].toString().trim() === '') {
                    this.showNotification(`Field ${this.getFieldLabel(field)} harus diisi`, 'error');
                    return false;
                }
            }

            // Validate email format if provided
            if (this.form.email && !this.isValidEmail(this.form.email)) {
                this.showNotification('Format email tidak valid', 'error');
                return false;
            }

            // Validate phone format
            if (this.form.phone && !this.isValidPhone(this.form.phone)) {
                this.showNotification('Format nomor telepon tidak valid', 'error');
                return false;
            }

            return true;
        },

        getFieldLabel(field) {
            const labels = {
                'code': 'Kode Pelanggan',
                'name': 'Nama Pelanggan',
                'phone': 'Nomor Telepon',
                'address': 'Alamat'
            };
            return labels[field] || field;
        },

        isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },

        isValidPhone(phone) {
            // Basic phone validation - adjust as needed
            const phoneRegex = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
            return phoneRegex.test(phone.replace(/\s+/g, ''));
        },

        showNotification(message, type) {
            // Use your notification system here
            if (window.Alpine && window.Alpine.store('notifications')) {
                window.Alpine.store('notifications').add({
                    message: message,
                    type: type,
                    duration: 5000
                });
            } else {
                alert(`${type.toUpperCase()}: ${message}`);
            }
        }
    }
}
</script>
@endpush