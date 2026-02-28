<?php

use App\Models\User;
use App\Models\Ticket;
use App\Models\Status;
use App\Models\Priority;
use App\Models\Category;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    // Seed roles, statuses, priorities, and categories needed for ticket creation
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

    $status   = Status::first();
    $priority = Priority::first();
    $category = Category::first();

    $mine  = Ticket::factory()->create(['created_by' => $owner->id,   'status_id' => $status->id, 'priority_id' => $priority->id, 'category_id' => $category->id]);
    $theirs = Ticket::factory()->create(['created_by' => $other->id,  'status_id' => $status->id, 'priority_id' => $priority->id, 'category_id' => $category->id]);

    $this->actingAs($owner)->get(route('tickets.index'))
        ->assertSee($mine->title)
        ->assertDontSee($theirs->title);
});

test('admin can see all tickets', function () {
    $admin = User::factory()->create()->assignRole('Admin');
    $user  = User::factory()->create()->assignRole('Cliente');

    $status   = Status::first();
    $priority = Priority::first();
    $category = Category::first();

    // Clear existing tickets so our test ticket appears on page 1
    Ticket::query()->delete();

    $ticket = Ticket::factory()->create(['created_by' => $user->id, 'status_id' => $status->id, 'priority_id' => $priority->id, 'category_id' => $category->id]);

    $this->actingAs($admin)->get(route('tickets.index'))
        ->assertSee($ticket->title);
});

// --- IDOR Prevention ---

test('user cannot view ticket that belongs to another user', function () {
    $owner = User::factory()->create()->assignRole('Cliente');
    $other = User::factory()->create()->assignRole('Cliente');

    $status   = Status::first();
    $priority = Priority::first();
    $category = Category::first();

    $ticket = Ticket::factory()->create([
        'created_by'  => $owner->id,
        'status_id'   => $status->id,
        'priority_id' => $priority->id,
        'category_id' => $category->id,
    ]);

    $this->actingAs($other)->get(route('tickets.show', $ticket))
        ->assertForbidden();
});

test('ticket owner can view their own ticket', function () {
    $owner    = User::factory()->create()->assignRole('Cliente');
    $status   = Status::first();
    $priority = Priority::first();
    $category = Category::first();

    $ticket = Ticket::factory()->create([
        'created_by'  => $owner->id,
        'status_id'   => $status->id,
        'priority_id' => $priority->id,
        'category_id' => $category->id,
    ]);

    $this->actingAs($owner)->get(route('tickets.show', $ticket))
        ->assertOk();
});

// --- Creación de Tickets ---

test('ticket can be created with valid data via livewire', function () {
    $user     = User::factory()->create()->assignRole('Cliente');
    $status   = Status::first();
    $priority = Priority::first();
    $category = Category::first();

    $this->actingAs($user);

    Livewire\Livewire::test(App\Livewire\TicketCreate::class)
        ->set('title', 'Equipo no enciende correctamente')
        ->set('description', 'El equipo no arranca luego de presionar el botón de encendido.')
        ->set('priority_id', $priority->id)
        ->set('category_id', $category->id)
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
