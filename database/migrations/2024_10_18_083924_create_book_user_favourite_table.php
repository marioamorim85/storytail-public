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
        Schema::create('book_user_favourite', function (Blueprint $table) {
            // Definir a chave primária composta
            $table->primary(['book_id', 'user_id']);

            // Definir ambas as colunas como foreign keys
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade'); // Referência ao livro
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Referência ao utilizador

            // Campos de timestamp
            $table->timestamps(); // Campos created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_user_favourite');
    }
};
