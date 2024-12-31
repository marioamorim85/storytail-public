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
        Schema::create('activity_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')
                ->constrained('activities')
                ->onDelete('cascade'); // Deletar imagens ao excluir a atividade
            $table->string('title', 255); // Título da imagem
            $table->string('image_url', 2083); // URL da imagem
            $table->integer('order')->default(0); // Ordem da imagem (opcional, padrão 0)
            $table->timestamps(); // Campos created_at e updated_at
            $table->softDeletes(); // Campo para exclusão lógica
            $table->index('activity_id'); // Índice para melhorar buscas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_images');
    }
};


