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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Título do livro
            $table->text('description')->nullable(); // Descrição do livro
            $table->string('cover_url')->nullable(); // URL da capa do livro
            $table->integer('read_time')->nullable(); // Tempo estimado de leitura (em minutos)
            $table->foreignId('age_group_id')->constrained('age_groups'); // Referência à tabela age_groups
            $table->boolean('is_active')->default(true); // Status do livro (ativo ou inativo)
            $table->integer('access_level'); // Nível de acesso (ex: Premium, Free)
            $table->timestamps(); // Campos created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
