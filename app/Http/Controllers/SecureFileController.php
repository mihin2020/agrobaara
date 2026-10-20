<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Services\SecureUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecureFileController extends Controller
{
    /**
     * Téléchargement / visualisation d'un document candidat (privé).
     * Query: candidate={uuid}&field=cv|identity|diploma&index=0
     */
    public function candidateDocument(Request $request, Candidate $candidate): StreamedResponse
    {
        $this->authorize('view', $candidate);

        $field = $request->string('field')->toString();
        $index = (int) $request->query('index', 0);

        $path = match ($field) {
            'identity' => $candidate->identity_document_path,
            'cv'       => $candidate->cv_path,
            'diploma'  => ($candidate->diploma_paths ?? [])[$index] ?? null,
            default    => null,
        };

        abort_unless($path, 404);

        $disk = Storage::disk(SecureUploadService::DISK_PRIVATE);

        // Compat: anciens fichiers encore sur public
        if (! $disk->exists($path) && Storage::disk('public')->exists($path)) {
            $disk = Storage::disk('public');
        }

        abort_unless($disk->exists($path), 404);

        $mime = $disk->mimeType($path) ?: 'application/octet-stream';
        $filename = basename($path);

        return $disk->response($path, $filename, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
