import './bootstrap';
import Alpine from 'alpinejs';

// Theme Management
Alpine.store('theme', {
    current: localStorage.getItem('theme') || 'light',
    
    init() {
        this.apply();
        
        // Listen for system theme changes
        if (this.current === 'system') {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                this.apply();
            });
        }
    },
    
    toggle() {
        const themes = ['light', 'dark', 'system'];
        const currentIndex = themes.indexOf(this.current);
        this.current = themes[(currentIndex + 1) % themes.length];
        localStorage.setItem('theme', this.current);
        this.apply();
    },
    
    setTheme(theme) {
        this.current = theme;
        localStorage.setItem('theme', theme);
        this.apply();
    },
    
    apply() {
        const root = document.documentElement;
        
        if (this.current === 'dark' ||
            (this.current === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }
    },
    
    isDark() {
        return this.current === 'dark' ||
               (this.current === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
    }
});

// Notification System
Alpine.store('notifications', {
    items: [],
    
    add(notification) {
        const id = Date.now();
        const item = {
            id,
            type: notification.type || 'info',
            title: notification.title || '',
            message: notification.message || '',
            duration: notification.duration || 5000,
            persistent: notification.persistent || false
        };
        
        this.items.push(item);
        
        if (!item.persistent) {
            setTimeout(() => {
                this.remove(id);
            }, item.duration);
        }
        
        return id;
    },
    
    remove(id) {
        this.items = this.items.filter(item => item.id !== id);
    },
    
    clear() {
        this.items = [];
    },
    
    success(message, title = 'Success') {
        return this.add({ type: 'success', title, message });
    },
    
    error(message, title = 'Error') {
        return this.add({ type: 'error', title, message, duration: 8000 });
    },
    
    warning(message, title = 'Warning') {
        return this.add({ type: 'warning', title, message });
    },
    
    info(message, title = 'Info') {
        return this.add({ type: 'info', title, message });
    }
});

// Modal System
Alpine.store('modal', {
    isOpen: false,
    component: null,
    data: {},
    
    open(component, data = {}) {
        this.component = component;
        this.data = data;
        this.isOpen = true;
        document.body.style.overflow = 'hidden';
    },
    
    close() {
        this.isOpen = false;
        this.component = null;
        this.data = {};
        document.body.style.overflow = 'auto';
    }
});

// Sidebar Management
Alpine.store('sidebar', {
    isOpen: false,
    isMobile: window.innerWidth < 768,
    
    init() {
        window.addEventListener('resize', () => {
            this.isMobile = window.innerWidth < 768;
            if (!this.isMobile) {
                this.isOpen = true;
            }
        });
        
        // Set initial state
        this.isOpen = !this.isMobile;
    },
    
    toggle() {
        this.isOpen = !this.isOpen;
    },
    
    close() {
        this.isOpen = false;
    }
});

// Loading State Management
Alpine.store('loading', {
    isLoading: false,
    message: 'Loading...',
    
    show(message = 'Loading...') {
        this.message = message;
        this.isLoading = true;
    },
    
    hide() {
        this.isLoading = false;
        this.message = 'Loading...';
    }
});

// Form Utilities
Alpine.data('form', (initialData = {}) => ({
    data: { ...initialData },
    errors: {},
    isSubmitting: false,
    
    setData(key, value) {
        this.data[key] = value;
        // Clear error when user starts typing
        if (this.errors[key]) {
            delete this.errors[key];
        }
    },
    
    setErrors(errors) {
        this.errors = errors;
    },
    
    clearErrors() {
        this.errors = {};
    },
    
    hasError(field) {
        return this.errors[field] !== undefined;
    },
    
    getError(field) {
        return this.errors[field] ? this.errors[field][0] : '';
    },
    
    async submit(url, options = {}) {
        this.isSubmitting = true;
        this.clearErrors();
        
        try {
            const response = await fetch(url, {
                method: options.method || 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    ...options.headers
                },
                body: JSON.stringify(this.data)
            });
            
            const result = await response.json();
            
            if (!response.ok) {
                if (response.status === 422) {
                    this.setErrors(result.errors || {});
                }
                throw new Error(result.message || 'An error occurred');
            }
            
            if (options.onSuccess) {
                options.onSuccess(result);
            }
            
            return result;
        } catch (error) {
            if (options.onError) {
                options.onError(error);
            } else {
                Alpine.store('notifications').error(error.message);
            }
            throw error;
        } finally {
            this.isSubmitting = false;
        }
    }
}));

// Table Utilities
Alpine.data('dataTable', (initialData = []) => ({
    data: initialData,
    filteredData: [],
    searchQuery: '',
    sortField: '',
    sortDirection: 'asc',
    currentPage: 1,
    perPage: 10,
    
    init() {
        this.filteredData = [...this.data];
    },
    
    search() {
        if (!this.searchQuery) {
            this.filteredData = [...this.data];
        } else {
            this.filteredData = this.data.filter(item => {
                return Object.values(item).some(value =>
                    String(value).toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            });
        }
        this.currentPage = 1;
    },
    
    sort(field) {
        if (this.sortField === field) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortField = field;
            this.sortDirection = 'asc';
        }
        
        this.filteredData.sort((a, b) => {
            let aVal = a[field];
            let bVal = b[field];
            
            if (typeof aVal === 'string') {
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
            }
            
            if (this.sortDirection === 'asc') {
                return aVal > bVal ? 1 : -1;
            } else {
                return aVal < bVal ? 1 : -1;
            }
        });
    },
    
    get paginatedData() {
        const start = (this.currentPage - 1) * this.perPage;
        const end = start + this.perPage;
        return this.filteredData.slice(start, end);
    },
    
    get totalPages() {
        return Math.ceil(this.filteredData.length / this.perPage);
    },
    
    nextPage() {
        if (this.currentPage < this.totalPages) {
            this.currentPage++;
        }
    },
    
    prevPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
        }
    },
    
    goToPage(page) {
        if (page >= 1 && page <= this.totalPages) {
            this.currentPage = page;
        }
    }
}));

// Initialize Alpine
window.Alpine = Alpine;
Alpine.start();

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', () => {
    Alpine.store('theme').init();
    Alpine.store('sidebar').init();
});

// Global utilities
window.formatCurrency = (amount, currency = 'IDR') => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: currency,
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
};

window.formatDate = (date, options = {}) => {
    const defaultOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    
    return new Intl.DateTimeFormat('id-ID', { ...defaultOptions, ...options }).format(new Date(date));
};

window.formatNumber = (number) => {
    return new Intl.NumberFormat('id-ID').format(number);
};

// CSRF Token setup for all AJAX requests
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}
