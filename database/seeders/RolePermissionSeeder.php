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
        Permission::create(['name' => 'create posts']);
        Permission::create(['name' => 'delete any post']);
        Permission::create(['name' => 'delete own post']);
        Permission::create(['name' => 'update any post']);
        Permission::create(['name' => 'update own post']);

        Permission::create(['name' => 'view all users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'view any user']);
        Permission::create(['name' => 'update any user']);
        Permission::create(['name' => 'delete any user']);

        Permission::create(['name' => Permissions::CAN_PUBLISH_COMMENT]);
        Permission::create(['name' => Permissions::CAN_UNPUBLISH_COMMENT]);
        Permission::create(['name' => Permissions::CAN_PENDING_COMMENT]);

        // Questionable permission because they allow action, but not the state changing
        Permission::create(['name' => Permissions::CAN_RATE_POST]);
        Permission::create(['name' => Permissions::CAN_COMMENT_POST]);

        Permission::create(['name' => Permissions::CAN_ALLOW_USER]);
        Permission::create(['name' => Permissions::CAN_REJECT_USER]);
        Permission::create(['name' => Permissions::CAN_PENDING_USER]);

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
        ]);

        $authorRole->givePermissionTo([
            'change post status to pending',
            'create posts',
            'delete own post',
            'update own post'
        ]);

        $memberRole->givePermissionTo([
            Permissions::CAN_RATE_POST,
            Permissions::CAN_COMMENT_POST
        ]);
    }
}
