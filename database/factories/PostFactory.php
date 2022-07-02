<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
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
            'author_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'meta_title' => $this->faker->sentence(),
            'summary' => $this->faker->text(),
            'content' => $this->faker->text(),
            'views' => 0,
            'hero_image' => $this->faker->imageUrl(640, 480, 'blog-image', true),
            'images' => $this->faker->imageUrl().','.$this->faker->imageUrl(),
            'time_to_read' => $this->faker->numberBetween(1, 20),
        ];
    }

    /**
     * The Post is published
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function published()
    {
        return $this->state(function () {
            return [
                'published' => Post::PUBLISHED
            ];
        });
    }

    /**
     * The Post is draft
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function draft()
    {
        return $this->state(function () {
            return [
                'published' => Post::DRAFT
            ];
        });
    }

    /**
     * The Post is pending
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function pending()
    {
        return $this->state(function () {
            return [
                'published' => Post::PENDING
            ];
        });
    }

    /**
     * The Post is unpublished
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unpublished()
    {
        return $this->state(function () {
            return [
                'published' => Post::UNPUBLISHED
            ];
        });
    }
}
