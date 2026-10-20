<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ENUM MySQL uniquement — SQLite/tests stockent déjà un string libre
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE job_offers MODIFY COLUMN status ENUM('brouillon','en_attente','publiee','archivee') NOT NULL DEFAULT 'brouillon'");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::table('job_offers')->where('status', 'en_attente')->update(['status' => 'brouillon']);
        DB::statement("ALTER TABLE job_offers MODIFY COLUMN status ENUM('brouillon','publiee','archivee') NOT NULL DEFAULT 'brouillon'");
    }
};
