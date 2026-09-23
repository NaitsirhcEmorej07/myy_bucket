<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BucketFile extends Model
{
    use HasFactory;

    protected $table = 'files';

    protected $fillable = [
        'user_id',
        'folder_id',
        'name',
        'disk',
        'path',
        'size',
        'original_size',
        'mime_type',
        'extension',
        'category',
        'is_starred',
        'is_compressed',
        'is_optimized',
    ];

    protected $casts = [
        'size' => 'integer',
        'original_size' => 'integer',
        'is_starred' => 'boolean',
        'is_compressed' => 'boolean',
        'is_optimized' => 'boolean',
    ];

    protected $appends = [
        'formatted_size',
        'formatted_original_size',
        'savings_percent',
        'saved_bytes',
        'formatted_updated_at',
        'download_url',
        'preview_url',
    ];

    public function getSavingsPercentAttribute(): int
    {
        if ($this->original_size && $this->original_size > $this->size) {
            return (int) round((($this->original_size - $this->size) / $this->original_size) * 100);
        }
        return 0;
    }

    public function getSavedBytesAttribute(): int
    {
        if ($this->original_size && $this->original_size > $this->size) {
            return $this->original_size - $this->size;
        }
        return 0;
    }

    public function getFormattedOriginalSizeAttribute(): string
    {
        $bytes = $this->original_size ?: $this->size;
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 1) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;

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

    public function getFormattedUpdatedAtAttribute(): string
    {
        return $this->updated_at ? $this->updated_at->format('M j, Y') : '';
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('bucket.file.download', $this->id);
    }

    public function getPreviewUrlAttribute(): string
    {
        return route('bucket.file.preview', $this->id);
    }

    /**
     * Categorize a file based on mime type and extension
     */
    public static function detectCategory(?string $mime, ?string $extension): string
    {
        $ext = strtolower($extension ?? '');
        $mime = strtolower($mime ?? '');

        if (str_starts_with($mime, 'image/') || in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'bmp', 'ico', 'heic'])) {
            return 'image';
        }

        if (str_starts_with($mime, 'video/') || in_array($ext, ['mp4', 'mov', 'avi', 'mkv', 'webm', 'wmv', 'flv'])) {
            return 'video';
        }

        if (str_starts_with($mime, 'audio/') || in_array($ext, ['mp3', 'wav', 'ogg', 'm4a', 'flac', 'aac'])) {
            return 'audio';
        }

        if (in_array($mime, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
            'text/markdown',
        ]) || in_array($ext, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'md', 'rtf'])) {
            return 'document';
        }

        if (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz', 'bz2'])) {
            return 'archive';
        }

        return 'other';
    }
}
