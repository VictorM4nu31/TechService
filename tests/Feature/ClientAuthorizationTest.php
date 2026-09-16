<?php

use App\Models\Client;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

// --- Acceso denegado a Clientes ---

test('un cliente no puede ver el listado de clientes', function () {
    $user = User::factory()->create()->assignRole('Cliente');
    $this->actingAs($user)->get(route('clients.index'))->assertForbidden();
});

test('un cliente no puede ver el detalle de un cliente', function () {
    $user = User::factory()->create()->assignRole('Cliente');
    $client = Client::factory()->create();
    $this->actingAs($user)->get(route('clients.show', $client))->assertForbidden();
});

test('un cliente no puede crear un cliente', function () {
    $user = User::factory()->create()->assignRole('Cliente');
    $this->actingAs($user)->get(route('clients.create'))->assertForbidden();
});

test('un cliente no puede almacenar un cliente', function () {
    $user = User::factory()->create()->assignRole('Cliente');
    $this->actingAs($user)->post(route('clients.store'), [
        'name' => 'Empresa Test',
    ])->assertForbidden();
});

test('un cliente no puede editar un cliente', function () {
    $user = User::factory()->create()->assignRole('Cliente');
    $client = Client::factory()->create();
    $this->actingAs($user)->get(route('clients.edit', $client))->assertForbidden();
});

test('un cliente no puede eliminar un cliente', function () {
    $user = User::factory()->create()->assignRole('Cliente');
    $client = Client::factory()->create();
    $this->actingAs($user)->delete(route('clients.destroy', $client))->assertForbidden();
});

// --- Acceso permitido a Agente ---

test('un agente puede ver el listado de clientes', function () {
    $agent = User::factory()->create()->assignRole('Agente');
    $this->actingAs($agent)->get(route('clients.index'))->assertOk();
});

test('un agente puede crear y editar un cliente', function () {
    $agent = User::factory()->create()->assignRole('Agente');
    $client = Client::factory()->create();

    $this->actingAs($agent)->get(route('clients.create'))->assertOk();
    $this->actingAs($agent)->get(route('clients.edit', $client))->assertOk();
});

test('un agente no puede eliminar un cliente', function () {
    $agent = User::factory()->create()->assignRole('Agente');
    $client = Client::factory()->create();
    $this->actingAs($agent)->delete(route('clients.destroy', $client))->assertForbidden();
});

// --- Acceso permitido a Admin ---

test('un admin puede ver, crear, editar y eliminar clientes', function () {
    $admin = User::factory()->create()->assignRole('Admin');
    $client = Client::factory()->create();

    $this->actingAs($admin)->get(route('clients.index'))->assertOk();
    $this->actingAs($admin)->get(route('clients.create'))->assertOk();
    $this->actingAs($admin)->get(route('clients.show', $client))->assertOk();
    $this->actingAs($admin)->get(route('clients.edit', $client))->assertOk();
});

test('un admin puede eliminar un cliente', function () {
    $admin = User::factory()->create()->assignRole('Admin');
    $client = Client::factory()->create();

    $this->actingAs($admin)
        ->delete(route('clients.destroy', $client))
        ->assertRedirect(route('clients.index'));

    $this->assertDatabaseMissing('clients', ['id' => $client->id]);
});
