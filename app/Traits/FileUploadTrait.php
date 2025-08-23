<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait FileUploadTrait
{
    /**
     * Upload a file to storage
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string $disk
     * @return array
     */
    public function uploadFile(UploadedFile $file, string $folder = 'uploads', string $disk = 'public'): array
    {
        try {
            // Generate unique filename
            $filename = Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Store file
            $path = $file->storeAs($folder, $filename, $disk);
            
            // Get file info
            return [
                'success' => true,
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'url' => Storage::disk($disk)->url($path),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to upload file: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Upload multiple files
     *
     * @param array $files
     * @param string $folder
     * @param string $disk
     * @return array
     */
    public function uploadMultipleFiles(array $files, string $folder = 'uploads', string $disk = 'public'): array
    {
        $uploadedFiles = [];
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploadedFiles[] = $this->uploadFile($file, $folder, $disk);
            }
        }
        
        return $uploadedFiles;
    }

    /**
     * Delete a file from storage
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public function deleteFile(string $path, string $disk = 'public'): bool
    {
        try {
            if (Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->delete($path);
            }
            return false;
        } catch (\Exception $e) {
            \Log::error('Failed to delete file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate file upload
     *
     * @param UploadedFile $file
     * @param array $allowedExtensions
     * @param int $maxSize (in KB)
     * @return array
     */
    public function validateFile(UploadedFile $file, array $allowedExtensions = [], int $maxSize = 10240): array
    {
        $errors = [];
        
        // Check file extension
        if (!empty($allowedExtensions)) {
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, $allowedExtensions)) {
                $errors[] = 'File type not allowed. Allowed types: ' . implode(', ', $allowedExtensions);
            }
        }
        
        // Check file size (convert KB to bytes)
        if ($file->getSize() > ($maxSize * 1024)) {
            $errors[] = 'File size exceeds maximum allowed size of ' . $maxSize . 'KB';
        }
        
        // Check if file is valid
        if (!$file->isValid()) {
            $errors[] = 'Invalid file upload';
        }
        
        return $errors;
    }

    /**
     * Get file URL
     *
     * @param string $path
     * @param string $disk
     * @return string|null
     */
    public function getFileUrl(string $path, string $disk = 'public'): ?string
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->url($path);
        }
        return null;
    }

    /**
     * Move uploaded file to permanent location
     *
     * @param string $tempPath
     * @param string $permanentFolder
     * @param string $disk
     * @return string|null
     */
    public function moveFile(string $tempPath, string $permanentFolder, string $disk = 'public'): ?string
    {
        try {
            if (Storage::disk($disk)->exists($tempPath)) {
                $filename = basename($tempPath);
                $newPath = $permanentFolder . '/' . $filename;
                
                Storage::disk($disk)->move($tempPath, $newPath);
                
                return $newPath;
            }
            return null;
        } catch (\Exception $e) {
            \Log::error('Failed to move file: ' . $e->getMessage());
            return null;
        }
    }
}