<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\AgeGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'read_time' => $this->faker->numberBetween(5, 30),
            'access_level' => $this->faker->numberBetween(1, 2),
            'is_active' => $this->faker->boolean,
            'age_group_id' => AgeGroup::factory(),
            'cover_url' => $this->faker->imageUrl(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
