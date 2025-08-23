<!-- Financial Notifications Component -->
<div x-data="financialNotifications()" x-init="init()" class="fixed top-4 right-4 z-50 space-y-3 max-w-sm w-full">
    <!-- Notification Container -->
    <template x-for="notification in notifications" :key="notification.id">
        <div x-show="notification.visible"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             class="card shadow-lg border-l-4 overflow-hidden"
             :class="getNotificationClass(notification.type)"
             role="alert"
             :aria-live="notification.priority === 'high' ? 'assertive' : 'polite'"
             :aria-label="notification.title">
            
            <div class="p-4">
                <div class="flex items-start">
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                        <!-- Success Icon -->
                        <svg x-show="notification.type === 'success'" class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        
                        <!-- Warning Icon -->
                        <svg x-show="notification.type === 'warning'" class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        
                        <!-- Error Icon -->
                        <svg x-show="notification.type === 'error'" class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        
                        <!-- Info Icon -->
                        <svg x-show="notification.type === 'info'" class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        
                        <!-- Critical Icon -->
                        <svg x-show="notification.type === 'critical'" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    
                    <!-- Content -->
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium text-foreground" x-text="notification.title"></p>
                        <p class="mt-1 text-sm text-muted" x-text="notification.message"></p>
                        
                        <!-- Financial Details -->
                        <div x-show="notification.financial_data" class="mt-2 text-xs text-muted">
                            <div x-show="notification.financial_data?.amount" class="flex justify-between">
                                <span>Jumlah:</span>
                                <span class="font-medium" x-text="formatCurrency(notification.financial_data?.amount)"></span>
                            </div>
                            <div x-show="notification.financial_data?.due_date" class="flex justify-between">
                                <span>Jatuh Tempo:</span>
                                <span class="font-medium" x-text="formatDate(notification.financial_data?.due_date)"></span>
                            </div>
                            <div x-show="notification.financial_data?.reference" class="flex justify-between">
                                <span>Referensi:</span>
                                <span class="font-medium" x-text="notification.financial_data?.reference"></span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div x-show="notification.actions && notification.actions.length > 0" class="mt-3 flex space-x-2">
                            <template x-for="action in notification.actions" :key="action.label">
                                <button @click="handleAction(notification, action)"
                                        class="text-xs px-2 py-1 rounded border transition-colors"
                                        :class="action.primary ? 'bg-primary text-primary-foreground border-primary hover:bg-primary/90' : 'bg-surface text-foreground border-border hover:bg-border'"
                                        x-text="action.label">
                                </button>
                            </template>
                        </div>
                        
                        <!-- Timestamp -->
                        <div class="mt-2 text-xs text-muted" x-text="formatTimestamp(notification.timestamp)"></div>
                    </div>
                    
                    <!-- Close Button -->
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="dismissNotification(notification.id)"
                                class="bg-surface rounded-md inline-flex text-muted hover:text-foreground focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                                :aria-label="'Tutup notifikasi: ' + notification.title">
                            <span class="sr-only">Tutup</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Progress Bar for Auto-dismiss -->
                <div x-show="notification.auto_dismiss && notification.progress !== undefined" class="mt-2">
                    <div class="w-full bg-border rounded-full h-1">
                        <div class="bg-primary h-1 rounded-full transition-all duration-100"
                             :style="'width: ' + (100 - notification.progress) + '%'"></div>
                    </div>
                </div>
            </div>
        </div>
    </template>
    
    <!-- Notification Center Toggle -->
    <div class="fixed bottom-4 right-4">
        <button @click="toggleNotificationCenter()"
                class="bg-primary text-primary-foreground p-3 rounded-full shadow-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                :class="{ 'animate-pulse': hasUnreadNotifications }"
                aria-label="Buka pusat notifikasi">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span x-show="unreadCount > 0" 
                  class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
                  x-text="unreadCount > 99 ? '99+' : unreadCount"
                  aria-label="Notifikasi belum dibaca"></span>
        </button>
    </div>
    
    <!-- Notification Center Modal -->
    <div x-show="showNotificationCenter"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;"
         role="dialog"
         aria-modal="true"
         aria-labelledby="notification-center-title">
        
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="toggleNotificationCenter()"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-surface rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-surface px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 id="notification-center-title" class="text-lg font-medium text-foreground">Pusat Notifikasi</h3>
                        <div class="flex items-center space-x-2">
                            <button @click="markAllAsRead()" 
                                    class="text-sm text-primary hover:text-primary/80"
                                    :disabled="unreadCount === 0">
                                Tandai Semua Dibaca
                            </button>
                            <button @click="toggleNotificationCenter()" 
                                    class="text-muted hover:text-foreground"
                                    aria-label="Tutup pusat notifikasi">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Notification History -->
                    <div class="max-h-96 overflow-y-auto space-y-2">
                        <template x-for="notification in notificationHistory" :key="notification.id">
                            <div class="p-3 border border-border rounded-lg"
                                 :class="{ 'bg-primary/5': !notification.read }">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-foreground" x-text="notification.title"></p>
                                        <p class="text-xs text-muted mt-1" x-text="notification.message"></p>
                                        <p class="text-xs text-muted mt-1" x-text="formatTimestamp(notification.timestamp)"></p>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <div x-show="!notification.read" class="w-2 h-2 bg-primary rounded-full" aria-label="Belum dibaca"></div>
                                        <button @click="markAsRead(notification.id)" 
                                                x-show="!notification.read"
                                                class="text-xs text-primary hover:text-primary/80"
                                                aria-label="Tandai sebagai dibaca">
                                            ✓
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="notificationHistory.length === 0" class="text-center py-8 text-muted">
                            <svg class="w-12 h-12 mx-auto mb-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <p>Tidak ada notifikasi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function financialNotifications() {
    return {
        notifications: [],
        notificationHistory: [],
        showNotificationCenter: false,
        unreadCount: 0,
        nextId: 1,
        
        init() {
            // Load notification history from localStorage
            this.loadNotificationHistory();
            
            // Start checking for financial alerts
            this.startFinancialAlertChecking();
            
            // Listen for custom notification events
            window.addEventListener('financial-alert', (event) => {
                this.addNotification(event.detail);
            });
            
            // Demo notifications for testing
            this.addDemoNotifications();
        },
        
        get hasUnreadNotifications() {
            return this.unreadCount > 0;
        },
        
        addNotification(config) {
            const notification = {
                id: this.nextId++,
                type: config.type || 'info',
                title: config.title,
                message: config.message,
                financial_data: config.financial_data || null,
                actions: config.actions || [],
                priority: config.priority || 'normal',
                auto_dismiss: config.auto_dismiss !== false,
                duration: config.duration || (config.priority === 'high' ? 10000 : 5000),
                timestamp: new Date(),
                visible: true,
                read: false,
                progress: 0
            };
            
            this.notifications.push(notification);
            this.notificationHistory.unshift(notification);
            this.unreadCount++;
            
            // Save to localStorage
            this.saveNotificationHistory();
            
            // Auto dismiss
            if (notification.auto_dismiss) {
                this.startAutoDismiss(notification);
            }
            
            // Play notification sound for high priority
            if (notification.priority === 'high') {
                this.playNotificationSound();
            }
        },
        
        startAutoDismiss(notification) {
            const interval = 100;
            const steps = notification.duration / interval;
            let currentStep = 0;
            
            const timer = setInterval(() => {
                currentStep++;
                notification.progress = (currentStep / steps) * 100;
                
                if (currentStep >= steps) {
                    clearInterval(timer);
                    this.dismissNotification(notification.id);
                }
            }, interval);
        },
        
        dismissNotification(id) {
            const index = this.notifications.findIndex(n => n.id === id);
            if (index !== -1) {
                this.notifications[index].visible = false;
                setTimeout(() => {
                    this.notifications.splice(index, 1);
                }, 300);
            }
        },
        
        handleAction(notification, action) {
            if (action.callback) {
                action.callback(notification);
            }
            
            if (action.url) {
                window.location.href = action.url;
            }
            
            if (action.dismiss !== false) {
                this.dismissNotification(notification.id);
            }
        },
        
        getNotificationClass(type) {
            const classes = {
                success: 'border-l-green-400',
                warning: 'border-l-yellow-400',
                error: 'border-l-red-400',
                info: 'border-l-blue-400',
                critical: 'border-l-red-600'
            };
            return classes[type] || classes.info;
        },
        
        toggleNotificationCenter() {
            this.showNotificationCenter = !this.showNotificationCenter;
        },
        
        markAsRead(id) {
            const notification = this.notificationHistory.find(n => n.id === id);
            if (notification && !notification.read) {
                notification.read = true;
                this.unreadCount = Math.max(0, this.unreadCount - 1);
                this.saveNotificationHistory();
            }
        },
        
        markAllAsRead() {
            this.notificationHistory.forEach(notification => {
                if (!notification.read) {
                    notification.read = true;
                }
            });
            this.unreadCount = 0;
            this.saveNotificationHistory();
        },
        
        loadNotificationHistory() {
            try {
                const saved = localStorage.getItem('financial_notifications');
                if (saved) {
                    const data = JSON.parse(saved);
                    this.notificationHistory = data.notifications || [];
                    this.unreadCount = data.unreadCount || 0;
                    this.nextId = data.nextId || 1;
                }
            } catch (error) {
                console.error('Error loading notification history:', error);
            }
        },
        
        saveNotificationHistory() {
            try {
                const data = {
                    notifications: this.notificationHistory.slice(0, 100), // Keep last 100
                    unreadCount: this.unreadCount,
                    nextId: this.nextId
                };
                localStorage.setItem('financial_notifications', JSON.stringify(data));
            } catch (error) {
                console.error('Error saving notification history:', error);
            }
        },
        
        startFinancialAlertChecking() {
            // Check for financial alerts every 5 minutes
            setInterval(() => {
                this.checkFinancialAlerts();
            }, 5 * 60 * 1000);
            
            // Initial check
            setTimeout(() => {
                this.checkFinancialAlerts();
            }, 2000);
        },
        
        async checkFinancialAlerts() {
            try {
                // Check overdue invoices
                const overdueResponse = await fetch('/api/financial/alerts/overdue');
                const overdueData = await overdueResponse.json();
                
                if (overdueData.alerts && overdueData.alerts.length > 0) {
                    overdueData.alerts.forEach(alert => {
                        this.addNotification({
                            type: 'critical',
                            title: 'Invoice Jatuh Tempo',
                            message: `Invoice ${alert.invoice_number} telah jatuh tempo`,
                            financial_data: {
                                amount: alert.amount,
                                due_date: alert.due_date,
                                reference: alert.invoice_number
                            },
                            priority: 'high',
                            actions: [
                                {
                                    label: 'Lihat Invoice',
                                    url: `/invoices/${alert.id}/view`,
                                    primary: true
                                },
                                {
                                    label: 'Kirim Pengingat',
                                    callback: () => this.sendReminder(alert.id)
                                }
                            ]
                        });
                    });
                }
                
                // Check low cash flow
                const cashflowResponse = await fetch('/api/financial/alerts/cashflow');
                const cashflowData = await cashflowResponse.json();
                
                if (cashflowData.low_balance) {
                    this.addNotification({
                        type: 'warning',
                        title: 'Saldo Kas Rendah',
                        message: 'Saldo kas Anda di bawah batas minimum',
                        financial_data: {
                            amount: cashflowData.current_balance
                        },
                        priority: 'high'
                    });
                }
                
                // Check budget alerts
                const budgetResponse = await fetch('/api/financial/alerts/budget');
                const budgetData = await budgetResponse.json();
                
                if (budgetData.alerts && budgetData.alerts.length > 0) {
                    budgetData.alerts.forEach(alert => {
                        this.addNotification({
                            type: 'warning',
                            title: 'Peringatan Budget',
                            message: `Budget ${alert.category} telah mencapai ${alert.percentage}%`,
                            financial_data: {
                                amount: alert.spent_amount
                            }
                        });
                    });
                }
                
            } catch (error) {
                console.error('Error checking financial alerts:', error);
            }
        },
        
        addDemoNotifications() {
            // Demo notifications for testing
            setTimeout(() => {
                this.addNotification({
                    type: 'warning',
                    title: 'Invoice Akan Jatuh Tempo',
                    message: 'Invoice INV-2024-001 akan jatuh tempo dalam 3 hari',
                    financial_data: {
                        amount: 5000000,
                        due_date: '2024-01-20',
                        reference: 'INV-2024-001'
                    },
                    actions: [
                        {
                            label: 'Lihat Invoice',
                            url: '/invoices/1/view',
                            primary: true
                        }
                    ]
                });
            }, 3000);
            
            setTimeout(() => {
                this.addNotification({
                    type: 'success',
                    title: 'Pembayaran Diterima',
                    message: 'Pembayaran dari PT. Maju Jaya telah diterima',
                    financial_data: {
                        amount: 3000000,
                        reference: 'PAY-2024-001'
                    }
                });
            }, 5000);
        },
        
        playNotificationSound() {
            // Create a simple notification sound
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.value = 800;
                oscillator.type = 'sine';
                
                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.5);
            } catch (error) {
                console.log('Audio not supported');
            }
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        },
        
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('id-ID');
        },
        
        formatTimestamp(timestamp) {
            const now = new Date();
            const time = new Date(timestamp);
            const diff = now - time;
            
            if (diff < 60000) return 'Baru saja';
            if (diff < 3600000) return Math.floor(diff / 60000) + ' menit lalu';
            if (diff < 86400000) return Math.floor(diff / 3600000) + ' jam lalu';
            return Math.floor(diff / 86400000) + ' hari lalu';
        },
        
        sendReminder(invoiceId) {
            // Implementation for sending reminder
            console.log('Sending reminder for invoice:', invoiceId);
            this.addNotification({
                type: 'success',
                title: 'Pengingat Terkirim',
                message: 'Pengingat pembayaran telah dikirim ke pelanggan'
            });
        }
    }
}
</script>