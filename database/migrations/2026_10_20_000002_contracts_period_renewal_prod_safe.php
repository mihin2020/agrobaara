<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('reference', 40)->nullable()->unique()->after('id');
            $table->date('starts_at')->nullable()->after('negotiated_salary');
            $table->date('ends_at')->nullable()->after('starts_at');
            $table->string('duration_label', 150)->nullable()->after('ends_at');
            $table->boolean('is_renewable')->default(true)->after('duration_label');
            $table->uuid('renewed_from_contract_id')->nullable()->after('is_renewable');
            $table->text('clauses_snapshot')->nullable()->after('notes');

            $table->foreign('renewed_from_contract_id')
                ->references('id')
                ->on('contracts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['renewed_from_contract_id']);
            $table->dropColumn([
                'reference',
                'starts_at',
                'ends_at',
                'duration_label',
                'is_renewable',
                'renewed_from_contract_id',
                'clauses_snapshot',
            ]);
        });
    }
};
