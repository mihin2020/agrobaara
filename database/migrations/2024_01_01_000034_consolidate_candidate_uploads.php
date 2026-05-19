<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            // Un seul champ document d'identité (remplace recto+verso)
            $table->string('identity_document_path')->nullable()->after('photo_path');
            $table->dropColumn(['identity_document_recto_path', 'identity_document_verso_path']);

            // Diplômes multiples JSON (remplace diploma_path simple)
            $table->json('diploma_paths')->nullable()->after('agro_training_place');
            $table->dropColumn('diploma_path');
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->string('identity_document_recto_path')->nullable();
            $table->string('identity_document_verso_path')->nullable();
            $table->dropColumn('identity_document_path');
            $table->string('diploma_path')->nullable();
            $table->dropColumn('diploma_paths');
        });
    }
};
