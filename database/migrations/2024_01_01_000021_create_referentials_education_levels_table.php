<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referentials_education_levels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed initial levels (match the old enum values)
        $now = now();
        DB::table('referentials_education_levels')->insert([
            ['id' => \Illuminate\Support\Str::uuid(), 'name' => 'Aucun',                       'code' => 'aucun',       'sort_order' => 1,  'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => \Illuminate\Support\Str::uuid(), 'name' => 'Primaire',                     'code' => 'primaire',    'sort_order' => 2,  'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => \Illuminate\Support\Str::uuid(), 'name' => 'Secondaire',                   'code' => 'secondaire',  'sort_order' => 3,  'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => \Illuminate\Support\Str::uuid(), 'name' => 'CAP / BEP',                    'code' => 'cap',         'sort_order' => 4,  'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => \Illuminate\Support\Str::uuid(), 'name' => 'BAC',                          'code' => 'bac',         'sort_order' => 5,  'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => \Illuminate\Support\Str::uuid(), 'name' => 'Licence (Bac+3)',              'code' => 'licence',     'sort_order' => 6,  'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => \Illuminate\Support\Str::uuid(), 'name' => 'Master (Bac+5)',               'code' => 'master',      'sort_order' => 7,  'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => \Illuminate\Support\Str::uuid(), 'name' => 'Autre',                        'code' => 'autre',       'sort_order' => 8,  'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('referentials_education_levels');
    }
};
