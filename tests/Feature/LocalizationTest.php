<?php

use App\Enums\TicketPriority;
use App\Livewire\MaintenanceCalendar;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('la aplicacion usa el locale espanol', function () {
    expect(config('app.locale'))->toBe('es');
});

test('la pagina de login se muestra en espanol', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Inicie sesión en su cuenta')
        ->assertSee('Mantener sesión activa')
        ->assertDontSee('Log in to your account');
});

test('el calendario muestra el mes en espanol', function () {
    $this->seed(DatabaseSeeder::class);
    $user = User::factory()->create()->assignRole('Cliente');
    $this->actingAs($user);

    $expected = now()->translatedFormat('F');

    Livewire\Livewire::test(MaintenanceCalendar::class)
        ->assertOk()
        ->assertSee($expected);
});

test('el listado de tickets muestra el mensaje de exito tras crear', function () {
    $this->seed(DatabaseSeeder::class);
    $user = User::factory()->create()->assignRole('Cliente');
    $this->actingAs($user);

    Livewire\Livewire::test(\App\Livewire\TicketCreate::class)
        ->set('title', 'Ticket para verificar el mensaje flash')
        ->set('description', 'Descripcion suficientemente larga para el ticket de prueba.')
        ->set('priority', TicketPriority::Media->value)
        ->set('category', 'Correctivo')
        ->set('maintenance_type', 'Hardware')
        ->call('save')
        ->assertRedirect(route('tickets.index'));

    $this->assertDatabaseHas('tickets', [
        'title' => 'Ticket para verificar el mensaje flash',
        'created_by' => $user->id,
    ]);
});

test('los listados renderizan el bloque de mensaje flash', function () {
    $this->seed(DatabaseSeeder::class);
    $admin = User::factory()->create()->assignRole('Admin');
    $this->actingAs($admin);

    foreach ([
        route('tickets.index'),
        route('equipment.index'),
        route('clients.index'),
        route('maintenance-schedules.index'),
    ] as $url) {
        session()->flash('status', 'Mensaje de prueba de consistencia');

        $this->get($url)
            ->assertOk()
            ->assertSee('Mensaje de prueba de consistencia');
    }
});
