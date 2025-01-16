<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'create products']);
        Permission::create(['name' => 'edit products']);
        Permission::create(['name' => 'archive products']);

        Permission::create(['name' => 'view orders']);
        Permission::create(['name' => 'edit orders']);

        Permission::create(['name' => 'view customers']);

        Permission::create(['name' => 'view reports']);

        Role::create(['name' => 'super-admin'])
            ->givePermissionTo(Permission::all());

        Role::create(['name' => 'main-admin'])
            ->givePermissionTo(Permission::all());

        Role::create(['name' => 'morelos-admin'])
            ->givePermissionTo(Permission::all());

        Role::create(['name' => 'customer']);
    }
}
