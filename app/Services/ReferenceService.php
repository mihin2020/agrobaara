<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Company;
use App\Models\Contract;
use App\Models\JobOffer;

class ReferenceService
{
    public function generateCandidateReference(): string
    {
        $year  = now()->year;
        $count = Candidate::withTrashed()->whereYear('created_at', $year)->count() + 1;

        return sprintf('AEB-%d-%04d', $year, $count);
    }

    public function generateCompanyReference(): string
    {
        $count = Company::withTrashed()->count() + 1;

        return sprintf('AEB-COM-%04d', $count);
    }

    public function generateOfferReference(): string
    {
        $year  = now()->year;
        $count = JobOffer::withTrashed()->whereYear('created_at', $year)->count() + 1;

        return sprintf('AEB-OFF-%d-%04d', $year, $count);
    }

    public function generateContractReference(): string
    {
        $year  = now()->year;
        $count = Contract::whereYear('created_at', $year)->count() + 1;

        return sprintf('AEB-CTR-%d-%04d', $year, $count);
    }
}
