<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_flags', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->boolean('enabled')->default(false);
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        $now = now();
        $flags = [
            ['key' => 'phase2_master', 'label' => 'Phase 2 (interrupteur global)', 'description' => 'Active l’ensemble Phase 2. Active aussi le matching auto top-5.'],
            ['key' => 'self_registration', 'label' => 'Inscriptions publiques', 'description' => 'Candidats, entreprises et centres peuvent s’inscrire.'],
            ['key' => 'portal_candidate', 'label' => 'Portail candidat', 'description' => 'Espace connecté candidat.'],
            ['key' => 'portal_company', 'label' => 'Portail entreprise', 'description' => 'Espace connecté entreprise.'],
            ['key' => 'portal_training_center', 'label' => 'Portail centre de formation', 'description' => 'Espace connecté centre de formation.'],
            ['key' => 'public_offers', 'label' => 'Offres publiques', 'description' => 'Listing public des offres publiées.'],
            ['key' => 'testimonials', 'label' => 'Témoignages', 'description' => 'Soumission et affichage de témoignages modérés.'],
            ['key' => 'news_ribbon', 'label' => 'Actualités & bandeau', 'description' => 'Actualités, annonces et événements + bandeau landing.'],
        ];

        foreach ($flags as $flag) {
            DB::table('feature_flags')->insert([
                'key'         => $flag['key'],
                'enabled'     => false,
                'label'       => $flag['label'],
                'description' => $flag['description'],
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_flags');
    }
};
