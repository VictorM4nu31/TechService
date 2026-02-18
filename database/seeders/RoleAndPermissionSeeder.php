<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Tickets
            'view tickets',
            'create tickets',
            'edit tickets',
            'delete tickets',
            'assign tickets',
            'view all tickets',
            
            // Comments
            'view comments',
            'create comments',
            'edit own comments',
            'delete own comments',
            
            // Teams
            'view teams',
            'manage teams',
            
            // Categories, Statuses, Priorities
            'manage settings',
            
            // Users & Roles
            'manage users',
            'manage roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and Assign Permissions

        // Cliente
        $roleCliente = Role::firstOrCreate(['name' => 'Cliente']);
        $roleCliente->syncPermissions([
            'view tickets',
            'create tickets',
            'view comments',
            'create comments',
            'edit own comments',
            'delete own comments',
        ]);

        // Agente
        $roleAgente = Role::firstOrCreate(['name' => 'Agente']);
        $roleAgente->syncPermissions([
            'view tickets',
            'view all tickets',
            'create tickets',
            'edit tickets',
            'assign tickets',
            'view comments',
            'create comments',
            'edit own comments',
            'delete own comments',
            'view teams',
        ]);

        // Admin
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin']);
        $roleAdmin->syncPermissions(Permission::all());
    }
}
