<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'parent_id' => 1,
            'title' => $this->faker->words(3, true),
            'meta_title' => $this->faker->sentence(),
            'content' => $this->faker->sentence(),
            'icon' => 'description',
            'color' => 'purple'
        ];
    }
}
