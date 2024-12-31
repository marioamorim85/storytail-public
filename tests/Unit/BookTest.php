<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_book()
    {
        $book = Book::factory()->create([
            'title' => 'Test Book',
            'description' => 'This is a test book description.',
        ]);

        $this->assertDatabaseHas('books', [
            'title' => 'Test Book',
            'description' => 'This is a test book description.',
        ]);
    }
}
