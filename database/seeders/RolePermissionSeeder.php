<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Permissions\Permissions;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $adminRole = Role::create(['name' => 'admin']);
        $authorRole = Role::create(['name' => 'author']);
        $memberRole = Role::create(['name' => 'member']);
        $sadminRole = Role::create(['name' => 'super-admin']);

        Permission::create(['name' => 'change post status to pending']);
        Permission::create(['name' => 'change post status to published']);
        Permission::create(['name' => 'change post status to unpublished']);

        Permission::create(['name' => 'change favorite']);

        Permission::create(['name' => 'view all posts']);
        Permission::create(['name' => 'view own posts']);
        Permission::create(['name' => 'create post']);
        Permission::create(['name' => 'delete any post']);
        Permission::create(['name' => 'delete own post']);
        Permission::create(['name' => 'update any post']);
        Permission::create(['name' => 'update own post']);

        Permission::create(['name' => 'view all users']);
        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'view any user']);
        Permission::create(['name' => 'update any user']);
        Permission::create(['name' => 'delete any user']);
        Permission::create(['name' => 'update own user profile']);

        Permission::create(['name' => 'update baseinfo']);

        Permission::create(['name' => 'view post comments']);
        Permission::create(['name' => 'change comments status']);

        Permission::create(['name' => 'have credentials shown']);



        $adminRole->givePermissionTo([
            'change post status to pending',
            'change post status to published',
            'change post status to unpublished',
            'change favorite',
            'view all posts',
            'delete any post',
            'update any post',
            'view all users',
            'view any user',
            'update any user',
            'delete any user',
            'view post comments',
            'change comments status'
        ]);

        $authorRole->givePermissionTo([
            'change post status to pending',
            'create post',
            'delete own post',
            'update own post',
            'view own posts',
            'update own user profile'
        ]);

        $memberRole->givePermissionTo([
            'have credentials shown'
        ]);
    }
}
