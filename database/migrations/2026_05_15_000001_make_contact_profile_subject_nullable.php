<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('subject', 150)->nullable()->default(null)->change();
            $table->string('profile', 20)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('subject', 150)->nullable(false)->change();
            $table->enum('profile', ['jeune','entreprise','ong','autre'])->nullable(false)->change();
        });
    }
};
