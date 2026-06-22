<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LibraryDocument extends Model
{
    use HasUuids;

    protected $fillable = [
        'title',
        'description',
        'type',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
        'cover_path',
        'external_url',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function publicUrl(): ?string
    {
        if ($this->type === 'file' && $this->file_path) {
            return Storage::url($this->file_path);
        }

        return $this->external_url;
    }

    public function coverUrl(): ?string
    {
        if (!$this->cover_path) {
            return null;
        }

        return Storage::url($this->cover_path);
    }

    public function usesPdfFirstPageAsCover(): bool
    {
        return !$this->cover_path && $this->isPdf();
    }

    public function isPdf(): bool
    {
        if ($this->mime_type === 'application/pdf') {
            return true;
        }

        $name = strtolower($this->original_name ?? '');
        if (str_ends_with($name, '.pdf')) {
            return true;
        }

        if ($this->type === 'link') {
            return str_contains(strtolower($this->external_url ?? ''), '.pdf');
        }

        return false;
    }

    public function canEmbed(): bool
    {
        return $this->isPdf();
    }

    public function fileSizeForHumans(): string
    {
        if (!$this->file_size) return '';
        $units = ['o', 'Ko', 'Mo'];
        $size  = $this->file_size;
        $i     = 0;
        while ($size >= 1024 && $i < 2) { $size /= 1024; $i++; }
        return round($size, 1) . ' ' . $units[$i];
    }
}
