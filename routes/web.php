<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\WebAuthController;

// Redirect root ke dashboard atau login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login']);
    Route::post('/ajax-login', [WebAuthController::class, 'ajaxLogin'])->name('ajax.login');
    // Register and forgot password routes removed - only admin can manage users
});

Route::get('/auth/check', [WebAuthController::class, 'check'])->name('auth.check');

// Protected Routes - require authentication
Route::middleware('auth')->group(function () {
    Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');
    
    // Dashboard Route
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

// Product Routes
Route::get('/products', function () {
    return view('products.index');
})->name('products.index');

Route::get('/products/create', function () {
    return view('products.create');
})->name('products.create');

Route::get('/products/{id}/edit', function ($id) {
    return view('products.create'); // Reuse create form for edit
})->name('products.edit');

Route::get('/products/{id}/costs', function ($id) {
    return view('products.costs');
})->name('products.costs');

Route::post('/products', function () {
    return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan');
})->name('products.store');

Route::get('/categories', function () {
    return view('dashboard'); // TODO: Create categories view
})->name('categories.index');

// Order Routes
Route::get('/orders', function () {
    return view('orders.index');
})->name('orders.index');

Route::get('/orders/create', function () {
    return view('orders.create');
})->name('orders.create');

Route::get('/orders/{id}/edit', function ($id) {
    return view('orders.edit');
})->name('orders.edit');

Route::get('/sales/reports', function () {
    return view('dashboard');
})->name('sales.reports');

// Customer Routes
Route::get('/customers', function () {
    return view('customers.index');
})->name('customers.index');

Route::get('/customers/create', function () {
    return view('customers.create');
})->name('customers.create');

Route::get('/customers/{id}/edit', function ($id) {
    return view('customers.edit');
})->name('customers.edit');

// Inventory Routes
Route::get('/inventory', function () {
    return view('inventory.index');
})->name('inventory.index');

Route::get('/inventory/movements', function () {
    return view('inventory.movements');
})->name('inventory.movements');

Route::get('/inventory/adjustments', function () {
    return view('inventory.adjustments');
})->name('inventory.adjustments');

Route::get('/inventory/reports', function () {
    return view('inventory.reports');
})->name('inventory.reports');

Route::get('/purchases', function () {
    return view('dashboard');
})->name('purchases.index');

Route::get('/suppliers', function () {
    return view('dashboard');
})->name('suppliers.index');

// Financial Routes
Route::get('/financial', function () {
    return view('financial.dashboard');
})->name('financial.dashboard');

Route::get('/financial/transactions', function () {
    return view('financial.transactions.index');
})->name('financial.transactions.index');

Route::get('/financial/transactions/create', function () {
    return view('financial.transactions.create');
})->name('financial.transactions.create');

Route::get('/financial/budgets', function () {
    return view('financial.budgets.index');
})->name('financial.budgets.index');

Route::get('/financial/budgets/create', function () {
    return view('financial.budgets.create');
})->name('financial.budgets.create');

Route::get('/financial/reports', function () {
    return view('financial.reports.index');
})->name('financial.reports.index');

Route::get('/financial/journal', function () {
    return view('financial.journal.index');
})->name('financial.journal.index');

Route::get('/financial/cashflow', function () {
    return view('financial.cashflow.index');
})->name('financial.cashflow.index');

Route::get('/expenses', function () {
    return view('financial.expenses.index');
})->name('expenses.index');

Route::get('/financial/journal/create', function () {
    return view('financial.journal.create');
})->name('financial.journal.create');

Route::get('/financial/expenses/create', function () {
    return view('financial.expenses.create');
})->name('financial.expenses.create');

// Settings Routes (Admin only - untuk testing)
Route::get('/users', function () {
    return view('users.index');
})->name('users.index');

Route::get('/settings/general', function () {
    return view('settings.general');
})->name('settings.general');

// Profile Routes
Route::get('/profile', function () {
    return view('profile.edit');
})->name('profile.edit');

Route::get('/settings/account', function () {
    return view('profile.edit'); // Reuse profile edit for account settings
})->name('settings.account');

// Manufacturing Routes (moved inside auth middleware)
Route::prefix('manufacturing')->name('manufacturing.')->group(function () {
    // Raw Materials routes
    Route::prefix('raw-materials')->name('raw-materials.')->group(function () {
        Route::get('/', function () {
            return view('manufacturing.raw-materials.index');
        })->name('index');
        
        Route::get('/create', function () {
            return view('manufacturing.raw-materials.create');
        })->name('create');
        
        Route::get('/{id}/edit', function ($id) {
            return view('manufacturing.raw-materials.edit', compact('id'));
        })->name('edit');
        
        // Raw Materials CRUD routes
        Route::post('/', [App\Http\Controllers\RawMaterialController::class, 'store'])->name('store');
        Route::get('/data', [App\Http\Controllers\RawMaterialController::class, 'index'])->name('data');
        Route::get('/{id}/show', [App\Http\Controllers\RawMaterialController::class, 'show'])->name('show');
        Route::put('/{id}', [App\Http\Controllers\RawMaterialController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\RawMaterialController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/adjust-stock', [App\Http\Controllers\RawMaterialController::class, 'adjustStock'])->name('adjust-stock');
        Route::get('/{id}/stock-movements', [App\Http\Controllers\RawMaterialController::class, 'stockMovements'])->name('stock-movements');
        Route::get('/stats', [App\Http\Controllers\RawMaterialController::class, 'statistics'])->name('statistics');
        Route::get('/low-stock', [App\Http\Controllers\RawMaterialController::class, 'lowStock'])->name('low-stock');
        Route::get('/form-data', [App\Http\Controllers\RawMaterialController::class, 'getFormData'])->name('form-data');
        Route::get('/categories', [App\Http\Controllers\RawMaterialController::class, 'getCategories'])->name('categories');
        Route::get('/units', [App\Http\Controllers\RawMaterialController::class, 'getUnits'])->name('units');
        
        Route::get('/suppliers', function () {
            return view('manufacturing.raw-materials.suppliers-simple');
        })->name('suppliers');
        
        Route::get('/suppliers/create', function () {
            return view('manufacturing.raw-materials.create-supplier-simple');
        })->name('suppliers.create');
        
        Route::post('/suppliers', [App\Http\Controllers\SupplierController::class, 'store'])->name('suppliers.store');
        
        Route::get('/suppliers/{id}', function ($id) {
            return view('manufacturing.raw-materials.view-supplier', compact('id'));
        })->name('suppliers.show');
        
        Route::get('/suppliers/{id}/edit', function ($id) {
            return view('manufacturing.raw-materials.edit-supplier', compact('id'));
        })->name('suppliers.edit');
        
        Route::put('/suppliers/{id}', [App\Http\Controllers\SupplierController::class, 'update'])->name('suppliers.update');
        
        Route::get('/purchasing', function () {
            return view('manufacturing.raw-materials.purchasing');
        })->name('purchasing');
        
        Route::get('/purchasing/create', function () {
            $suppliers = \App\Models\Supplier::all();
            $rawMaterials = \App\Models\RawMaterial::all();
            return view('manufacturing.raw-materials.create-purchase-order',compact('suppliers','rawMaterials'));
        })->name('purchasing.create');
        
        Route::get('/purchase-order-detail', function () {
            return view('manufacturing.raw-materials.purchase-order-detail');
        })->name('purchase-order-detail');
    });
    
    // Recipes routes
    Route::prefix('recipes')->name('recipes.')->group(function () {
        Route::get('/', function () {
            return view('manufacturing.recipes.index');
        })->name('index');
        
        Route::get('/create', function () {
            $rawMaterials = \App\Models\RawMaterial::all();
            $products = \App\Models\Product::all();
            return view('manufacturing.recipes.create', compact('rawMaterials','products'));
        })->name('create');
        
        Route::get('/{id}/edit', function ($id) {
            return view('manufacturing.recipes.edit', compact('id'));
        })->name('edit');
        
        Route::get('/cost-calculation', function () {
            return view('manufacturing.recipes.cost-calculation');
        })->name('cost-calculation');
    });
    
    // Production routes
    Route::prefix('production')->name('production.')->group(function () {
        Route::get('/', function () {
            return view('manufacturing.production.index');
        })->name('index');
        
        Route::get('/orders', function () {
            return view('manufacturing.production.orders');
        })->name('orders');
        
        Route::get('/orders/create', function () {
            return view('manufacturing.production.create-order');
        })->name('orders.create');
        
        Route::get('/quality-control', function () {
            return view('manufacturing.production.quality-control');
        })->name('quality-control');
        
        Route::get('/history', function () {
            return view('manufacturing.production.history');
        })->name('history');
    });
    
    // Finished Products routes
    Route::prefix('finished-products')->name('finished-products.')->group(function () {
        Route::get('/', function () {
            return view('manufacturing.finished-products.index');
        })->name('index');
        
        Route::get('/catalog', function () {
            return view('manufacturing.finished-products.catalog');
        })->name('catalog');
        
        Route::get('/pricing', function () {
            return view('manufacturing.finished-products.pricing');
        })->name('pricing');
    });
});

    // AJAX routes for fetching data (inside auth middleware)
    Route::get('/ajax/suppliers', function() {
        $suppliers = \App\Models\Supplier::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'contact_person', 'phone', 'email', 'address', 'rating', 'lead_time_days']);
        
        return response()->json([
            'success' => true,
            'data' => $suppliers
        ]);
    })->name('ajax.suppliers');
    
    Route::get('/ajax/raw-materials', function(Request $request) {
        $query = \App\Models\RawMaterial::where('is_active', true);
        
        // Filter by supplier if supplier_id is provided
        if ($request->has('supplier_id') && $request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }
        
        $materials = $query->orderBy('name')
            ->get(['id', 'name', 'unit', 'last_purchase_price', 'average_price', 'supplier_id']);
        
        return response()->json([
            'success' => true,
            'data' => $materials
        ]);
    })->name('ajax.raw-materials');

    // Purchase Orders AJAX routes
    Route::get('/ajax/purchase-orders', [App\Http\Controllers\PurchaseOrderController::class, 'index'])->name('ajax.purchase-orders');
    Route::post('/ajax/purchase-orders', [App\Http\Controllers\PurchaseOrderController::class, 'store'])->name('ajax.purchase-orders.store');
    Route::get('/ajax/purchase-orders/{id}', [App\Http\Controllers\PurchaseOrderController::class, 'show'])->name('ajax.purchase-orders.show');
    Route::put('/ajax/purchase-orders/{id}', [App\Http\Controllers\PurchaseOrderController::class, 'update'])->name('ajax.purchase-orders.update');
    Route::delete('/ajax/purchase-orders/{id}', [App\Http\Controllers\PurchaseOrderController::class, 'destroy'])->name('ajax.purchase-orders.destroy');
    Route::post('/ajax/purchase-orders/{id}/send', [App\Http\Controllers\PurchaseOrderController::class, 'send'])->name('ajax.purchase-orders.send');
    Route::post('/ajax/purchase-orders/{id}/approve', [App\Http\Controllers\PurchaseOrderController::class, 'approve'])->name('ajax.purchase-orders.approve');
    Route::post('/ajax/purchase-orders/{id}/receive', [App\Http\Controllers\PurchaseOrderController::class, 'receive'])->name('ajax.purchase-orders.receive');
    Route::post('/ajax/purchase-orders/{id}/cancel', [App\Http\Controllers\PurchaseOrderController::class, 'cancel'])->name('ajax.purchase-orders.cancel');
    Route::post('/ajax/purchase-orders/{id}/quick-receive', [App\Http\Controllers\PurchaseOrderController::class, 'quickReceive'])->name('ajax.purchase-orders.quick-receive');
    Route::get('/ajax/purchase-orders/{id}/print', [App\Http\Controllers\PurchaseOrderController::class, 'print'])->name('ajax.purchase-orders.print');

}); // End of auth middleware group

