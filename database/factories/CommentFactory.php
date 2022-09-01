<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'post_id' => Post::factory(),
            'title' => $this->faker->word(),
            'content' => $this->faker->text(),
            'published_at' => $this->faker->dateTimeBetween('-2 weeks')
        ];
    }

    /**
     * The Comment is published
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function published()
    {
        return $this->state(function () {
            return [
                'published' => Comment::PUBLISHED
            ];
        });
    }

    /**
     * The Comment is pending
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function pending()
    {
        return $this->state(function () {
            return [
                'published' => Comment::PENDING
            ];
        });
    }

    /**
     * The Comment is unpublished
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unpublished()
    {
        return $this->state(function () {
            return [
                'published' => Comment::UNPUBLISHED
            ];
        });
    }
}
