# Implementation Rules & Best Practices

## 1. Coding Standards

### 1.1 PHP/Laravel Standards
```php
// ✅ GOOD: Clear naming, type hints, return types
public function calculateProductionCost(Product $product): ProductionCost
{
    $ingredientCost = $this->calculateIngredientCost($product);
    $laborCost = $this->calculateLaborCost($product);
    $overheadCost = $this->calculateOverheadCost($product);
    
    return ProductionCost::create([
        'product_id' => $product->id,
        'ingredient_cost' => $ingredientCost,
        'labor_cost' => $laborCost,
        'overhead_cost' => $overheadCost,
        'total_cost' => $ingredientCost + $laborCost + $overheadCost,
    ]);
}

// ❌ BAD: No type hints, unclear naming
public function calc($p)
{
    $cost = $this->getCost($p);
    return $cost;
}
```

### 1.2 Database Conventions
- Table names: Plural, snake_case (e.g., `products`, `order_items`)
- Column names: Snake_case (e.g., `created_at`, `unit_price`)
- Foreign keys: Singular with `_id` suffix (e.g., `product_id`, `user_id`)
- Indexes: Descriptive names (e.g., `idx_orders_customer_date`)
- Always use migrations, never modify database directly

### 1.3 API Response Standards
```php
// Success Response
return response()->json([
    'success' => true,
    'message' => 'Product created successfully',
    'data' => $product,
], 201);

// Error Response
return response()->json([
    'success' => false,
    'message' => 'Validation failed',
    'errors' => $validator->errors(),
], 422);
```

## 2. Business Logic Rules

### 2.1 Product Management
```php
// Rule: Production cost must be calculated before setting selling price
class ProductService
{
    public function createProduct(array $data): Product
    {
        DB::transaction(function () use ($data) {
            $product = Product::create($data);
            
            // Auto-calculate production cost if ingredients provided
            if (isset($data['ingredients'])) {
                $this->attachIngredients($product, $data['ingredients']);
                $this->calculateProductionCost($product);
            }
            
            // Ensure minimum profit margin (30%)
            $minSellingPrice = $product->total_cost * 1.3;
            if ($product->selling_price < $minSellingPrice) {
                $product->selling_price = $minSellingPrice;
                $product->save();
            }
            
            return $product;
        });
    }
}
```

### 2.2 Order Processing
```php
// Rule: Orders must follow status workflow
class OrderService
{
    const STATUS_WORKFLOW = [
        'draft' => ['confirmed', 'cancelled'],
        'confirmed' => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped' => ['delivered'],
        'delivered' => [],
        'cancelled' => [],
    ];
    
    public function updateStatus(Order $order, string $newStatus): void
    {
        $allowedStatuses = self::STATUS_WORKFLOW[$order->status] ?? [];
        
        if (!in_array($newStatus, $allowedStatuses)) {
            throw new InvalidStatusTransitionException(
                "Cannot change status from {$order->status} to {$newStatus}"
            );
        }
        
        $order->status = $newStatus;
        $order->save();
        
        // Trigger status-specific actions
        $this->handleStatusChange($order, $newStatus);
    }
    
    private function handleStatusChange(Order $order, string $status): void
    {
        switch ($status) {
            case 'confirmed':
                $this->reserveStock($order);
                $this->createJournalEntry($order);
                break;
            case 'shipped':
                $this->updateInventory($order);
                $this->calculateShippingCost($order);
                break;
            case 'delivered':
                $this->recordIncome($order);
                $this->updateCustomerBalance($order);
                break;
            case 'cancelled':
                $this->releaseStock($order);
                $this->reverseJournalEntry($order);
                break;
        }
    }
}
```

