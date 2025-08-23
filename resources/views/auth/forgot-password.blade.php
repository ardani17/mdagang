@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
<div x-data="form({
    email: ''
})">
    <!-- Header -->
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-foreground">Lupa Kata Sandi</h2>
        <p class="mt-2 text-sm text-muted">
            Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi
        </p>
    </div>

    <!-- Forgot Password Form -->
    <form @submit.prevent="submit('/api/auth/forgot-password', {
        onSuccess: (result) => {
            $store.notifications.success('Password reset link sent! Please check your email.');
            // Optionally redirect or show success state
        },
        onError: (error) => {
            $store.notifications.error(error.message || 'Failed to send reset link. Please try again.');
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

        <!-- Submit Button -->
        <div>
            <button type="submit"
                    :disabled="isSubmitting"
                    :class="isSubmitting ? 'opacity-50 cursor-not-allowed' : ''"
                    class="btn-primary w-full flex justify-center items-center">
                <span x-show="!isSubmitting">Kirim Tautan Reset</span>
                <span x-show="isSubmitting" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-primary-foreground" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Mengirim...
                </span>
            </button>
        </div>

        <!-- Back to Login -->
        <div class="text-center">
            <a href="{{ route('login') }}"
               class="inline-flex items-center text-sm font-medium text-primary hover:text-primary/80 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Masuk
            </a>
        </div>
    </form>

    <!-- Help Text -->
    <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                    Butuh Bantuan?
                </h3>
                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Periksa folder spam/sampah jika Anda tidak menerima email</li>
                        <li>Tautan reset akan kedaluwarsa dalam 60 menit</li>
                        <li>Hubungi dukungan jika Anda terus mengalami masalah</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection