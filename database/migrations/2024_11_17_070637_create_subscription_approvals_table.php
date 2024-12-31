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
        Schema::create('subscription_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')
                ->constrained('subscriptions')
                ->onDelete('cascade'); // Relacionamento com a subscrição

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null'); // Admin ou utilizador relacionado à aprovação

            $table->enum('status', ['pending', 'approved', 'rejected', 'resolved'])
                ->default('pending'); // Estado do pedido

            $table->string('plan_name')->nullable(); // Nome do plano associado à aprovação

            $table->text('notes')->nullable(); // Notas do admin
            $table->timestamp('approval_date')->nullable(); // Data de aprovação/rejeição
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_approvals');
    }
};
