@extends('layouts.dashboard')

@section('title', 'Kalkulasi Biaya Resep')
@section('page-title', 'Kalkulasi Biaya Produksi')

@section('content')
<div x-data="costCalculation()" class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-foreground leading-tight">Kalkulasi Biaya Resep</h1>
            <p class="text-sm md:text-base text-muted leading-relaxed">Analisis biaya produksi dan margin keuntungan</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('manufacturing.recipes.index') }}"
               class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Resep
            </a>
            <button @click="exportCalculation" class="btn btn-outline touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export
            </button>
            <button @click="updateAllCosts" class="btn btn-primary touch-manipulation">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Update Semua Biaya
            </button>
        </div>
    </div>

    <!-- Recipe Selection -->
    <div class="card p-4 md:p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex flex-col gap-4 flex-1 md:flex-row md:gap-6">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-foreground mb-2">Pilih Resep</label>
                    <select x-model="selectedRecipeId" @change="loadRecipeDetails" class="input w-full h-12 text-base">
                        <option value="">Pilih resep untuk kalkulasi</option>
                        <template x-for="recipe in recipes" :key="recipe.id">
                            <option :value="recipe.id" x-text="recipe.product_name + ' (' + recipe.product_sku + ')'"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Batch Size</label>
                    <div class="flex gap-2">
                        <input type="number" x-model="batchSize" @input="recalculateCosts" class="input w-24 md:w-28 h-12 text-base" placeholder="100">
                        <span class="input bg-border/30 text-muted w-16 md:w-20 h-12 flex items-center justify-center text-base" x-text="selectedRecipe?.batch_unit || 'pcs'"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cost Breakdown -->
    <div x-show="selectedRecipe" class="space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-muted leading-tight">Total Biaya Bahan</p>
                        <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="formatCurrency(totalMaterialCost)">Rp 0</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-muted leading-tight">Biaya Overhead</p>
                        <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="formatCurrency(overheadCost)">Rp 0</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-muted leading-tight">Total Biaya Produksi</p>
                        <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="formatCurrency(totalProductionCost)">Rp 0</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-muted leading-tight">Biaya per Unit</p>
                        <p class="text-xl md:text-2xl font-bold text-foreground leading-none" x-text="formatCurrency(costPerUnit)">Rp 0</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Material Cost Breakdown -->
        <div class="card">
            <div class="p-6 border-b border-border">
                <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight">Rincian Biaya Bahan Baku</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-border/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Bahan Baku</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Harga per Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Total Biaya</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">% dari Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <template x-for="ingredient in selectedRecipe?.ingredients || []" :key="ingredient.id">
                            <tr class="hover:bg-border/30">
                                <td class="px-6 py-4">
                                    <div class="text-sm md:text-base font-medium text-foreground leading-tight" x-text="ingredient.material_name"></div>
                                    <div class="text-xs md:text-sm text-muted leading-relaxed" x-text="ingredient.material_sku"></div>
                                </td>
                                <td class="px-6 py-4 text-sm text-foreground" x-text="ingredient.quantity"></td>
                                <td class="px-6 py-4 text-sm text-foreground" x-text="ingredient.unit"></td>
                                <td class="px-6 py-4">
                                    <input type="number" 
                                           :value="ingredient.unit_cost" 
                                           @input="updateIngredientCost(ingredient, $event.target.value)"
                                           class="input w-24 text-sm">
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-foreground" x-text="formatCurrency(ingredient.total_cost)"></td>
                                <td class="px-6 py-4 text-sm text-foreground" x-text="calculatePercentage(ingredient.total_cost) + '%'"></td>
                                <td class="px-6 py-4">
                                    <button @click="updateMaterialPrice(ingredient)" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg touch-manipulation" title="Update Harga">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Overhead Costs -->
        <div class="card p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Biaya Overhead</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 md:gap-6">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Biaya Tenaga Kerja</label>
                    <input type="number" x-model="overheadCosts.labor" @input="recalculateCosts" class="input w-full h-12 text-base" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Biaya Utilitas</label>
                    <input type="number" x-model="overheadCosts.utilities" @input="recalculateCosts" class="input w-full h-12 text-base" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Biaya Kemasan</label>
                    <input type="number" x-model="overheadCosts.packaging" @input="recalculateCosts" class="input w-full h-12 text-base" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Biaya Operasional</label>
                    <input type="number" x-model="overheadCosts.operational" @input="recalculateCosts" class="input w-full h-12 text-base" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Biaya Lain-lain</label>
                    <input type="number" x-model="overheadCosts.others" @input="recalculateCosts" class="input w-full h-12 text-base" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Total Overhead</label>
                    <input type="number" :value="overheadCost" class="input w-full h-12 text-base bg-border/30" readonly>
                </div>
            </div>
        </div>

        <!-- Pricing Analysis -->
        <div class="card p-4 md:p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Analisis Harga Jual</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4 md:gap-6">
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Target Margin (%)</label>
                    <input type="number" x-model="targetMargin" @input="calculateSuggestedPrice" class="input w-full h-12 text-base" placeholder="30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Harga Jual Saran</label>
                    <input type="number" :value="suggestedPrice" class="input w-full h-12 text-base bg-green-50 dark:bg-green-900/20" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Harga Jual Aktual</label>
                    <input type="number" x-model="actualPrice" @input="calculateActualMargin" class="input w-full h-12 text-base" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Margin Aktual (%)</label>
                    <input type="number" :value="actualMargin" class="input w-full h-12 text-base" :class="actualMargin >= targetMargin ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20'" readonly>
                </div>
            </div>

            <!-- Profit Analysis -->
            <div class="mt-6 p-4 bg-border/30 rounded-lg">
                <h4 class="text-md font-semibold text-foreground mb-3">Analisis Keuntungan per Batch</h4>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-muted">Revenue</p>
                        <p class="text-lg font-bold text-foreground" x-text="formatCurrency(totalRevenue)"></p>
                    </div>
                    <div>
                        <p class="text-sm text-muted">Total Cost</p>
                        <p class="text-lg font-bold text-foreground" x-text="formatCurrency(totalProductionCost)"></p>
                    </div>
                    <div>
                        <p class="text-sm text-muted">Gross Profit</p>
                        <p class="text-lg font-bold" :class="grossProfit >= 0 ? 'text-green-600' : 'text-red-600'" x-text="formatCurrency(grossProfit)"></p>
                    </div>
                    <div>
                        <p class="text-sm text-muted">ROI (%)</p>
                        <p class="text-lg font-bold" :class="roi >= 0 ? 'text-green-600' : 'text-red-600'" x-text="roi.toFixed(1) + '%'"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cost Comparison Chart -->
        <div class="card p-6">
            <h3 class="text-base md:text-lg font-semibold text-foreground leading-tight mb-4">Perbandingan Biaya</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <canvas id="costBreakdownChart" width="400" height="300"></canvas>
                </div>
                <div>
                    <h4 class="text-md font-semibold text-foreground mb-3">Komponen Biaya</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Bahan Baku</span>
                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(totalMaterialCost)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Tenaga Kerja</span>
                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(overheadCosts.labor)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Utilitas</span>
                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(overheadCosts.utilities)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Kemasan</span>
                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(overheadCosts.packaging)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted">Lain-lain</span>
                            <span class="text-sm font-medium text-foreground" x-text="formatCurrency(overheadCosts.operational + overheadCosts.others)"></span>
                        </div>
                        <hr class="border-border">
                        <div class="flex justify-between items-center font-semibold">
                            <span class="text-sm text-foreground">Total</span>
                            <span class="text-sm text-foreground" x-text="formatCurrency(totalProductionCost)"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div x-show="!selectedRecipe" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
        </svg>
        <h3 class="mt-2 text-sm md:text-base font-medium text-foreground leading-tight">Pilih Resep</h3>
        <p class="mt-1 text-xs md:text-sm text-muted leading-relaxed">Pilih resep dari dropdown di atas untuk memulai kalkulasi biaya</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function costCalculation() {
    return {
        recipes: [],
        selectedRecipeId: '',
        selectedRecipe: null,
        batchSize: 100,
        overheadCosts: {
            labor: 0,
            utilities: 0,
            packaging: 0,
            operational: 0,
            others: 0
        },
        targetMargin: 30,
        actualPrice: 0,

        init() {
            this.loadRecipes();
        },

        async loadRecipes() {
            try {
                const response = await fetch('/api/recipes');
                const data = await response.json();
                this.recipes = data.data;
            } catch (error) {
                console.error('Error loading recipes:', error);
            }
        },

        async loadRecipeDetails() {
            if (!this.selectedRecipeId) {
                this.selectedRecipe = null;
                return;
            }

            try {
                const response = await fetch(`/api/recipes/${this.selectedRecipeId}/details`);
                const data = await response.json();
                this.selectedRecipe = data.data;
                this.batchSize = this.selectedRecipe.batch_size;
                this.recalculateCosts();
            } catch (error) {
                console.error('Error loading recipe details:', error);
            }
        },

        updateIngredientCost(ingredient, newCost) {
            ingredient.unit_cost = parseFloat(newCost) || 0;
            ingredient.total_cost = ingredient.quantity * ingredient.unit_cost;
            this.recalculateCosts();
        },

        recalculateCosts() {
            if (!this.selectedRecipe) return;
            
            // Recalculate ingredient costs based on batch size
            const batchRatio = this.batchSize / this.selectedRecipe.batch_size;
            this.selectedRecipe.ingredients.forEach(ingredient => {
                ingredient.adjusted_quantity = ingredient.quantity * batchRatio;
                ingredient.total_cost = ingredient.adjusted_quantity * ingredient.unit_cost;
            });
        },

        get totalMaterialCost() {
            if (!this.selectedRecipe) return 0;
            return this.selectedRecipe.ingredients.reduce((sum, ingredient) => sum + ingredient.total_cost, 0);
        },

        get overheadCost() {
            return Object.values(this.overheadCosts).reduce((sum, cost) => sum + (parseFloat(cost) || 0), 0);
        },

        get totalProductionCost() {
            return this.totalMaterialCost + this.overheadCost;
        },

        get costPerUnit() {
            if (this.batchSize <= 0) return 0;
            return this.totalProductionCost / this.batchSize;
        },

        get suggestedPrice() {
            if (this.targetMargin <= 0) return this.costPerUnit;
            return this.costPerUnit / (1 - this.targetMargin / 100);
        },

        get actualMargin() {
            if (this.actualPrice <= 0 || this.costPerUnit <= 0) return 0;
            return Math.round(((this.actualPrice - this.costPerUnit) / this.actualPrice) * 100);
        },

        get totalRevenue() {
            return this.actualPrice * this.batchSize;
        },

        get grossProfit() {
            return this.totalRevenue - this.totalProductionCost;
        },

        get roi() {
            if (this.totalProductionCost <= 0) return 0;
            return (this.grossProfit / this.totalProductionCost) * 100;
        },

        calculatePercentage(amount) {
            if (this.totalMaterialCost <= 0) return 0;
            return Math.round((amount / this.totalMaterialCost) * 100);
        },

        calculateSuggestedPrice() {
            // This will trigger the computed property update
        },

        calculateActualMargin() {
            // This will trigger the computed property update
        },

        async updateMaterialPrice(ingredient) {
            try {
                const response = await fetch(`/api/raw-materials/${ingredient.material_id}/price`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        unit_cost: ingredient.unit_cost
                    })
                });

                if (response.ok) {
                    console.log('Material price updated');
                } else {
                    console.error('Failed to update material price');
                }
            } catch (error) {
                console.error('Error updating material price:', error);
            }
        },

        async updateAllCosts() {
            if (!this.selectedRecipe) return;

            try {
                const response = await fetch(`/api/recipes/${this.selectedRecipeId}/update-costs`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    this.loadRecipeDetails();
                } else {
                    console.error('Failed to update costs');
                }
            } catch (error) {
                console.error('Error updating costs:', error);
            }
        },

        exportCalculation() {
            if (!this.selectedRecipe) return;
            
            const data = {
                recipe: this.selectedRecipe,
                batchSize: this.batchSize,
                costs: {
                    material: this.totalMaterialCost,
                    overhead: this.overheadCost,
                    total: this.totalProductionCost,
                    perUnit: this.costPerUnit
                },
                pricing: {
                    suggested: this.suggestedPrice,
                    actual: this.actualPrice,
                    margin: this.actualMargin
                }
            };
            
            console.log('Export calculation:', data);
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }
    }
}
</script>
@endpush