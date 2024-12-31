<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_actions', function (Blueprint $table) {
            $table->id();
            $table->string('action_name')->unique(); // Nome único para cada ação
            $table->integer('points'); // Pontos atribuídos à ação
            $table->string('description'); // Descrição da ação
            $table->boolean('is_active')->default(true); // Permite desativar ações sem as apagar
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_actions');
    }
};
