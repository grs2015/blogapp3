<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gallery>
 */
class GalleryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'original' => $this->faker->imageUrl(1280, 768, 'Original', true),
            'lowres' => $this->faker->imageUrl(1280, 768, 'Low Resolution', true),
            'thumbs' => array($this->faker->imageUrl(400, 400, 'Thumb 1', true), $this->faker->imageUrl(400, 400, 'Thumb 2', true))
        ];
    }
}
