<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookUserRead extends Model
{
    use HasFactory;

    protected $table = 'book_user_read'; // Define o nome da tabela

    // Campos que podem ser preenchidos em massa
    protected $fillable = ['user_id', 'book_id', 'progress', 'rating', 'read_date'];

    // Relacionamento: Um livro lido pertence a um utilizador
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento: Um livro lido pertence a um livro
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
