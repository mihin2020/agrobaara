<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference', 20)->unique();
            $table->string('name', 200);
            $table->enum('status', ['gie','sa','sarl','scoop','entreprise_individuelle','sas','autre']);
            $table->string('legal_rep_first_name', 100)->nullable();
            $table->string('legal_rep_last_name', 100)->nullable();
            $table->json('activity_types');
            $table->text('description')->nullable();
            $table->string('phone', 30);
            $table->string('email', 150)->nullable();
            $table->string('website', 255)->nullable();
            $table->json('social_links')->nullable();
            $table->boolean('need_training')->default(false);
            $table->boolean('need_financing')->default(false);
            $table->boolean('need_contract_support')->default(false);
            $table->text('operator_notes')->nullable();
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->index('reference');
            $table->index('name');
            $table->index('phone');
        });

        Schema::create('company_sites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->string('label', 150);
            $table->uuid('commune_id')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('gps_coordinates', 100)->nullable();
            $table->boolean('is_main')->default(false);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('commune_id')->references('id')->on('referentials_communes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_sites');
        Schema::dropIfExists('companies');
    }
};