// Invoice view routes
Route::get('/invoices', function () {
    return view('financial.invoices.index');
})->name('invoices.index');

Route::get('/invoices/{id}/view', function ($id) {
    return view('financial.invoices.view', compact('id'));
})->name('invoices.view');

Route::get('/invoices/{id}/print', function ($id) {
    return view('financial.invoices.print', compact('id'));
})->name('invoices.print');

// Payment routes
Route::get('/payments', function () {
    return view('financial.payments.index');
})->name('payments.index');

Route::get('/payments/{id}/receipt', function ($id) {
    return view('financial.payments.receipt', compact('id'));
})->name('payments.receipt');

// Receivables routes
Route::get('/receivables', function () {
    return view('financial.receivables.index');
})->name('receivables.index');

// Payables routes
Route::get('/payables', function () {
    return view('financial.payables.index');
})->name('payables.index');

// Tax routes
Route::get('/taxes', function () {
    return view('financial.taxes.index');
})->name('taxes.index');

// Audit routes
Route::get('/audit', function () {
    return view('financial.audit.index');
})->name('audit.index');

// Redirect role management to user management (unified system)
Route::get('/financial/roles', function () {
    return redirect()->route('users.index')->with('info', 'Role management telah dipindahkan ke Manajemen Pengguna');
})->name('roles.index');
