@extends('layouts.dashboard')

@section('title', 'Manajemen Role Keuangan')
@section('page-title')
<span class="text-base lg:text-2xl">Manajemen Role Keuangan</span>
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
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Keuangan</span>
    </div>
</li>
<li>
    <div class="flex items-center">
        <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Role Management</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="roleManager()" class="space-y-4 lg:space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Manajemen Role Keuangan</h2>
            <p class="text-sm text-muted">Kelola hak akses pengguna untuk modul keuangan (Administrator dan User)</p>
        </div>
        
        <!-- Desktop Actions -->
        <div class="hidden sm:flex items-center justify-end space-x-3">
            <button @click="exportRoles" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor Role
            </button>
            <button @click="openUserModal" class="btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah User
            </button>
        </div>

        <!-- Mobile Actions -->
        <div class="sm:hidden grid grid-cols-2 gap-3">
            <button @click="exportRoles" class="btn-secondary flex items-center justify-center text-sm py-3 px-4">
                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="truncate">Ekspor</span>
            </button>
            <button @click="openUserModal" class="btn-primary flex items-center justify-center text-sm py-3 px-4">
                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="truncate">Tambah User</span>
            </button>
        </div>
    </div>

    <!-- Role Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">Administrator</p>
                    <p class="text-2xl lg:text-3xl font-bold text-foreground" x-text="roleStats.administrators"></p>
                    <p class="text-xs text-muted mt-1">
                        <span class="text-blue-600">Full access ke semua modul</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">User</p>
                    <p class="text-2xl lg:text-3xl font-bold text-foreground" x-text="roleStats.users"></p>
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-foreground mb-2">Cari User</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           x-model="filters.search"
                           @input.debounce.300ms="filterUsers()"
                           class="input pl-10" 
                           placeholder="Nama, email, atau role...">
                </div>
            </div>

            <!-- Role Filter -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Role</label>
                <select x-model="filters.role" 
                        @change="filterUsers()"
                        class="input">
                    <option value="">Semua Role</option>
                    <option value="administrator">Administrator</option>
                    <option value="user">User</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="p-4 lg:p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-foreground">
                    Daftar User (<span x-text="totalUsers"></span>)
                </h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-muted">Tampilkan:</span>
                    <select x-model="perPage" 
                            @change="loadUsers()"
                            class="input py-1 text-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="user in filteredUsers" :key="user.id">
                        <tr class="hover:bg-surface/50">
                            <td>
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-foreground" x-text="user.name"></div>
                                        <div class="text-sm text-muted" x-text="'ID: ' + user.id"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-foreground" x-text="user.email"></span>
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
                                <span class="text-foreground" x-text="formatDate(user.created_at)"></span>
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <button @click="editUser(user)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button @click="toggleUserStatus(user)"
                                            class="p-1 text-muted hover:text-foreground">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
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
            <template x-for="user in filteredUsers" :key="user.id">
                <div class="card rounded-xl p-4 shadow-sm">
                    <!-- Header with role -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-foreground text-base" x-text="user.name"></h3>
                                <p class="text-sm text-muted" x-text="user.email"></p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                              :class="getStatusClass(user.status)"
                              x-text="getStatusText(user.status)">
                        </span>
                    </div>

                    <!-- User Details -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Role</span>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                      :class="getRoleClass(user.role)"
                                      x-text="getRoleText(user.role)">
                                </span>
                            </div>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-muted uppercase tracking-wide">Dibuat</span>
                            <p class="text-sm text-foreground mt-1" x-text="formatDate(user.created_at)"></p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-1">
                        <button @click="editUser(user)"
                                class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button @click="toggleUserStatus(user)"
                                class="mobile-action-button p-2.5 text-muted hover:text-foreground bg-background rounded-lg border border-border hover:border-primary/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="filteredUsers.length === 0" class="text-center py-12">
            <svg class="w-16 h-16 text-muted mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
            <h3 class="text-lg font-medium text-foreground mb-2">Tidak ada user</h3>
            <p class="text-muted mb-4">Belum ada user yang dibuat atau sesuai dengan filter.</p>
            <button @click="openUserModal" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah User Pertama
            </button>
        </div>

        <!-- Pagination -->
        <div class="p-4 lg:p-6 border-t border-border">
            <!-- Mobile Pagination -->
            <div class="lg:hidden">
                <div class="text-center text-sm text-muted mb-4">
                    Halaman <span x-text="currentPage"></span> dari <span x-text="lastPage"></span>
                    (<span x-text="totalUsers"></span> user)
                </div>
                <div class="flex items-center justify-center space-x-2">
                    <button @click="previousPage()" 
                            :disabled="currentPage === 1"
                            class="flex items-center justify-center w-12 h-12 rounded-lg border border-border bg-background text-muted hover:text-foreground disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button @click="nextPage()" 
                            :disabled="currentPage === lastPage"
                            class="flex items-center justify-center w-12 h-12 rounded-lg border border-border bg-background text-muted hover:text-foreground disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Desktop Pagination -->
            <div class="hidden lg:flex items-center justify-between">
                <div class="text-sm text-muted">
                    Menampilkan <span x-text="((currentPage - 1) * perPage) + 1"></span> sampai 
                    <span x-text="Math.min(currentPage * perPage, totalUsers)"></span> dari 
                    <span x-text="totalUsers"></span> user
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="previousPage()" 
                            :disabled="currentPage === 1"
                            class="btn-secondary text-sm py-1 px-3 disabled:opacity-50 disabled:cursor-not-allowed">
                        Sebelumnya
                    </button>
                    <button @click="nextPage()" 
                            :disabled="currentPage === lastPage"
                            class="btn-secondary text-sm py-1 px-3 disabled:opacity-50 disabled:cursor-not-allowed">
                        Selanjutnya
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit User Modal -->
    <div x-show="showUserModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50" x-cloak>
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-background">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-foreground" x-text="editMode ? 'Edit User' : 'Tambah User Baru'"></h3>
                <button @click="closeUserModal" class="text-muted hover:text-foreground">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="saveUser" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Nama Lengkap *</label>
                        <input type="text" x-model="userForm.name" required class="input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Email *</label>
                        <input type="email" x-model="userForm.email" required class="input">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">Role *</label>
                        <select x-model="userForm.role" required class="input">
                            <option value="">Pilih Role</option>
                            <option value="administrator">Administrator</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <div x-show="!editMode">
                        <label class="block text-sm font-medium text-foreground mb-2">Password *</label>
                        <input type="password" x-model="userForm.password" :required="!editMode" class="input">
                    </div>
                </div>

                <div x-show="editMode">
                    <label class="block text-sm font-medium text-foreground mb-2">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                    <input type="password" x-model="userForm.password" class="input">
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

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="closeUserModal" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary" :disabled="loading">
                        <span x-show="loading" class="mr-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </span>
                        <span x-text="editMode ? 'Update User' : 'Simpan User'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function roleManager() {
    return {
        users: [],
        filteredUsers: [],
        roleStats: {
            administrators: 2,
            users: 8
        },
        filters: {
            search: '',
            role: ''
        },
        showUserModal: false,
        selectedUser: null,
        editMode: false,
        loading: false,
        currentPage: 1,
        perPage: 25,
        totalUsers: 0,
        lastPage: 1,
        userForm: {
            name: '',
            email: '',
            role: '',
            password: ''
        },

        init() {
            this.loadUsers();
this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal menyimpan user'
                });
            } finally {
                this.loading = false;
            }
        },

        async toggleUserStatus(user) {
            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: `Status user ${user.name} berhasil diubah`
                });
                this.loadUsers();
            } catch (error) {
                console.error('Error toggling user status:', error);
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal mengubah status user'
                });
            }
        },

        async exportRoles() {
            try {
                this.$store.notifications.add({
                    type: 'success',
                    title: 'Berhasil!',
                    message: 'Data role berhasil diekspor'
                });
            } catch (error) {
                console.error('Error exporting roles:', error);
                this.$store.notifications.add({
                    type: 'error',
                    title: 'Gagal!',
                    message: 'Gagal mengekspor data role'
                });
            }
        },

        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.loadUsers();
            }
        },

        nextPage() {
            if (this.currentPage < this.lastPage) {
                this.currentPage++;
                this.loadUsers();
            }
        },

        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('id-ID');
        },

        getRoleClass(role) {
            const classes = {
                'administrator': 'bg-red-100 text-red-800',
                'user': 'bg-green-100 text-green-800'
            };
            return classes[role] || 'bg-gray-100 text-gray-800';
        },

        getRoleText(role) {
            const texts = {
                'administrator': 'Administrator',
                'user': 'User'
            };
            return texts[role] || role;
        },

        getStatusClass(status) {
            const classes = {
                'active': 'bg-green-100 text-green-800',
                'inactive': 'bg-red-100 text-red-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },

        getStatusText(status) {
            const texts = {
                'active': 'Aktif',
                'inactive': 'Tidak Aktif'
            };
            return texts[status] || status;
        }
    }
}
</script>
@endsection