<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Log critical errors with context
            if ($this->shouldReport($e)) {
                Log::error('Application Error', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                    'ip' => request()->ip(),
                    'user_id' => auth()->id(),
                ]);
            }
        });

        // Custom rendering for API responses
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return $this->handleApiException($e, $request);
            }
        });
    }

    /**
     * Handle API exceptions
     */
    protected function handleApiException(Throwable $e, $request)
    {
        $response = [
            'success' => false,
            'message' => 'An error occurred',
        ];

        // Model not found
        if ($e instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($e->getModel()));
            $response['message'] = ucfirst($modelName) . ' not found';
            $response['error'] = 'RESOURCE_NOT_FOUND';
            return response()->json($response, 404);
        }

        // Route not found
        if ($e instanceof NotFoundHttpException) {
            $response['message'] = 'Endpoint not found';
            $response['error'] = 'ENDPOINT_NOT_FOUND';
            return response()->json($response, 404);
        }

        // Validation errors
        if ($e instanceof ValidationException) {
            $response['message'] = 'Validation failed';
            $response['error'] = 'VALIDATION_ERROR';
            $response['errors'] = $e->errors();
            return response()->json($response, 422);
        }

        // Authentication errors
        if ($e instanceof AuthenticationException) {
            $response['message'] = 'Unauthenticated. Please login.';
            $response['error'] = 'UNAUTHENTICATED';
            return response()->json($response, 401);
        }

        // Authorization errors
        if ($e instanceof AuthorizationException) {
            $response['message'] = 'You are not authorized to perform this action';
            $response['error'] = 'UNAUTHORIZED';
            return response()->json($response, 403);
        }

        // Method not allowed
        if ($e instanceof MethodNotAllowedHttpException) {
            $response['message'] = 'Method not allowed for this endpoint';
            $response['error'] = 'METHOD_NOT_ALLOWED';
            $response['allowed_methods'] = $e->getHeaders()['Allow'] ?? '';
            return response()->json($response, 405);
        }

        // Database errors
        if ($e instanceof QueryException) {
            // Log the actual database error
            Log::error('Database Error', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
            ]);

            // Check for specific database errors
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $response['message'] = 'Duplicate entry. This record already exists.';
                $response['error'] = 'DUPLICATE_ENTRY';
                return response()->json($response, 409);
            }

            if (str_contains($e->getMessage(), 'foreign key constraint')) {
                $response['message'] = 'Cannot perform this action due to related records.';
                $response['error'] = 'FOREIGN_KEY_CONSTRAINT';
                return response()->json($response, 409);
            }

            // Generic database error (don't expose details in production)
            if (config('app.debug')) {
                $response['message'] = 'Database error: ' . $e->getMessage();
            } else {
                $response['message'] = 'A database error occurred. Please try again.';
            }
            $response['error'] = 'DATABASE_ERROR';
            return response()->json($response, 500);
        }

        // Generic server error
        if (config('app.debug')) {
            $response['message'] = $e->getMessage();
            $response['error'] = class_basename($e);
            $response['file'] = $e->getFile();
            $response['line'] = $e->getLine();
            $response['trace'] = collect($e->getTrace())->take(5)->toArray();
        } else {
            $response['message'] = 'An unexpected error occurred. Please try again later.';
            $response['error'] = 'SERVER_ERROR';
        }

        // Log unexpected errors
        Log::error('Unexpected API Error', [
            'exception' => class_basename($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return response()->json($response, 500);
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please login.',
                'error' => 'UNAUTHENTICATED',
            ], 401);
        }

        return redirect()->guest(route('login'));
    }
}