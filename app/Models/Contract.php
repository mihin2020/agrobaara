<?php

namespace App\Models;

use App\Enums\ContractStatus;
use App\Services\ReferenceService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Contract extends Model
{
    use HasUuids;

    protected $fillable = [
        'reference', 'match_id', 'candidate_id', 'company_id', 'offer_id',
        'negotiated_salary', 'starts_at', 'ends_at', 'duration_label', 'is_renewable',
        'renewed_from_contract_id', 'status', 'signed_at', 'notes', 'clauses_snapshot', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status'        => ContractStatus::class,
            'signed_at'     => 'datetime',
            'starts_at'     => 'date',
            'ends_at'       => 'date',
            'is_renewable'  => 'boolean',
        ];
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(CandidateMatch::class, 'match_id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class, 'offer_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function renewedFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'renewed_from_contract_id');
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(self::class, 'renewed_from_contract_id');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForCandidate($query, string $candidateId)
    {
        return $query->where('candidate_id', $candidateId);
    }

    public function scopeForCompany($query, string $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeWithList($query)
    {
        return $query->with(['candidate', 'company', 'offer']);
    }

    public function isExpired(): bool
    {
        return $this->ends_at !== null && $this->ends_at->copy()->endOfDay()->isPast();
    }

    /**
     * Renouvellement possible uniquement après expiration (bouton dédié).
     */
    public function canRenew(): bool
    {
        if (! $this->isExpired()) {
            return false;
        }

        // Déjà remplacé par un renouvellement ultérieur
        if (array_key_exists('renewals_count', $this->getAttributes())) {
            if ((int) $this->renewals_count > 0) {
                return false;
            }
        } elseif ($this->relationLoaded('renewals') ? $this->renewals->isNotEmpty() : $this->renewals()->exists()) {
            return false;
        }

        return in_array($this->status, [ContractStatus::Actif, ContractStatus::Clos], true);
    }

    public function periodLabel(): string
    {
        if ($this->starts_at && $this->ends_at) {
            return $this->starts_at->format('d/m/Y') . ' → ' . $this->ends_at->format('d/m/Y');
        }
        if ($this->starts_at) {
            return 'À partir du ' . $this->starts_at->format('d/m/Y');
        }
        if ($this->duration_label) {
            return $this->duration_label;
        }

        return 'Période non définie';
    }

    /**
     * Estime une date de fin à partir d’un libellé de durée d’offre (texte libre).
     */
    public static function estimateEndDate(Carbon $start, ?string $duration): ?Carbon
    {
        if (! $duration) {
            return null;
        }

        $d = mb_strtolower(trim($duration));

        if (preg_match('/(\d+)\s*mois/', $d, $m)) {
            return $start->copy()->addMonths((int) $m[1]);
        }
        if (preg_match('/(\d+)\s*ans?/', $d, $m)) {
            return $start->copy()->addYears((int) $m[1]);
        }
        if (preg_match('/(\d+)\s*semaines?/', $d, $m)) {
            return $start->copy()->addWeeks((int) $m[1]);
        }
        if (preg_match('/(\d+)\s*jours?/', $d, $m)) {
            return $start->copy()->addDays((int) $m[1]);
        }
        // Nombre seul → mois (ex. "6")
        if (preg_match('/^(\d+)\s*$/', $d, $m)) {
            return $start->copy()->addMonths((int) $m[1]);
        }

        return null;
    }

    public static function buildClausesSnapshot(?JobOffer $offer): ?string
    {
        if (! $offer) {
            return null;
        }

        $lines = [
            'Offre : ' . ($offer->reference ?? '') . ' — ' . ($offer->title ?? ''),
            'Type de contrat : ' . ($offer->contract_type?->label() ?? '—'),
            'Durée (offre) : ' . ($offer->duration ?: '—'),
            'Date de début prévue (offre) : ' . ($offer->start_date?->format('d/m/Y') ?? '—'),
            'Conditions économiques : ' . ($offer->economic_conditions ?: '—'),
            'Fourchette salariale : ' . ($offer->salary_range ?: '—'),
            'Mission : ' . ($offer->mission_description ?: '—'),
            'Autres exigences : ' . ($offer->other_requirements ?: '—'),
        ];

        return implode("\n", $lines);
    }

    /**
     * Prépare les attributs période / clauses à partir d’une offre.
     */
    public static function attributesFromOffer(JobOffer $offer, ?string $startsAtOverride = null): array
    {
        $starts = $startsAtOverride
            ? Carbon::parse($startsAtOverride)
            : ($offer->start_date ? $offer->start_date->copy() : now()->startOfDay());

        return [
            'duration_label'   => $offer->duration,
            'starts_at'        => $starts->toDateString(),
            'ends_at'          => static::estimateEndDate($starts, $offer->duration)?->toDateString(),
            'clauses_snapshot' => static::buildClausesSnapshot($offer),
        ];
    }

    /**
     * @param  array{keep_clauses?: bool, duration_label?: ?string, starts_at?: ?string, ends_at?: ?string, negotiated_salary?: ?string, notes?: ?string, clauses_snapshot?: ?string}  $options
     */
    public function renew(ReferenceService $referenceService, ?User $createdBy = null, array $options = []): self
    {
        $this->loadMissing('offer');

        $keepClauses = $options['keep_clauses'] ?? true;

        $newStart = ! empty($options['starts_at'])
            ? Carbon::parse($options['starts_at'])->startOfDay()
            : ($this->ends_at
                ? $this->ends_at->copy()->addDay()
                : now()->startOfDay());

        $duration = array_key_exists('duration_label', $options)
            ? $options['duration_label']
            : ($this->duration_label ?? $this->offer?->duration);

        $newEnd = ! empty($options['ends_at'])
            ? Carbon::parse($options['ends_at'])->startOfDay()
            : static::estimateEndDate($newStart, $duration);

        if (! $newEnd && $this->starts_at && $this->ends_at) {
            $days = $this->starts_at->diffInDays($this->ends_at);
            if ($days > 0) {
                $newEnd = $newStart->copy()->addDays($days);
            }
        }

        if ($keepClauses) {
            $clauses = $this->clauses_snapshot ?: static::buildClausesSnapshot($this->offer);
            $salary = $this->negotiated_salary;
            $notes = $this->notes;
        } else {
            $clauses = $options['clauses_snapshot']
                ?? static::buildClausesSnapshot($this->offer)
                ?? $this->clauses_snapshot;
            $salary = array_key_exists('negotiated_salary', $options)
                ? $options['negotiated_salary']
                : $this->negotiated_salary;
            $notes = array_key_exists('notes', $options)
                ? $options['notes']
                : $this->notes;
        }

        $clone = self::create([
            'reference'                => $referenceService->generateContractReference(),
            'match_id'                 => null,
            'candidate_id'             => $this->candidate_id,
            'company_id'               => $this->company_id,
            'offer_id'                 => $this->offer_id,
            'negotiated_salary'        => $salary,
            'starts_at'                => $newStart->toDateString(),
            'ends_at'                  => $newEnd?->toDateString(),
            'duration_label'           => $duration,
            'is_renewable'             => true,
            'renewed_from_contract_id' => $this->id,
            'status'                   => ContractStatus::Brouillon,
            'signed_at'                => null,
            'notes'                    => $notes,
            'clauses_snapshot'         => $clauses,
            'created_by'               => $createdBy?->id ?? Auth::id() ?? $this->created_by,
        ]);

        // Historiser l’ancien (expiré → clos)
        $this->update(['status' => ContractStatus::Clos]);

        return $clone;
    }

    /**
     * Chaîne d’historique (du plus ancien au plus récent) pour le suivi.
     */
    public function renewalChain()
    {
        $root = $this;
        while ($root->renewed_from_contract_id) {
            $root->loadMissing('renewedFrom');
            if (! $root->renewedFrom) {
                break;
            }
            $root = $root->renewedFrom;
        }

        $chain = collect([$root]);
        $current = $root;
        while (true) {
            $current->loadMissing('renewals');
            $next = $current->renewals()->latest()->first();
            if (! $next) {
                break;
            }
            $chain->push($next);
            $current = $next;
        }

        return $chain;
    }
}
