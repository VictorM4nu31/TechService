<?php

use App\Models\Equipment;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('un cliente no puede ver el listado de equipos tecnicos (teams)', function () {
    $client = User::factory()->create()->assignRole('Cliente');

    $this->actingAs($client)->get(route('teams.index'))->assertForbidden();
});

test('un agente puede ver el listado de equipos tecnicos (teams)', function () {
    $agent = User::factory()->create()->assignRole('Agente');

    $this->actingAs($agent)->get(route('teams.index'))->assertOk();
});

test('un admin puede ver el listado de equipos tecnicos (teams)', function () {
    $admin = User::factory()->create()->assignRole('Admin');

    $this->actingAs($admin)->get(route('teams.index'))->assertOk();
});

test('un agente puede ver el inventario completo de equipos', function () {
    $owner = User::factory()->create()->assignRole('Cliente');
    $agent = User::factory()->create()->assignRole('Agente');
    $equipment = Equipment::factory()->create(['user_id' => $owner->id, 'name' => 'Equipo QA Agente']);

    $this->assertTrue($agent->can('view', $equipment));

    $this->actingAs($agent)->get(route('equipment.show', $equipment))->assertOk();
    $this->actingAs($agent)->get(route('equipment.index'))->assertOk();
});

test('un cliente solo ve sus propios equipos en el indice', function () {
    $owner = User::factory()->create()->assignRole('Cliente');
    $other = User::factory()->create()->assignRole('Cliente');

    Equipment::factory()->create(['user_id' => $owner->id, 'name' => 'Equipo Propio QA']);
    Equipment::factory()->create(['user_id' => $other->id, 'name' => 'Equipo Ajeno QA']);

    $this->actingAs($owner)->get(route('equipment.index'))
        ->assertSee('Equipo Propio QA')
        ->assertDontSee('Equipo Ajeno QA');
});

test('maintenance store exige autorizacion de staff aunque el form request tambien la valide', function () {
    $client = User::factory()->create()->assignRole('Cliente');
    $equipment = Equipment::factory()->create();

    $this->actingAs($client)->post(route('maintenance-schedules.store'), [
        'equipment_id' => $equipment->id,
        'name' => 'Tarea QA',
        'frequency_days' => 30,
    ])->assertForbidden();
});
