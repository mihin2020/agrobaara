<?php

namespace App\Console\Commands;

use App\Models\Candidate;
use App\Services\SecureUploadService;
use Illuminate\Console\Command;

class MigrateSensitiveFilesToPrivate extends Command
{
    protected $signature = 'files:migrate-private {--dry-run : Afficher sans déplacer}';

    protected $description = 'Déplace CV / pièce d\'identité / diplômes du disque public vers private';

    public function handle(SecureUploadService $uploads): int
    {
        $dry = (bool) $this->option('dry-run');
        $moved = 0;

        Candidate::query()
            ->where(function ($q) {
                $q->whereNotNull('identity_document_path')
                    ->orWhereNotNull('cv_path')
                    ->orWhereNotNull('diploma_paths');
            })
            ->orderBy('created_at')
            ->chunkById(50, function ($candidates) use ($uploads, $dry, &$moved) {
                foreach ($candidates as $candidate) {
                    $dirty = false;

                    foreach (['identity_document_path', 'cv_path'] as $attr) {
                        $path = $candidate->{$attr};
                        if (! $path) {
                            continue;
                        }
                        if ($dry) {
                            $this->line("would move {$attr}: {$path}");
                            $moved++;
                            continue;
                        }
                        if ($uploads->migrateToPrivate($path)) {
                            $moved++;
                            $dirty = true;
                        }
                    }

                    $diplomas = $candidate->diploma_paths ?? [];
                    $newDiplomas = [];
                    foreach ($diplomas as $path) {
                        if ($dry) {
                            $this->line("would move diploma: {$path}");
                            $moved++;
                            $newDiplomas[] = $path;
                            continue;
                        }
                        $uploads->migrateToPrivate($path);
                        $newDiplomas[] = $path;
                        $moved++;
                        $dirty = true;
                    }

                    if (! $dry && $dirty && $newDiplomas !== ($candidate->diploma_paths ?? [])) {
                        $candidate->diploma_paths = $newDiplomas;
                        $candidate->save();
                    }
                }
            });

        $this->info(($dry ? 'Dry-run: ' : '')."{$moved} fichier(s) traité(s).");

        return self::SUCCESS;
    }
}
