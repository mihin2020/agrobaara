<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference', 20)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->enum('gender', ['M', 'F']);
            $table->enum('marital_status', ['celibataire', 'marie', 'veuf'])->nullable();
            $table->date('birth_date');
            $table->string('birth_place', 150)->nullable();
            $table->string('nationality', 80)->default('Burkinabè');
            $table->uuid('commune_id')->nullable();
            $table->string('address', 255)->nullable();
            $table->enum('transport_mode', ['deux_roues', 'voiture', 'autre', 'aucun'])->nullable();
            $table->string('phone', 30);
            $table->string('phone_secondary', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->enum('education_level', ['secondaire', 'licence', 'master', 'autre'])->nullable();
            $table->text('agro_training_text')->nullable();
            $table->string('agro_training_place', 200)->nullable();
            $table->text('other_skills_text')->nullable();
            $table->boolean('has_previous_jobs')->default(false);
            $table->json('need_types')->nullable();
            $table->boolean('need_training')->default(false);
            $table->boolean('need_financing')->default(false);
            $table->boolean('need_cv_support')->default(false);
            $table->text('operator_notes')->nullable();
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('commune_id')->references('id')->on('referentials_communes')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users');
            $table->index('reference');
            $table->index('phone');
            $table->index('email');
            $table->index('commune_id');
            $table->index('education_level');
        });

        Schema::create('candidate_languages', function (Blueprint $table) {
            $table->uuid('candidate_id');
            $table->uuid('language_id');
            $table->primary(['candidate_id', 'language_id']);
            $table->foreign('candidate_id')->references('id')->on('candidates')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('referentials_languages')->onDelete('cascade');
        });

        Schema::create('candidate_licenses', function (Blueprint $table) {
            $table->uuid('candidate_id');
            $table->uuid('license_id');
            $table->primary(['candidate_id', 'license_id']);
            $table->foreign('candidate_id')->references('id')->on('candidates')->onDelete('cascade');
            $table->foreign('license_id')->references('id')->on('referentials_licenses')->onDelete('cascade');
        });

        Schema::create('candidate_skills', function (Blueprint $table) {
            $table->uuid('candidate_id');
            $table->uuid('skill_id');
            $table->primary(['candidate_id', 'skill_id']);
            $table->foreign('candidate_id')->references('id')->on('candidates')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('referentials_skills')->onDelete('cascade');
        });

        Schema::create('candidate_experiences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('candidate_id');
            $table->year('year')->nullable();
            $table->string('location', 200)->nullable();
            $table->string('position', 200)->nullable();
            $table->text('employer_contacts')->nullable();
            $table->timestamps();

            $table->foreign('candidate_id')->references('id')->on('candidates')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_experiences');
        Schema::dropIfExists('candidate_skills');
        Schema::dropIfExists('candidate_licenses');
        Schema::dropIfExists('candidate_languages');
        Schema::dropIfExists('candidates');
    }
};
