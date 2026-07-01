<?php

namespace App\Exports;

use App\Models\CandidateMatch;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MatchesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        protected ?string $from = null,
        protected ?string $to = null,
        protected ?string $status = null,
    ) {}

    public function collection()
    {
        return CandidateMatch::with(['candidate', 'offer.company', 'operator'])
            ->when($this->from, fn($q) => $q->whereDate('created_at', '>=', $this->from))
            ->when($this->to, fn($q) => $q->whereDate('created_at', '<=', $this->to))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Candidat',
            'Référence candidat',
            'Offre',
            'Entreprise',
            'Statut',
            'Score',
            'Opérateur',
            'Notes',
            'Date de proposition',
            'Date de clôture',
            'Date de création',
        ];
    }

    public function map($match): array
    {
        return [
            $match->candidate?->full_name ?? '',
            $match->candidate?->reference ?? '',
            $match->offer?->title ?? '',
            $match->offer?->company?->name ?? '',
            $match->status?->label() ?? '',
            $match->score ? round($match->score, 1) . '%' : '',
            $match->operator?->full_name ?? '',
            $match->notes ?? '',
            $match->proposed_at?->format('d/m/Y') ?? '',
            $match->closed_at?->format('d/m/Y') ?? '',
            $match->created_at?->format('d/m/Y H:i') ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
