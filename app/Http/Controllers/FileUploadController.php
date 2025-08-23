<?php

namespace App\Http\Controllers;

use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\ActivityLog;

class FileUploadController extends Controller
{
    use FileUploadTrait;

    /**
     * Upload a single file
     */
    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // Max 10MB
            'type' => 'required|in:invoice,purchase_order,product_image,document,report',
            'reference_id' => 'nullable|integer',
            'reference_type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        try {
            $file = $request->file('file');
            $type = $request->type;

            // Define allowed extensions and folder based on type
            $config = $this->getUploadConfig($type);
            
            // Validate file
            $errors = $this->validateFile($file, $config['extensions'], $config['max_size']);
            if (!empty($errors)) {
                return $this->errorResponse('File validation failed', $errors);
            }

            // Upload file
            $result = $this->uploadFile($file, $config['folder']);
            
            if (!$result['success']) {
                return $this->errorResponse($result['error']);
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'upload',
                'model_type' => 'File',
                'model_id' => $request->reference_id ?? 0,
                'description' => "Uploaded {$type} file: {$result['original_name']}",
                'changes' => json_encode($result),
            ]);

            return $this->successResponse([
                'message' => 'File uploaded successfully',
                'file' => $result,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to upload file: ' . $e->getMessage());
        }
    }

    /**
     * Upload multiple files
     */
    public function uploadMultiple(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'files' => 'required|array',
            'files.*' => 'required|file|max:10240',
            'type' => 'required|in:invoice,purchase_order,product_image,document,report',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        try {
            $files = $request->file('files');
            $type = $request->type;
            $config = $this->getUploadConfig($type);
            
            $uploadedFiles = [];
            $errors = [];

            foreach ($files as $index => $file) {
                // Validate each file
                $fileErrors = $this->validateFile($file, $config['extensions'], $config['max_size']);
                if (!empty($fileErrors)) {
                    $errors[$index] = $fileErrors;
                    continue;
                }

                // Upload file
                $result = $this->uploadFile($file, $config['folder']);
                if ($result['success']) {
                    $uploadedFiles[] = $result;
                } else {
                    $errors[$index] = [$result['error']];
                }
            }

            // Log activity
            if (!empty($uploadedFiles)) {
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action_type' => 'upload',
                    'model_type' => 'File',
                    'model_id' => 0,
                    'description' => "Uploaded " . count($uploadedFiles) . " {$type} files",
                    'changes' => json_encode($uploadedFiles),
                ]);
            }

            return $this->successResponse([
                'message' => 'Files processed',
                'uploaded' => $uploadedFiles,
                'errors' => $errors,
                'total_uploaded' => count($uploadedFiles),
                'total_failed' => count($errors),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to upload files: ' . $e->getMessage());
        }
    }

    /**
     * Delete a file
     */
    public function delete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'path' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        try {
            $path = $request->path;
            
            if ($this->deleteFile($path)) {
                // Log activity
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action_type' => 'delete',
                    'model_type' => 'File',
                    'model_id' => 0,
                    'description' => "Deleted file: {$path}",
                    'changes' => json_encode(['path' => $path]),
                ]);

                return $this->successResponse([
                    'message' => 'File deleted successfully',
                ]);
            } else {
                return $this->errorResponse('File not found or could not be deleted');
            }
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete file: ' . $e->getMessage());
        }
    }

    /**
     * Get upload configuration based on type
     */
    private function getUploadConfig(string $type): array
    {
        $configs = [
            'invoice' => [
                'folder' => 'invoices',
                'extensions' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
                'max_size' => 5120, // 5MB
            ],
            'purchase_order' => [
                'folder' => 'purchase-orders',
                'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
                'max_size' => 5120,
            ],
            'product_image' => [
                'folder' => 'products',
                'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'max_size' => 2048, // 2MB
            ],
            'document' => [
                'folder' => 'documents',
                'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'],
                'max_size' => 10240, // 10MB
            ],
            'report' => [
                'folder' => 'reports',
                'extensions' => ['pdf', 'xls', 'xlsx', 'csv'],
                'max_size' => 10240,
            ],
        ];

        return $configs[$type] ?? $configs['document'];
    }

    /**
     * Get uploaded files list
     */
    public function list(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|in:invoice,purchase_order,product_image,document,report',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors()->toArray());
        }

        try {
            // This would typically query a files table in the database
            // For now, returning a placeholder response
            return $this->successResponse([
                'message' => 'Files list retrieved',
                'files' => [],
                'total' => 0,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve files: ' . $e->getMessage());
        }
    }
}