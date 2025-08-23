@extends('layouts.dashboard')

@section('title', 'Manajemen Pengguna')
@section('page-title')
<span class="text-base lg:text-2xl">Manajemen Pengguna</span>
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
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Manajemen Pengguna</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="userManager()" class="space-y-4 lg:space-y-6 p-4 lg:p-0">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Manajemen Pengguna</h2>
            <p class="text-sm text-muted">Kelola pengguna dan role akses sistem (Administrator & User)</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="exportUsers()" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor
            </button>
            
            <button @click="showUserForm = true" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Pengguna
            </button>
        </div>
    </div>

    <!-- User Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Total Users -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Total Pengguna</p>
                    <p class="text-2xl lg:text-3xl font-bold text-primary" x-text="stats.total_users"></p>
                    <p class="text-xs text-muted mt-1">
                        Terdaftar
                    </p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Pengguna Aktif</p>
                    <p class="text-2xl lg:text-3xl font-bold text-green-600" x-text="stats.active_users"></p>
                    <p class="text-xs text-muted mt-1">
                        Online hari ini
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Administrator Users -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Administrator</p>
                    <p class="text-2xl lg:text-3xl font-bold text-red-600" x-text="stats.admin_users"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-red-600">Full access + user management</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- User Role -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">User</p>
                    <p class="text-2xl lg:text-3xl font-bold text-green-600" x-text="stats.user_role"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-green-600">Akses keuangan tanpa user management</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card p-4 lg:p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-foreground mb-2">Cari Pengguna</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           x-model="filters.search"
                           @input.debounce.300ms="loadUsers()"
                           class="input pl-10" 
                           placeholder="Cari berdasarkan nama, email, atau role...">
                </div>
            </div>

            <!-- Role Filter -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Role</label>
                <select x-model="filters.role" 
                        @change="loadUsers()"
                        class="input">
                    <option value="">Semua Role</option>
                    <option value="administrator">Administrator</option>
                    <option value="user">User</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                <select x-model="filters.status" 
                        @change="loadUsers()"
                        class="input">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                    <option value="pending">Menunggu Verifikasi</option>
                    <option value="suspended">Ditangguhkan</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="p-4 lg:p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-foreground">
                    Daftar Pengguna (<span x-text="pagination.total"></span>)
                </h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-muted">Tampilkan:</span>
                    <select x-model="pagination.per_page" 
                            @change="loadUsers()"
                            class="input py-1 text-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-12">
                            <input type="checkbox"
                                   @change="toggleSelectAll($event.target.checked)"
                                   class="rounded border-border text-primary focus:ring-primary">
                        </th>
                        <th>Pengguna</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Terakhir Login</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="user in users" :key="user.id">
                        <tr class="hover:bg-surface/50">
                            <td>
                                <input type="checkbox"
                                       :value="user.id"
                                       x-model="selectedUsers"
                                       class="rounded border-border text-primary focus:ring-primary">
                            </td>
                            <td>
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-primary" x-text="user.name.charAt(0).toUpperCase()"></span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-foreground" x-text="user.name"></div>
                                        <div class="text-sm text-muted" x-text="user.phone || 'Tidak ada telepon'"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm text-foreground" x-text="user.email"></div>
                                <div class="text-xs text-muted" x-show="user.email_verified_at">
                                    <svg class="w-3 h-3 inline mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Terverifikasi
                                </div>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="getRoleClass(user.role)"
                                      x-text="getRoleText(user.role)">
                                </span>
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="getStatusClass(user.status)"
                                      x-text="getStatusText(user.status)">
                                </span>
                            </td>
                            <td>
                                <div class="text-sm text-foreground" x-text="formatDate(user.last_login_at)"></div>
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <button @click="viewUser(user)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button @click="editUser(user)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button @click="toggleUserStatus(user)"
                                            :class="user.status === 'active' ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800'"
                                            class="p-1">
                                        <svg x-show="user.status === 'active'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                        </svg>
                                        <svg x-show="user.status !== 'active'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden space-y-3 mobile-card-spacing">
            <template x-for="user in users" :key="user.id">
                <div class="card rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox"
                                   :value="user.id"
                                   x-model="selectedUsers"
                                   class="w-5 h-5 rounded border-border text-primary focus:ring-primary focus:ring-2">
                            <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-primary" x-text="user.name.charAt(0).toUpperCase()"></span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-foreground text-base leading-tight" x-text="user.name"></h3>
                                <p class="text-sm text-muted" x-text="user.email"></p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-1">
                            <button @click="viewUser(user)"
                                    class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <button @click="editUser(user)"
                                    class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 pt-3 border-t border-border">
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Role</span>
                            <p class="text-sm text-foreground mt-1" x-text="getRoleText(user.role)"></p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Status</span>
                            <p class="text-sm text-foreground mt-1" x-text="getStatusText(user.status)"></p>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Terakhir Login</span>
                            <p class="text-sm text-foreground mt-1" x-text="formatDate(user.last_login_at)"></p>
                        </div>
                        <div class="flex items-end justify-end">
                            <button @click="toggleUserStatus(user)"
                                    :class="user.status === 'active' ? 'text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 border-red-200' : 'text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100 border-green-200'"
                                    class="mobile-action-button p-2.5 rounded-lg border transition-colors">
                                <svg x-show="user.status === 'active'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                </svg>
                                <svg x-show="user.status !== 'active'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="users.length === 0 && !loading" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-foreground">Tidak ada pengguna ditemukan</h3>
            <p class="mt-1 text-sm text-muted">Mulai dengan menambahkan pengguna pertama.</p>
            <div class="mt-6">
                <button @click="showUserForm = true" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Pengguna
                </button>
            </div>
        </div>
    </div>

    <!-- User Form Modal -->
    <div x-show="showUserForm" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-background rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form @submit.prevent="saveUser()">
                    <div class="bg-background px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-foreground mb-4">
                                    <span x-text="editingUser ? 'Edit Pengguna' : 'Tambah Pengguna'"></span>
                                </h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Nama Lengkap *</label>
                                        <input type="text" 
                                               x-model="userForm.name"
                                               class="input"
                                               placeholder="Masukkan nama lengkap"
                                               required>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Email *</label>
                                        <input type="email" 
                                               x-model="userForm.email"
                                               class="input"
                                               placeholder="user@example.com"
                                               required>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Telepon</label>
                                        <input type="tel" 
                                               x-model="userForm.phone"
                                               class="input"
                                               placeholder="+62 812 3456 7890">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Role *</label>
                                        <select x-model="userForm.role" class="input" required>
                                            <option value="">Pilih Role</option>
                                            <option value="administrator">Administrator</option>
                                            <option value="user">User</option>
                                        </select>
                                    </div>
                                    
                                    <div x-show="!editingUser">
                                        <label class="block text-sm font-medium text-foreground mb-2">Password *</label>
                                        <input
