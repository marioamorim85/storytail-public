<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments'; // Define o nome da tabela

    // Campos que podem ser preenchidos em massa
    protected $fillable = ['user_id', 'book_id', 'comment_text'];

    // Relacionamento: Um comentário pertence a um utilizador
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento: Um comentário pertence a um livro
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Relacionamento: Um comentário tem uma moderação
    public function moderation()
    {
        return $this->hasOne(CommentModeration::class, 'comment_id');
    }

    // Método helper: Verifica o status atual do comentário via moderação
    public function getStatusAttribute()
    {
        return $this->moderation ? $this->moderation->status : 'pending';
    }
}