### 2.3 Financial Rules
```php
// Rule: All financial transactions must maintain double-entry bookkeeping
class JournalService
{
    public function createSalesEntry(Order $order): JournalEntry
    {
        $entry = JournalEntry::create([
            'entry_number' => $this->generateEntryNumber(),
            'entry_date' => now(),
            'description' => "Sales Order #{$order->order_number}",
            'reference_type' => 'order',
            'reference_id' => $order->id,
            'user_id' => auth()->id(),
        ]);
        
        // Debit: Accounts Receivable or Cash
        $entry->lines()->create([
            'account_id' => $this->getAccount('accounts_receivable'),
            'debit' => $order->total_amount,
            'credit' => 0,
        ]);
        
        // Credit: Sales Revenue
        $entry->lines()->create([
            'account_id' => $this->getAccount('sales_revenue'),
            'debit' => 0,
            'credit' => $order->subtotal,
        ]);
        
        // Credit: Tax Payable
        if ($order->tax_amount > 0) {
            $entry->lines()->create([
                'account_id' => $this->getAccount('tax_payable'),
                'debit' => 0,
                'credit' => $order->tax_amount,
            ]);
        }
        
        // Validate balanced entry
        $this->validateEntry($entry);
        
        return $entry;
    }
    
    private function validateEntry(JournalEntry $entry): void
    {
        $totalDebit = $entry->lines()->sum('debit');
        $totalCredit = $entry->lines()->sum('credit');
        
        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw new UnbalancedEntryException(
                "Journal entry is not balanced. Debit: {$totalDebit}, Credit: {$totalCredit}"
            );
        }
    }
}
```

### 2.4 Inventory Management
```php
// Rule: Stock movements must be tracked with proper documentation
class InventoryService
{
    public function adjustStock(
        Product $product, 
        int $quantity, 
        string $type, 
        string $reason,
        $reference = null
    ): StockMovement {
        DB::transaction(function () use ($product, $quantity, $type, $reason, $reference) {
            // Record movement
            $movement = StockMovement::create([
                'product_id' => $product->id,
                'type' => $type,
                'quantity' => $quantity,
                'reason' => $reason,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference ? $reference->id : null,
                'user_id' => auth()->id(),
            ]);
            
            // Update product stock
            if ($type === 'in') {
                $product->increment('current_stock', $quantity);
            } elseif ($type === 'out') {
                if ($product->current_stock < $quantity) {
                    throw new InsufficientStockException(
                        "Insufficient stock for {$product->name}. Available: {$product->current_stock}"
                    );
                }
                $product->decrement('current_stock', $quantity);
            }
            
            // Check for low stock alert
            if ($product->current_stock <= $product->min_stock) {
                $this->createLowStockAlert($product);
            }
            
            return $movement;
        });
    }
}
```

## 3. Validation Rules

### 3.1 Product Validation
```php
class ProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $this->product?->id,
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:food,beverage',
            'unit' => 'required|string|max:50',
            'base_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:base_price',
            'min_stock' => 'integer|min:0',
            'current_stock' => 'integer|min:0',
            'ingredients' => 'array',
            'ingredients.*.name' => 'required_with:ingredients|string',
            'ingredients.*.quantity' => 'required_with:ingredients|numeric|min:0',
            'ingredients.*.unit' => 'required_with:ingredients|string',
            'ingredients.*.cost_per_unit' => 'required_with:ingredients|numeric|min:0',
        ];
    }
    
    public function messages(): array
    {
        return [
            'selling_price.gte' => 'Selling price must be greater than or equal to base price',
            'sku.unique' => 'This SKU is already in use',
        ];
    }
}
```

### 3.2 Order Validation
```php
class OrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'delivery_date' => 'nullable|date|after_or_equal:order_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount_amount' => 'numeric|min:0|max:subtotal',
            'shipping_method' => 'required_if:status,confirmed|string',
            'payment_method' => 'required_if:payment_status,paid|string',
        ];
    }
    
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check stock availability
            foreach ($this->items as $index => $item) {
                $product = Product::find($item['product_id']);
                if ($product && $product->current_stock < $item['quantity']) {
                    $validator->errors()->add(
                        "items.{$index}.quantity",
                        "Insufficient stock. Available: {$product->current_stock}"
                    );
                }
            }
            
            // Check customer credit limit
            if ($this->customer_id) {
                $customer = Customer::find($this->customer_id);
                $orderTotal = $this->calculateTotal();
                if ($customer && ($customer->outstanding_balance + $orderTotal) > $customer->credit_limit) {
                    $validator->errors()->add(
                        'customer_id',
                        'Order exceeds customer credit limit'
                    );
                }
            }
        });
    }
}
```

