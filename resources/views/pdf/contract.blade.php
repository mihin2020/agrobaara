<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Contrat {{ $contract->reference }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e1b18; line-height: 1.45; }
        h1 { font-size: 18px; margin: 0 0 4px; color: #2c6904; }
        h2 { font-size: 13px; margin: 18px 0 8px; color: #875212; border-bottom: 1px solid #c1c9b6; padding-bottom: 4px; }
        .meta { color: #717a69; font-size: 10px; margin-bottom: 16px; }
        .box { border: 1px solid #c1c9b6; border-radius: 6px; padding: 10px 12px; margin-bottom: 10px; }
        .label { color: #717a69; font-size: 9px; text-transform: uppercase; letter-spacing: .04em; }
        .value { font-weight: bold; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; width: 50%; padding: 0 6px 0 0; }
        .clauses { white-space: pre-wrap; background: #fbfdfb; border: 1px solid #e5e7eb; padding: 10px; font-size: 10px; }
        .footer { margin-top: 28px; font-size: 9px; color: #717a69; border-top: 1px solid #c1c9b6; padding-top: 8px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; background: #e8f5e0; color: #2c6904; }
        .hist td { font-size: 10px; padding: 4px 6px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <h1>Fiche contrat — Agro Eco BAARA</h1>
    <p class="meta">
        Référence : <strong>{{ $contract->reference ?? $contract->id }}</strong>
        · Statut : {{ $contract->status->label() }}
        · Généré le {{ now()->format('d/m/Y H:i') }}
        @if($contract->is_renewable)
            · <span class="badge">Renouvelable</span>
        @endif
    </p>

    <h2>1. Période</h2>
    <div class="box">
        <table>
            <tr>
                <td>
                    <div class="label">Début</div>
                    <div class="value">{{ $contract->starts_at?->format('d/m/Y') ?? '—' }}</div>
                </td>
                <td>
                    <div class="label">Fin</div>
                    <div class="value">{{ $contract->ends_at?->format('d/m/Y') ?? '—' }}</div>
                </td>
            </tr>
            <tr>
                <td style="padding-top:8px">
                    <div class="label">Durée (clauses offre)</div>
                    <div class="value">{{ $contract->duration_label ?: '—' }}</div>
                </td>
                <td style="padding-top:8px">
                    <div class="label">Salaire négocié</div>
                    <div class="value">{{ $contract->negotiated_salary ?: '—' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <h2>2. Candidat</h2>
    <div class="box">
        <table>
            <tr>
                <td>
                    <div class="label">Nom</div>
                    <div class="value">{{ $contract->candidate->full_name }}</div>
                </td>
                <td>
                    <div class="label">Référence</div>
                    <div class="value">{{ $contract->candidate->reference }}</div>
                </td>
            </tr>
            <tr>
                <td style="padding-top:8px">
                    <div class="label">Téléphone</div>
                    <div class="value">{{ $contract->candidate->phone ?? '—' }}</div>
                </td>
                <td style="padding-top:8px">
                    <div class="label">Commune</div>
                    <div class="value">{{ $contract->candidate->commune?->name ?? '—' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <h2>3. Entreprise</h2>
    <div class="box">
        <table>
            <tr>
                <td>
                    <div class="label">Raison sociale</div>
                    <div class="value">{{ $contract->company->name }}</div>
                </td>
                <td>
                    <div class="label">Référence</div>
                    <div class="value">{{ $contract->company->reference }}</div>
                </td>
            </tr>
            <tr>
                <td style="padding-top:8px">
                    <div class="label">Téléphone</div>
                    <div class="value">{{ $contract->company->phone ?? '—' }}</div>
                </td>
                <td style="padding-top:8px">
                    <div class="label">E-mail</div>
                    <div class="value">{{ $contract->company->email ?? '—' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <h2>4. Offre & clauses</h2>
    <div class="box">
        @if($contract->offer)
            <div class="label">Offre</div>
            <div class="value">{{ $contract->offer->reference }} — {{ $contract->offer->title }}</div>
            <p style="margin:6px 0 0;color:#717a69">
                Type : {{ $contract->offer->contract_type?->label() ?? '—' }}
                · Durée offre : {{ $contract->offer->duration ?: '—' }}
            </p>
        @else
            <p>Offre non renseignée.</p>
        @endif
    </div>

    @if($contract->clauses_snapshot)
        <div class="clauses">{{ $contract->clauses_snapshot }}</div>
    @elseif($contract->offer)
        <div class="clauses">
Conditions économiques : {{ $contract->offer->economic_conditions ?: '—' }}
Fourchette salariale : {{ $contract->offer->salary_range ?: '—' }}
Mission : {{ $contract->offer->mission_description ?: '—' }}
Autres exigences : {{ $contract->offer->other_requirements ?: '—' }}
        </div>
    @endif

    @if($contract->notes)
        <h2>5. Notes</h2>
        <div class="box">{{ $contract->notes }}</div>
    @endif

    @if(isset($history) && $history->count() > 1)
        <h2>Historique des renouvellements</h2>
        <table class="hist">
            <thead>
                <tr>
                    <td><strong>Référence</strong></td>
                    <td><strong>Période</strong></td>
                    <td><strong>Statut</strong></td>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $item)
                    <tr>
                        <td>{{ $item->reference ?? '—' }}</td>
                        <td>{{ $item->periodLabel() }}</td>
                        <td>{{ $item->status->label() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Document généré par Agro Eco BAARA — suivi contrats / offres.
        @if($contract->signed_at)
            Signé le {{ $contract->signed_at->format('d/m/Y H:i') }}.
        @endif
        @if($contract->createdBy)
            Créé par {{ $contract->createdBy->full_name }}.
        @endif
    </div>
</body>
</html>
