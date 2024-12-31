<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'description',
        'nationality',
        'author_photo_url'
    ];

    // Relacionamento Muitos-para-Muitos com Books
    public function books()
    {
        return $this->belongsToMany(Book::class, 'author_book');
    }

    // Nome completo
    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }


    public function getFullName()
    {
        return $this->name;
    }
}




