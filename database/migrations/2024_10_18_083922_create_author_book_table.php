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
        Schema::create('author_book', function (Blueprint $table) {
            // Definir a chave primária composta
            $table->primary(['author_id', 'book_id']);

            // Definir ambas as colunas como foreign keys
            $table->foreignId('author_id')->constrained('authors')->onDelete('cascade'); // Referência ao autor
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade'); // Referência ao livro

            // Campos de timestamp
            $table->timestamps(); // Campos created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('author_book');
    }
};
