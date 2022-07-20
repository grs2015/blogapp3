<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
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
        Post::factory(3)
        ->hasComments(3)
        ->hasPostmetas(3)
        ->hasTags(3)
        ->hasCategories(3)
        ->create();
    }
}
