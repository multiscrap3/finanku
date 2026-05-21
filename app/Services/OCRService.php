<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class OCRService
{
    public function __construct(
        private readonly ImageService $imageService
    ) {
    }

    public function validateFile(UploadedFile $file): array
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower($file->getClientOriginalExtension());
        $maxSizeKb = 5 * 1024;

        if (! $file->isValid()) {
            return [
                'valid' => false,
                'error' => 'File upload tidak valid.',
            ];
        }

        if (! in_array($extension, $allowedExtensions, true)) {
            return [
                'valid' => false,
                'error' => 'Format file harus jpg, jpeg, png, atau webp.',
            ];
        }

        if ($file->getSize() > ($maxSizeKb * 1024)) {
            return [
                'valid' => false,
                'error' => 'Ukuran file maksimal 5MB.',
            ];
        }

        return [
            'valid' => true,
            'error' => null,
        ];
    }

    public function compressAndSave(UploadedFile $file, string $folder = 'struk'): string
    {
        $compressedPath = $this->imageService->compress($file);
        $filename = Str::uuid() . '.jpg';
        $storagePath = trim($folder, '/') . '/' . $filename;

        try {
            Storage::disk('public')->put($storagePath, file_get_contents($compressedPath));
        } finally {
            if (is_file($compressedPath)) {
                @unlink($compressedPath);
            }
        }

        return $storagePath;
    }

    public function toBase64(string $path): string
    {
        if (! Storage::disk('public')->exists($path)) {
            throw new RuntimeException('File OCR tidak ditemukan.');
        }

        return base64_encode(Storage::disk('public')->get($path));
    }

    public function getMimeType(string $path): string
    {
        if (! Storage::disk('public')->exists($path)) {
            throw new RuntimeException('File OCR tidak ditemukan.');
        }

        $mimeType = Storage::disk('public')->mimeType($path);

        return is_string($mimeType) && $mimeType !== ''
            ? $mimeType
            : 'image/jpeg';
    }

    public function processUpload(UploadedFile $file): array
    {
        $validation = $this->validateFile($file);

        if (! $validation['valid']) {
            return [
                'valid' => false,
                'error' => $validation['error'],
                'path' => null,
                'base64' => null,
                'mime_type' => null,
                'original_name' => $file->getClientOriginalName(),
                'size_kb' => round(($file->getSize() ?: 0) / 1024, 2),
            ];
        }

        $path = $this->compressAndSave($file);
        $base64 = $this->toBase64($path);
        $mimeType = $this->getMimeType($path);

        return [
            'valid' => true,
            'error' => null,
            'path' => $path,
            'base64' => $base64,
            'mime_type' => $mimeType,
            'original_name' => $file->getClientOriginalName(),
            'size_kb' => round(($file->getSize() ?: 0) / 1024, 2),
        ];
    }
}