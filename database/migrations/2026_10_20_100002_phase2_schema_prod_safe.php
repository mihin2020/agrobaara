<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->foreignUuid('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->foreignUuid('training_center_id')->nullable()->after('user_id')->constrained('companies')->nullOnDelete();
            $table->string('approval_status', 30)->default('approved')->after('operator_notes');
            $table->timestamp('approved_at')->nullable()->after('approval_status');
            $table->foreignUuid('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable()->after('approved_by');
            $table->unique('user_id');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->foreignUuid('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->string('entity_type', 30)->default('entreprise')->after('user_id');
            $table->string('approval_status', 30)->default('approved')->after('operator_notes');
            $table->timestamp('approved_at')->nullable()->after('approval_status');
            $table->foreignUuid('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable()->after('approved_by');
            $table->unique('user_id');
            $table->index('entity_type');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->string('source', 20)->default('manual')->after('score');
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_id')->nullable()->constrained('candidates')->nullOnDelete();
            $table->string('author_name');
            $table->string('role_label')->nullable();
            $table->text('body');
            $table->string('status', 20)->default('pending');
            $table->timestamp('published_at')->nullable();
            $table->foreignUuid('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('news_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('kind', 20); // actu | annonce | evenement
            $table->string('excerpt', 500)->nullable();
            $table->text('body')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('status', 20)->default('draft'); // draft | published | archived
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['kind', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_items');
        Schema::dropIfExists('testimonials');

        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn('source');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['entity_type', 'approval_status', 'approved_at', 'rejection_reason']);
        });

        Schema::table('candidates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropConstrainedForeignId('training_center_id');
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['approval_status', 'approved_at', 'rejection_reason']);
        });
    }
};
