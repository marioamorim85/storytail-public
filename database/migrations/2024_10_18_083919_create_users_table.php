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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_type_id')
                ->constrained('user_types') // Relacionamento com a tabela user_types
                ->onDelete('cascade') // Se o tipo de utilizador for apagado, o utilizador será também
                ->onUpdate('cascade'); // Atualiza automaticamente se o ID do tipo de utilizador mudar
            $table->string('first_name'); // Primeiro nome
            $table->string('last_name'); // Sobrenome
            $table->string('email')->unique(); // E-mail único
            $table->string('password'); // Senha encriptada
            $table->string('user_photo_url')->nullable(); // URL da foto de perfil
            $table->date('birth_date')->nullable(); // Data de nascimento
            $table->timestamp('email_verified_at')->nullable(); // Verificação de email
            $table->enum('status', ['active', 'suspended', 'inactive'])->default('active'); // Estado da conta
            $table->rememberToken(); // Token para "Lembrar-me" no login
            $table->timestamps(); // Campos created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
