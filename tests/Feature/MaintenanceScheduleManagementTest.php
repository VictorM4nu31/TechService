<?php

use App\Models\Equipment;
use App\Models\MaintenanceSchedule;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('un cliente no puede acceder al listado de programaciones', function () {
    $client = User::factory()->create()->assignRole('Cliente');
    $this->actingAs($client);

    $this->get(route('maintenance-schedules.index'))->assertForbidden();
});

test('un agente puede ver el listado de programaciones', function () {
    $agent = User::factory()->create()->assignRole('Agente');
    $this->actingAs($agent);

    MaintenanceSchedule::factory()->create();

    $this->get(route('maintenance-schedules.index'))->assertOk();
});

test('un agente puede crear una programación', function () {
    $agent = User::factory()->create()->assignRole('Agente');
    $equipment = Equipment::factory()->create(['user_id' => $agent->id]);

    $this->actingAs($agent)->post(route('maintenance-schedules.store'), [
        'equipment_id' => $equipment->id,
        'name' => 'Cambio de pasta térmica',
        'frequency_days' => 90,
        'is_active' => true,
    ])->assertRedirect(route('maintenance-schedules.index'));

    $this->assertDatabaseHas('maintenance_schedules', [
        'equipment_id' => $equipment->id,
        'name' => 'Cambio de pasta térmica',
    ]);
});

test('la validación exige nombre y frecuencia', function () {
    $agent = User::factory()->create()->assignRole('Agente');
    $equipment = Equipment::factory()->create(['user_id' => $agent->id]);

    $this->actingAs($agent)->post(route('maintenance-schedules.store'), [
        'equipment_id' => $equipment->id,
    ])->assertSessionHasErrors(['name', 'frequency_days']);
});

test('un agente puede actualizar una programación', function () {
    $agent = User::factory()->create()->assignRole('Agente');
    $schedule = MaintenanceSchedule::factory()->create();

    $this->actingAs($agent)->put(route('maintenance-schedules.update', $schedule), [
        'equipment_id' => $schedule->equipment_id,
        'name' => 'Revisión de discos',
        'frequency_days' => 180,
        'is_active' => false,
    ])->assertRedirect(route('maintenance-schedules.index'));

    $this->assertDatabaseHas('maintenance_schedules', [
        'id' => $schedule->id,
        'name' => 'Revisión de discos',
        'is_active' => false,
    ]);
});

test('un agente puede eliminar una programación', function () {
    $agent = User::factory()->create()->assignRole('Agente');
    $schedule = MaintenanceSchedule::factory()->create();

    $this->actingAs($agent)->delete(route('maintenance-schedules.destroy', $schedule))
        ->assertRedirect(route('maintenance-schedules.index'));

    $this->assertDatabaseMissing('maintenance_schedules', ['id' => $schedule->id]);
});
