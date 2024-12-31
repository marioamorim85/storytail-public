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
        Schema::create('activity_book', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')
                ->constrained('activities')
                ->onDelete('cascade'); // Referência à atividade, deletar associação ao deletar a atividade
            $table->foreignId('book_id')
                ->constrained('books')
                ->onDelete('cascade'); // Referência ao livro, deletar associação ao deletar o livro
            $table->unique(['activity_id', 'book_id']); // Garante que não haja duplicações no relacionamento
            $table->timestamps(); // Campos created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_book');
    }
};

