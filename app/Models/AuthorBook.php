<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorBook extends Model
{
    use HasFactory;

    protected $table = 'author_book'; // Define o nome da tabela

    // Campos que podem ser preenchidos em massa
    protected $fillable = ['author_id', 'book_id'];

    // Relacionamento: Um registo de AuthorBook pertence a um Autor
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    // Relacionamento: Um registo de AuthorBook pertence a um Livro
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
