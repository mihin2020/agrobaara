<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_sections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug', 60)->unique();
            $table->string('type', 60);
            $table->string('title', 200)->nullable();
            $table->json('content')->nullable();
            $table->json('draft_content')->nullable();
            $table->smallInteger('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('always_visible')->default(false);
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->index('order_index');
        });

        Schema::create('landing_publications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('published_by');
            $table->json('snapshot')->nullable();
            $table->timestamps();

            $table->foreign('published_by')->references('id')->on('users');
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->string('key', 100)->primary();
            $table->text('value')->nullable();
            $table->string('type', 30)->default('string');
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('full_name', 150);
            $table->string('email', 150);
            $table->string('phone', 30)->nullable();
            $table->enum('profile', ['jeune','entreprise','ong','autre']);
            $table->string('subject', 150);
            $table->text('message');
            $table->boolean('rgpd_consent')->default(false);
            $table->boolean('is_read')->default(false);
            $table->uuid('read_by')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('is_read');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('landing_publications');
        Schema::dropIfExists('landing_sections');
    }
};
