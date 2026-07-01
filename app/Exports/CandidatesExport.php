<?php

namespace App\Exports;

use App\Models\Candidate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CandidatesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        protected ?string $from = null,
        protected ?string $to = null,
    ) {}

    public function collection()
    {
        return Candidate::with(['commune', 'skills', 'languages'])
            ->when($this->from, fn($q) => $q->whereDate('created_at', '>=', $this->from))
            ->when($this->to, fn($q) => $q->whereDate('created_at', '<=', $this->to))
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Nom',
            'Prénom',
            'Genre',
            'Date de naissance',
            'Lieu de naissance',
            'Âge',
            'Nationalité',
            'Commune',
            'Adresse',
            'Téléphone',
            'Téléphone 2',
            'Email',
            'Niveau d\'étude',
            'Formation reçue',
            'Lieu de formation',
            'Compétences',
            'Langues',
            'Expérience antérieure',
            'Besoins',
            'Notes opérateur',
            'Date d\'inscription',
        ];
    }

    public function map($candidate): array
    {
        return [
            $candidate->reference,
            $candidate->last_name,
            $candidate->first_name,
            $candidate->gender?->label() ?? '',
            $candidate->birth_date?->format('d/m/Y') ?? '',
            $candidate->birth_place ?? '',
            $candidate->age,
            $candidate->nationality ?? '',
            $candidate->commune?->name ?? '',
            $candidate->address ?? '',
            $candidate->phone ?? '',
            $candidate->phone_secondary ?? '',
            $candidate->email ?? '',
            $candidate->education_level ?? '',
            $candidate->agro_training_text ?? '',
            $candidate->agro_training_place ?? '',
            $candidate->skills->pluck('name')->implode(', '),
            $candidate->languages->pluck('name')->implode(', '),
            $candidate->has_previous_jobs ? 'Oui' : 'Non',
            is_array($candidate->need_types) ? implode(', ', $candidate->need_types) : '',
            $candidate->operator_notes ?? '',
            $candidate->created_at?->format('d/m/Y H:i') ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
