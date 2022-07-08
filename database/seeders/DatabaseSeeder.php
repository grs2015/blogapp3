<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call([RolePermissionSeeder::class]);

        $users = User::factory()->hasPosts(3)->create();
        $users->each(fn($user) => $user->assignRole('author'));

        $users = User::factory()->create();
        $users->each(fn($user) => $user->assignRole('member'));

        $user = User::factory()->create();
        $user->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('super-admin');
    }
}
