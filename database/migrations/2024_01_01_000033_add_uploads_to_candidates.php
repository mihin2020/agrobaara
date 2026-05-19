<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('address');
            $table->string('identity_document_recto_path')->nullable()->after('photo_path');
            $table->string('identity_document_verso_path')->nullable()->after('identity_document_recto_path');
            $table->string('diploma_path')->nullable()->after('agro_training_place');
            $table->string('cv_path')->nullable()->after('diploma_path');
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn(['photo_path', 'identity_document_recto_path', 'identity_document_verso_path', 'diploma_path', 'cv_path']);
        });
    }
};
