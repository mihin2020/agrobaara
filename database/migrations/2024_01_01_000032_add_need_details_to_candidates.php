<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->json('need_employment_types')->nullable()->after('need_types');
            $table->json('need_formation_types')->nullable()->after('need_employment_types');
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn(['need_employment_types', 'need_formation_types']);
        });
    }
};
