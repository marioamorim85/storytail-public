<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'read_time',
        'access_level',
        'age_group_id',
        'is_active',
        'cover_url',
        'video_url'
    ];

    // Relacionamento: Um livro pode ter muitos autores
    public function authors()
    {
        return $this->belongsToMany(Author::class, 'author_book', 'book_id', 'author_id')
            ->withTimestamps();
    }

    // Relacionamento: Um livro pertence a uma faixa etária
    public function ageGroup()
    {
        return $this->belongsTo(AgeGroup::class, 'age_group_id');
    }

    // Relacionamento: Um livro pode ter muitas atividades
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activity_book');
    }


    // Relacionamento: Um livro pode ter muitas tags
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tagging_tagged', 'book_id', 'tag_id');
    }

    // Relacionamento: Um livro pode ter muitos cliques
    public function clicks()
    {
        return $this->hasMany(BookClick::class, 'book_id');
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function usersRead()
    {
        return $this->belongsToMany(User::class, 'book_user_read')
            ->withPivot('progress', 'rating', 'read_date')
            ->withTimestamps();
    }


    public function userFavorite()
    {
        return $this->hasMany(BookUserFavourite::class);
    }

    public function avgRating()
    {
        return $this->hasMany(BookUserRead::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'book_user_favourite', 'book_id', 'user_id')
            ->withTimestamps();
    }

    public function video()
    {
        return $this->hasOne(Video::class);
    }

    public function getYoutubeId($url)
    {
        if (empty($url)) return null;

        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';

        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

}
