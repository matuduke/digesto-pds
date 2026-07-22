<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_type_id')->index()->constrained('document_types');
            $table->foreignId('user_id')->constrained('users');
            $table->string('number')->nullable();
            $table->enum('status', ['borrador', 'publicado']);
            $table->boolean('is_public')->default(false);
            $table->longText('content')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->fullText('number');
            $table->fullText('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
