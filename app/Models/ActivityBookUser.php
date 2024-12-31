<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityBookUser extends Model
{
    use HasFactory;

    protected $table = 'activity_book_user'; // Define o nome da tabela

    protected $fillable = ['activity_book_id', 'user_id', 'progress'];

    // Relacionamento: Uma instância de progresso pertence a uma atividade associada a um livro
    public function activityBook()
    {
        return $this->belongsTo(ActivityBook::class, 'activity_book_id');
    }

    // Relacionamento: Um progresso pertence a um usuário
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

