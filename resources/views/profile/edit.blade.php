@extends('layouts.dashboard')

@section('title', 'Profil Pengguna')
@section('page-title', 'Profil Pengguna')

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
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Profil Pengguna</span>
    </div>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Profile Header -->
    <div class="card p-6">
        <div class="flex items-center space-x-6">
            <div class="relative">
                <div class="w-24 h-24 bg-primary rounded-full flex items-center justify-center text-2xl font-bold text-primary-foreground">
                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                </div>
                <button class="absolute bottom-0 right-0 w-8 h-8 bg-surface border-2 border-background rounded-full flex items-center justify-center hover:bg-border transition-colors">
                    <svg class="w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </button>
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-foreground">{{ auth()->user()->name ?? 'Nama Pengguna' }}</h2>
                <p class="text-muted">{{ auth()->user()->email ?? 'email@example.com' }}</p>
                <div class="flex items-center mt-2 space-x-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                        Administrator
                    </span>
                    <span class="text-sm text-muted">
                        Bergabung {{ now()->format('F Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Form -->
    <div class="card">
        <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold text-foreground">Informasi Profil</h3>
            <p class="text-sm text-muted">Perbarui informasi profil dan alamat email akun Anda.</p>
        </div>
        
        <form class="p-6 space-y-6" x-data="profileForm()">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-sm font-medium text-foreground mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           x-model="form.name"
                           class="input"
                           placeholder="Masukkan nama lengkap"
                           required>
                    <p x-show="errors.name" x-text="errors.name" class="mt-1 text-sm text-red-600"></p>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-foreground mb-2">
                        Alamat Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           x-model="form.email"
                           class="input"
                           placeholder="Masukkan alamat email"
                           required>
                    <p x-show="errors.email" x-text="errors.email" class="mt-1 text-sm text-red-600"></p>
                </div>

                <!-- Nomor Telepon -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-foreground mb-2">
                        Nomor Telepon
                    </label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           x-model="form.phone"
                           class="input"
                           placeholder="Masukkan nomor telepon">
                    <p x-show="errors.phone" x-text="errors.phone" class="mt-1 text-sm text-red-600"></p>
                </div>

                <!-- Preferensi Tema -->
                <div>
                    <label for="theme" class="block text-sm font-medium text-foreground mb-2">
                        Preferensi Tema
                    </label>
                    <select id="theme" 
                            name="theme_preference" 
                            x-model="form.theme_preference"
                            class="input">
                        <option value="light">Terang</option>
                        <option value="dark">Gelap</option>
                        <option value="system">Mengikuti Sistem</option>
                    </select>
                </div>
            </div>

            <!-- Alamat -->
            <div>
                <label for="address" class="block text-sm font-medium text-foreground mb-2">
                    Alamat
                </label>
                <textarea id="address" 
                          name="address" 
                          x-model="form.address"
                          rows="3" 
                          class="input"
                          placeholder="Masukkan alamat lengkap"></textarea>
                <p x-show="errors.address" x-text="errors.address" class="mt-1 text-sm text-red-600"></p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
                <button type="button" 
                        class="btn-secondary"
                        @click="resetForm()">
                    Batal
                </button>
                <button type="submit" 
                        class="btn-primary"
                        @click.prevent="submitForm()"
                        :disabled="loading">
                    <span x-show="!loading">Simpan Perubahan</span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password -->
    <div class="card">
        <div class="p-6 border-b border-border">
            <h3 class="text-lg font-semibold text-foreground">Ubah Kata Sandi</h3>
            <p class="text-sm text-muted">Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.</p>
        </div>
        
        <form class="p-6 space-y-6" x-data="passwordForm()">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kata Sandi Saat Ini -->
                <div class="md:col-span-2">
                    <label for="current_password" class="block text-sm font-medium text-foreground mb-2">
                        Kata Sandi Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showCurrentPassword ? 'text' : 'password'" 
                               id="current_password" 
                               name="current_password" 
                               x-model="form.current_password"
                               class="input pr-10"
                               placeholder="Masukkan kata sandi saat ini"
                               required>
                        <button type="button" 
                                @click="showCurrentPassword = !showCurrentPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg x-show="!showCurrentPassword" class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showCurrentPassword" class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <p x-show="errors.current_password" x-text="errors.current_password" class="mt-1 text-sm text-red-600"></p>
                </div>

                <!-- Kata Sandi Baru -->
                <div>
                    <label for="password" class="block text-sm font-medium text-foreground mb-2">
                        Kata Sandi Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showNewPassword ? 'text' : 'password'" 
                               id="password" 
                               name="password" 
                               x-model="form.password"
                               class="input pr-10"
                               placeholder="Masukkan kata sandi baru"
                               required>
                        <button type="button" 
                                @click="showNewPassword = !showNewPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg x-show="!showNewPassword" class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showNewPassword" class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <p x-show="errors.password" x-text="errors.password" class="mt-1 text-sm text-red-600"></p>
                </div>

                <!-- Konfirmasi Kata Sandi -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-foreground mb-2">
                        Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showConfirmPassword ? 'text' : 'password'" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               x-model="form.password_confirmation"
                               class="input pr-10"
                               placeholder="Konfirmasi kata sandi baru"
                               required>
                        <button type="button" 
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg x-show="!showConfirmPassword" class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showConfirmPassword" class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <p x-show="errors.password_confirmation" x-text="errors.password_confirmation" class="mt-1 text-sm text-red-600"></p>
                </div>
            </div>

            <!-- Password Strength Indicator -->
            <div x-show="form.password.length > 0">
                <div class="mb-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-foreground">Kekuatan Kata Sandi</span>
                        <span x-text="passwordStrength.label" :class="passwordStrength.color"></span>
                    </div>
                    <div class="w-full bg-border rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-300" 
                             :class="passwordStrength.bgColor" 
                             :style="`width: ${passwordStrength.percentage}%`"></div>
                    </div>
                </div>
                <ul class="text-xs text-muted space-y-1">
                    <li :class="form.password.length >= 8 ? 'text-green-600' : 'text-muted'">
                        ✓ Minimal 8 karakter
                    </li>
                    <li :class="/[A-Z]/.test(form.password) ? 'text-green-600' : 'text-muted'">
                        ✓ Mengandung huruf besar
                    </li>
                    <li :class="/[a-z]/.test(form.password) ? 'text-green-600' : 'text-muted'">
                        ✓ Mengandung huruf kecil
                    </li>
                    <li :class="/[0-9]/.test(form.password) ? 'text-green-600' : 'text-muted'">
                        ✓ Mengandung angka
                    </li>
                    <li :class="/[^A-Za-z0-9]/.test(form.password) ? 'text-green-600' : 'text-muted'">
                        ✓ Mengandung karakter khusus
                    </li>
                </ul>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
                <button type="button" 
                        class="btn-secondary"
                        @click="resetPasswordForm()">
                    Batal
                </button>
                <button type="submit" 
                        class="btn-primary"
                        @click.prevent="submitPasswordForm()"
                        :disabled="loading">
                    <span x-show="!loading">Ubah Kata Sandi</span>
                    <span x-show="loading" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Mengubah...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function profileForm() {
    return {
        loading: false,
        form: {
            name: '{{ auth()->user()->name ?? "" }}',
            email: '{{ auth()->user()->email ?? "" }}',
            phone: '{{ auth()->user()->phone ?? "" }}',
            address: '{{ auth()->user()->address ?? "" }}',
            theme_preference: '{{ auth()->user()->theme_preference ?? "light" }}'
        },
        errors: {},

        async submitForm() {
            this.loading = true;
            this.errors = {};

            try {
                const response = await fetch('{{ route("profile.update") }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (response.ok) {
                    this.$store.notifications.add({
                        type: 'success',
                        title: 'Berhasil!',
                        message: 'Profil berhasil diperbarui.'
                    });
                } else {
                    if (data.errors) {
                        this.errors = data.errors;
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan');
                    }
                }
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: error.message || 'Terjadi kesalahan saat memperbarui profil.'
                });
            } finally {
                this.loading = false;
            }
        },

        resetForm() {
            this.form = {
                name: '{{ auth()->user()->name ?? "" }}',
                email: '{{ auth()->user()->email ?? "" }}',
                phone: '{{ auth()->user()->phone ?? "" }}',
                address: '{{ auth()->user()->address ?? "" }}',
                theme_preference: '{{ auth()->user()->theme_preference ?? "light" }}'
            };
            this.errors = {};
        }
    }
}

function passwordForm() {
    return {
        loading: false,
        showCurrentPassword: false,
        showNewPassword: false,
        showConfirmPassword: false,
        form: {
            current_password: '',
            password: '',
            password_confirmation: ''
        },
        errors: {},

        get passwordStrength() {
            const password = this.form.password;
            let score = 0;
            
            if (password.length >= 8) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[a-z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;

            const levels = [
                { label: 'Sangat Lemah', color: 'text-red-600', bgColor: 'bg-red-500', percentage: 20 },
                { label: 'Lemah', color: 'text-red-500', bgColor: 'bg-red-400', percentage: 40 },
                { label: 'Sedang', color: 'text-yellow-500', bgColor: 'bg-yellow-400', percentage: 60 },
                { label: 'Kuat', color: 'text-green-500', bgColor: 'bg-green-400', percentage: 80 },
                { label: 'Sangat Kuat', color: 'text-green-600', bgColor: 'bg-green-500', percentage: 100 }
            ];

            return levels[score] || levels[0];
        },

        async submitPasswordForm() {
            this.loading = true;
            this.errors = {};

            try {
                const response = await fetch('{{ route("password.update") }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (response.ok) {
                    this.$store.notifications.add({
                        type: 'success',
                        title: 'Berhasil!',
                        message: 'Kata sandi berhasil diubah.'
                    });
                    this.resetPasswordForm();
                } else {
                    if (data.errors) {
                        this.errors = data.errors;
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan');
                    }
                }
            } catch (error) {
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: error.message || 'Terjadi kesalahan saat mengubah kata sandi.'
                });
            } finally {
                this.loading = false;
            }
        },

        resetPasswordForm() {
            this.form = {
                current_password: '',
                password: '',
                password_confirmation: ''
            };
            this.errors = {};
            this.showCurrentPassword = false;
            this.showNewPassword = false;
            this.showConfirmPassword = false;
        }
    }
}
</script>
@endpush