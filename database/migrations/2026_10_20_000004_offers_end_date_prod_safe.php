<?php

use App\Models\Contract;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->date('end_date')->nullable()->after('start_date');
        });

        // Backfill end_date depuis start_date + duration quand possible
        $rows = DB::table('job_offers')
            ->whereNotNull('start_date')
            ->whereNotNull('duration')
            ->whereNull('end_date')
            ->get(['id', 'start_date', 'duration']);

        foreach ($rows as $row) {
            $end = Contract::estimateEndDate(Carbon::parse($row->start_date)->startOfDay(), $row->duration);
            if ($end) {
                DB::table('job_offers')->where('id', $row->id)->update(['end_date' => $end->toDateString()]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->dropColumn('end_date');
        });
    }
};
