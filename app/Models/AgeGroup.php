<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgeGroup extends Model
{
    use HasFactory;

    protected $table = 'age_groups'; // Define o nome da tabela

    // Campos que podem ser preenchidos em massa
    protected $fillable = ['age_group'];

    // Relação com Books
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
