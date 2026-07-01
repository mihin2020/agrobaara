<?php

namespace App\Exports;

use App\Models\JobOffer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OffersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        protected ?string $from = null,
        protected ?string $to = null,
        protected ?string $status = null,
        protected ?string $commune = null,
    ) {}

    public function collection()
    {
        return JobOffer::with(['company', 'skills'])
            ->when($this->from, fn($q) => $q->whereDate('created_at', '>=', $this->from))
            ->when($this->to, fn($q) => $q->whereDate('created_at', '<=', $this->to))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->commune, fn($q) => $q->whereJsonContains('locations', $this->commune))
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Titre',
            'Entreprise',
            'Type de contrat',
            'Durée',
            'Conditions économiques',
            'Description de la mission',
            'Localisation(s)',
            'Autres exigences',
            'Date de début',
            'Postes disponibles',
            'Statut',
            'Compétences requises',
            'Date de publication',
            'Date de création',
        ];
    }

    public function map($offer): array
    {
        return [
            $offer->reference ?? '',
            $offer->title,
            $offer->company?->name ?? '',
            $offer->contract_type?->label() ?? '',
            $offer->duration ?? '',
            $offer->economic_conditions ?? '',
            $offer->mission_description ?? '',
            is_array($offer->locations) ? implode(', ', $offer->locations) : '',
            $offer->other_requirements ?? '',
            $offer->start_date?->format('d/m/Y') ?? '',
            $offer->positions_count ?? '',
            $offer->status?->label() ?? '',
            $offer->skills->pluck('name')->implode(', '),
            $offer->published_at?->format('d/m/Y') ?? '',
            $offer->created_at?->format('d/m/Y H:i') ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
