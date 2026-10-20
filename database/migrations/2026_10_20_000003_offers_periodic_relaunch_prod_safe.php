<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->boolean('is_periodic')->default(false)->after('duration');
        });

        // Reconnaissance rétroactive des libellés périodiques déjà saisis
        if (Schema::hasColumn('job_offers', 'duration')) {
            DB::table('job_offers')
                ->whereNotNull('duration')
                ->where(function ($q) {
                    $q->where('duration', 'like', '%renouvel%')
                        ->orWhere('duration', 'like', '%period%')
                        ->orWhere('duration', 'like', '%périod%')
                        ->orWhere('duration', 'like', '%saison%')
                        ->orWhere('duration', 'like', '%recurrent%')
                        ->orWhere('duration', 'like', '%récurrent%');
                })
                ->update(['is_periodic' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->dropColumn('is_periodic');
        });
    }
};
