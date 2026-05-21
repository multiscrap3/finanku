<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ImageService
{
    public function compress(UploadedFile $file, int $maxWidth = 800, int $quality = 80): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $sourcePath = $file->getRealPath();

        if (! $sourcePath || ! is_file($sourcePath)) {
            throw new RuntimeException('File gambar tidak valid.');
        }

        $tempPath = storage_path('app/temp/' . Str::uuid() . '.jpg');

        if (! is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        if (! function_exists('imagecreatetruecolor')) {
            copy($sourcePath, $tempPath);

            return $tempPath;
        }

        [$width, $height] = getimagesize($sourcePath) ?: [0, 0];

        if ($width <= 0 || $height <= 0) {
            throw new RuntimeException('Dimensi gambar tidak dapat dibaca.');
        }

        $source = match ($extension) {
            'jpg', 'jpeg' => imagecreatefromjpeg($sourcePath),
            'png' => imagecreatefrompng($sourcePath),
            'webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($sourcePath) : false,
            default => false,
        };

        if (! $source) {
            copy($sourcePath, $tempPath);

            return $tempPath;
        }

        $newWidth = min($width, $maxWidth);
        $newHeight = (int) round($height * ($newWidth / $width));

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);

        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagejpeg($canvas, $tempPath, max(1, min(100, $quality)));

        imagedestroy($source);
        imagedestroy($canvas);

        return $tempPath;
    }

    public function generateThumbnail(string $path, int $width = 200): string
    {
        $fullPath = Storage::disk('public')->path($path);

        if (! is_file($fullPath)) {
            throw new RuntimeException('File gambar tidak ditemukan.');
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $thumbnailPath = dirname($path) . '/thumb_' . basename($path);
        $thumbnailFullPath = Storage::disk('public')->path($thumbnailPath);

        if (! is_dir(dirname($thumbnailFullPath))) {
            mkdir(dirname($thumbnailFullPath), 0755, true);
        }

        if (! function_exists('imagecreatetruecolor')) {
            Storage::disk('public')->copy($path, $thumbnailPath);

            return $thumbnailPath;
        }

        [$originalWidth, $originalHeight] = getimagesize($fullPath) ?: [0, 0];

        if ($originalWidth <= 0 || $originalHeight <= 0) {
            throw new RuntimeException('Dimensi gambar tidak dapat dibaca.');
        }

        $source = match ($extension) {
            'jpg', 'jpeg' => imagecreatefromjpeg($fullPath),
            'png' => imagecreatefrompng($fullPath),
            'webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($fullPath) : false,
            default => false,
        };

        if (! $source) {
            Storage::disk('public')->copy($path, $thumbnailPath);

            return $thumbnailPath;
        }

        $newWidth = min($originalWidth, $width);
        $newHeight = (int) round($originalHeight * ($newWidth / $originalWidth));

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);

        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        imagejpeg($canvas, $thumbnailFullPath, 80);

        imagedestroy($source);
        imagedestroy($canvas);

        return $thumbnailPath;
    }
}