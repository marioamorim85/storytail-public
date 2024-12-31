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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade'); // Referência ao livro
            $table->string('page_image_url'); // URL da imagem da página
            $table->string('audio_url')->nullable(); // URL do áudio da página (se aplicável)
            $table->integer('page_index'); // Índice da página (para ordenar as páginas do livro)
            $table->timestamps(); // Campos created_at e updated_at
            $table->softDeletes(); // Campo deleted_at para soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};

