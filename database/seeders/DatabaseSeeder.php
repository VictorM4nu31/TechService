<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        // Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@techservice.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password')]
        );
        $admin->syncRoles(['Admin']);

        // Agent User
        $agent = User::updateOrCreate(
            ['email' => 'agent@techservice.com'],
            ['name' => 'Agent User', 'password' => bcrypt('password')]
        );
        $agent->syncRoles(['Agente']);

        // Create additional agents
        User::factory(5)->create()->each(function ($user) {
            $user->syncRoles(['Agente']);
        });

        // Client User
        $client = User::updateOrCreate(
            ['email' => 'client@techservice.com'],
            ['name' => 'Client User', 'password' => bcrypt('password')]
        );
        $client->syncRoles(['Cliente']);

        // Create additional clients
        User::factory(3)->create()->each(function ($user) {
            $user->syncRoles(['Cliente']);
        });

        $this->call(TicketSeeder::class);
    }
}
