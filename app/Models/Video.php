<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $table = 'videos'; // Define o nome da tabela

    // Campos que podem ser preenchidos em massa
    protected $fillable = ['title', 'book_id', 'video_url'];

    // Relacionamento: Um vídeo pertence a um livro
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
