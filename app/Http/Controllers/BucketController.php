<?php

namespace App\Http\Controllers;

use App\Models\BucketFile;
use App\Models\Folder;
use App\Services\MediaOptimizerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BucketController extends Controller
{
    /**
     * Determine active storage disk (Cloudflare R2/S3 or local fallback)
     */
    protected function getStorageDisk(): string
    {
        $hasKey = !empty(config('filesystems.disks.s3.key'));
        $hasSecret = !empty(config('filesystems.disks.s3.secret'));
        $hasBucket = !empty(config('filesystems.disks.s3.bucket'));

        if ($hasKey && $hasSecret && $hasBucket) {
            return 's3';
        }

        return 'public';
    }

    /**
     * Display bucket page with initial data
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($request->wantsJson()) {
            return $this->data();
        }

        $initialData = $this->buildUserData($user->id);

        return view('bucket', [
            'initialFolders' => $initialData['folders'],
            'initialFiles' => $initialData['files'],
            'initialStats' => $initialData['stats'],
            'storageDisk' => $this->getStorageDisk(),
        ]);
    }

    /**
     * Fetch user folders, files and storage stats as JSON
     */
    public function data(): JsonResponse
    {
        $data = $this->buildUserData(Auth::id());
        return response()->json($data);
    }

    /**
     * Build user data payload
     */
    protected function buildUserData(int $userId): array
    {
        $folders = Folder::where('user_id', $userId)
            ->withCount('files')
            ->orderBy('name')
            ->get();

        $files = BucketFile::where('user_id', $userId)
            ->latest()
            ->get();

        $totalBytes = $files->sum('size');
        $totalFiles = $files->count();
        $totalFolders = $folders->count();

        $categories = [
            'image' => ['bytes' => 0, 'count' => 0],
            'video' => ['bytes' => 0, 'count' => 0],
            'audio' => ['bytes' => 0, 'count' => 0],
            'document' => ['bytes' => 0, 'count' => 0],
            'archive' => ['bytes' => 0, 'count' => 0],
            'other' => ['bytes' => 0, 'count' => 0],
        ];

        $totalOriginalBytes = 0;
        foreach ($files as $file) {
            $cat = $file->category ?? 'other';
            if (!isset($categories[$cat])) {
                $categories[$cat] = ['bytes' => 0, 'count' => 0];
            }
            $categories[$cat]['bytes'] += $file->size;
            $categories[$cat]['count'] += 1;
            $totalOriginalBytes += ($file->original_size && $file->original_size > $file->size) ? $file->original_size : $file->size;
        }

        $totalSavedBytes = max(0, $totalOriginalBytes - $totalBytes);
        $savingsPercent = $totalOriginalBytes > 0 ? (int) round(($totalSavedBytes / $totalOriginalBytes) * 100) : 0;

        return [
            'folders' => $folders,
            'files' => $files,
            'stats' => [
                'total_bytes' => $totalBytes,
                'formatted_total_bytes' => $this->formatBytes($totalBytes),
                'total_original_bytes' => $totalOriginalBytes,
                'formatted_original_bytes' => $this->formatBytes($totalOriginalBytes),
                'total_saved_bytes' => $totalSavedBytes,
                'formatted_saved_bytes' => $this->formatBytes($totalSavedBytes),
                'savings_percent' => $savingsPercent,
                'total_files' => $totalFiles,
                'total_folders' => $totalFolders,
                'categories' => $categories,
                'storage_disk' => $this->getStorageDisk(),
            ],
        ];
    }

    /**
     * Create a new folder
     */
    public function storeFolder(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'color' => 'nullable|string|max:20',
            'parent_id' => [
                'nullable',
                Rule::exists('folders', 'id')->where('user_id', Auth::id()),
            ],
        ]);

        $folder = Folder::create([
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'name' => trim($request->name),
            'color' => $request->color ?? 'blue',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Folder created successfully.',
            'folder' => $folder->loadCount('files'),
        ]);
    }

    /**
     * Rename or update a folder
     */
    public function updateFolder(Request $request, Folder $folder): JsonResponse
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'color' => 'nullable|string|max:20',
        ]);

        $folder->update([
            'name' => trim($request->name),
            'color' => $request->color ?? $folder->color,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Folder updated successfully.',
            'folder' => $folder->fresh()->loadCount('files'),
        ]);
    }

    /**
     * Delete a folder and its files
     */
    public function destroyFolder(Folder $folder): JsonResponse
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete associated files from storage
        $files = $folder->files;
        foreach ($files as $file) {
            try {
                Storage::disk($file->disk)->delete($file->path);
            } catch (\Throwable $e) {
                // Ignore storage deletion errors
            }
            $file->delete();
        }

        $folder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Folder and files deleted successfully.',
        ]);
    }

    /**
     * Upload single or multiple files
     */
    public function upload(Request $request, MediaOptimizerService $optimizer): JsonResponse
    {
        @ini_set('max_execution_time', 600);
        @ini_set('max_input_time', 600);

        if (!$request->hasFile('files')) {
            return response()->json([
                'success' => false,
                'message' => 'No files were received. The file may exceed the maximum allowed server upload size (512MB).',
            ], 422);
        }

        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|max:524288', // 512MB max per file
            'folder_id' => [
                'required',
                Rule::exists('folders', 'id')->where('user_id', Auth::id()),
            ],
        ], [
            'folder_id.required' => 'Please select or open a folder before uploading files.',
            'folder_id.exists' => 'The selected folder is invalid or does not belong to your account.',
        ]);

        $userId = Auth::id();
        $folderId = $request->folder_id;
        $disk = $this->getStorageDisk();
        $uploaded = [];

        foreach ($request->file('files') as $uploadedFile) {
            $originalName = $uploadedFile->getClientOriginalName();
            $extension = strtolower($uploadedFile->getClientOriginalExtension());
            $mimeType = $uploadedFile->getMimeType() ?: 'application/octet-stream';
            $category = BucketFile::detectCategory($mimeType, $extension);

            // Transparently optimize image or compress document
            $optResult = $optimizer->optimize($uploadedFile);
            $sourceToUpload = $optResult['path'];
            $finalSize = $optResult['size'];
            $originalSize = $optResult['original_size'];
            $isCompressed = $optResult['is_compressed'];
            $isOptimized = $optResult['is_optimized'];

            $folderPath = "buckets/{$userId}" . ($folderId ? "/folders/{$folderId}" : '');
            $storageFilename = Str::uuid()->toString() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . ($extension ?: 'bin');
            $fullStoragePath = $folderPath . '/' . $storageFilename;

            try {
                $fileStream = fopen($sourceToUpload, 'rb');
                Storage::disk($disk)->put($fullStoragePath, $fileStream);
                if (is_resource($fileStream)) {
                    fclose($fileStream);
                }
                $storedPath = $fullStoragePath;
            } catch (\Throwable $e) {
                // Fallback to public local disk if S3 fails
                $disk = 'public';
                $fileStream = fopen($sourceToUpload, 'rb');
                Storage::disk($disk)->put($fullStoragePath, $fileStream);
                if (is_resource($fileStream)) {
                    fclose($fileStream);
                }
                $storedPath = $fullStoragePath;
            }

            // Cleanup temp file if created by optimizer
            if (!empty($optResult['is_temp']) && file_exists($sourceToUpload)) {
                @unlink($sourceToUpload);
            }

            $fileRecord = BucketFile::create([
                'user_id' => $userId,
                'folder_id' => $folderId,
                'name' => $originalName,
                'disk' => $disk,
                'path' => $storedPath,
                'size' => $finalSize,
                'original_size' => $originalSize,
                'is_compressed' => $isCompressed,
                'is_optimized' => $isOptimized,
                'mime_type' => $mimeType,
                'extension' => strtoupper($extension),
                'category' => $category,
                'is_starred' => false,
            ]);

            $uploaded[] = $fileRecord;
        }

        return response()->json([
            'success' => true,
            'message' => count($uploaded) . ' file(s) uploaded and optimized successfully.',
            'files' => $uploaded,
        ]);
    }

    /**
     * Rename a file
     */
    public function renameFile(Request $request, BucketFile $file): JsonResponse
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $file->update([
            'name' => trim($request->name),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File renamed successfully.',
            'file' => $file->fresh(),
        ]);
    }

    /**
     * Toggle starred status of a file
     */
    public function toggleStar(BucketFile $file): JsonResponse
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        $file->update([
            'is_starred' => !$file->is_starred,
        ]);

        return response()->json([
            'success' => true,
            'starred' => $file->is_starred,
            'file' => $file->fresh(),
        ]);
    }

    /**
     * Delete a file
     */
    public function destroyFile(BucketFile $file): JsonResponse
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            Storage::disk($file->disk)->delete($file->path);
        } catch (\Throwable $e) {
            // Continue with record deletion
        }

        $file->delete();

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully.',
        ]);
    }

    /**
     * Download a file (with transparent on-the-fly decompression if compressed)
     */
    public function download(BucketFile $file, MediaOptimizerService $optimizer)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        if (!Storage::disk($file->disk)->exists($file->path)) {
            abort(404, 'File not found on storage.');
        }

        // If file was transparently compressed on the bucket, decompress to original byte-for-byte
        if ($file->is_compressed) {
            $compressedData = Storage::disk($file->disk)->get($file->path);
            $decompressed = $optimizer->decompress($compressedData);
            if ($decompressed !== null) {
                return response()->streamDownload(function () use ($decompressed) {
                    echo $decompressed;
                }, $file->name, [
                    'Content-Type' => $file->mime_type ?: 'application/octet-stream',
                    'Content-Length' => strlen($decompressed),
                ]);
            }
        }

        return Storage::disk($file->disk)->download($file->path, $file->name);
    }

    /**
     * Stream a file preview inline (with transparent decompression for compressed docs)
     */
    public function preview(BucketFile $file, MediaOptimizerService $optimizer): StreamedResponse
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        if (!Storage::disk($file->disk)->exists($file->path)) {
            abort(404, 'File not found on storage.');
        }

        $mime = $file->mime_type ?: 'application/octet-stream';

        // If compressed, decompress on-the-fly for preview
        if ($file->is_compressed) {
            $compressedData = Storage::disk($file->disk)->get($file->path);
            $decompressed = $optimizer->decompress($compressedData);
            if ($decompressed !== null) {
                return response()->stream(function () use ($decompressed) {
                    echo $decompressed;
                }, 200, [
                    'Content-Type' => $mime,
                    'Content-Disposition' => 'inline; filename="' . addslashes($file->name) . '"',
                    'Cache-Control' => 'private, max-age=3600',
                ]);
            }
        }

        $stream = Storage::disk($file->disk)->readStream($file->path);

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . addslashes($file->name) . '"',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Helper to format bytes
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 1) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
