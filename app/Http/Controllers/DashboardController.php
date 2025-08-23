<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\ProductionOrder;
use App\Models\RawMaterial;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function stats(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year
        $startDate = $this->getStartDate($period);
        $previousStartDate = $this->getPreviousStartDate($period);

        // Sales Statistics
        $currentSales = Order::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->sum('total_amount');

        $previousSales = Order::where('status', 'completed')
            ->whereBetween('created_at', [$previousStartDate, $startDate])
            ->sum('total_amount');

        $salesGrowth = $previousSales > 0 
            ? round((($currentSales - $previousSales) / $previousSales) * 100, 2)
            : 0;

        // Orders Statistics
        $currentOrders = Order::where('created_at', '>=', $startDate)->count();
        $previousOrders = Order::whereBetween('created_at', [$previousStartDate, $startDate])->count();
        $ordersGrowth = $previousOrders > 0 
            ? round((($currentOrders - $previousOrders) / $previousOrders) * 100, 2)
            : 0;

        // Customers Statistics
        $currentCustomers = Customer::where('created_at', '>=', $startDate)->count();
        $totalCustomers = Customer::where('is_active', true)->count();

        // Production Statistics
        $currentProduction = ProductionOrder::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->sum('actual_quantity');

        $previousProduction = ProductionOrder::where('status', 'completed')
            ->whereBetween('created_at', [$previousStartDate, $startDate])
            ->sum('actual_quantity');

        $productionGrowth = $previousProduction > 0 
            ? round((($currentProduction - $previousProduction) / $previousProduction) * 100, 2)
            : 0;

        // Financial Statistics
        $totalRevenue = Transaction::where('type', 'income')
            ->where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->sum('amount');

        $totalExpenses = Transaction::where('type', 'expense')
            ->where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->sum('amount');

        $netProfit = $totalRevenue - $totalExpenses;
        $profitMargin = $totalRevenue > 0 
            ? round(($netProfit / $totalRevenue) * 100, 2)
            : 0;

        // Inventory Statistics
        $lowStockProducts = Product::belowReorderPoint()->count();
        $lowStockMaterials = RawMaterial::belowReorderPoint()->count();
        $totalInventoryValue = Product::where('is_active', true)->get()->sum('stock_value') +
                               RawMaterial::where('is_active', true)->get()->sum('stock_value');

        // Pending Tasks
        $pendingOrders = Order::whereIn('status', ['pending', 'confirmed'])->count();
        $pendingProduction = ProductionOrder::where('status', 'pending')->count();
        $overdueInvoices = Invoice::overdue()->count();
        $pendingPayments = Invoice::unpaid()->sum('balance_due');

        return $this->successResponse([
            'sales' => [
                'current' => $currentSales,
                'previous' => $previousSales,
                'growth' => $salesGrowth,
                'growth_type' => $salesGrowth >= 0 ? 'increase' : 'decrease',
            ],
            'orders' => [
                'current' => $currentOrders,
                'previous' => $previousOrders,
                'growth' => $ordersGrowth,
                'growth_type' => $ordersGrowth >= 0 ? 'increase' : 'decrease',
                'pending' => $pendingOrders,
            ],
            'customers' => [
                'new' => $currentCustomers,
                'total' => $totalCustomers,
            ],
            'production' => [
                'current' => $currentProduction,
                'previous' => $previousProduction,
                'growth' => $productionGrowth,
                'growth_type' => $productionGrowth >= 0 ? 'increase' : 'decrease',
                'pending' => $pendingProduction,
            ],
            'financial' => [
                'revenue' => $totalRevenue,
                'expenses' => $totalExpenses,
                'profit' => $netProfit,
                'profit_margin' => $profitMargin,
                'pending_payments' => $pendingPayments,
                'overdue_invoices' => $overdueInvoices,
            ],
            'inventory' => [
                'low_stock_products' => $lowStockProducts,
                'low_stock_materials' => $lowStockMaterials,
                'total_value' => $totalInventoryValue,
            ],
        ]);
    }

    /**
     * Get dashboard charts data
     */
    public function charts(Request $request)
    {
        $period = $request->get('period', 'week'); // week, month, year
        $startDate = $this->getStartDate($period);

        // Sales Chart
        $salesData = $this->getSalesChartData($startDate, $period);

        // Orders Chart
        $ordersData = $this->getOrdersChartData($startDate, $period);

        // Production Chart
        $productionData = $this->getProductionChartData($startDate, $period);

        // Top Products
        $topProducts = $this->getTopProducts($startDate);

        // Top Customers
        $topCustomers = $this->getTopCustomers($startDate);

        // Category Sales Distribution
        $categorySales = $this->getCategorySales($startDate);

        // Cash Flow
        $cashFlow = $this->getCashFlowData($startDate, $period);

        return $this->successResponse([
            'sales' => $salesData,
            'orders' => $ordersData,
            'production' => $productionData,
            'top_products' => $topProducts,
            'top_customers' => $topCustomers,
            'category_sales' => $categorySales,
            'cash_flow' => $cashFlow,
        ]);
    }

    /**
     * Get recent activities
     */
    public function recentActivities(Request $request)
    {
        $limit = $request->get('limit', 20);

        // Recent Orders
        $recentOrders = Order::with('customer')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'type' => 'order',
                    'icon' => 'shopping-cart',
                    'title' => 'New Order #' . $order->order_number,
                    'description' => 'From ' . $order->customer->name,
                    'amount' => $order->total_amount,
                    'status' => $order->status,
                    'time' => $order->created_at->diffForHumans(),
                    'timestamp' => $order->created_at,
                ];
            });

        // Recent Productions
        $recentProductions = ProductionOrder::with('product')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($production) {
                return [
                    'type' => 'production',
                    'icon' => 'factory',
                    'title' => 'Production #' . $production->order_number,
                    'description' => $production->product->name . ' x ' . $production->quantity,
                    'status' => $production->status,
                    'time' => $production->created_at->diffForHumans(),
                    'timestamp' => $production->created_at,
                ];
            });

        // Recent Payments
        $recentPayments = Transaction::with('customer')
            ->where('type', 'income')
            ->where('category', 'payment_received')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'type' => 'payment',
                    'icon' => 'dollar-sign',
                    'title' => 'Payment Received',
                    'description' => 'From ' . ($payment->customer ? $payment->customer->name : 'Unknown'),
                    'amount' => $payment->amount,
                    'status' => $payment->status,
                    'time' => $payment->created_at->diffForHumans(),
                    'timestamp' => $payment->created_at,
                ];
            });

        // Combine and sort by timestamp
        $activities = $recentOrders->concat($recentProductions)
            ->concat($recentPayments)
            ->sortByDesc('timestamp')
            ->take($limit)
            ->values();

        return $this->successResponse($activities);
    }

    /**
     * Get quick stats for header/widgets
     */
    public function quickStats()
    {
        $today = now()->startOfDay();

        return $this->successResponse([
            'today_sales' => Order::whereDate('created_at', $today)
                ->where('status', 'completed')
                ->sum('total_amount'),
            'today_orders' => Order::whereDate('created_at', $today)->count(),
            'pending_orders' => Order::whereIn('status', ['pending', 'confirmed'])->count(),
            'low_stock_alerts' => Product::belowMinimumStock()->count() + 
                                  RawMaterial::belowMinimumStock()->count(),
            'pending_production' => ProductionOrder::where('status', 'pending')->count(),
            'overdue_invoices' => Invoice::overdue()->count(),
        ]);
    }

    /**
     * Helper: Get start date based on period
     */
    private function getStartDate($period)
    {
        return match($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };
    }

    /**
     * Helper: Get previous period start date
     */
    private function getPreviousStartDate($period)
    {
        return match($period) {
            'day' => now()->subDay()->startOfDay(),
            'week' => now()->subWeek()->startOfWeek(),
            'month' => now()->subMonth()->startOfMonth(),
            'year' => now()->subYear()->startOfYear(),
            default => now()->subMonth()->startOfMonth(),
        };
    }

    /**
     * Helper: Get sales chart data
     */
    private function getSalesChartData($startDate, $period)
    {
        $groupBy = match($period) {
            'week' => 'DATE(created_at)',
            'month' => 'DATE(created_at)',
            'year' => "DATE_FORMAT(created_at, '%Y-%m')",
            default => 'DATE(created_at)',
        };

        return Order::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw($groupBy . ' as date'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Helper: Get orders chart data
     */
    private function getOrdersChartData($startDate, $period)
    {
        $groupBy = match($period) {
            'week' => 'DATE(created_at)',
            'month' => 'DATE(created_at)',
            'year' => "DATE_FORMAT(created_at, '%Y-%m')",
            default => 'DATE(created_at)',
        };

        return Order::where('created_at', '>=', $startDate)
            ->select(
                DB::raw($groupBy . ' as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled")
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Helper: Get production chart data
     */
    private function getProductionChartData($startDate, $period)
    {
        $groupBy = match($period) {
            'week' => 'DATE(created_at)',
            'month' => 'DATE(created_at)',
            'year' => "DATE_FORMAT(created_at, '%Y-%m')",
            default => 'DATE(created_at)',
        };

        return ProductionOrder::where('created_at', '>=', $startDate)
            ->select(
                DB::raw($groupBy . ' as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(quantity) as planned_quantity'),
                DB::raw('SUM(actual_quantity) as actual_quantity')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Helper: Get top products
     */
    private function getTopProducts($startDate, $limit = 5)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', 'completed')
            ->where('orders.created_at', '>=', $startDate)
            ->select(
                'products.id',
                'products.name',
                'products.code',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.code')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();
    }

    /**
     * Helper: Get top customers
     */
    private function getTopCustomers($startDate, $limit = 5)
    {
        return Customer::withSum(['orders as total_spent' => function ($query) use ($startDate) {
                $query->where('status', 'completed')
                      ->where('created_at', '>=', $startDate);
            }], 'total_amount')
            ->withCount(['orders as order_count' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get(['id', 'name', 'code', 'email']);
    }

    /**
     * Helper: Get category sales distribution
     */
    private function getCategorySales($startDate)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', 'completed')
            ->where('orders.created_at', '>=', $startDate)
            ->select(
                'categories.name',
                DB::raw('SUM(order_items.total) as total')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Helper: Get cash flow data
     */
    private function getCashFlowData($startDate, $period)
    {
        $groupBy = match($period) {
            'week' => 'DATE(transaction_date)',
            'month' => 'DATE(transaction_date)',
            'year' => "DATE_FORMAT(transaction_date, '%Y-%m')",
            default => 'DATE(transaction_date)',
        };

        return Transaction::where('status', 'completed')
            ->where('transaction_date', '>=', $startDate)
            ->select(
                DB::raw($groupBy . ' as date'),
                DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income"),
                DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense"),
                DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END) as net")
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}