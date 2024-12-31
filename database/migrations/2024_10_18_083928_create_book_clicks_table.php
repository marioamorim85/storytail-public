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
        Schema::create('book_clicks', function (Blueprint $table) {
            $table->id(); // Chave primária simples
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade'); // Referência ao livro
            $table->timestamp('clicked_at')->useCurrent(); // Data e hora do clique
            $table->timestamps(); // Campos created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_clicks');
    }
};
