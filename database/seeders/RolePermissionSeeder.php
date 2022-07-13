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

        Permission::create(['name' => Permissions::CAN_PUBLISH_BLOGPOST]);
        Permission::create(['name' => Permissions::CAN_UNPUBLISH_BLOGPOST]);
        Permission::create(['name' => Permissions::CAN_PENDING_BLOGPOST]);
        Permission::create(['name' => Permissions::CAN_REJECT_BLOGPOST]);

        Permission::create(['name' => Permissions::CAN_SET_FAVORITE_BLOGPOST]);
        Permission::create(['name' => Permissions::CAN_UNSET_FAVORITE_BLOGPOST]);

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
            Permissions::CAN_PUBLISH_BLOGPOST,
            Permissions::CAN_UNPUBLISH_BLOGPOST,
            Permissions::CAN_PENDING_BLOGPOST,
            Permissions::CAN_REJECT_BLOGPOST,
            Permissions::CAN_SET_FAVORITE_BLOGPOST,
            Permissions::CAN_UNSET_FAVORITE_BLOGPOST,
            Permissions::CAN_UNPUBLISH_COMMENT,
            Permissions::CAN_ALLOW_USER,
            Permissions::CAN_REJECT_USER,
            Permissions::CAN_PENDING_USER
        ]);

        $authorRole->givePermissionTo([
            Permissions::CAN_PENDING_BLOGPOST,
        ]);

        $memberRole->givePermissionTo([
            Permissions::CAN_RATE_POST,
            Permissions::CAN_COMMENT_POST
        ]);
    }
}
