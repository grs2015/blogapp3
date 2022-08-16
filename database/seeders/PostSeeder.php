<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Enums\PostStatus;
use App\Enums\FavoriteStatus;
use App\Enums\UserStatus;
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

        $authors = User::factory(6)
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
        ->state(new Sequence(
            ['status' => UserStatus::Enabled],
            ['status' => UserStatus::Enabled],
            ['status' => UserStatus::Enabled],
            ['status' => UserStatus::Enabled],
            ['status' => UserStatus::Disabled],
            ['status' => UserStatus::Pending]
        ))
        ->create([
            'registered_at' => now()->toDateString(),
            'last_login' => now()->toDateString()
        ]);

        $authors->each(function($user) { $user->assignRole('author'); });
        $authors->first()->email = 'author@author.com';
        $authors->first()->save();

        $members = User::factory(3)
        ->state(new Sequence(
            ['status' => UserStatus::Enabled],
            ['status' => UserStatus::Disabled],
            ['status' => UserStatus::Pending]
        ))
        ->create([
            'registered_at' => now()->toDateString(),
            'last_login' => now()->toDateString()
        ]);

        $members->each(function($user) { $user->assignRole('member'); });
        $members->first()->email = 'member@member.com';
        $members->first()->save();

        $admins = User::factory(2)
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
        ->state(new Sequence(
            ['status' => UserStatus::Enabled],
            ['status' => UserStatus::Disabled],

        ))
        ->create([
            'registered_at' => now()->toDateString(),
            'last_login' => now()->toDateString()
        ]);

        $admins->each(function($user) { $user->assignRole('admin'); });
        $admins->first()->email = 'admin@admin.com';
        $admins->first()->save();

        User::factory()->create(['email' => 'superadmin@admin.com', 'status' => 'enabled']);
        User::get()->last()->assignRole('super-admin');

        // User::find(1)->assignRole('admin');
        // User::find(2)->assignRole('author');
        // User::find(3)->assignRole('author');
        // User::find(4)->assignRole('author');
        // User::find(5)->assignRole('member');
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
