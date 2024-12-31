<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRanking extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'user_id',
        'total_points',
        'current_rank',
        'previous_rank',
        'rank_level',
        'last_calculated_at'
    ];

    // Atributos que devem ser convertidos
    protected $casts = [
        'total_points' => 'integer',
        'current_rank' => 'integer',
        'previous_rank' => 'integer',
        'last_calculated_at' => 'datetime'
    ];

    // Relação: Um ranking pertence a um utilizador
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Método para atualizar o nível baseado nos pontos
    public function updateRankLevel()
    {
        $this->rank_level = match(true) {
            $this->total_points >= 1000 => 'Gold',
            $this->total_points >= 500 => 'Silver',
            default => 'Bronze'
        };

        return $this;
    }
}