## 4. Security Rules

### 4.1 Authentication & Authorization
```php
// Middleware for role-based access
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/calculate-cost', [ProductController::class, 'calculateCost']);
});

// Policy for resource authorization
class ProductPolicy
{
    public function update(User $user, Product $product): bool
    {
        return $user->hasRole(['admin', 'manager']) || 
               ($user->hasRole('staff') && $product->created_by === $user->id);
    }
    
    public function delete(User $user, Product $product): bool
    {
        return $user->hasRole('admin');
    }
}
```

### 4.2 Data Sanitization
```php
// Always sanitize user input
class SanitizationService
{
    public function sanitizeInput(array $data): array
    {
        return collect($data)->map(function ($value) {
            if (is_string($value)) {
                return strip_tags(trim($value));
            }
            return $value;
        })->toArray();
    }
    
    public function sanitizeHtml(string $html): string
    {
        return clean($html, [
            'HTML.Allowed' => 'p,br,strong,em,ul,ol,li',
        ]);
    }
}
```

## 5. Performance Rules

### 5.1 Query Optimization
```php
// ✅ GOOD: Eager loading, selective columns
$orders = Order::with(['customer:id,name,email', 'items.product:id,name,sku'])
    ->select('id', 'order_number', 'customer_id', 'total_amount', 'status')
    ->where('status', 'confirmed')
    ->orderBy('created_at', 'desc')
    ->paginate(20);

// ❌ BAD: N+1 problem, loading all columns
$orders = Order::all();
foreach ($orders as $order) {
    echo $order->customer->name; // N+1 query
}
```

### 5.2 Caching Strategy
```php
class ProductService
{
    public function getActiveProducts(): Collection
    {
        return Cache::remember('active_products', 3600, function () {
            return Product::with('category')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }
    
    public function clearProductCache(): void
    {
        Cache::forget('active_products');
        Cache::tags(['products'])->flush();
    }
}
```

### 5.3 Job Queues for Heavy Operations
```php
// Queue job for report generation
class GenerateMonthlyReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public function handle(): void
    {
        $report = new MonthlyReportService();
        $data = $report->generate();
        
        // Store report
        Storage::put("reports/monthly-{$this->month}.pdf", $data);
        
        // Notify user
        $this->user->notify(new ReportReadyNotification($reportPath));
    }
}
```

## 6. Testing Rules

### 6.1 Unit Testing
```php
class ProductionCostTest extends TestCase
{
    public function test_production_cost_calculation()
    {
        $product = Product::factory()->create();
        $ingredients = [
            ['name' => 'Flour', 'quantity' => 1, 'unit' => 'kg', 'cost_per_unit' => 50],
            ['name' => 'Sugar', 'quantity' => 0.5, 'unit' => 'kg', 'cost_per_unit' => 60],
        ];
        
        $service = new ProductionCostService();
        $cost = $service->calculate($product, $ingredients);
        
        $this->assertEquals(80, $cost->ingredient_cost); // 50 + (0.5 * 60)
        $this->assertGreaterThan(0, $cost->total_cost);
    }
}
```

### 6.2 Feature Testing
```php
class OrderApiTest extends TestCase
{
    public function test_create_order_with_valid_data()
    {
        $user = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['current_stock' => 100]);
        
        $response = $this->actingAs($user)
            ->postJson('/api/orders', [
                'customer_id' => $customer->id,
                'order_date' => now()->toDateString(),
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 5,
                        'unit_price' => $product->selling_price,
                    ]
                ]
            ]);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'order_number', 'total_amount']
            ]);
        
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
        ]);
        
        $product->refresh();
        $this->assertEquals(95, $product->current_stock);
    }
}
```

## 7. Frontend Rules

