<?php

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Book;
use App\Models\Author;
use App\Models\AgeGroup;
use App\Models\Activity;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test] public function it_lists_books_with_filters()
    {
        $author = Author::factory()->create();
        $ageGroup = AgeGroup::factory()->create();
        $activity = Activity::factory()->create();
        $tag = Tag::factory()->create();

        $book = Book::factory()->create([
            'title' => 'Sample Book',
            'age_group_id' => $ageGroup->id,
            'is_active' => true,
        ]);

        $book->authors()->attach($author->id);
        $book->activities()->attach($activity->id);
        $book->tags()->attach($tag->id);

        $response = $this->getJson('/api/books?author_id=' . $author->id . '&age_group_id=' . $ageGroup->id . '&activity_id=' . $activity->id . '&tag_id=' . $tag->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Sample Book']);
    }


    #[Test] public function it_filters_books_by_author_name()
    {
        $author = Author::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);
        $book = Book::factory()->create();
        $book->authors()->attach($author->id);

        $response = $this->getJson('/api/books?author_name=John');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => $book->title]);
    }

    #[Test] public function it_filters_books_by_title()
    {
        $book = Book::factory()->create(['title' => 'Unique Title']);

        $response = $this->getJson('/api/books?title=Unique Title');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Unique Title']);
    }
}
