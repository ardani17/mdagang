<!-- Confirmation Dialogs Component -->
<div x-data="confirmationDialogs()" x-init="init()">
    <!-- Confirmation Dialog Modal -->
    <div x-show="showDialog"
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
         :aria-labelledby="'dialog-title-' + (currentDialog ? currentDialog.id : '')">
        
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
                 @click="currentDialog?.allowBackdropClose !== false && cancelDialog()"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <!-- Dialog content -->
            <div x-show="showDialog"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-surface rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
                 @keydown.escape="(currentDialog && currentDialog.allowEscapeClose !== false) && cancelDialog()">
                
                <div class="sm:flex sm:items-start">
                    <!-- Icon -->
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                         :class="getIconClass(currentDialog ? currentDialog.type : '')">
                        
                        <!-- Delete/Danger Icon -->
                        <svg x-show="(currentDialog ? currentDialog.type : '') === 'delete' || (currentDialog ? currentDialog.type : '') === 'danger'"
                             class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        
                        <!-- Warning Icon -->
                        <svg x-show="(currentDialog ? currentDialog.type : '') === 'warning'"
                             class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        
                        <!-- Info Icon -->
                        <svg x-show="(currentDialog ? currentDialog.type : '') === 'info'"
                             class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        
                        <!-- Success Icon -->
                        <svg x-show="(currentDialog ? currentDialog.type : '') === 'success'"
                             class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    
                    <!-- Content -->
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 :id="'dialog-title-' + (currentDialog ? currentDialog.id : '')"
                            class="text-lg leading-6 font-medium text-foreground"
                            x-text="(currentDialog ? currentDialog.title : '')"></h3>
                        
                        <div class="mt-2">
                            <p class="text-sm text-muted" x-text="(currentDialog ? currentDialog.message : '')"></p>
                            
                            <!-- Additional details -->
                            <div x-show="(currentDialog ? currentDialog.details : null)" class="mt-3 p-3 bg-border/20 rounded-lg">
                                <div x-show="(currentDialog ? (currentDialog.details ? currentDialog.details.items : null) : null)" class="space-y-1">
                                    <template x-for="item in (currentDialog ? (currentDialog.details ? currentDialog.details.items : []) : [])" :key="item">
                                        <div class="text-sm text-foreground flex justify-between">
                                            <span x-text="item.label"></span>
                                            <span x-text="item.value" class="font-medium"></span>
                                        </div>
                                    </template>
                                </div>
                                
                                <div x-show="(currentDialog ? (currentDialog.details ? currentDialog.details.warning : null) : null)"
                                     class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800">
                                    <div class="flex">
                                        <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                        <span x-text="(currentDialog ? (currentDialog.details ? currentDialog.details.warning : '') : '')"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Input field for confirmation -->
                            <div x-show="currentDialog && currentDialog.requireConfirmation" class="mt-4">
                                <label class="block text-sm font-medium text-foreground mb-2"
                                       x-text="(currentDialog.confirmationLabel || 'Ketik \"KONFIRMASI\" untuk melanjutkan:')"></label>
                                <input type="text"
                                       x-model="confirmationInput"
                                       :placeholder="(currentDialog.confirmationPlaceholder || 'KONFIRMASI')"
                                       class="input"
                                       @keydown.enter="confirmDialog()"
                                       :aria-describedby="'confirmation-help-' + (currentDialog.id || '')">
                                <p :id="'confirmation-help-' + (currentDialog.id || '')"
                                   class="mt-1 text-xs text-muted">
                                    Ketik persis seperti yang diminta untuk mengaktifkan tombol konfirmasi
                                </p>
                            </div>
                            
                            <!-- Checkbox confirmations -->
                            <div x-show="(currentDialog ? currentDialog.checkboxes : []) && (currentDialog ? currentDialog.checkboxes : []).length > 0" class="mt-4 space-y-2">
                                <template x-for="(checkbox, index) in (currentDialog ? currentDialog.checkboxes : [])" :key="index">
                                    <label class="flex items-start">
                                        <input type="checkbox"
                                               x-model="checkboxStates[index]"
                                               class="rounded border-border text-primary focus:ring-primary mt-0.5">
                                        <span class="ml-2 text-sm text-foreground" x-text="checkbox.label"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action buttons -->
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button"
                            @click="confirmDialog()"
                            :disabled="!canConfirm"
                            :class="getConfirmButtonClass(currentDialog ? currentDialog.type : '')"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
                            x-text="(currentDialog ? currentDialog.confirmText : '') || 'Konfirmasi'">
                    </button>
                    
                    <button type="button"
                            @click="cancelDialog()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-border shadow-sm px-4 py-2 bg-surface text-base font-medium text-foreground hover:bg-border focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:w-auto sm:text-sm"
                            x-text="(currentDialog ? currentDialog.cancelText : '') || 'Batal'">
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Loading overlay for async operations -->
    <div x-show="isProcessing"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-60 flex items-center justify-center bg-black bg-opacity-50"
         style="display: none;">
        <div class="bg-surface rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary"></div>
                <span class="text-foreground font-medium" x-text="processingMessage || 'Memproses...'"></span>
            </div>
        </div>
    </div>
</div>

