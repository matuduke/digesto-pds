<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('groups')->insert([
            [
                'name' => 'creadores_de_reglamentaciones',
                'description' => 'Creadores de reglamentaciones',
            ],
            [
                'name' => 'aprobadores_de_reglamentaciones',
                'description' => 'Aprobadores de reglamentaciones',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('groups')->whereIn('name', [
            'creadores_de_reglamentaciones',
            'aprobadores_de_reglamentaciones',
        ])->delete();
    }
};
