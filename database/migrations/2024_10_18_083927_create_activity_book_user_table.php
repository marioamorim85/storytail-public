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
        Schema::create('activity_book_user', function (Blueprint $table) {
            // Definir a chave primária composta
            $table->primary(['activity_book_id', 'user_id']);

            // Definir ambas as colunas como foreign keys
            $table->foreignId('activity_book_id')->constrained('activity_book')->onDelete('cascade'); // Referência à atividade associada ao livro
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Referência ao utilizador

            // Campo de progresso da atividade
            $table->integer('progress'); // Progresso da atividade (0-100%)

            // Campos de timestamp
            $table->timestamps(); // Campos created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_book_user');
    }
};
