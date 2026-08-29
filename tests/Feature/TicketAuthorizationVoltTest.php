<?php

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

function voltTicketShow(Ticket $ticket)
{
    return Livewire::test('tickets.show', ['ticket' => $ticket]);
}

// --- Cambio de estado: solo staff ---

test('un cliente no puede cambiar el estado de un ticket', function () {
    $client = User::factory()->create()->assignRole('Cliente');
    $ticket = Ticket::factory()->create(['created_by' => $client->id]);

    $this->actingAs($client);

    voltTicketShow($ticket)
        ->call('updateStatus', TicketStatus::Cerrado->value)
        ->assertForbidden();
});

test('un cliente no puede cambiar el estado de un ticket ajeno', function () {
    $owner = User::factory()->create()->assignRole('Cliente');
    $client = User::factory()->create()->assignRole('Cliente');
    $ticket = Ticket::factory()->create(['created_by' => $owner->id]);

    $this->actingAs($client);

    voltTicketShow($ticket)
        ->call('updateStatus', TicketStatus::Cerrado->value)
        ->assertForbidden();
});

test('un agente puede cambiar el estado de un ticket', function () {
    $agent = User::factory()->create()->assignRole('Agente');
    $client = User::factory()->create()->assignRole('Cliente');
    $ticket = Ticket::factory()->create(['created_by' => $client->id]);

    $this->actingAs($agent);

    voltTicketShow($ticket)
        ->call('updateStatus', TicketStatus::Cerrado->value)
        ->assertOk();

    expect($ticket->fresh()->status)->toBe(TicketStatus::Cerrado);
});

test('un estado invalido devuelve 422 y no un 500', function () {
    $agent = User::factory()->create()->assignRole('Agente');
    $ticket = Ticket::factory()->create(['created_by' => $agent->id]);

    $this->actingAs($agent);

    voltTicketShow($ticket)
        ->call('updateStatus', 'estado-inexistente')
        ->assertStatus(422);
});

// --- Asignación: solo staff ---

test('un cliente no puede asignar un ticket', function () {
    $client = User::factory()->create()->assignRole('Cliente');
    $agent = User::factory()->create()->assignRole('Agente');
    $ticket = Ticket::factory()->create(['created_by' => $client->id]);

    $this->actingAs($client);

    voltTicketShow($ticket)
        ->call('assignTo', $agent->id)
        ->assertForbidden();
});

test('un agente puede asignar un ticket', function () {
    $agent = User::factory()->create()->assignRole('Agente');
    $anotherAgent = User::factory()->create()->assignRole('Agente');
    $ticket = Ticket::factory()->create();

    $this->actingAs($agent);

    voltTicketShow($ticket)
        ->call('assignTo', $anotherAgent->id)
        ->assertOk();

    expect($ticket->fresh()->assigned_to)->toBe($anotherAgent->id);
});

test('asignar a un usuario inexistente devuelve 422', function () {
    $agent = User::factory()->create()->assignRole('Agente');
    $ticket = Ticket::factory()->create();

    $this->actingAs($agent);

    voltTicketShow($ticket)
        ->call('assignTo', 999999)
        ->assertStatus(422);
});

// --- Comentarios: requiere visibilidad ---

test('un cliente puede comentar su propio ticket', function () {
    $client = User::factory()->create()->assignRole('Cliente');
    $ticket = Ticket::factory()->create(['created_by' => $client->id]);

    $this->actingAs($client);

    voltTicketShow($ticket)
        ->set('newComment', 'Comentario de prueba para el propietario')
        ->call('addComment')
        ->assertOk();

    expect($ticket->fresh()->comments)->toHaveCount(1);
});

test('un cliente no puede comentar un ticket ajeno', function () {
    $owner = User::factory()->create()->assignRole('Cliente');
    $client = User::factory()->create()->assignRole('Cliente');
    $ticket = Ticket::factory()->create(['created_by' => $owner->id]);

    $this->actingAs($client);

    voltTicketShow($ticket)
        ->set('newComment', 'Intento de comentario ajeno')
        ->call('addComment')
        ->assertForbidden();
});
