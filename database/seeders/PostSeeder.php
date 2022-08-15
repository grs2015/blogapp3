<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
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
        $this->call(RolePermissionSeeder::class);

        User::factory(5)
        ->has(Post::factory(3)->hasComments(3)->hasPostmetas(3)->hasTags(1)->hasCategories(1)
        ->state(new Sequence(
            ['status' => PostStatus::Draft],
            ['status' => PostStatus::Pending],
            ['status' => PostStatus::Published]
        ))
        ->state(new Sequence(
            ['favorite' => FavoriteStatus::Nonfavorite],
            ['favorite' => FavoriteStatus::Favorite],
        )))
        ->create(['registered_at' => now()->toDateString(), 'last_login' => now()->toDateString()]);

        $user = User::find(1)->assignRole('admin');
        $user = User::find(2)->assignRole('author');
        $user = User::find(3)->assignRole('super-admin');
        $user = User::find(4)->assignRole('author');
        $user = User::find(5)->assignRole('member');
        // $user->assignRole('admin');


        // Post::factory(3)
        // ->hasComments(3)
        // ->hasPostmetas(3)
        // ->for(User::factory()->create())
        // ->hasTags(1)
        // ->hasCategories(1)
        // ->state(new Sequence(
        //     ['status' => PostStatus::Draft],
        //     ['status' => PostStatus::Pending],
        //     ['status' => PostStatus::Published]
        // ))
        // ->state(new Sequence(
        //     ['favorite' => FavoriteStatus::Nonfavorite],
        //     ['favorite' => FavoriteStatus::Favorite],
        // ))
        // ->create();
    }
}
