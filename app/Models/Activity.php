<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    /**
     * Campos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'title',         // Título da atividade
        'description',   // Descrição da atividade
        'is_active',     // Estado da atividade (ativa ou não)
    ];

    /**
     * Relacionamento: Uma atividade pode estar associada a muitos livros.
     * A relação usa a tabela pivot `activity_book`.
     */
    public function books()
    {
        return $this->belongsToMany(Book::class, 'activity_book');
    }

    /**
     * Relacionamento: Uma atividade tem muitas imagens.
     */
    public function activityImages()
    {
        return $this->hasMany(ActivityImage::class, 'activity_id');
    }

}
