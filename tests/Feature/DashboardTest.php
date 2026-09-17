<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('el dashboard muestra los contadores reales de tickets', function () {
    $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);

    $user = \App\Models\User::factory()->create()->assignRole('Cliente');
    \App\Models\Ticket::factory()->create([
        'created_by' => $user->id,
        'title' => 'Solicitud del dashboard',
        'status' => \App\Enums\TicketStatus::Abierto,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('Qué necesita atención')
        ->assertSee('Próximas acciones')
        ->assertSee('Solicitud del dashboard');
});
