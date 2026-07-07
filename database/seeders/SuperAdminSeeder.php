<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Crear rol SuperAdmin
        $superAdmin = Role::create([
            'name' => 'superadmin',
            'guard_name' => 'web',
            'description' => 'Administrador total con acceso a todo',
        ]);

        // Permisos para Stripe
        $stripePermissions = [
            'manage stripe keys',
            'view stripe dashboard',
            'process payments',
            'view transactions',
        ];

        foreach ($stripePermissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Asignar permisos al SuperAdmin
        $superAdmin->givePermissionTo($stripePermissions);

        // Crear usuario SuperAdmin
        $user = User::updateOrCreate(
            ['email' => 'jacararsco@garageos.com'],
            [
                'name' => 'Jacar',
                'email' => 'jacararsco@garageos.com',
                'password' => bcrypt('admin'),
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('superadmin');

        $this->command->info('SuperAdmin creado: jacararsco@garageos.com / password: admin');
    }
}