### 7.1 Component Structure
```javascript
// Alpine.js component for product form
Alpine.data('productForm', () => ({
    product: {
        name: '',
        sku: '',
        category_id: '',
        type: 'food',
        ingredients: []
    },
    
    addIngredient() {
        this.product.ingredients.push({
            name: '',
            quantity: 0,
            unit: '',
            cost_per_unit: 0
        });
    },
    
    removeIngredient(index) {
        this.product.ingredients.splice(index, 1);
        this.calculateCost();
    },
    
    calculateCost() {
        const totalCost = this.product.ingredients.reduce((sum, item) => {
            return sum + (item.quantity * item.cost_per_unit);
        }, 0);
        
        this.product.base_price = totalCost;
        this.product.selling_price = totalCost * 1.3; // 30% markup
    },
    
    async submit() {
        try {
            const response = await fetch('/api/products', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.product)
            });
            
            if (response.ok) {
                window.location.href = '/products';
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
}));
```

### 7.2 Responsive Design
```html
<!-- Mobile-first responsive layout -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <!-- Card content -->
    </div>
</div>

<!-- Responsive table -->
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <!-- Table content -->
    </table>
</div>
```

### 7.3 Theme Switching
```javascript
// Theme manager
Alpine.store('theme', {
    current: localStorage.getItem('theme') || 'light',
    
    toggle() {
        this.current = this.current === 'light' ? 'dark' : 'light';
        localStorage.setItem('theme', this.current);
        this.apply();
    },
    
    apply() {
        if (this.current === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
});
```

## 8. Error Handling

### 8.1 Global Exception Handler
```php
// app/Exceptions/Handler.php
public function render($request, Throwable $exception)
{
    if ($request->expectsJson()) {
        if ($exception instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $exception->errors(),
            ], 422);
        }
        
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found',
            ], 404);
        }
        
        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action',
            ], 403);
        }
        
        // Log unexpected errors
        Log::error($exception->getMessage(), [
            'exception' => $exception,
            'request' => $request->all(),
            'user' => auth()->user()?->id,
        ]);
        
        return response()->json([
            'success' => false,
            'message' => app()->environment('production') 
                ? 'An error occurred' 
                : $exception->getMessage(),
        ], 500);
    }
    
    return parent::render($request, $exception);
}
```

## 9. Deployment Checklist

### 9.1 Pre-deployment
- [ ] Run all tests: `php artisan test`
- [ ] Check code style: `./vendor/bin/pint`
- [ ] Update dependencies: `composer update --no-dev`
- [ ] Compile assets: `npm run build`
- [ ] Clear caches: `php artisan optimize:clear`
- [ ] Check environment variables
- [ ] Backup database

### 9.2 Deployment
- [ ] Set APP_ENV=production
- [ ] Set APP_DEBUG=false
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Cache configuration: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Cache views: `php artisan view:cache`
- [ ] Set proper file permissions
- [ ] Configure queue workers
- [ ] Setup cron jobs

### 9.3 Post-deployment
- [ ] Verify application is running
- [ ] Check error logs
- [ ] Test critical features
- [ ] Monitor performance
- [ ] Verify backup systems

## 10. Maintenance Rules

### 10.1 Regular Tasks
- Daily: Check error logs, monitor disk space
- Weekly: Review performance metrics, update dependencies
- Monthly: Database optimization, security updates
- Quarterly: Full system audit, disaster recovery test

### 10.2 Database Maintenance
```sql
-- Optimize tables
VACUUM ANALYZE products;
VACUUM ANALYZE orders;
VACUUM ANALYZE order_items;

-- Rebuild indexes
REINDEX TABLE products;
REINDEX TABLE orders;

-- Check for unused indexes
SELECT schemaname, tablename, indexname, idx_scan
FROM pg_stat_user_indexes
WHERE idx_scan = 0
ORDER BY schemaname, tablename;
```

### 10.3 Monitoring Queries
```sql
-- Check slow queries
SELECT query, calls, mean_exec_time, max_exec_time
FROM pg_stat_statements
WHERE mean_exec_time > 1000
ORDER BY mean_exec_time DESC
LIMIT 10;

-- Check table sizes
SELECT 
    schemaname,
    tablename,
    pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) AS size
FROM pg_tables
WHERE schemaname = 'public'
ORDER BY pg_total_relation_size(schemaname||'.'||tablename) DESC;