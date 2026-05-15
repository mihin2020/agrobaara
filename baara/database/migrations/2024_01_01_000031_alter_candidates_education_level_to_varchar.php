<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            // Change from ENUM to VARCHAR — existing code values (secondaire, licence, master, autre)
            // are preserved as-is and match the 'code' column in referentials_education_levels.
            $table->string('education_level', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->enum('education_level', ['secondaire', 'licence', 'master', 'autre'])->nullable()->change();
        });
    }
};
