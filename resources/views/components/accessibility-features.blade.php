<!-- Accessibility Features Component -->
<div x-data="accessibilityFeatures()" x-init="init()">
    <!-- Skip Links -->
    <div class="sr-only focus:not-sr-only focus:absolute focus:top-0 focus:left-0 z-50">
        <a href="#main-content" 
           class="bg-primary text-primary-foreground px-4 py-2 rounded-br-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
            Lewati ke konten utama
        </a>
        <a href="#main-navigation" 
           class="bg-primary text-primary-foreground px-4 py-2 rounded-br-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary ml-2">
            Lewati ke navigasi
        </a>
    </div>

    <!-- Accessibility Toolbar -->
    <div x-show="showToolbar"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="-translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="-translate-y-full"
         class="fixed top-0 left-0 right-0 z-40 bg-surface border-b border-border shadow-lg"
         role="toolbar"
         aria-label="Alat aksesibilitas">
        
        <div class="container mx-auto px-4 py-2">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center space-x-4 flex-wrap">
                    <!-- Font Size Controls -->
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-foreground">Ukuran Font:</span>
                        <button @click="decreaseFontSize()" 
                                :disabled="fontSize <= minFontSize"
                                class="p-1 rounded border border-border hover:bg-border disabled:opacity-50 disabled:cursor-not-allowed"
                                aria-label="Perkecil ukuran font">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>
                        <span class="text-sm px-2" x-text="fontSize + 'px'"></span>
                        <button @click="increaseFontSize()" 
                                :disabled="fontSize >= maxFontSize"
                                class="p-1 rounded border border-border hover:bg-border disabled:opacity-50 disabled:cursor-not-allowed"
                                aria-label="Perbesar ukuran font">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Contrast Toggle -->
                    <button @click="toggleHighContrast()" 
                            :class="{ 'bg-primary text-primary-foreground': highContrast }"
                            class="px-3 py-1 rounded border border-border hover:bg-border text-sm"
                            :aria-pressed="highContrast"
                            aria-label="Toggle kontras tinggi">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Kontras Tinggi
                    </button>

                    <!-- Focus Indicators -->
                    <button @click="toggleEnhancedFocus()" 
                            :class="{ 'bg-primary text-primary-foreground': enhancedFocus }"
                            class="px-3 py-1 rounded border border-border hover:bg-border text-sm"
                            :aria-pressed="enhancedFocus"
                            aria-label="Toggle indikator fokus yang diperkuat">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Fokus Diperkuat
                    </button>

                    <!-- Motion Reduction -->
                    <button @click="toggleReduceMotion()" 
                            :class="{ 'bg-primary text-primary-foreground': reduceMotion }"
                            class="px-3 py-1 rounded border border-border hover:bg-border text-sm"
                            :aria-pressed="reduceMotion"
                            aria-label="Toggle pengurangan animasi">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                        </svg>
                        Kurangi Animasi
                    </button>

                    <!-- Screen Reader Mode -->
                    <button @click="toggleScreenReaderMode()" 
                            :class="{ 'bg-primary text-primary-foreground': screenReaderMode }"
                            class="px-3 py-1 rounded border border-border hover:bg-border text-sm"
                            :aria-pressed="screenReaderMode"
                            aria-label="Toggle mode pembaca layar">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                        </svg>
                        Mode Pembaca Layar
                    </button>
                </div>

                <!-- Close Toolbar -->
                <button @click="hideToolbar()" 
                        class="p-1 rounded border border-border hover:bg-border"
                        aria-label="Tutup toolbar aksesibilitas">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Accessibility Toggle Button -->
    <button @click="toggleToolbar()" 
            x-show="!showToolbar"
            class="fixed top-4 left-4 z-30 bg-primary text-primary-foreground p-3 rounded-full shadow-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
            aria-label="Buka alat aksesibilitas"
            title="Alat Aksesibilitas">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
        </svg>
    </button>

    <!-- Live Region for Announcements -->
    <div aria-live="polite" aria-atomic="true" class="sr-only" x-text="announcement"></div>
    <div aria-live="assertive" aria-atomic="true" class="sr-only" x-text="urgentAnnouncement"></div>

    <!-- Keyboard Navigation Helper -->
    <div x-show="showKeyboardHelp"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
         style="display: none;"
         role="dialog"
         aria-modal="true"
         aria-labelledby="keyboard-help-title">
        
        <div class="bg-surface rounded-lg p-6 max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 id="keyboard-help-title" class="text-lg font-semibold text-foreground">Pintasan Keyboard</h2>
                <button @click="hideKeyboardHelp()" 
                        class="text-muted hover:text-foreground"
                        aria-label="Tutup bantuan keyboard">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-4">
                <div>
                    <h3 class="font-medium text-foreground mb-2">Navigasi Umum</h3>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-muted">Tab / Shift+Tab</span>
                            <span class="text-foreground">Navigasi maju/mundur</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted">Enter / Space</span>
                            <span class="text-foreground">Aktivasi elemen</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted">Escape</span>
                            <span class="text-foreground">Tutup dialog/modal</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted">Alt + A</span>
                            <span class="text-foreground">Buka alat aksesibilitas</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-medium text-foreground mb-2">Navigasi Tabel</h3>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-muted">Arrow Keys</span>
                            <span class="text-foreground">Navigasi sel tabel</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted">Home / End</span>
                            <span class="text-foreground">Awal/akhir baris</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted">Ctrl + Home/End</span>
                            <span class="text-foreground">Awal/akhir tabel</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-medium text-foreground mb-2">Form</h3>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-muted">Ctrl + Enter</span>
                            <span class="text-foreground">Submit form</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted">Ctrl + Z</span>
                            <span class="text-foreground">Undo</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function accessibilityFeatures() {
    return {
        showToolbar: false,
        showKeyboardHelp: false,
        fontSize: 16,
        minFontSize: 12,
        maxFontSize: 24,
        highContrast: false,
        enhancedFocus: false,
        reduceMotion: false,
        screenReaderMode: false,
        announcement: '',
        urgentAnnouncement: '',
        
        init() {
            // Load saved preferences
            this.loadPreferences();
            
            // Apply initial settings
            this.applySettings();
            
            // Keyboard shortcuts
            this.setupKeyboardShortcuts();
            
            // Auto-detect user preferences
            this.detectUserPreferences();
            
            // Setup focus management
            this.setupFocusManagement();
        },
        
        loadPreferences() {
            try {
                const saved = localStorage.getItem('accessibility_preferences');
                if (saved) {
                    const prefs = JSON.parse(saved);
                    this.fontSize = prefs.fontSize || 16;
                    this.highContrast = prefs.highContrast || false;
                    this.enhancedFocus = prefs.enhancedFocus || false;
                    this.reduceMotion = prefs.reduceMotion || false;
                    this.screenReaderMode = prefs.screenReaderMode || false;
                }
            } catch (error) {
                console.error('Error loading accessibility preferences:', error);
            }
        },
        
        savePreferences() {
            try {
                const prefs = {
                    fontSize: this.fontSize,
                    highContrast: this.highContrast,
                    enhancedFocus: this.enhancedFocus,
                    reduceMotion: this.reduceMotion,
                    screenReaderMode: this.screenReaderMode
                };
                localStorage.setItem('accessibility_preferences', JSON.stringify(prefs));
            } catch (error) {
                console.error('Error saving accessibility preferences:', error);
            }
        },
        
        applySettings() {
            // Apply font size
            document.documentElement.style.fontSize = this.fontSize + 'px';
            
            // Apply high contrast
            document.documentElement.classList.toggle('high-contrast', this.highContrast);
            
            // Apply enhanced focus
            document.documentElement.classList.toggle('enhanced-focus', this.enhancedFocus);
            
            // Apply reduced motion
            document.documentElement.classList.toggle('reduce-motion', this.reduceMotion);
            
            // Apply screen reader mode
            document.documentElement.classList.toggle('screen-reader-mode', this.screenReaderMode);
        },
        
        setupKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                // Alt + A: Toggle accessibility toolbar
                if (e.altKey && e.key === 'a') {
                    e.preventDefault();
                    this.toggleToolbar();
                }
                
                // Alt + H: Show keyboard help
                if (e.altKey && e.key === 'h') {
                    e.preventDefault();
                    this.showKeyboardHelp = true;
                }
                
                // Alt + +: Increase font size
                if (e.altKey && e.key === '=') {
                    e.preventDefault();
                    this.increaseFontSize();
                }
                
                // Alt + -: Decrease font size
                if (e.altKey && e.key === '-') {
                    e.preventDefault();
                    this.decreaseFontSize();
                }
                
                // Alt + C: Toggle high contrast
                if (e.altKey && e.key === 'c') {
                    e.preventDefault();
                    this.toggleHighContrast();
                }
            });
        },
        
        detectUserPreferences() {
            // Detect prefers-reduced-motion
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                this.reduceMotion = true;
                this.applySettings();
            }
            
            // Detect prefers-contrast
            if (window.matchMedia('(prefers-contrast: high)').matches) {
                this.highContrast = true;
                this.applySettings();
            }
            
            // Detect screen reader
            if (navigator.userAgent.includes('NVDA') || 
                navigator.userAgent.includes('JAWS') || 
                navigator.userAgent.includes('VoiceOver')) {
                this.screenReaderMode = true;
                this.applySettings();
            }
        },
        
        setupFocusManagement() {
            // Enhanced focus indicators
            document.addEventListener('focusin', (e) => {
                if (this.enhancedFocus) {
                    e.target.classList.add('enhanced-focus-active');
                }
            });
            
            document.addEventListener('focusout', (e) => {
                e.target.classList.remove('enhanced-focus-active');
            });
            
            // Skip links functionality
            document.querySelectorAll('a[href^="#"]').forEach(link => {
                link.addEventListener('click', (e) => {
                    const target = document.querySelector(link.getAttribute('href'));
                    if (target) {
                        target.focus();
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
        },
        
        toggleToolbar() {
            this.showToolbar = !this.showToolbar;
            this.announce(this.showToolbar ? 'Toolbar aksesibilitas dibuka' : 'Toolbar aksesibilitas ditutup');
        },
        
        hideToolbar() {
            this.showToolbar = false;
            this.announce('Toolbar aksesibilitas ditutup');
        },
        
        showKeyboardHelp() {
            this.showKeyboardHelp = true;
        },
        
        hideKeyboardHelp() {
            this.showKeyboardHelp = false;
        },
        
        increaseFontSize() {
            if (this.fontSize < this.maxFontSize) {
                this.fontSize += 2;
                this.applySettings();
                this.savePreferences();
                this.announce(`Ukuran font diperbesar menjadi ${this.fontSize}px`);
            }
        },
        
        decreaseFontSize() {
            if (this.fontSize > this.minFontSize) {
                this.fontSize -= 2;
                this.applySettings();
                this.savePreferences();
                this.announce(`Ukuran font diperkecil menjadi ${this.fontSize}px`);
            }
        },
        
        toggleHighContrast() {
            this.highContrast = !this.highContrast;
            this.applySettings();
            this.savePreferences();
            this.announce(this.highContrast ? 'Mode kontras tinggi diaktifkan' : 'Mode kontras tinggi dinonaktifkan');
        },
        
        toggleEnhancedFocus() {
            this.enhancedFocus = !this.enhancedFocus;
            this.applySettings();
            this.savePreferences();
            this.announce(this.enhancedFocus ? 'Indikator fokus diperkuat diaktifkan' : 'Indikator fokus diperkuat dinonaktifkan');
        },
        
        toggleReduceMotion() {
            this.reduceMotion = !this.reduceMotion;
            this.applySettings();
            this.savePreferences();
            this.announce(this.reduceMotion ? 'Animasi dikurangi' : 'Animasi normal');
        },
        
        toggleScreenReaderMode() {
            this.screenReaderMode = !this.screenReaderMode;
            this.applySettings();
            this.savePreferences();
            this.announce(this.screenReaderMode ? 'Mode pembaca layar diaktifkan' : 'Mode pembaca layar dinonaktifkan');
        },
        
        announce(message, urgent = false) {
            if (urgent) {
                this.urgentAnnouncement = message;
                setTimeout(() => this.urgentAnnouncement = '', 1000);
            } else {
                this.announcement = message;
                setTimeout(() => this.announcement = '', 1000);
            }
        }
    }
}

// Global accessibility functions
window.announceToScreenReader = function(message, urgent = false) {
    const event = new CustomEvent('accessibility-announce', {
        detail: { message, urgent }
    });
    window.dispatchEvent(event);
};

window.focusElement = function(selector) {
    const element = document.querySelector(selector);
    if (element) {
        element.focus();
        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
};
</script>

<style>
/* High Contrast Mode */
.high-contrast {
    --foreground: #000000;
    --background: #ffffff;
    --muted: #666666;
    --border: #000000;
    --primary: #0000ff;
    --surface: #f0f0f0;
}

.high-contrast .card {
    border: 2px solid var(--border) !important;
}

.high-contrast button {
    border: 2px solid var(--border) !important;
}

/* Enhanced Focus */
.enhanced-focus *:focus,
.enhanced-focus-active {
    outline: 3px solid #ff6b35 !important;
    outline-offset: 2px !important;
    box-shadow: 0 0 0 5px rgba(255, 107, 53, 0.3) !important;
}

/* Reduced Motion */
.reduce-motion *,
.reduce-motion *::before,
.reduce-motion *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
}

/* Screen Reader Mode */
.screen-reader-mode .sr-only {
    position: static !important;
    width: auto !important;
    height: auto !important;
    padding: 0.25rem !important;
    margin: 0.25rem !important;
    overflow: visible !important;
    clip: auto !important;
    white-space: normal !important;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 0.25rem;
}

/* Larger Click Targets */
@media (pointer: coarse) {
    button, a, input, select, textarea {
        min-height: 44px;
        min-width: 44px;
    }
}

/* Focus Visible Polyfill */
.js-focus-visible :focus:not(.focus-visible) {
    outline: none;
}

/* Skip Links */
.sr-only:not(:focus):not(:active) {
    clip: rect(0 0 0 0);
    clip-path: inset(50%);
    height: 1px;
    overflow: hidden;
    position: absolute;
    white-space: nowrap;
    width: 1px;
}
</style>