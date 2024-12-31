<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_an_author()
    {
        $author = Author::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'Author',
            'description' => 'This is a test author description.',
            'nationality' => 'Test Nationality',
            'author_photo_url' => 'authors/Rachel.jpg',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->assertDatabaseHas('authors', [
            'first_name' => 'Test',
            'last_name' => 'Author',
            'description' => 'This is a test author description.',
            'nationality' => 'Test Nationality',

        ]);
    }

    /** @test */
    public function it_creates_an_author_with_default_attributes()
    {
        $author = Author::factory()->create();

        $this->assertNotNull($author->first_name);
        $this->assertNotNull($author->last_name);
        $this->assertNotNull($author->description);
        $this->assertNotNull($author->nationality);
    }
}
