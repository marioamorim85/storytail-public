<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookClick extends Model
{
    use HasFactory;

    protected $table = 'book_clicks'; // Define o nome da tabela

    protected $fillable = ['book_id', 'user_id', 'clicked_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento: Um clique pertence a um livro
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
