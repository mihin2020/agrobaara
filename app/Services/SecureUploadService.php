<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SecureUploadService
{
    public const DISK_PUBLIC = 'public';

    public const DISK_PRIVATE = 'local'; // storage/app/private

    /**
     * Stocke un fichier sensible hors webroot, nom UUID.
     */
    public function storePrivate(UploadedFile $file, string $directory): string
    {
        $this->assertSafeUpload($file);

        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
        $name = Str::uuid()->toString().'.'.$ext;

        return $file->storeAs($directory, $name, self::DISK_PRIVATE);
    }

    /**
     * Assets affichables (photos, logos) — disque public, nom UUID.
     */
    public function storePublic(UploadedFile $file, string $directory): string
    {
        $this->assertSafeUpload($file);

        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
        $name = Str::uuid()->toString().'.'.$ext;

        return $file->storeAs($directory, $name, self::DISK_PUBLIC);
    }

    public function delete(?string $path, string $disk = self::DISK_PRIVATE): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    public function deleteMany(?array $paths, string $disk = self::DISK_PRIVATE): void
    {
        foreach ($paths ?? [] as $path) {
            $this->delete($path, $disk);
        }
    }

    /**
     * Migre un fichier public → private (si présent).
     */
    public function migrateToPrivate(string $path): ?string
    {
        if (! Storage::disk(self::DISK_PUBLIC)->exists($path)) {
            return null;
        }

        $contents = Storage::disk(self::DISK_PUBLIC)->get($path);
        Storage::disk(self::DISK_PRIVATE)->put($path, $contents);
        Storage::disk(self::DISK_PUBLIC)->delete($path);

        return $path;
    }

    private function assertSafeUpload(UploadedFile $file): void
    {
        if (! $file->isValid()) {
            abort(422, 'Fichier invalide.');
        }

        $mime = (string) $file->getMimeType();
        $allowed = [
            'image/jpeg', 'image/png', 'image/webp', 'image/gif',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        if (! in_array($mime, $allowed, true)) {
            abort(422, 'Type de fichier non autorisé.');
        }
    }
}
