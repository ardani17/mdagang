@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div x-data="form({
    email: '',
    password: '',
    remember: false
})">
    <!-- Header -->
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-foreground">Selamat Datang Kembali</h2>
        <p class="mt-2 text-sm text-muted">Masuk ke akun Anda untuk melanjutkan</p>
    </div>

    <!-- Login Form -->
    <form @submit.prevent="submit('/ajax-login', {
        onSuccess: (result) => {
            $store.notifications.success('Login berhasil! Mengalihkan...');
            setTimeout(() => {
                window.location.href = result.redirect || '/dashboard';
            }, 1000);
        },
        onError: (error) => {
            $store.notifications.error(error.message || 'Login gagal. Silakan coba lagi.');
        }
    })" class="space-y-6">
        
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
        <div x-data="{ showPassword: false }">
            <label for="password" class="block text-sm font-medium text-foreground mb-2">
                Kata Sandi
            </label>
            <div class="relative">
                <input :type="showPassword ? 'text' : 'password'" 
                       id="password"
                       x-model="data.password"
                       @input="setData('password', $event.target.value)"
                       :class="hasError('password') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                       class="input w-full pr-10"
                       placeholder="Masukkan kata sandi Anda"
                       required
                       autocomplete="current-password">
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
            <p x-show="hasError('password')" 
               x-text="getError('password')" 
               class="mt-1 text-sm text-red-600"
               style="display: none;"></p>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input type="checkbox"
                   id="remember"
                   x-model="data.remember"
                   class="h-4 w-4 text-primary focus:ring-primary border-border rounded">
            <label for="remember" class="ml-2 block text-sm text-foreground">
                Ingat saya
            </label>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit"
                    :disabled="isSubmitting"
                    :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : ''"
                    class="btn-primary w-full flex justify-center items-center">
                <span x-show="!isSubmitting">Masuk</span>
                <span x-show="isSubmitting" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-primary-foreground" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sedang Masuk...
                </span>
            </button>
        </div>

        <!-- Divider -->
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-border"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-surface text-muted">Atau lanjutkan dengan</span>
            </div>
        </div>

        <!-- Social Login Buttons (Optional) -->
        <div class="grid grid-cols-1 gap-3">
            <!-- Google Login -->
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

        <!-- Admin Notice -->
        <div class="text-center">
            <p class="text-sm text-muted">
                Hubungi administrator untuk membuat akun baru
            </p>
        </div>
    </form>
</div>

<!-- Demo Credentials (Development Only) -->
@if(app()->environment('local'))
<div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
    <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-2">Akun Demo</h4>
    <div class="text-xs text-yellow-700 dark:text-yellow-300 space-y-1">
        <p><strong>Administrator:</strong> admin@mdagang.com / admin123</p>
        <p><strong>User:</strong> john@mdagang.com / password123</p>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
// Auto-fill demo credentials (development only)
@if(app()->environment('local'))
document.addEventListener('alpine:init', () => {
    // Add quick fill buttons for demo
    window.fillDemo = (role) => {
        const emails = {
            admin: 'admin@mdagang.com',
            manager: 'manager@mdagang.com',
            staff: 'staff@mdagang.com'
        };
        
        // This would need to be integrated with Alpine data
        console.log('Demo fill for:', role, emails[role]);
    };
});
@endif
</script>
@endpush