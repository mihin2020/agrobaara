<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_offers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference', 20)->unique();
            $table->uuid('company_id');
            $table->string('title', 200);
            $table->enum('contract_type', ['salarie','saisonnier','ponctuel','apprentissage','entrepreneuriat','autre']);
            $table->string('duration', 100)->nullable();
            $table->text('economic_conditions')->nullable();
            $table->text('mission_description');
            $table->json('locations')->nullable();
            $table->text('other_requirements')->nullable();
            $table->date('start_date')->nullable();
            $table->unsignedSmallInteger('positions_count')->default(1);
            $table->enum('status', ['brouillon','publiee','archivee'])->default('brouillon');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->uuid('created_by');
            $table->uuid('published_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            $table->index('reference');
            $table->index('status');
            $table->index('published_at');
        });

        Schema::create('offer_skills', function (Blueprint $table) {
            $table->uuid('offer_id');
            $table->uuid('skill_id');
            $table->primary(['offer_id', 'skill_id']);
            $table->foreign('offer_id')->references('id')->on('job_offers')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('referentials_skills')->onDelete('cascade');
        });

        Schema::create('matches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('candidate_id');
            $table->uuid('offer_id');
            $table->enum('status', ['proposee','contactee','entretien','acceptee','refusee','abandonnee'])
                  ->default('proposee');
            $table->uuid('operator_id');
            $table->text('notes')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamp('proposed_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->foreign('candidate_id')->references('id')->on('candidates')->onDelete('cascade');
            $table->foreign('offer_id')->references('id')->on('job_offers')->onDelete('cascade');
            $table->foreign('operator_id')->references('id')->on('users');
            $table->unique(['candidate_id', 'offer_id']);
            $table->index('status');
            $table->index('operator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
        Schema::dropIfExists('offer_skills');
        Schema::dropIfExists('job_offers');
    }
};
