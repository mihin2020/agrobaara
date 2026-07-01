<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompaniesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        protected ?string $from = null,
        protected ?string $to = null,
        protected ?string $commune = null,
        protected ?string $status = null,
    ) {}

    public function collection()
    {
        return Company::with(['sites.commune'])
            ->when($this->from, fn($q) => $q->whereDate('created_at', '>=', $this->from))
            ->when($this->to, fn($q) => $q->whereDate('created_at', '<=', $this->to))
            ->when($this->commune, fn($q) => $q->whereHas('sites', fn($s) => $s->where('commune_id', $this->commune)))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Nom',
            'Statut',
            'Représentant légal',
            'Types d\'activité',
            'Description',
            'Téléphone',
            'Email',
            'Site web',
            'Adresse (site principal)',
            'Besoin formation',
            'Besoin financement',
            'Besoin appui contrats',
            'Notes opérateur',
            'Date de création',
        ];
    }

    public function map($company): array
    {
        $mainSite = $company->sites->where('is_main', true)->first();

        return [
            $company->reference ?? '',
            $company->name,
            $company->status?->label() ?? '',
            trim(($company->legal_rep_first_name ?? '') . ' ' . ($company->legal_rep_last_name ?? '')),
            is_array($company->activity_types) ? implode(', ', $company->activity_types) : '',
            $company->description ?? '',
            $company->phone ?? '',
            $company->email ?? '',
            $company->website ?? '',
            $mainSite?->address ?? '',
            $company->need_training ? 'Oui' : 'Non',
            $company->need_financing ? 'Oui' : 'Non',
            $company->need_contract_support ? 'Oui' : 'Non',
            $company->operator_notes ?? '',
            $company->created_at?->format('d/m/Y H:i') ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
