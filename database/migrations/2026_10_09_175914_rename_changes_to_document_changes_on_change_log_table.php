<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('change_log', function (Blueprint $table) {
            $table->renameColumn('changes', 'document_changes');
        });
    }

    public function down(): void
    {
        Schema::table('change_log', function (Blueprint $table) {
            $table->renameColumn('document_changes', 'changes');
        });
    }
};