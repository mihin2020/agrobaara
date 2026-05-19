<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'external_url',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
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
