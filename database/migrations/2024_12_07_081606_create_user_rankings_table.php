<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->bigInteger('total_points')->default(0); // Total de pontos do utilizador
            $table->integer('current_rank')->nullable(); // Posição atual no ranking
            $table->integer('previous_rank')->nullable(); // Posição anterior no ranking
            $table->string('rank_level')->default('Bronze'); // Bronze, Silver, Gold, etc.
            $table->timestamp('last_calculated_at')->nullable(); // Última vez que o ranking foi calculado
            $table->timestamps();

            // Índices para melhorar performance
            $table->index('total_points');
            $table->index('current_rank');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_rankings');
    }
};
