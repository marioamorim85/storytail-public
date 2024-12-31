<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityImage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nome da tabela associada ao modelo.
     */
    protected $table = 'activity_images';

    /**
     * Campos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'activity_id',  // ID da atividade associada
        'title',        // Título da imagem
        'image_url',    // URL da imagem
        'order',        // Ordem da imagem (opcional)
    ];

    /**
     * Relacionamento: A imagem pertence a uma atividade.
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }
}


