<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        // Admin principal
        $admin = User::updateOrCreate(
            ['email' => 'admin@techservice.com'],
            ['name' => 'Carlos Mendoza', 'password' => bcrypt('password')]
        );
        $admin->syncRoles(['Admin']);

        // Agentes con nombres realistas
        $agentNames = [
            'María García López',
            'Juan Pérez Rodríguez',
            'Ana Martínez Sánchez',
            'Roberto Hernández Díaz',
            'Laura Ramírez Torres',
            'Miguel Ángel Flores',
        ];

        $agents = [];
        foreach ($agentNames as $index => $name) {
            $email = 'agent'.($index + 1).'@techservice.com';
            $agent = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => bcrypt('password')]
            );
            $agent->syncRoles(['Agente']);
            $agents[] = $agent;
        }

        // Clientes con nombres realistas
        $clientNames = [
            'Sofía Rodríguez Castro',
            'Diego Fernández Ruiz',
            'Valentina López Morales',
            'Andrés Gómez Vargas',
            'Camila Silva Mendoza',
            'Alejandro Torres Reyes',
            'Isabella Morales Cruz',
            'Sebastián Vargas Jiménez',
        ];

        $clients = [];
        foreach ($clientNames as $index => $name) {
            $email = 'client'.($index + 1).'@techservice.com';
            $client = User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => bcrypt('password')]
            );
            $client->syncRoles(['Cliente']);
            $clients[] = $client;
        }

        // Ejecutar seeders en orden
        $this->call(ClientSeeder::class);
        $this->call(EquipmentSeeder::class);
        $this->call(TicketSeeder::class);
        $this->call(MaintenanceScheduleSeeder::class);
    }
}
