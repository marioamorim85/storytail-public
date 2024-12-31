<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tags'; // Define o nome da tabela

    // Campos que podem ser preenchidos em massa
    protected $fillable = ['name'];

    // Relacionamento: Uma tag pode estar associada a muitos livros
    public function books()
    {
        return $this->belongsToMany(Book::class, 'tagging_tagged', 'tag_id', 'book_id');
    }
}
