<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookUserFavourite extends Model
{
    use HasFactory;

    protected $table = 'book_user_favourite'; // Define o nome da tabela

    // Campos que podem ser preenchidos em massa
    protected $fillable = ['user_id', 'book_id'];

    // Relacionamento: Um favorito pertence a um utilizador
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento: Um favorito pertence a um livro
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
