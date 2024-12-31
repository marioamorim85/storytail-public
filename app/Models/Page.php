<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes; // Inclui o SoftDeletes

    /**
     * Nome da tabela associada ao modelo.
     */
    protected $table = 'pages'; // Define o nome da tabela

    /**
     * Campos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'book_id',         // ID do livro associado
        'page_image_url',  // URL da imagem da página
        'audio_url',       // URL do áudio da página (opcional)
        'page_index',      // Índice da página
    ];

    /**
     * Relacionamento: Uma página pertence a um livro.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
