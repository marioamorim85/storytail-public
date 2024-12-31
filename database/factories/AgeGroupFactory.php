<?php

namespace Database\Factories;

use App\Models\AgeGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgeGroupFactory extends Factory
{
    protected $model = AgeGroup::class;

    public function definition()
    {
        return [
            'age_group' => $this->faker->randomElement(['3-4', '5-6', '7-9']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
