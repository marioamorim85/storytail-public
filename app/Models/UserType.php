<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use HasFactory;

    // Constantes para os tipos de usuário
    const ADMIN = 1;
    const NORMAL_USER = 2;

    protected $table = 'user_types';
    protected $fillable = ['user_type'];

    // Relacionamento
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Método helper
    public static function getTypes()
    {
        return [
            self::ADMIN => 'Admin',
            self::NORMAL_USER => 'Normal User'
        ];
    }
}
