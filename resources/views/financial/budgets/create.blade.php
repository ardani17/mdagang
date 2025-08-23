@extends('layouts.dashboard')

@section('title', 'Create Budget')
@section('page-title')
<span class="text-base lg:text-2xl">Create Budget</span>
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
        <a href="{{ route('financial.dashboard') }}" class="ml-1 text-sm font-medium text-muted hover:text-foreground md:ml-2">Financial</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('financial.budgets.index') }}" class="ml-1 text-sm font-medium text-muted hover:text-foreground md:ml-2">Budgets</a>
    </div>
</li>
<li>
    <div class="flex items-center">
        <svg class="w-3 h-3 text-muted mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="ml-1 text-sm font-medium text-foreground md:ml-2">Create</span>
    </div>
</li>
@endsection

@section('content')
<div x-data="budgetForm()" class="space-y-4 lg:space-y-6 p-4 lg:p-0">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-foreground">Create New Budget</h2>
            <p class="text-sm text-muted">Set up a new budget plan for your financial management</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('financial.budgets.index') }}" class="btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Budgets
            </a>
        </div>
    </div>

    <!-- Budget Form -->
    <form @submit.prevent="submitBudget()" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Budget Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="card p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Budget Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Budget Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-foreground mb-2">Budget Name *</label>
                            <input type="text" 
                                   id="name"
                                   x-model="form.name"
                                   class="input"
                                   :class="errors.name ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                   placeholder="Enter budget name">
                            <div x-show="errors.name" class="mt-1 text-sm text-red-600" x-text="errors.name"></div>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-foreground mb-2">Category *</label>
                            <select id="category"
                                    x-model="form.category"
                                    class="input"
                                    :class="errors.category ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''">
                                <option value="">Select Category</option>
                                <option value="marketing">Marketing</option>
                                <option value="operations">Operations</option>
                                <option value="equipment">Equipment</option>
                                <option value="utilities">Utilities</option>
                                <option value="salaries">Salaries</option>
                                <option value="travel">Travel</option>
                                <option value="professional">Professional Services</option>
                                <option value="other">Other</option>
                            </select>
                            <div x-show="errors.category" class="mt-1 text-sm text-red-600" x-text="errors.category"></div>
                        </div>

                        <!-- Budget Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-foreground mb-2">Budget Amount *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-muted text-sm">Rp</span>
                                </div>
                                <input type="number" 
                                       id="amount"
                                       x-model="form.amount"
                                       class="input pl-12"
                                       :class="errors.amount ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                       placeholder="0"
                                       step="0.01"
                                       min="0">
                            </div>
                            <div x-show="errors.amount" class="mt-1 text-sm text-red-600" x-text="errors.amount"></div>
                        </div>

                        <!-- Budget Period -->
                        <div>
                            <label for="period_type" class="block text-sm font-medium text-foreground mb-2">Period Type *</label>
                            <select id="period_type"
                                    x-model="form.period_type"
                                    @change="updatePeriodDates()"
                                    class="input"
                                    :class="errors.period_type ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''">
                                <option value="">Select Period</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="yearly">Yearly</option>
                                <option value="custom">Custom Period</option>
                            </select>
                            <div x-show="errors.period_type" class="mt-1 text-sm text-red-600" x-text="errors.period_type"></div>
                        </div>

                        <!-- Budget Year/Month -->
                        <div x-show="form.period_type && form.period_type !== 'custom'">
                            <label for="budget_year" class="block text-sm font-medium text-foreground mb-2">Budget Year *</label>
                            <select id="budget_year"
                                    x-model="form.budget_year"
                                    @change="updatePeriodDates()"
                                    class="input">
                                <option value="">Select Year</option>
                                <template x-for="year in getAvailableYears()" :key="year">
                                    <option :value="year" x-text="year"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-foreground mb-2">Start Date *</label>
                            <input type="date" 
                                   id="start_date"
                                   x-model="form.start_date"
                                   class="input"
                                   :class="errors.start_date ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                   :readonly="form.period_type && form.period_type !== 'custom'">
                            <div x-show="errors.start_date" class="mt-1 text-sm text-red-600" x-text="errors.start_date"></div>
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-foreground mb-2">End Date *</label>
                            <input type="date" 
                                   id="end_date"
                                   x-model="form.end_date"
                                   class="input"
                                   :class="errors.end_date ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                   :readonly="form.period_type && form.period_type !== 'custom'">
                            <div x-show="errors.end_date" class="mt-1 text-sm text-red-600" x-text="errors.end_date"></div>
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-foreground mb-2">Description</label>
                            <textarea id="description"
                                      x-model="form.description"
                                      rows="3"
                                      class="input"
                                      placeholder="Budget description and notes"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Budget Allocation -->
                <div class="card p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Budget Allocation</h3>
                    
                    <div class="space-y-4">
                        <template x-for="(allocation, index) in form.allocations" :key="index">
                            <div class="border border-border rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-foreground">Allocation <span x-text="index + 1"></span></h4>
                                    <button type="button" 
                                            @click="removeAllocation(index)"
                                            x-show="form.allocations.length > 1"
                                            class="text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Subcategory</label>
                                        <input type="text" 
                                               x-model="allocation.subcategory"
                                               class="input"
                                               placeholder="e.g., Digital Marketing">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Amount</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-muted text-sm">Rp</span>
                                            </div>
                                            <input type="number" 
                                                   x-model="allocation.amount"
                                                   @input="calculateTotalAllocation()"
                                                   class="input pl-12"
                                                   placeholder="0"
                                                   step="0.01"
                                                   min="0">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-foreground mb-2">Percentage</label>
                                        <div class="relative">
                                            <input type="number" 
                                                   x-model="allocation.percentage"
                                                   class="input pr-8"
                                                   placeholder="0"
                                                   step="0.01"
                                                   min="0"
                                                   max="100"
                                                   readonly>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-muted text-sm">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <button type="button" 
                                @click="addAllocation()"
                                class="w-full btn-secondary flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Allocation
                        </button>
                    </div>
                </div>

                <!-- Alert Settings -->
                <div class="card p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Alert Settings</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="alert_threshold" class="block text-sm font-medium text-foreground mb-2">Alert Threshold (%)</label>
                            <input type="number" 
                                   id="alert_threshold"
                                   x-model="form.alert_threshold"
                                   class="input"
                                   placeholder="80"
                                   min="0"
                                   max="100">
                            <p class="text-xs text-muted mt-1">Get notified when spending reaches this percentage</p>
                        </div>
                        
                        <div>
                            <label for="alert_frequency" class="block text-sm font-medium text-foreground mb-2">Alert Frequency</label>
                            <select id="alert_frequency"
                                    x-model="form.alert_frequency"
                                    class="input">
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="threshold">Only at threshold</option>
                            </select>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       x-model="form.email_alerts"
                                       class="rounded border-border text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-foreground">Send email alerts</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Budget Summary -->
                <div class="card p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Budget Summary</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Total Budget:</span>
                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(form.amount || 0)"></span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Total Allocated:</span>
                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(totalAllocated)"></span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Remaining:</span>
                            <span class="text-sm font-medium" 
                                  :class="(form.amount - totalAllocated) >= 0 ? 'text-green-600' : 'text-red-600'"
                                  x-text="formatCurrency((form.amount || 0) - totalAllocated)"></span>
                        </div>
                        
                        <div class="border-t border-border pt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-base font-semibold text-foreground">Allocation Status:</span>
                                <span class="text-base font-bold" 
                                      :class="totalAllocated <= (form.amount || 0) ? 'text-green-600' : 'text-red-600'"
                                      x-text="totalAllocated <= (form.amount || 0) ? 'Valid' : 'Over Budget'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Budget Period Info -->
                <div class="card p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Period Information</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Period Type:</span>
                            <span class="text-sm font-medium text-foreground" x-text="form.period_type || 'Not selected'"></span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Duration:</span>
                            <span class="text-sm font-medium text-foreground" x-text="calculateDuration()"></span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Daily Budget:</span>
                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(calculateDailyBudget())"></span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Monthly Budget:</span>
                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(calculateMonthlyBudget())"></span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Quick Actions</h3>
                    
                    <div class="space-y-3">
                        <button type="button" 
                                @click="saveAsDraft()"
                                class="w-full btn-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Save as Draft
                        </button>
                        
                        <button type="button" 
                                @click="previewBudget()"
                                class="w-full btn-secondary text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Preview Budget
                        </button>
                        
                        <button type="button" 
                                @click="resetForm()"
                                class="w-full btn-secondary text-sm text-red-600 hover:text-red-800">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset Form
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-6 border-t border-border">
            <div class="flex items-center space-x-4">
                <label class="flex items-center">
                    <input type="checkbox" 
                           x-model="form.auto_rollover"
                           class="rounded border-border text-primary focus:ring-primary">
                    <span class="ml-2 text-sm text-foreground">Auto-rollover unused budget</span>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" 
                           x-model="form.is_active"
                           class="rounded border-border text-primary focus:ring-primary">
                    <span class="ml-2 text-sm text-foreground">Activate immediately</span>
                </label>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('financial.budgets.index') }}" class="btn-secondary">
                    Cancel
                </a>
                
                <button type="submit" 
                        :disabled="loading || totalAllocated > (form.amount || 0)"
                        class="btn-primary flex items-center"
                        :class="(loading || totalAllocated > (form.amount || 0)) ? 'opacity-50 cursor-not-allowed' : ''">
                    <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Creating...' : 'Create Budget'"></span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function budgetForm() {
    return {
        loading: false,
        errors: {},
        totalAllocated: 0,
        form: {
            name: '',
            category: '',
            amount: '',
            period_type: '',
            budget_year: new Date().getFullYear(),
            start_date: '',
            end_date: '',
            description: '',
            alert_threshold: 80,
            alert_frequency: 'weekly',
            email_alerts: true,
            auto_rollover: false,
            is_active: true,
            allocations: [
                { subcategory: '', amount: '', percentage: 0 }
            ]
        },

        init() {
            this.calculateTotalAllocation();
        },

        getAvailableYears() {
            const currentYear = new Date().getFullYear();
            const years = [];
            for (let i = currentYear - 1; i <= currentYear + 5; i++) {
                years.push(i);
            }
            return years;
        },

        updatePeriodDates() {
            if (!this.form.period_type || !this.form.budget_year) return;

            const year = parseInt(this.form.budget_year);
            
            switch (this.form.period_type) {
                case 'monthly':
                    // Default to current month or next month
                    const currentMonth = new Date().getMonth();
                    this.form.start_date = new Date(year, currentMonth, 1).toISOString().split('T')[0];
                    this.form.end_date = new Date(year, currentMonth + 1, 0).toISOString().split('T')[0];
                    break;
                    
                case 'quarterly':
                    // Default to current quarter
                    const currentQuarter = Math.floor(new Date().getMonth() / 3);
                    const quarterStart = currentQuarter * 3;
                    this.form.start_date = new Date(year, quarterStart, 1).toISOString().split('T')[0];
                    this.form.end_date = new Date(year, quarterStart + 3, 0).toISOString().split('T')[0];
                    break;
                    
                case 'yearly':
                    this.form.start_date = new Date(year, 0, 1).toISOString().split('T')[0];
                    this.form.end_date = new Date(year, 11, 31).toISOString().split('T')[0];
                    break;
                    
                case 'custom':
                    // Clear dates for custom input
                    this.form.start_date = '';
                    this.form.end_date = '';
                    break;
            }
        },

        addAllocation() {
            this.form.allocations.push({
                subcategory: '',
                amount: '',
                percentage: 0
            });
        },

        removeAllocation(index) {
            this.form.allocations.splice(index, 1);
            this.calculateTotalAllocation();
        },

        calculateTotalAllocation() {
            this.totalAllocated = this.form.allocations.reduce((total, allocation) => {
                return total + (parseFloat(allocation.amount) || 0);
            }, 0);

            // Update percentages
            const totalBudget = parseFloat(this.form.amount) || 0;
            if (totalBudget > 0) {
                this.form.allocations.forEach(allocation => {
                    const amount = parseFloat(allocation.amount) || 0;
                    allocation.percentage = ((amount / totalBudget) * 100).toFixed(2);
                });
            }
        },

        calculateDuration() {
            if (!this.form.start_date || !this.form.end_date) return 'Not set';
            
            const start = new Date(this.form.start_date);
            const end = new Date(this.form.end_date);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            return `${diffDays} days`;
        },

        calculateDailyBudget() {
            if (!this.form.start_date || !this.form.end_date || !this.form.amount) return 0;
            
            const start = new Date(this.form.start_date);
            const end = new Date(this.form.end_date);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            return (
parseFloat(this.form.amount) || 0) / diffDays;
        },

        calculateMonthlyBudget() {
            if (!this.form.start_date || !this.form.end_date || !this.form.amount) return 0;
            
            const start = new Date(this.form.start_date);
            const end = new Date(this.form.end_date);
            const diffTime = Math.abs(end - start);
            const diffMonths = diffTime / (1000 * 60 * 60 * 24 * 30.44); // Average days per month
            
            return (parseFloat(this.form.amount) || 0) / diffMonths;
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },

        validateForm() {
            this.errors = {};

            if (!this.form.name.trim()) {
                this.errors.name = 'Budget name is required';
            }

            if (!this.form.category) {
                this.errors.category = 'Category is required';
            }

            if (!this.form.amount || parseFloat(this.form.amount) <= 0) {
                this.errors.amount = 'Amount must be greater than 0';
            }

            if (!this.form.period_type) {
                this.errors.period_type = 'Period type is required';
            }

            if (!this.form.start_date) {
                this.errors.start_date = 'Start date is required';
            }

            if (!this.form.end_date) {
                this.errors.end_date = 'End date is required';
            }

            if (this.form.start_date && this.form.end_date && new Date(this.form.start_date) >= new Date(this.form.end_date)) {
                this.errors.end_date = 'End date must be after start date';
            }

            if (this.totalAllocated > (parseFloat(this.form.amount) || 0)) {
                this.errors.amount = 'Total allocations exceed budget amount';
            }

            return Object.keys(this.errors).length === 0;
        },

        async submitBudget() {
            if (!this.validateForm()) {
                return;
            }

            this.loading = true;

            try {
                const response = await fetch('/api/financial/budgets', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (response.ok) {
                    this.showNotification('Budget created successfully!', 'success');
                    window.location.href = '/financial/budgets';
                } else {
                    this.errors = data.errors || {};
                    this.showNotification(data.message || 'Error creating budget', 'error');
                }
            } catch (error) {
                console.error('Error creating budget:', error);
                this.showNotification('Network error. Please try again.', 'error');
            } finally {
                this.loading = false;
            }
        },

        async saveAsDraft() {
            this.form.status = 'draft';
            await this.submitBudget();
        },

        previewBudget() {
            console.log('Preview budget:', this.form);
        },

        resetForm() {
            this.form = {
                name: '',
                category: '',
                amount: '',
                period_type: '',
                budget_year: new Date().getFullYear(),
                start_date: '',
                end_date: '',
                description: '',
                alert_threshold: 80,
                alert_frequency: 'weekly',
                email_alerts: true,
                auto_rollover: false,
                is_active: true,
                allocations: [
                    { subcategory: '', amount: '', percentage: 0 }
                ]
            };
            this.errors = {};
            this.totalAllocated = 0;
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