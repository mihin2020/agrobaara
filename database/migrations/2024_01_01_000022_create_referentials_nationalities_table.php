<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referentials_nationalities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        $nationalities = [
            'Burkinabè', 'Ivoirien(ne)', 'Malien(ne)', 'Nigérien(ne)', 'Ghanéen(ne)',
            'Nigérian(e)', 'Béninois(e)', 'Togolais(e)', 'Sénégalais(e)', 'Guinéen(ne)',
            'Mauritanien(ne)', 'Camerounais(e)', 'Congolais(e)', 'Français(e)',
            'Américain(e)', 'Autre',
        ];

        foreach ($nationalities as $name) {
            DB::table('referentials_nationalities')->insert([
                'id'         => \Illuminate\Support\Str::uuid(),
                'name'       => $name,
                'is_active'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('referentials_nationalities');
    }
};