<input type="password" 
                                               x-model="userForm.password"
                                               class="input"
                                               placeholder="Minimal 8 karakter"
                                               :required="!editingUser">
                                    </div>
                                    
                                    <div x-show="!editingUser">
                                        <label class="block text-sm font-medium text-foreground mb-2">Konfirmasi Password *</label>
                                        <input type="password" 
                                               x-model="userForm.password_confirmation"
                                               class="input"
                                               placeholder="Ulangi password"
                                               :required="!editingUser">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Status</label>
                                        <select x-model="userForm.status" class="input">
                                            <option value="active">Aktif</option>
                                            <option value="inactive">Tidak Aktif</option>
                                            <option value="pending">Menunggu Verifikasi</option>
                                        </select>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   x-model="userForm.send_welcome_email"
                                                   class="rounded border-border text-primary focus:ring-primary">
                                            <span class="ml-2 text-sm text-foreground">Kirim email selamat datang</span>
                                        </label>
                                        
                                        <label class="flex items-center" x-show="!editingUser">
                                            <input type="checkbox" 
                                                   x-model="userForm.force_password_change"
                                                   class="rounded border-border text-primary focus:ring-primary">
                                            <span class="ml-2 text-sm text-foreground">Paksa ganti password saat login pertama</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Role Description -->
                                <div class="bg-surface p-4 rounded-lg border border-border">
                                    <h4 class="font-medium text-foreground mb-2">Deskripsi Role</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-start space-x-2">
                                            <div class="w-2 h-2 bg-red-500 rounded-full mt-2 flex-shrink-0"></div>
                                            <div>
                                                <span class="font-medium text-foreground">Administrator:</span>
                                                <span class="text-muted">Full access ke semua modul keuangan termasuk user management, audit trail, dan pengaturan sistem.</span>
                                            </div>
                                        </div>
                                        <div class="flex items-start space-x-2">
                                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                                            <div>
                                                <span class="font-medium text-foreground">User:</span>
                                                <span class="text-muted">Akses ke semua modul keuangan kecuali user management. Dapat mengelola transaksi, invoice, laporan, dll.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-surface px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" 
                                :disabled="userFormLoading"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm"
                                :class="userFormLoading ? 'opacity-50 cursor-not-allowed' : ''">
                            <span x-text="userFormLoading ? 'Menyimpan...' : (editingUser ? 'Perbarui' : 'Tambah')"></span>
                        </button>
                        <button type="button" 
                                @click="closeUserForm()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-border shadow-sm px-4 py-2 bg-background text-base font-medium text-foreground hover:bg-surface focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function userManager() {
    return {
        loading: false,
        showUserForm: false,
        editingUser: null,
        userFormLoading: false,
        selectedUsers: [],
        users: [],
        stats: {
            total_users: 0,
            active_users: 0,
            admin_users: 0,
            pending_users: 0
        },
        filters: {
            search: '',
            role: '',
            status: ''
        },
        pagination: {
            current_page: 1,
            per_page: 25,
            total: 0,
            last_page: 1
        },
        userForm: {
            name: '',
            email: '',
            phone: '',
            role: '',
            password: '',
            password_confirmation: '',
            status: 'active',
            send_welcome_email: true,
            force_password_change: false
        },

        init() {
            this.loadUsers();
            this.loadStats();
        },

        async loadUsers() {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    ...this.filters
                });

                const response = await fetch(`/api/users?${params}`);
                const data = await response.json();
                
                this.users = data.data || [];
                this.pagination = {
                    current_page: data.current_page || 1,
                    per_page: data.per_page || 25,
                    total: data.total || 0,
                    last_page: data.last_page || 1
                };
            } catch (error) {
                console.error('Error loading users:', error);
                this.users = this.getDemoUsers();
            } finally {
                this.loading = false;
            }
        },

        async loadStats() {
            try {
                const response = await fetch('/api/users/stats');
                const data = await response.json();
                this.stats = data;
            } catch (error) {
                console.error('Error loading stats:', error);
                this.stats = {
                    total_users: 15,
                    active_users: 12,
                    admin_users: 3,
                    pending_users: 2
                };
            }
        },

        getDemoUsers() {
            return [
                {
                    id: 1,
                    name: 'Admin System',
                    email: 'admin@mdagang.com',
                    phone: '+62 812 3456 7890',
                    role: 'administrator',
                    status: 'active',
                    email_verified_at: '2024-01-01T00:00:00Z',
                    last_login_at: '2024-01-15T10:30:00Z'
                },
                {
                    id: 2,
                    name: 'Financial Manager',
                    email: 'finance@mdagang.com',
                    phone: '+62 813 4567 8901',
                    role: 'administrator',
                    status: 'active',
                    email_verified_at: '2024-01-02T00:00:00Z',
                    last_login_at: '2024-01-15T09:15:00Z'
                },
                {
                    id: 3,
                    name: 'Staff Keuangan',
                    email: 'staff@mdagang.com',
                    phone: '+62 814 5678 9012',
                    role: 'user',
                    status: 'active',
                    email_verified_at: '2024-01-03T00:00:00Z',
                    last_login_at: '2024-01-14T16:45:00Z'
                },
                {
                    id: 4,
                    name: 'User Keuangan 1',
                    email: 'user1@mdagang.com',
                    phone: '+62 815 6789 0123',
                    role: 'user',
                    status: 'active',
                    email_verified_at: '2024-01-04T00:00:00Z',
                    last_login_at: '2024-01-14T14:30:00Z'
                },
                {
                    id: 5,
                    name: 'User Keuangan 2',
                    email: 'user2@mdagang.com',
                    phone: '+62 816 7890 1234',
                    role: 'user',
                    status: 'active',
                    email_verified_at: '2024-01-05T00:00:00Z',
                    last_login_at: '2024-01-13T16:15:00Z'
                },
                {
                    id: 6,
                    name: 'User Baru',
                    email: 'newuser@mdagang.com',
                    phone: null,
                    role: 'user',
                    status: 'pending',
                    email_verified_at: null,
                    last_login_at: null
                }
            ];
        },

        getRoleText(role) {
            const roles = {
                administrator: 'Administrator',
                user: 'User'
            };
            return roles[role] || role;
        },

        getRoleClass(role) {
            const classes = {
                administrator: 'bg-red-100 text-red-800',
                user: 'bg-green-100 text-green-800'
            };
            return classes[role] || 'bg-gray-100 text-gray-800';
        },

        getStatusText(status) {
            const statuses = {
                active: 'Aktif',
                inactive: 'Tidak Aktif',
                pending: 'Menunggu Verifikasi',
                suspended: 'Ditangguhkan'
            };
            return statuses[status] || status;
        },

        getStatusClass(status) {
            const classes = {
                active: 'bg-green-100 text-green-800',
                inactive: 'bg-gray-100 text-gray-800',
                pending: 'bg-yellow-100 text-yellow-800',
                suspended: 'bg-red-100 text-red-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },

        formatDate(dateString) {
            if (!dateString) return 'Belum pernah';
            return new Date(dateString).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        toggleSelectAll(checked) {
            if (checked) {
                this.selectedUsers = this.users.map(u => u.id);
            } else {
                this.selectedUsers = [];
            }
        },

        viewUser(user) {
            console.log('View user:', user);
        },

        editUser(user) {
            this.editingUser = user;
            this.userForm = {
                name: user.name,
                email: user.email,
                phone: user.phone || '',
                role: user.role,
                password: '',
                password_confirmation: '',
                status: user.status,
                send_welcome_email: false,
                force_password_change: false
            };
            this.showUserForm = true;
        },

        async toggleUserStatus(user) {
            const newStatus = user.status === 'active' ? 'inactive' : 'active';
            const action = newStatus === 'active' ? 'mengaktifkan' : 'menonaktifkan';
            
            if (confirm(`Apakah Anda yakin ingin ${action} pengguna ${user.name}?`)) {
                try {
                    const response = await fetch(`/api/users/${user.id}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ status: newStatus })
                    });

                    if (response.ok) {
                        user.status = newStatus;
                        this.showNotification(`Pengguna berhasil ${action === 'mengaktifkan' ? 'diaktifkan' : 'dinonaktifkan'}!`, 'success');
                    }
                } catch (error) {
                    console.error('Error updating user status:', error);
                    this.showNotification('Gagal mengubah status pengguna', 'error');
                }
            }
        },

        async saveUser() {
            this.userFormLoading = true;
            
            try {
                const url = this.editingUser 
                    ? `/api/users/${this.editingUser.id}`
                    : '/api/users';
                
                const method = this.editingUser ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.userForm)
                });

                const data = await response.json();

                if (response.ok) {
                    this.closeUserForm();
                    this.loadUsers();
                    this.loadStats();
                    this.showNotification('Pengguna berhasil disimpan!', 'success');
                } else {
                    this.showNotification(data.message || 'Gagal menyimpan pengguna', 'error');
                }
            } catch (error) {
                console.error('Error saving user:', error);
                this.showNotification('Kesalahan jaringan. Silakan coba lagi.', 'error');
            } finally {
                this.userFormLoading = false;
            }
        },

        closeUserForm() {
            this.showUserForm = false;
            this.editingUser = null;
            this.userForm = {
                name: '',
                email: '',
                phone: '',
                role: '',
                password: '',
                password_confirmation: '',
                status: 'active',
                send_welcome_email: true,
                force_password_change: false
            };
        },

        async exportUsers() {
            try {
                const response = await fetch('/api/users/export');
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'users.xlsx';
                a.click();
            } catch (error) {
                console.error('Error exporting users:', error);
                this.showNotification('Gagal mengekspor data pengguna', 'error');
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