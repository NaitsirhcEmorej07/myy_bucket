<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
        'color',
    ];

    protected $appends = [
        'formatted_updated_at',
        'files_count',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(BucketFile::class, 'folder_id');
    }

    public function getFormattedUpdatedAtAttribute(): string
    {
        return $this->updated_at ? $this->updated_at->format('M j, Y') : '';
    }

    public function getFilesCountAttribute(): int
    {
        return $this->files()->count();
    }
}
