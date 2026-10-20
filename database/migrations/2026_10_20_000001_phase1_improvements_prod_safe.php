<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Référentiels Emploi / Formation / Types d'activité ───────────
        Schema::create('referentials_employment_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('referentials_formation_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('referentials_activity_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 150)->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // ── Champs métier additifs ────────────────────────────────────────
        Schema::table('companies', function (Blueprint $table) {
            $table->text('other_needs')->nullable()->after('need_contract_support');
        });

        Schema::table('candidates', function (Blueprint $table) {
            $table->string('salary_expectation', 150)->nullable()->after('need_cv_support');
        });

        Schema::table('job_offers', function (Blueprint $table) {
            $table->string('salary_range', 150)->nullable()->after('economic_conditions');
            $table->uuid('renewed_from_offer_id')->nullable()->after('published_by');
            $table->foreign('renewed_from_offer_id')->references('id')->on('job_offers')->nullOnDelete();
        });

        // ── Module Contrats ──────────────────────────────────────────────
        Schema::create('contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('match_id')->nullable()->unique();
            $table->uuid('candidate_id');
            $table->uuid('company_id');
            $table->uuid('offer_id')->nullable();
            $table->string('negotiated_salary', 150)->nullable();
            $table->enum('status', ['brouillon', 'actif', 'clos'])->default('brouillon');
            $table->timestamp('signed_at')->nullable();
            $table->text('notes')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('match_id')->references('id')->on('matches')->nullOnDelete();
            $table->foreign('candidate_id')->references('id')->on('candidates')->cascadeOnDelete();
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('offer_id')->references('id')->on('job_offers')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->index('status');
        });

        // ── Setting Agroécologie (défaut activé) ─────────────────────────
        if (Schema::hasTable('site_settings')) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => 'show_agroecology_mention'],
                ['value' => '1', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');

        Schema::table('job_offers', function (Blueprint $table) {
            $table->dropForeign(['renewed_from_offer_id']);
            $table->dropColumn(['salary_range', 'renewed_from_offer_id']);
        });

        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('salary_expectation');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('other_needs');
        });

        Schema::dropIfExists('referentials_activity_types');
        Schema::dropIfExists('referentials_formation_types');
        Schema::dropIfExists('referentials_employment_types');

        if (Schema::hasTable('site_settings')) {
            DB::table('site_settings')->where('key', 'show_agroecology_mention')->delete();
        }
    }
};
