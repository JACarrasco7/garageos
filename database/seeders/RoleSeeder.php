<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'admin' => 'Administrador del sistema',
            'importer' => 'Importador de vehículos',
            'user' => 'Usuario normal',
        ];

        foreach ($roles as $name => $description) {
            Role::create([
                'name' => $name,
                'description' => $description,
            ]);
        }

        $permissions = [
            'admin.access_dashboard',
            'admin.manage_users',
            'admin.manage_roles',
            'importer.create_listings',
            'importer.manage_imports',
            'user.view_dashboard',
            'user.manage_profile',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $admin = Role::findByName('admin');
        $admin->givePermissionTo([
            'admin.access_dashboard',
            'admin.manage_users',
            'admin.manage_roles',
            'importer.create_listings',
            'importer.manage_imports',
            'user.view_dashboard',
            'user.manage_profile',
        ]);

        $importer = Role::findByName('importer');
        $importer->givePermissionTo([
            'importer.create_listings',
            'importer.manage_imports',
            'user.view_dashboard',
            'user.manage_profile',
        ]);

        $user = Role::findByName('user');
        $user->givePermissionTo([
            'user.view_dashboard',
            'user.manage_profile',
        ]);
    }
}