<script>
function confirmationDialogs() {
    return {
        showDialog: false,
        currentDialog: null,
        confirmationInput: '',
        checkboxStates: [],
        isProcessing: false,
        processingMessage: '',
        
        init() {
            // Listen for confirmation dialog events
            window.addEventListener('show-confirmation', (event) => {
                this.showConfirmation(event.detail);
            });
            
            // Global confirmation function
            window.confirmAction = (config) => {
                return this.showConfirmation(config);
            };
        },
        
        get canConfirm() {
            if (!this.currentDialog) return false;
            
            // Check text confirmation
            if (this.currentDialog.requireConfirmation) {
                const expected = this.currentDialog.confirmationText || 'KONFIRMASI';
                if (this.confirmationInput.trim() !== expected) {
                    return false;
                }
            }
            
            // Check checkbox confirmations
            if (this.currentDialog.checkboxes && this.currentDialog.checkboxes.length > 0) {
                const requiredChecked = this.currentDialog.checkboxes.filter(cb => cb.required !== false).length;
                const actualChecked = this.checkboxStates.filter(Boolean).length;
                if (actualChecked < requiredChecked) {
                    return false;
                }
            }
            
            return true;
        },
        
        showConfirmation(config) {
            return new Promise((resolve, reject) => {
                this.currentDialog = {
                    id: Date.now(),
                    type: config.type || 'warning',
                    title: config.title || 'Konfirmasi Aksi',
                    message: config.message || 'Apakah Anda yakin ingin melanjutkan?',
                    details: config.details || null,
                    confirmText: config.confirmText || 'Ya, Lanjutkan',
                    cancelText: config.cancelText || 'Batal',
                    requireConfirmation: config.requireConfirmation || false,
                    confirmationText: config.confirmationText || 'KONFIRMASI',
                    confirmationLabel: config.confirmationLabel || '',
                    confirmationPlaceholder: config.confirmationPlaceholder || '',
                    checkboxes: config.checkboxes || [],
                    allowBackdropClose: config.allowBackdropClose !== false,
                    allowEscapeClose: config.allowEscapeClose !== false,
                    onConfirm: resolve,
                    onCancel: reject,
                    asyncAction: config.asyncAction || null
                };
                
                // Reset states
                this.confirmationInput = '';
                this.checkboxStates = new Array((this.currentDialog.checkboxes || []).length).fill(false);
                
                this.showDialog = true;
                
                // Focus management
                $nextTick(() => {
                    const firstInput = document.querySelector('[x-model="confirmationInput"]');
                    if (firstInput) {
                        firstInput.focus();
                    }
                });
            });
        },
        
        async confirmDialog() {
            if (!this.canConfirm) return;
            
            try {
                // If there's an async action, show loading and execute it
                if (this.currentDialog.asyncAction) {
                    this.isProcessing = true;
                    this.processingMessage = this.currentDialog.processingMessage || 'Memproses...';
                    
                    await this.currentDialog.asyncAction();
                    
                    this.isProcessing = false;
                }
                
                this.currentDialog.onConfirm(true);
                this.closeDialog();
                
            } catch (error) {
                this.isProcessing = false;
                this.currentDialog.onCancel(error);
                this.closeDialog();
            }
        },
        
        cancelDialog() {
            this.currentDialog.onCancel(false);
            this.closeDialog();
        },
        
        closeDialog() {
            this.showDialog = false;
            this.currentDialog = null;
            this.confirmationInput = '';
            this.checkboxStates = [];
        },
        
        getIconClass(type) {
            const classes = {
                delete: 'bg-red-100 text-red-600',
                danger: 'bg-red-100 text-red-600',
                warning: 'bg-yellow-100 text-yellow-600',
                info: 'bg-blue-100 text-blue-600',
                success: 'bg-green-100 text-green-600'
            };
            return classes[type] || classes.warning;
        },
        
        getConfirmButtonClass(type) {
            const baseClasses = 'disabled:opacity-50 disabled:cursor-not-allowed';
            const typeClasses = {
                delete: 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
                danger: 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
                warning: 'bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500',
                info: 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
                success: 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500'
            };
            return `${baseClasses} ${typeClasses[type] || typeClasses.warning}`;
        }
    }
}

// Global helper functions for easy use
window.confirmDelete = function(config) {
    return window.confirmAction({
        type: 'delete',
        title: 'Konfirmasi Hapus',
        message: config.message || 'Data yang dihapus tidak dapat dikembalikan.',
        confirmText: 'Ya, Hapus',
        requireConfirmation: config.requireConfirmation || false,
        ...config
    });
};

window.confirmDangerousAction = function(config) {
    return window.confirmAction({
        type: 'danger',
        title: 'Peringatan!',
        confirmText: 'Ya, Saya Mengerti',
        requireConfirmation: true,
        confirmationText: 'SAYA MENGERTI',
        confirmationLabel: 'Ketik "SAYA MENGERTI" untuk melanjutkan:',
        ...config
    });
};

window.confirmFinancialAction = function(config) {
    return window.confirmAction({
        type: 'warning',
        title: 'Konfirmasi Transaksi Keuangan',
        checkboxes: [
            { label: 'Saya telah memeriksa semua detail transaksi', required: true },
            { label: 'Saya memahami dampak dari aksi ini', required: true }
        ],
        ...config
    });
};
</script>