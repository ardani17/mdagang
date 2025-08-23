@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<div x-data="form({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false
})">
    <!-- Header -->
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-foreground">Buat Akun</h2>
        <p class="mt-2 text-sm text-muted">Bergabung dengan BRO Manajemen untuk mengelola penjualan Anda dengan efisien</p>
    </div>

    <!-- Register Form -->
    <form @submit.prevent="submit('/api/auth/register', {
        onSuccess: (result) => {
            $store.notifications.success('Account created successfully! Please check your email for verification.');
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
        },
        onError: (error) => {
            $store.notifications.error(error.message || 'Registration failed. Please try again.');
        }
    })" class="space-y-6">
        
        <!-- Name Field -->
        <div>
            <label for="name" class="block text-sm font-medium text-foreground mb-2">
                Nama Lengkap
            </label>
            <input type="text"
                   id="name"
                   x-model="data.name"
                   @input="setData('name', $event.target.value)"
                   :class="hasError('name') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                   class="input w-full"
                   placeholder="Masukkan nama lengkap Anda"
                   required
                   autocomplete="name">
            <p x-show="hasError('name')" 
               x-text="getError('name')" 
               class="mt-1 text-sm text-red-600"
               style="display: none;"></p>
        </div>

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-medium text-foreground mb-2">
                Alamat Email
            </label>
            <input type="email"
                   id="email"
                   x-model="data.email"
                   @input="setData('email', $event.target.value)"
                   :class="hasError('email') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                   class="input w-full"
                   placeholder="Masukkan alamat email Anda"
                   required
                   autocomplete="email">
            <p x-show="hasError('email')" 
               x-text="getError('email')" 
               class="mt-1 text-sm text-red-600"
               style="display: none;"></p>
        </div>

        <!-- Password Field -->
        <div x-data="{ showPassword: false, strength: 0 }">
            <label for="password" class="block text-sm font-medium text-foreground mb-2">
                Kata Sandi
            </label>
            <div class="relative">
                <input :type="showPassword ? 'text' : 'password'" 
                       id="password"
                       x-model="data.password"
                       @input="setData('password', $event.target.value); checkPasswordStrength($event.target.value)"
                       :class="hasError('password') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                       class="input w-full pr-10"
                       placeholder="Buat kata sandi yang kuat"
                       required
                       autocomplete="new-password">
                <button type="button" 
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-muted hover:text-foreground">
                    <!-- Eye Icon (Show) -->
                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <!-- Eye Slash Icon (Hide) -->
                    <svg x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                    </svg>
                </button>
            </div>
            
            <!-- Password Strength Indicator -->
            <div x-show="data.password.length > 0" class="mt-2">
                <div class="flex space-x-1">
                    <div :class="strength >= 1 ? 'bg-red-500' : 'bg-gray-200'" class="h-1 w-1/4 rounded"></div>
                    <div :class="strength >= 2 ? 'bg-yellow-500' : 'bg-gray-200'" class="h-1 w-1/4 rounded"></div>
                    <div :class="strength >= 3 ? 'bg-blue-500' : 'bg-gray-200'" class="h-1 w-1/4 rounded"></div>
                    <div :class="strength >= 4 ? 'bg-green-500' : 'bg-gray-200'" class="h-1 w-1/4 rounded"></div>
                </div>
                <p class="text-xs mt-1 text-muted">
                    <span x-show="strength === 1" class="text-red-600">Lemah</span>
                    <span x-show="strength === 2" class="text-yellow-600">Cukup</span>
                    <span x-show="strength === 3" class="text-blue-600">Baik</span>
                    <span x-show="strength === 4" class="text-green-600">Kuat</span>
                </p>
            </div>
            
            <p x-show="hasError('password')" 
               x-text="getError('password')" 
               class="mt-1 text-sm text-red-600"
               style="display: none;"></p>
        </div>

        <!-- Confirm Password Field -->
        <div x-data="{ showPassword: false }">
            <label for="password_confirmation" class="block text-sm font-medium text-foreground mb-2">
                Konfirmasi Kata Sandi
            </label>
            <div class="relative">
                <input :type="showPassword ? 'text' : 'password'" 
                       id="password_confirmation"
                       x-model="data.password_confirmation"
                       @input="setData('password_confirmation', $event.target.value)"
                       :class="hasError('password_confirmation') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                       class="input w-full pr-10"
                       placeholder="Konfirmasi kata sandi Anda"
                       required
                       autocomplete="new-password">
                <button type="button" 
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-muted hover:text-foreground">
                    <!-- Eye Icon (Show) -->
                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <!-- Eye Slash Icon (Hide) -->
                    <svg x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                    </svg>
                </button>
            </div>
            
            <!-- Password Match Indicator -->
            <div x-show="data.password_confirmation.length > 0" class="mt-1">
                <p x-show="data.password === data.password_confirmation" class="text-xs text-green-600">
                    ✓ Kata sandi cocok
                </p>
                <p x-show="data.password !== data.password_confirmation" class="text-xs text-red-600">
                    ✗ Kata sandi tidak cocok
                </p>
            </div>
            
            <p x-show="hasError('password_confirmation')" 
               x-text="getError('password_confirmation')" 
               class="mt-1 text-sm text-red-600"
               style="display: none;"></p>
        </div>

        <!-- Terms and Conditions -->
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input type="checkbox"
                       id="terms"
                       x-model="data.terms"
                       :class="hasError('terms') ? 'border-red-500 focus:ring-red-500' : ''"
                       class="h-4 w-4 text-primary focus:ring-primary border-border rounded"
                       required>
            </div>
            <div class="ml-3 text-sm">
                <label for="terms" class="text-foreground">
                    Saya setuju dengan
                    <a href="/terms" target="_blank" class="font-medium text-primary hover:text-primary/80 transition-colors">
                        Syarat dan Ketentuan
                    </a>
                    dan
                    <a href="/privacy" target="_blank" class="font-medium text-primary hover:text-primary/80 transition-colors">
                        Kebijakan Privasi
                    </a>
                </label>
            </div>
        </div>
        <p x-show="hasError('terms')" 
           x-text="getError('terms')" 
           class="mt-1 text-sm text-red-600"
           style="display: none;"></p>

        <!-- Submit Button -->
        <div>
            <button type="submit"
                    :disabled="isSubmitting || !data.terms || data.password !== data.password_confirmation"
                    :class="(isSubmitting || !data.terms || data.password !== data.password_confirmation) ? 'opacity-50 cursor-not-allowed' : ''"
                    class="btn-primary w-full flex justify-center items-center">
                <span x-show="!isSubmitting">Buat Akun</span>
                <span x-show="isSubmitting" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-primary-foreground" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sedang Membuat Akun...
                </span>
            </button>
        </div>

        <!-- Divider -->
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-border"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-surface text-muted">Atau daftar dengan</span>
            </div>
        </div>

        <!-- Social Registration Buttons (Optional) -->
        <div class="grid grid-cols-1 gap-3">
            <!-- Google Registration -->
            <button type="button" 
                    class="w-full inline-flex justify-center py-2 px-4 border border-border rounded-lg shadow-sm bg-surface text-sm font-medium text-foreground hover:bg-border transition-colors">
                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Lanjutkan dengan Google
            </button>
        </div>

        <!-- Login Link -->
        <div class="text-center">
            <p class="text-sm text-muted">
                Sudah punya akun?
                <a href="{{ route('login') }}"
                   class="font-medium text-primary hover:text-primary/80 transition-colors">
                    Masuk di sini
                </a>
            </p>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    // Password strength checker
    window.checkPasswordStrength = function(password) {
        let strength = 0;
        
        // Length check
        if (password.length >= 8) strength++;
        
        // Lowercase and uppercase
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        
        // Numbers
        if (/\d/.test(password)) strength++;
        
        // Special characters
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        // Update strength in Alpine component
        this.strength = strength;
    };
});
</script>
@endpush