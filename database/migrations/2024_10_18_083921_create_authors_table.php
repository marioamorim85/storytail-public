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
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name'); // Primeiro nome do autor
            $table->string('last_name'); // Sobrenome do autor
            $table->text('description')->nullable(); // Descrição/biografia do autor
            $table->string('author_photo_url')->nullable(); // URL da foto do autor (opcional)
            $table->string('nationality')->nullable(); // Nacionalidade do autor
            $table->timestamps(); // Campos created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
