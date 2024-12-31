<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityBook extends Model
{
    use HasFactory;

    /**
     * Nome da tabela associado ao modelo.
     */
    protected $table = 'activity_book';

    /**
     * Campos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'activity_id',  // ID da atividade
        'book_id',      // ID do livro
    ];

    /**
     * Relacionamento: Pertence a uma atividade.
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    /**
     * Relacionamento: Pertence a um livro.
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    /**
     * Relacionamento: Uma atividade tem muitas imagens.
     */
    public function activityImages()
    {
        return $this->hasMany(ActivityImage::class, 'activity_id', 'activity_id');
    }
}
