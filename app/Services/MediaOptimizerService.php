<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class MediaOptimizerService
{
    /**
     * JPEG compression quality (84 is virtually identical to human eyes, saves 50-70% size).
     */
    protected int $jpegQuality = 84;

    /**
     * WebP compression quality.
     */
    protected int $webpQuality = 84;

    /**
     * PNG compression level (0-9).
     */
    protected int $pngCompression = 8;

    /**
     * Optimize an uploaded file before storing it in the cloud bucket.
     *
     * @param UploadedFile $file
     * @return array
     */
    public function optimize(UploadedFile $file): array
    {
        $originalPath = $file->getRealPath();
        $originalSize = $file->getSize() ?: filesize($originalPath);
        $mimeType = strtolower($file->getMimeType() ?: '');
        $extension = strtolower($file->getClientOriginalExtension() ?: '');

        // 1. Try Image Optimization
        if ($this->isImage($mimeType, $extension)) {
            $imageResult = $this->optimizeImage($originalPath, $extension, $originalSize);
            if ($imageResult['success'] && $imageResult['size'] < $originalSize) {
                return [
                    'path' => $imageResult['path'],
                    'size' => $imageResult['size'],
                    'original_size' => $originalSize,
                    'is_compressed' => false,
                    'is_optimized' => true,
                    'is_temp' => true,
                ];
            }
        }

        // 2. Try Document / Data Lossless Compression
        if ($this->isCompressibleDocument($mimeType, $extension)) {
            $docResult = $this->compressDocument($originalPath, $originalSize);
            if ($docResult['success'] && $docResult['size'] < $originalSize) {
                return [
                    'path' => $docResult['path'],
                    'size' => $docResult['size'],
                    'original_size' => $originalSize,
                    'is_compressed' => true,
                    'is_optimized' => true,
                    'is_temp' => true,
                ];
            }
        }

        // Default: return original file
        return [
            'path' => $originalPath,
            'size' => $originalSize,
            'original_size' => $originalSize,
            'is_compressed' => false,
            'is_optimized' => false,
            'is_temp' => false,
        ];
    }

    /**
     * Optimize image using PHP GD.
     */
    protected function optimizeImage(string $sourcePath, string $extension, int $originalSize): array
    {
        if (!extension_loaded('gd')) {
            return ['success' => false];
        }

        try {
            $image = null;
            $format = in_array($extension, ['jpg', 'jpeg']) ? 'jpeg' : $extension;

            switch ($format) {
                case 'jpeg':
                    if (function_exists('imagecreatefromjpeg')) {
                        $image = @imagecreatefromjpeg($sourcePath);
                        if ($image && function_exists('exif_read_data')) {
                            $image = $this->correctExifOrientation($image, $sourcePath);
                        }
                    }
                    break;

                case 'png':
                    if (function_exists('imagecreatefrompng')) {
                        $image = @imagecreatefrompng($sourcePath);
                        if ($image) {
                            imagealphablending($image, false);
                            imagesavealpha($image, true);
                        }
                    }
                    break;

                case 'webp':
                    if (function_exists('imagecreatefromwebp')) {
                        $image = @imagecreatefromwebp($sourcePath);
                    }
                    break;

                default:
                    return ['success' => false];
            }

            if (!$image) {
                return ['success' => false];
            }

            $tempOptimized = tempnam(sys_get_temp_dir(), 'opt_img_') . '.' . $extension;

            switch ($format) {
                case 'jpeg':
                    imageinterlace($image, true);
                    imagejpeg($image, $tempOptimized, $this->jpegQuality);
                    break;

                case 'png':
                    imagepng($image, $tempOptimized, $this->pngCompression);
                    break;

                case 'webp':
                    imagewebp($image, $tempOptimized, $this->webpQuality);
                    break;
            }

            imagedestroy($image);

            if (file_exists($tempOptimized)) {
                $newSize = filesize($tempOptimized);
                if ($newSize > 0 && $newSize < $originalSize) {
                    return [
                        'success' => true,
                        'path' => $tempOptimized,
                        'size' => $newSize,
                    ];
                }
                @unlink($tempOptimized);
            }
        } catch (\Throwable $e) {
            Log::warning('Image optimization failed: ' . $e->getMessage());
        }

        return ['success' => false];
    }

    /**
     * Correct photo orientation based on EXIF tag.
     */
    protected function correctExifOrientation($image, string $sourcePath)
    {
        try {
            $exif = @exif_read_data($sourcePath);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $image = imagerotate($image, 180, 0);
                        break;
                    case 6:
                        $image = imagerotate($image, -90, 0);
                        break;
                    case 8:
                        $image = imagerotate($image, 90, 0);
                        break;
                }
            }
        } catch (\Throwable $e) {
            // Ignore exif errors
        }

        return $image;
    }

    /**
     * Losslessly compress text-based documents using Gzip level 9.
     */
    protected function compressDocument(string $sourcePath, int $originalSize): array
    {
        // Don't compress tiny files below 512 bytes
        if ($originalSize < 512) {
            return ['success' => false];
        }

        try {
            $content = file_get_contents($sourcePath);
            if ($content === false) {
                return ['success' => false];
            }

            $compressed = gzencode($content, 9);
            if ($compressed === false) {
                return ['success' => false];
            }

            $compressedSize = strlen($compressed);

            // Require at least 8% savings to justify compression
            if ($compressedSize < ($originalSize * 0.92)) {
                $tempPath = tempnam(sys_get_temp_dir(), 'opt_doc_');
                file_put_contents($tempPath, $compressed);

                return [
                    'success' => true,
                    'path' => $tempPath,
                    'size' => $compressedSize,
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('Document compression failed: ' . $e->getMessage());
        }

        return ['success' => false];
    }

    /**
     * Decompress data from a gzipped string.
     */
    public function decompress(string $compressedData): ?string
    {
        $decompressed = @gzdecode($compressedData);
        return $decompressed !== false ? $decompressed : null;
    }

    /**
     * Check if a file is an image that can be optimized.
     */
    protected function isImage(string $mimeType, string $extension): bool
    {
        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])
            || in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * Check if a file is a document/data file that benefits from lossless compression.
     */
    protected function isCompressibleDocument(string $mimeType, string $extension): bool
    {
        $compressibleExtensions = [
            'txt', 'csv', 'json', 'xml', 'html', 'css', 'js', 'md', 'sql', 'log',
            'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'rtf', 'tsv',
        ];

        return in_array($extension, $compressibleExtensions)
            || str_starts_with($mimeType, 'text/')
            || in_array($mimeType, [
                'application/json',
                'application/xml',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }
}
