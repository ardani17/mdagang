<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ProductionOrderController;
use App\Http\Controllers\QualityInspectionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    // Register endpoint removed - only admin can create users
    Route::post('/login', [AuthController::class, 'login']);
});

Route::prefix('get')->group(function () {
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::get('/raw-materials', [RawMaterialController::class, 'get']);
});


// Purchase Orders module routes
    Route::prefix('purchase-orders')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index']);
        Route::post('/', [PurchaseOrderController::class, 'store']);
        Route::get('/statistics', [PurchaseOrderController::class, 'statistics']);
        Route::get('/export', [PurchaseOrderController::class, 'export']);
        Route::get('/{id}', [PurchaseOrderController::class, 'show']);
        Route::put('/{id}', [PurchaseOrderController::class, 'update']);
        Route::delete('/{id}', [PurchaseOrderController::class, 'destroy']);
        Route::post('/{id}/approve', [PurchaseOrderController::class, 'approve']);
        Route::post('/{id}/send', [PurchaseOrderController::class, 'send']);
        Route::post('/{id}/receive', [PurchaseOrderController::class, 'receive']);
        Route::post('/{id}/cancel', [PurchaseOrderController::class, 'cancel']);
        Route::post('/{id}/items', [PurchaseOrderController::class, 'addItem']);
        Route::delete('/{id}/items/{itemId}', [PurchaseOrderController::class, 'removeItem']);
    });

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    });

    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardController::class, 'stats']);
        Route::get('/charts', [DashboardController::class, 'charts']);
        Route::get('/recent-activities', [DashboardController::class, 'recentActivities']);
        Route::get('/quick-stats', [DashboardController::class, 'quickStats']);
    });

    // Manufacturing module routes
    Route::prefix('manufacturing')->group(function () {
        // Raw Materials
        Route::prefix('raw-materials')->group(function () {
            Route::get('/', [RawMaterialController::class, 'index']);
            Route::post('/', [RawMaterialController::class, 'store']);
            Route::get('/low-stock', [RawMaterialController::class, 'lowStock']);
            Route::get('/statistics', [RawMaterialController::class, 'statistics']);
            Route::get('/stats', [RawMaterialController::class, 'statistics']); // Alias for frontend compatibility
            Route::get('/export', [RawMaterialController::class, 'export']);
            Route::get('/{id}', [RawMaterialController::class, 'show']);
            Route::put('/{id}', [RawMaterialController::class, 'update']);
            Route::delete('/{id}', [RawMaterialController::class, 'destroy']);
            Route::post('/{id}/adjust-stock', [RawMaterialController::class, 'adjustStock']);
            Route::get('/{id}/stock-movements', [RawMaterialController::class, 'stockMovements']);
        });

        // Recipes
        Route::prefix('recipes')->group(function () {
            Route::get('/', [RecipeController::class, 'index']);
            Route::post('/', [RecipeController::class, 'store']);
            Route::get('/statistics', [RecipeController::class, 'statistics']);
            Route::get('/export', [RecipeController::class, 'export']);
            Route::get('/{id}', [RecipeController::class, 'show']);
            Route::put('/{id}', [RecipeController::class, 'update']);
            Route::delete('/{id}', [RecipeController::class, 'destroy']);
            Route::get('/{id}/calculate-cost', [RecipeController::class, 'calculateCost']);
            Route::get('/{id}/check-availability', [RecipeController::class, 'checkAvailability']);
            Route::post('/{id}/duplicate', [RecipeController::class, 'duplicate']);
            Route::post('/{id}/toggle-status', [RecipeController::class, 'toggleStatus']);
        });

        // Production Orders
        Route::prefix('production-orders')->group(function () {
            Route::get('/', [ProductionOrderController::class, 'index']);
            Route::post('/', [ProductionOrderController::class, 'store']);
            Route::get('/statistics', [ProductionOrderController::class, 'statistics']);
            Route::get('/{id}', [ProductionOrderController::class, 'show']);
            Route::put('/{id}', [ProductionOrderController::class, 'update']);
            Route::delete('/{id}', [ProductionOrderController::class, 'destroy']);
            Route::post('/{id}/start', [ProductionOrderController::class, 'start']);
            Route::post('/{id}/complete', [ProductionOrderController::class, 'complete']);
            Route::post('/{id}/cancel', [ProductionOrderController::class, 'cancel']);
        });

        // Quality Inspections
        Route::prefix('quality-inspections')->group(function () {
            Route::get('/', [QualityInspectionController::class, 'index']);
            Route::post('/', [QualityInspectionController::class, 'store']);
            Route::get('/statistics', [QualityInspectionController::class, 'statistics']);
            Route::get('/{id}', [QualityInspectionController::class, 'show']);
            Route::put('/{id}', [QualityInspectionController::class, 'update']);
            Route::delete('/{id}', [QualityInspectionController::class, 'destroy']);
            Route::post('/{id}/approve', [QualityInspectionController::class, 'approve']);
            Route::post('/{id}/reject', [QualityInspectionController::class, 'reject']);
        });

        // Finished Products
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::post('/', [ProductController::class, 'store']);
            Route::get('/statistics', [ProductController::class, 'statistics']);
            Route::get('/export', [ProductController::class, 'export']);
            Route::get('/{id}', [ProductController::class, 'show']);
            Route::put('/{id}', [ProductController::class, 'update']);
            Route::delete('/{id}', [ProductController::class, 'destroy']);
            Route::post('/{id}/adjust-stock', [ProductController::class, 'adjustStock']);
            Route::get('/{id}/calculate-costs', [ProductController::class, 'calculateCosts']);
            Route::post('/{id}/toggle-status', [ProductController::class, 'toggleStatus']);
        });
    });

    // Financial module routes
    Route::prefix('financial')->group(function () {
        // Transactions
        Route::prefix('transactions')->group(function () {
            Route::get('/', [TransactionController::class, 'index']);
            Route::post('/', [TransactionController::class, 'store']);
            Route::get('/cash-flow', [TransactionController::class, 'cashFlow']);
            Route::get('/category-breakdown', [TransactionController::class, 'categoryBreakdown']);
            Route::get('/export', [TransactionController::class, 'export']);
            Route::get('/{id}', [TransactionController::class, 'show']);
            Route::put('/{id}', [TransactionController::class, 'update']);
            Route::delete('/{id}', [TransactionController::class, 'destroy']);
            Route::post('/{id}/complete', [TransactionController::class, 'complete']);
            Route::post('/{id}/cancel', [TransactionController::class, 'cancel']);
            Route::post('/{id}/reverse', [TransactionController::class, 'reverse']);
        });

        // Invoices
        Route::prefix('invoices')->group(function () {
            Route::get('/', [InvoiceController::class, 'index']);
            Route::post('/', [InvoiceController::class, 'store']);
            Route::get('/statistics', [InvoiceController::class, 'statistics']);
            Route::get('/overdue', [InvoiceController::class, 'overdue']);
            Route::get('/export', [InvoiceController::class, 'export']);
            Route::get('/{id}', [InvoiceController::class, 'show']);
            Route::put('/{id}', [InvoiceController::class, 'update']);
            Route::delete('/{id}', [InvoiceController::class, 'destroy']);
            Route::post('/{id}/send', [InvoiceController::class, 'send']);
            Route::post('/{id}/payment', [InvoiceController::class, 'recordPayment']);
            Route::post('/{id}/void', [InvoiceController::class, 'void']);
            Route::get('/{id}/pdf', [InvoiceController::class, 'generatePdf']);
        });

        // Payments
        Route::prefix('payments')->group(function () {
            Route::get('/', [PaymentController::class, 'index']);
            Route::post('/', [PaymentController::class, 'store']);
            Route::get('/statistics', [PaymentController::class, 'statistics']);
            Route::get('/export', [PaymentController::class, 'export']);
            Route::get('/{id}', [PaymentController::class, 'show']);
            Route::put('/{id}', [PaymentController::class, 'update']);
            Route::delete('/{id}', [PaymentController::class, 'destroy']);
            Route::post('/{id}/verify', [PaymentController::class, 'verify']);
            Route::post('/{id}/refund', [PaymentController::class, 'refund']);
        });

        // Cash Flow
        Route::prefix('cashflow')->group(function () {
            Route::get('/', function () {
                return response()->json(['message' => 'Cash flow report']);
            });
            Route::get('/summary', function () {
                return response()->json(['message' => 'Cash flow summary']);
            });
        });
    });

    // Sales module routes
    Route::prefix('sales')->group(function () {
        // Orders
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store']);
            Route::get('/statistics', [OrderController::class, 'statistics']);
            Route::get('/export', [OrderController::class, 'export']);
            Route::get('/{id}', [OrderController::class, 'show']);
            Route::put('/{id}', [OrderController::class, 'update']);
            Route::delete('/{id}', [OrderController::class, 'destroy']);
            Route::post('/{id}/confirm', [OrderController::class, 'confirm']);
            Route::post('/{id}/process', [OrderController::class, 'process']);
            Route::post('/{id}/ship', [OrderController::class, 'ship']);
            Route::post('/{id}/deliver', [OrderController::class, 'deliver']);
            Route::post('/{id}/complete', [OrderController::class, 'complete']);
            Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
            Route::post('/{id}/items', [OrderController::class, 'addItem']);
            Route::delete('/{id}/items/{itemId}', [OrderController::class, 'removeItem']);
        });

        // Customers
        Route::prefix('customers')->group(function () {
            Route::get('/', [CustomerController::class, 'index']);
            Route::post('/', [CustomerController::class, 'store']);
            Route::get('/{id}', [CustomerController::class, 'show']);
            Route::put('/{id}', [CustomerController::class, 'update']);
            Route::delete('/{id}', [CustomerController::class, 'destroy']);
            Route::get('/{id}/orders', [CustomerController::class, 'orders']);
            Route::get('/{id}/invoices', [CustomerController::class, 'invoices']);
            Route::get('/{id}/payments', [CustomerController::class, 'payments']);
            Route::get('/{id}/statement', [CustomerController::class, 'statement']);
            Route::put('/{id}/credit-limit', [CustomerController::class, 'updateCreditLimit']);
        });
    });

    // Inventory module routes
    Route::prefix('inventory')->group(function () {
        // Stock Movements
        Route::prefix('stock-movements')->group(function () {
            Route::get('/', [StockMovementController::class, 'index']);
            Route::post('/', [StockMovementController::class, 'store']);
            Route::get('/summary', [StockMovementController::class, 'summary']);
            Route::get('/by-item', [StockMovementController::class, 'byItem']);
            Route::get('/export', [StockMovementController::class, 'export']);
            Route::get('/{id}', [StockMovementController::class, 'show']);
        });

        // Stock Status and Reports
        Route::get('/low-stock', [StockMovementController::class, 'lowStock']);
        Route::get('/valuation', [StockMovementController::class, 'valuation']);
        Route::post('/stock-take', [StockMovementController::class, 'stockTake']);
    });

    // Suppliers module routes
    Route::prefix('suppliers')->group(function () {
        Route::get('/', [SupplierController::class, 'index']);
        Route::post('/', [SupplierController::class, 'store']);
        Route::get('/statistics', [SupplierController::class, 'statistics']);
        Route::get('/export', [SupplierController::class, 'export']);
        Route::get('/{id}', [SupplierController::class, 'show']);
        Route::put('/{id}', [SupplierController::class, 'update']);
        Route::delete('/{id}', [SupplierController::class, 'destroy']);
        Route::post('/{id}/toggle-status', [SupplierController::class, 'toggleStatus']);
        Route::get('/{id}/raw-materials', [SupplierController::class, 'rawMaterials']);
        Route::get('/{id}/purchase-orders', [SupplierController::class, 'purchaseOrders']);
    });

    

    // Reports module routes
    Route::prefix('reports')->group(function () {
        Route::get('/sales', function () {
            return response()->json(['message' => 'Sales report']);
        });
        Route::get('/production', function () {
            return response()->json(['message' => 'Production report']);
        });
        Route::get('/inventory', function () {
            return response()->json(['message' => 'Inventory report']);
        });
        Route::get('/financial', function () {
            return response()->json(['message' => 'Financial report']);
        });
        Route::get('/customers', function () {
            return response()->json(['message' => 'Customers report']);
        });
        Route::get('/suppliers', function () {
            return response()->json(['message' => 'Suppliers report']);
        });
    });

    // Categories routes
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/tree', [CategoryController::class, 'tree']);
        Route::get('/{id}', [CategoryController::class, 'show']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });

    // Activity Logs routes
    Route::prefix('activity-logs')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Activity logs list']);
        });
        Route::get('/statistics', function () {
            return response()->json(['message' => 'Activity logs statistics']);
        });
    });

    // Users management routes (admin only)
    Route::prefix('users')->middleware('role:Administrator')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Users list']);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Create user']);
        });
        Route::get('/{id}', function ($id) {
            return response()->json(['message' => 'Get user ' . $id]);
        });
        Route::put('/{id}', function ($id) {
            return response()->json(['message' => 'Update user ' . $id]);
        });
        Route::delete('/{id}', function ($id) {
            return response()->json(['message' => 'Delete user ' . $id]);
        });
        Route::post('/{id}/activate', function ($id) {
            return response()->json(['message' => 'Activate user ' . $id]);
        });
        Route::post('/{id}/deactivate', function ($id) {
            return response()->json(['message' => 'Deactivate user ' . $id]);
        });
    });

    // System settings routes (admin only)
    Route::prefix('settings')->middleware('role:Administrator')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'System settings']);
        });
        Route::put('/', function () {
            return response()->json(['message' => 'Update settings']);
        });
    });

    // File upload routes
    Route::prefix('files')->group(function () {
        Route::post('/upload', [FileUploadController::class, 'upload']);
        Route::post('/upload-multiple', [FileUploadController::class, 'uploadMultiple']);
        Route::delete('/delete', [FileUploadController::class, 'delete']);
        Route::get('/list', [FileUploadController::class, 'list']);
    });
    
    // Direct raw materials routes (for frontend compatibility)
    Route::prefix('raw-materials')->group(function () {
        Route::get('/', [RawMaterialController::class, 'index']);
        Route::post('/', [RawMaterialController::class, 'store']);
        Route::get('/stats', [RawMaterialController::class, 'statistics']);
        Route::get('/low-stock', [RawMaterialController::class, 'lowStock']);
        Route::get('/{id}', [RawMaterialController::class, 'show']);
        Route::put('/{id}', [RawMaterialController::class, 'update']);
        Route::delete('/{id}', [RawMaterialController::class, 'destroy']);
        Route::post('/{id}/adjust-stock', [RawMaterialController::class, 'adjustStock']);
    });
});

// Health check route
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
});