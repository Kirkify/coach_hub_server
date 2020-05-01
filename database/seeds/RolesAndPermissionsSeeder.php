<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Super admin only permissions
        Permission::create(['name' => config('permission.names.create_base_profile')]);
        Permission::create(['name' => config('permission.names.create_base_profile')]);

        // Super admin only permissions
        Permission::create(['name' => config('permission.names.create_admin')]);
        Permission::create(['name' => config('permission.names.read_admin')]);
        Permission::create(['name' => config('permission.names.update_admin')]);
        Permission::create(['name' => config('permission.names.delete_admin')]);

        // Admin permissions
        Permission::create(['name' => config('permission.names.create_user')]);
        Permission::create(['name' => config('permission.names.read_user')]);
        Permission::create(['name' => config('permission.names.update_user')]);
        Permission::create(['name' => config('permission.names.delete_user')]);

        // create roles and assign existing permissions
        $role = Role::create(['name' => config('role.names.admin')]);
        $role->givePermissionTo(config('permission.names.create_user'));
        $role->givePermissionTo(config('permission.names.read_user'));
        $role->givePermissionTo(config('permission.names.update_user'));
        $role->givePermissionTo(config('permission.names.delete_user'));

        // Give super admins all permissions
        $role = Role::create(['name' => config('role.names.super_admin')]);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => config('role.names.coach')]);
    }
}
