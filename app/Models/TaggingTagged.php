<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaggingTagged extends Model // Renomeie aqui
{
    use HasFactory;

    protected $table = 'tagging_tagged'; // Define o nome da tabela

    // Campos que podem ser preenchidos em massa
    protected $fillable = ['book_id', 'tag_id'];

    // Relacionamento: Um registo de TaggingTagged pertence a um livro
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Relacionamento: Um registo de TaggingTagged pertence a uma tag
    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
