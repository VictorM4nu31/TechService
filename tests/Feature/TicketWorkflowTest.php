<?php

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Equipment;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

// --- Edición de tickets (C1) ---

test('guardar la edición de un ticket no lanza error de enum', function () {
    $client = User::factory()->create()->assignRole('Cliente');
    $admin = User::factory()->create()->assignRole('Admin');
    $ticket = Ticket::factory()->create([
        'created_by' => $client->id,
        'priority' => 'Alta',
        'category' => 'Correctivo',
    ]);

    $this->actingAs($admin);

    Livewire::test('tickets.edit', ['ticket' => $ticket->fresh()])
        ->assertSet('priority', 'Alta')
        ->set('title', 'Título actualizado correctamente')
        ->set('priority', 'Baja')
        ->set('category', 'Preventivo')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('tickets.show', $ticket->fresh()));

    expect($ticket->fresh()->priority)->toBe(TicketPriority::Baja);
    expect($ticket->fresh()->title)->toBe('Título actualizado correctamente');
});

test('la edición de un ticket valida los campos con mensajes en español', function () {
    $client = User::factory()->create()->assignRole('Cliente');
    $admin = User::factory()->create()->assignRole('Admin');
    $ticket = Ticket::factory()->create([
        'created_by' => $client->id,
        'status' => TicketStatus::Abierto,
    ]);

    $this->actingAs($admin);

    Livewire::test('tickets.edit', ['ticket' => $ticket->fresh()])
        ->set('title', 'abc')
        ->call('save')
        ->assertHasErrors(['title']);
});

test('un cliente no puede asociar el equipo de otro cliente al editar', function () {
    $client = User::factory()->create()->assignRole('Cliente');
    $otherClient = User::factory()->create()->assignRole('Cliente');
    $foreignEquipment = Equipment::factory()->create(['user_id' => $otherClient->id]);
    $ticket = Ticket::factory()->create([
        'created_by' => $client->id,
        'status' => TicketStatus::Abierto,
    ]);

    $this->actingAs($client);

    Livewire::test('tickets.edit', ['ticket' => $ticket->fresh()])
        ->set('equipment_id', $foreignEquipment->id)
        ->call('save')
        ->assertHasErrors(['equipment_id']);
});

// --- Equipos visibles al crear tickets (C2) ---

test('un admin puede ver equipos de todos los clientes al crear un ticket', function () {
    $admin = User::factory()->create()->assignRole('Admin');
    $client = User::factory()->create()->assignRole('Cliente');
    Equipment::factory()->create(['user_id' => $client->id, 'name' => 'Servidor Dell Especial']);

    $this->actingAs($admin)
        ->get(route('tickets.create'))
        ->assertOk()
        ->assertSee('Servidor Dell Especial');
});

test('un cliente solo ve sus propios equipos al crear un ticket', function () {
    $client = User::factory()->create()->assignRole('Cliente');
    $otherClient = User::factory()->create()->assignRole('Cliente');
    Equipment::factory()->create(['user_id' => $client->id, 'name' => 'Impresora Propia']);
    Equipment::factory()->create(['user_id' => $otherClient->id, 'name' => 'Impresora Ajena']);

    $this->actingAs($client)
        ->get(route('tickets.create'))
        ->assertOk()
        ->assertSee('Impresora Propia')
        ->assertDontSee('Impresora Ajena');
});

test('un cliente no puede asociar el equipo de otro cliente al crear', function () {
    $client = User::factory()->create()->assignRole('Cliente');
    $otherClient = User::factory()->create()->assignRole('Cliente');
    $foreignEquipment = Equipment::factory()->create(['user_id' => $otherClient->id]);

    $this->actingAs($client);

    Livewire::test(\App\Livewire\TicketCreate::class)
        ->set('equipment_id', $foreignEquipment->id)
        ->set('title', 'Problema con equipo externo')
        ->set('description', 'Descripción suficientemente larga para validar el formulario.')
        ->call('save')
        ->assertHasErrors(['equipment_id']);
});

test('el equipo se preselecciona al abrir el formulario con el parámetro equipment', function () {
    $client = User::factory()->create()->assignRole('Cliente');
    $equipment = Equipment::factory()->create(['user_id' => $client->id]);

    $this->actingAs($client);

    Livewire::test(\App\Livewire\TicketCreate::class, ['equipment' => $equipment->id])
        ->assertSet('equipment_id', $equipment->id);
});

// --- Filtros del listado de tickets ---

test('el listado de tickets filtra por estado, categoría y búsqueda', function () {
    $admin = User::factory()->create()->assignRole('Admin');
    $client = User::factory()->create()->assignRole('Cliente');
    $open = Ticket::factory()->create([
        'created_by' => $client->id,
        'title' => 'Impresora rota en sala',
        'status' => 'Abierto',
        'category' => 'Correctivo',
    ]);
    $closed = Ticket::factory()->create([
        'created_by' => $client->id,
        'title' => 'Problema de red crítico',
        'status' => 'Cerrado',
        'category' => 'Emergencia',
    ]);

    $this->actingAs($admin)
        ->get(route('tickets.index', ['status' => 'Abierto']))
        ->assertOk()
        ->assertSee($open->title)
        ->assertDontSee($closed->title);

    $this->get(route('tickets.index', ['category' => 'Emergencia']))
        ->assertOk()
        ->assertSee($closed->title)
        ->assertDontSee($open->title);

    $this->get(route('tickets.index', ['search' => 'Impresora']))
        ->assertOk()
        ->assertSee($open->title)
        ->assertDontSee($closed->title)
        ->assertSee('Resultados para');
});

test('el listado de tickets muestra estado limpio sin filtros', function () {
    $admin = User::factory()->create()->assignRole('Admin');

    $this->actingAs($admin)
        ->get(route('tickets.index'))
        ->assertOk()
        ->assertSee('Todos los Tickets');
});

// --- Dashboard sin duplicados ---

test('los tickets críticos no se duplican en la lista de recientes del dashboard', function () {
    $admin = User::factory()->create()->assignRole('Admin');
    $client = User::factory()->create()->assignRole('Cliente');
    Ticket::factory()->create([
        'created_by' => $client->id,
        'title' => 'Fallo critico unico en backup',
        'priority' => 'Alta',
        'status' => 'Abierto',
    ]);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertOk();
    expect(substr_count((string) $response->getContent(), 'Fallo critico unico en backup'))->toBe(1);
});

// --- Comentarios ---

test('los comentarios requieren contenido minimo con mensaje en español', function () {
    $client = User::factory()->create()->assignRole('Cliente');
    $ticket = Ticket::factory()->create(['created_by' => $client->id]);

    $this->actingAs($client);

    Livewire::test('tickets.show', ['ticket' => $ticket->fresh()])
        ->set('newComment', 'ok')
        ->call('addComment')
        ->assertHasErrors(['newComment']);
});
