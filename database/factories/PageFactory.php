<?php

namespace Database\Factories;

use App\Models\Page;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition()
    {
        return [
            'book_id' => Book::factory(),
            'page_image_url' => $this->faker->imageUrl(),
            'audio_url' => $this->faker->url,
            'page_index' => $this->faker->numberBetween(1, 10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
