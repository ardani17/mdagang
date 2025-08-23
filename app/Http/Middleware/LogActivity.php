<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActivityLog;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users and non-GET requests
        if ($request->user() && !in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            try {
                $this->logActivity($request, $response);
            } catch (\Exception $e) {
                // Don't break the request if logging fails
                \Log::error('Activity logging failed: ' . $e->getMessage());
            }
        }

        return $response;
    }

    /**
     * Log the activity
     */
    protected function logActivity(Request $request, Response $response)
    {
        $user = $request->user();
        $method = $request->method();
        $path = $request->path();
        $statusCode = $response->getStatusCode();

        // Determine action type based on HTTP method
        $actionType = match($method) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => strtolower($method),
        };

        // Extract model information from path
        $pathSegments = explode('/', $path);
        $modelType = $this->extractModelType($pathSegments);
        $modelId = $this->extractModelId($pathSegments);

        // Create activity log
        ActivityLog::create([
            'user_id' => $user->id,
            'action_type' => $actionType,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $this->generateDescription($method, $path, $statusCode),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'changes' => json_encode([
                'method' => $method,
                'path' => $path,
                'status' => $statusCode,
                'request_data' => $this->sanitizeRequestData($request->all()),
            ]),
        ]);
    }

    /**
     * Extract model type from path segments
     */
    protected function extractModelType(array $segments): ?string
    {
        // Map API endpoints to model types
        $modelMap = [
            'suppliers' => 'Supplier',
            'raw-materials' => 'RawMaterial',
            'products' => 'Product',
            'recipes' => 'Recipe',
            'production-orders' => 'ProductionOrder',
            'quality-inspections' => 'QualityInspection',
            'invoices' => 'Invoice',
            'payments' => 'Payment',
            'transactions' => 'Transaction',
            'orders' => 'Order',
            'purchase-orders' => 'PurchaseOrder',
            'stock-movements' => 'StockMovement',
            'customers' => 'Customer',
            'categories' => 'Category',
            'users' => 'User',
        ];

        foreach ($segments as $segment) {
            if (isset($modelMap[$segment])) {
                return $modelMap[$segment];
            }
        }

        return null;
    }

    /**
     * Extract model ID from path segments
     */
    protected function extractModelId(array $segments): ?int
    {
        foreach ($segments as $segment) {
            if (is_numeric($segment)) {
                return (int) $segment;
            }
        }

        return null;
    }

    /**
     * Generate human-readable description
     */
    protected function generateDescription(string $method, string $path, int $statusCode): string
    {
        $action = match($method) {
            'POST' => 'created',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default => strtolower($method),
        };

        $status = $statusCode >= 200 && $statusCode < 300 ? 'successfully' : 'failed';

        return "User {$action} resource at {$path} {$status}";
    }

    /**
     * Sanitize request data to remove sensitive information
     */
    protected function sanitizeRequestData(array $data): array
    {
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'api_key', 'secret'];

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '***REDACTED***';
            }
        }

        return $data;
    }
}