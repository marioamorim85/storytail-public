<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPoint extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'user_id',
        'point_action_id',
        'points_earned',
        'reference_type',
        'reference_id'
    ];

    // Atributos que devem ser convertidos
    protected $casts = [
        'points_earned' => 'integer'
    ];

    // Relação: Um registo de pontos pertence a um utilizador
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relação: Um registo de pontos pertence a uma ação
    public function pointAction()
    {
        return $this->belongsTo(PointAction::class);
    }

    // Relação polimórfica para o item referenciado (livro, comentário, etc)
    public function reference()
    {
        return $this->morphTo();
    }
}
