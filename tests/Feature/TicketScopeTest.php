<?php

use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('un cliente solo puede ver sus propios tickets', function () {
    $client = User::factory()->create()->assignRole('Cliente');

    $mine = Ticket::factory()->create(['created_by' => $client->id]);
    $mine2 = Ticket::factory()->create(['created_by' => $client->id]);
    $other = Ticket::factory()->create();

    $visible = Ticket::query()->visibleTo($client)->get();

    expect($visible->pluck('id')->all())
        ->toContain($mine->id, $mine2->id)
        ->not->toContain($other->id);
});

test('un admin puede ver todos los tickets', function () {
    $admin = User::factory()->create()->assignRole('Admin');
    $client = User::factory()->create()->assignRole('Cliente');

    $fromClient = Ticket::factory()->create(['created_by' => $client->id]);
    $fromAdmin = Ticket::factory()->create(['created_by' => $admin->id]);

    $visible = Ticket::query()->visibleTo($admin)->get();

    expect($visible->pluck('id')->all())
        ->toContain($fromClient->id, $fromAdmin->id);
});

test('un agente puede ver todos los tickets', function () {
    $agent = User::factory()->create()->assignRole('Agente');
    $client = User::factory()->create()->assignRole('Cliente');

    $ticketA = Ticket::factory()->create(['created_by' => $client->id]);
    $ticketB = Ticket::factory()->create(['created_by' => $agent->id]);

    $visible = Ticket::query()->visibleTo($agent)->get();

    expect($visible->pluck('id')->all())
        ->toContain($ticketA->id, $ticketB->id);
});
