<?php

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

// --- Autenticación ---

test('guest is redirected away from tickets list', function () {
    $this->get(route('tickets.index'))->assertRedirect(route('login'));
});

// --- Visibilidad por Rol ---

test('regular user only sees their own tickets', function () {
    $owner = User::factory()->create()->assignRole('Cliente');
    $other = User::factory()->create()->assignRole('Cliente');

    $mine = Ticket::factory()->create(['created_by' => $owner->id]);
    $theirs = Ticket::factory()->create(['created_by' => $other->id]);

    $this->actingAs($owner)->get(route('tickets.index'))
        ->assertSee($mine->title)
        ->assertDontSee($theirs->title);
});

test('admin can see all tickets', function () {
    $admin = User::factory()->create()->assignRole('Admin');
    $user = User::factory()->create()->assignRole('Cliente');

    $ticket = Ticket::factory()->create([
        'title' => 'Unique Admin Ticket Title',
        'created_by' => $user->id,
        'created_at' => now()->addMinute(),
    ]);

    $this->actingAs($admin)->get(route('tickets.index'))
        ->assertSee('Unique Admin Ticket Title');
});

// --- IDOR Prevention ---

test('user cannot view ticket that belongs to another user', function () {
    $owner = User::factory()->create()->assignRole('Cliente');
    $other = User::factory()->create()->assignRole('Cliente');

    $ticket = Ticket::factory()->create([
        'created_by' => $owner->id,
    ]);

    $this->actingAs($other)->get(route('tickets.show', $ticket))
        ->assertForbidden();
});

test('ticket owner can view their own ticket', function () {
    $owner = User::factory()->create()->assignRole('Cliente');

    $ticket = Ticket::factory()->create([
        'created_by' => $owner->id,
    ]);

    $this->actingAs($owner)->get(route('tickets.show', $ticket))
        ->assertOk();
});

// --- Creación de Tickets ---

test('ticket can be created with valid data via livewire', function () {
    $user = User::factory()->create()->assignRole('Cliente');

    $this->actingAs($user);

    Livewire\Livewire::test(App\Livewire\TicketCreate::class)
        ->set('title', 'Equipo no enciende correctamente')
        ->set('description', 'El equipo no arranca luego de presionar el botón de encendido.')
        ->set('priority', TicketPriority::Media->value)
        ->set('category', TicketCategory::Correctivo->value)
        ->set('maintenance_type', 'Hardware')
        ->call('save')
        ->assertRedirect(route('tickets.index'));

    $this->assertDatabaseHas('tickets', ['title' => 'Equipo no enciende correctamente', 'created_by' => $user->id]);
});

test('ticket creation fails without title', function () {
    $user = User::factory()->create()->assignRole('Cliente');
    $this->actingAs($user);

    Livewire\Livewire::test(App\Livewire\TicketCreate::class)
        ->set('title', '')
        ->set('description', 'Descripcion larga valida para el test')
        ->call('save')
        ->assertHasErrors(['title']);
});

// --- Estado visual de selección en el formulario ---

test('solo la prioridad seleccionada aparece activa', function () {
    $user = User::factory()->create()->assignRole('Cliente');
    $this->actingAs($user);

    $component = Livewire\Livewire::test(App\Livewire\TicketCreate::class)
        ->set('priority', TicketPriority::Alta->value);

    $html = $component->html();

    // La tarjeta/clase de prioridad alta (seleccionada) debe tener 'scale-105'
    expect(substr_count($html, 'scale-105'))->toBe(1);

    $component->assertSee(TicketPriority::Alta->value);
});
