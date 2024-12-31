<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointAction extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'action_name',
        'points',
        'description',
        'is_active'
    ];

    // Atributos que devem ser convertidos
    protected $casts = [
        'points' => 'integer',
        'is_active' => 'boolean'
    ];

    // Relação: Uma ação tem muitos registos de pontos de utilizadores
    public function userPoints()
    {
        return $this->hasMany(UserPoint::class);
    }
}
