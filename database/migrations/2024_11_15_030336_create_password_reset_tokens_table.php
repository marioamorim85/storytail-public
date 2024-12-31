<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->index(); // Coluna de email, que é usada para procurar o utilizador
            $table->string('token');          // Coluna para armazenar o token de redefinição
            $table->timestamp('created_at')->nullable(); // Timestamp para saber quando foi criado o pedido de redefinição
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
