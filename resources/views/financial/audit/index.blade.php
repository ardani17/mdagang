@extends('layouts.dashboard')

@section('title', 'Audit Trail')

@section('content')
<div x-data="auditManager()" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-foreground">Audit Trail</h1>
            <p class="text-muted">Lacak semua aktivitas dan perubahan sistem</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button @click="exportAuditLog" class="btn-secondary">
                <i class="fas fa-download mr-2"></i>
                Ekspor Log
            </button>
            <button @click="clearOldLogs" class="btn-danger" x-show="canClearLogs">
                <i class="fas fa-trash mr-2"></i>
                Bersihkan Log Lama
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-history text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted">Total Aktivitas Hari Ini</p>
                    <p class="text-2xl font-bold text-foreground" x-text="auditStats.today_activities"></p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted">Pengguna Aktif</p>
                    <p class="text-2xl font-bold text-foreground" x-text="auditStats.active_users"></p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted">Aktivitas Berisiko</p>
                    <p class="text-2xl font-bold text-foreground" x-text="auditStats.high_risk_activities"></p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-shield-alt text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-muted">Pelanggaran Keamanan</p>
                    <p class="text-2xl font-bold text-foreground" x-text="auditStats.security_violations"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Cari Aktivitas</label>
                <input type="text" x-model="filters.search" @input="filterAuditLogs"
                       placeholder="Cari pengguna, aksi, atau deskripsi..."
                       class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Pengguna</label>
                <select x-model="filters.user" @change="filterAuditLogs"
                        class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                    <option value="">Semua Pengguna</option>
                    <option value="Admin System">Admin System</option>
                    <option value="Manager Toko">Manager Toko</option>
                    <option value="Staff Gudang">Staff Gudang</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Jenis Aktivitas</label>
                <select x-model="filters.action_type" @change="filterAuditLogs"
                        class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                    <option value="">Semua Jenis</option>
                    <option value="create">Buat</option>
                    <option value="update">Ubah</option>
                    <option value="delete">Hapus</option>
                    <option value="login">Login</option>
                    <option value="logout">Logout</option>
                    <option value="export">Ekspor</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Modul</label>
                <select x-model="filters.module" @change="filterAuditLogs"
                        class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
                    <option value="">Semua Modul</option>
                    <option value="financial">Keuangan</option>
                    <option value="inventory">Inventori</option>
                    <option value="sales">Penjualan</option>
                    <option value="users">Pengguna</option>
                    <option value="settings">Pengaturan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Dari Tanggal</label>
                <input type="date" x-model="filters.date_from" @change="filterAuditLogs"
                       class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Sampai Tanggal</label>
                <input type="date" x-model="filters.date_to" @change="filterAuditLogs"
                       class="w-full px-3 py-2 border border-border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-surface text-foreground">
            </div>
        </div>
    </div>

    <!-- Audit Logs -->
    <div class="card">
        <div class="px-6 py-4 border-b border-border">
            <h2 class="text-lg font-semibold text-foreground">Log Aktivitas</h2>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-surface">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Waktu
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Pengguna
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Aktivitas
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Modul
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Deskripsi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            IP Address
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-border">
                    <template x-for="log in filteredLogs" :key="log.id">
                        <tr class="hover:bg-surface">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground" x-text="formatDateTime(log.created_at)"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-600 text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-foreground" x-text="log.user_name"></div>
                                        <div class="text-sm text-muted" x-text="log.user_role"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getActionTypeClass(log.action_type)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" x-text="getActionTypeText(log.action_type)"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getModuleClass(log.module)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" x-text="getModuleText(log.module)"></span>
                            </td>
                            <td class="px-6 py-4 text-sm text-foreground" x-text="log.description"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-muted" x-text="log.ip_address"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getRiskLevelClass(log.risk_level)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" x-text="getRiskLevelText(log.risk_level)"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="viewLogDetails(log)" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden">
            <template x-for="log in filteredLogs" :key="log.id">
                <div class="p-4 border-b border-border">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8">
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600 text-xs"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-foreground" x-text="log.user_name"></div>
                                <div class="text-xs text-muted" x-text="formatDateTime(log.created_at)"></div>
                            </div>
                        </div>
                        <span :class="getRiskLevelClass(log.risk_level)" class="px-2 py-1 text-xs font-semibold rounded-full" x-text="getRiskLevelText(log.risk_level)"></span>
                    </div>
                    <div class="mb-2">
                        <span :class="getActionTypeClass(log.action_type)" class="px-2 py-1 text-xs font-semibold rounded-full mr-2" x-text="getActionTypeText(log.action_type)"></span>
                        <span :class="getModuleClass(log.module)" class="px-2 py-1 text-xs font-semibold rounded-full" x-text="getModuleText(log.module)"></span>
                    </div>
                    <p class="text-sm text-foreground mb-2" x-text="log.description"></p>
                    <div class="text-xs text-muted">
                        IP: <span x-text="log.ip_address"></span>
                    </div>
                    <div class="mt-2 flex justify-end">
                        <button @click="viewLogDetails(log)" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye mr-1"></i> Detail
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="filteredLogs.length === 0" class="text-center py-12">
            <i class="fas fa-history text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-foreground mb-2">Tidak ada log aktivitas</h3>
            <p class="text-muted mb-4">Belum ada aktivitas yang tercatat atau sesuai dengan filter.</p>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between">
        <div class="text-sm text-muted">
            Menampilkan <span x-text="((currentPage - 1) * perPage) + 1"></span> sampai 
            <span x-text="Math.min(currentPage * perPage, totalLogs)"></span> dari 
            <span x-text="totalLogs"></span> log
        </div>
        <div class="flex space-x-2">
            <button @click="previousPage" :disabled="currentPage === 1" 
                    class="px-3 py-2 text-sm font-medium text-muted bg-surface border border-border rounded-md hover:bg-surface disabled:opacity-50 disabled:cursor-not-allowed">
                Sebelumnya
            </button>
            <button @click="nextPage" :disabled="currentPage === lastPage" 
                    class="px-3 py-2 text-sm font-medium text-muted bg-surface border border-border rounded-md hover:bg-surface disabled:opacity-50 disabled:cursor-not-allowed">
                Selanjutnya
            </button>
        </div>
    </div>

    <!-- Log Detail Modal -->
    <div x-show="showDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-cloak>
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md card">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-foreground">Detail Log Aktivitas</h3>
                <button @click="closeDetailModal" class="text-muted hover:text-foreground">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div x-show="selectedLog" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">Waktu</label>
                        <p class="text-sm text-foreground" x-text="selectedLog ? formatDateTime(selectedLog.created_at) : ''"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">Pengguna</label>
                        <p class="text-sm text-foreground" x-text="selectedLog ? selectedLog.user_name + ' (' + selectedLog.user_role + ')' : ''"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">Aktivitas</label>
                        <span x-show="selectedLog" :class="getActionTypeClass(selectedLog.action_type)" class="px-2 py-1 text-xs font-semibold rounded-full" x-text="getActionTypeText(selectedLog.action_type)"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">Modul</label>
                        <span x-show="selectedLog" :class="getModuleClass(selectedLog.module)" class="px-2 py-1 text-xs font-semibold rounded-full" x-text="getModuleText(selectedLog.module)"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">IP Address</label>
                        <p class="text-sm text-foreground" x-text="selectedLog ? selectedLog.ip_address : ''"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">User Agent</label>
                        <p class="text-sm text-foreground" x-text="selectedLog ? selectedLog.user_agent : ''"></p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">Deskripsi</label>
                    <p class="text-sm text-foreground" x-text="selectedLog ? selectedLog.description : ''"></p>
                </div>

                <div x-show="selectedLog && selectedLog.changes">
                    <label class="block text-sm font-medium text-foreground mb-1">Perubahan Data</label>
                    <div class="bg-surface rounded-lg p-3">
                        <pre class="text-xs text-foreground whitespace-pre-wrap" x-text="selectedLog ? JSON.stringify(selectedLog.changes, null, 2) : ''"></pre>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button @click="closeDetailModal" class="btn-secondary">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function auditManager() {
    return {
        auditLogs: [],
        filteredLogs: [],
        auditStats: {
            today_activities: 0,
            active_users: 0,
            high_risk_activities: 0,
            security_violations: 0
        },
        filters: {
            search: '',
            user: '',
            action_type: '',
            module: '',
            date_from: '',
            date_to: ''
        },
        showDetailModal: false,
        selectedLog: null,
        loading: false,
        currentPage: 1,
        perPage: 25,
        totalLogs: 0,
        lastPage: 1,
        canClearLogs: false,

        init() {
            this.loadAuditLogs();
            this.loadAuditStats();
            this.checkClearLogsPermission();
        },

        async loadAuditLogs() {
            this.loading = true;
            try {
                const response = await fetch('/api/audit/logs');
                const data = await response.json();
                this.auditLogs = data.data;
                this.filteredLogs = [...this.auditLogs];
                this.totalLogs = data.total;
                this.lastPage = data.last_page;
                this.currentPage = data.current_page;
            } catch (error) {
                console.error('Error loading audit logs:', error);
                this.$store.notifications.add('error', 'Gagal memuat log audit');
            } finally {
                this.loading = false;
            }
        },

        async loadAuditStats() {
            try {
                const response = await fetch('/api/audit/stats');
                const data = await response.json();
                this.auditStats = data;
            } catch (error) {
                console.error('Error loading audit stats:', error);
            }
        },

        checkClearLogsPermission() {
            // Check if user has permission to clear logs
            this.canClearLogs = true; // In real app, check user permissions
        },

        filterAuditLogs() {
            let filtered = [...this.auditLogs];

            if (this.filters.search) {
                const search = this.filters.search.toLowerCase();
                filtered = filtered.filter(log => 
                    log.user_name.toLowerCase().includes(search) ||
                    log.description.toLowerCase().includes(search) ||
                    log.action_type.toLowerCase().includes(search)
                );
            }

            if (this.filters.user) {
                filtered = filtered.filter(log => log.user_name === this.filters.user);
            }

            if (this.filters.action_type) {
                filtered = filtered.filter(log => log.action_type === this.filters.action_type);
            }

            if (this.filters.module) {
                filtered = filtered.filter(log => log.module === this.filters.module);
            }

            if (this.filters.date_from) {
                filtered = filtered.filter(log => log.created_at >= this.filters.date_from);
            }

            if (this.filters.date_to) {
                filtered = filtered.filter(log => log.created_at <= this.filters.date_to + ' 23:59:59');
            }

            this.filteredLogs = filtered;
        },

        viewLogDetails(log) {
            this.selectedLog = log;
            this.showDetailModal = true;
        },

        closeDetailModal() {
            this.showDetailModal = false;
            this.selectedLog = null;
        },

        async exportAuditLog() {
            try {
                const response = await fetch('/api/audit/export');
                const data = await response.json();
                
                if (data.success) {
                    this.$store.notifications.add('success', 'Log audit berhasil diekspor');
                } else {
                    this.$store.notifications.add('error', 'Gagal mengekspor log audit');
                }
            } catch (error) {
                console.error('Error exporting audit log:', error);
                this.$store.notifications.add('error', 'Gagal mengekspor log audit');
            }
        },

        async clearOldLogs() {
            if (!confirm('Apakah Anda yakin ingin menghapus log lama? Tindakan ini tidak dapat dibatalkan.')) {
                return;
            }

            try {
                const response = await fetch('/api/audit/clear-old', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    this.$store.notifications.add('success', 'Log lama berhasil dihapus');
                    this.loadAuditLogs();
                    this.loadAuditStats();
                } else {
                    this.$store.notifications.add('error', 'Gagal menghapus log lama');
                }
            } catch (error) {
                console.error('Error clearing old logs:', error);
                this.$store.notifications.add('error', 'Gagal menghapus log lama');
            }
        },

        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.loadAuditLogs();
            }
        },

        nextPage() {
            if (this.currentPage < this.lastPage) {
                this.currentPage++;
                this.loadAuditLogs();
            }
        },

        formatDateTime(datetime) {
            if (!datetime) return '-';
            return new Date(datetime).toLocaleString('id-ID');
        },

        getActionTypeClass(actionType) {
            const classes = {
                'create': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                'update': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                'delete': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                'login': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                'logout': 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
                'export': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
            };
            return classes[actionType] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
        },

        getActionTypeText(actionType) {
            const texts = {
                'create': 'Buat',
                'update': 'Ubah',
                'delete': 'Hapus',
                'login': 'Login',
                'logout': 'Logout',
                'export':
getActionTypeText(actionType) {
            const texts = {
                'create': 'Buat',
                'update': 'Ubah',
                'delete': 'Hapus',
                'login': 'Login',
                'logout': 'Logout',
                'export': 'Ekspor'
            };
            return texts[actionType] || actionType;
        },

        getModuleClass(module) {
            const classes = {
                'financial': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                'inventory': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                'sales': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                'users': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                'settings': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
            };
            return classes[module] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
        },

        getModuleText(module) {
            const texts = {
                'financial': 'Keuangan',
                'inventory': 'Inventori',
                'sales': 'Penjualan',
                'users': 'Pengguna',
                'settings': 'Pengaturan'
            };
            return texts[module] || module;
        },

        getRiskLevelClass(riskLevel) {
            const classes = {
                'low': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                'medium': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                'high': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                'critical': 'bg-red-200 text-red-900 dark:bg-red-800 dark:text-red-200'
            };
            return classes[riskLevel] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
        },

        getRiskLevelText(riskLevel) {
            const texts = {
                'low': 'Rendah',
                'medium': 'Sedang',
                'high': 'Tinggi',
                'critical': 'Kritis'
            };
            return texts[riskLevel] || riskLevel;
        }
    }
}
</script>
@endsection