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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Utilizador
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade'); // Plano
            $table->enum('status', ['active', 'expired', 'canceled', 'inactive'])->default('active'); // Estado (com inactive adicionado)
            $table->timestamp('start_date'); // Data de início
            $table->timestamp('end_date')->nullable(); // Data de término (se terminou)
            $table->boolean('is_renewable')->default(true); // Subscrição pode ser renovada
            $table->timestamps();

            // Índices para otimização de consultas
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
