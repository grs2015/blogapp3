<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Enums\PostStatus;
use App\Enums\FavoriteStatus;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Post::factory(5)
        ->hasComments(3)
        ->hasPostmetas(3)
        ->hasTags(3)
        ->hasCategories(3)
        ->state(new Sequence(
            ['status' => PostStatus::Draft],
            ['status' => PostStatus::Pending],
            ['status' => PostStatus::Published]
        ))
        ->state(new Sequence(
            ['favorite' => FavoriteStatus::Nonfavorite],
            ['favorite' => FavoriteStatus::Favorite],
        ))
        ->create();
    }
}
