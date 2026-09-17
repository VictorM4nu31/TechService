<?php

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Equipment;
use App\Models\MaintenanceSchedule;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('it generates preventive work orders for due schedules', function () {
    $user = User::factory()->create();
    $equipment = Equipment::factory()->create(['user_id' => $user->id]);

    $schedule = MaintenanceSchedule::create([
        'equipment_id' => $equipment->id,
        'name' => 'Limpieza Mensual',
        'frequency_days' => 30,
        'next_run_at' => now()->subDay(),
        'is_active' => true,
    ]);

    Artisan::call('app:generate-preventive-maintenance');

    $this->assertDatabaseHas('tickets', [
        'equipment_id' => $equipment->id,
        'maintenance_type' => 'Preventivo',
        'status' => TicketStatus::Abierto->value,
        'priority' => TicketPriority::Media->value,
        'category' => TicketCategory::Preventivo->value,
    ]);

    $schedule->refresh();
    $this->assertNotNull($schedule->last_run_at);
    $this->assertEquals(
        now()->addDays(30)->format('Y-m-d'),
        $schedule->next_run_at->format('Y-m-d')
    );
});

test('it does not generate work orders for future schedules', function () {
    $user = User::factory()->create();
    $equipment = Equipment::factory()->create(['user_id' => $user->id]);

    MaintenanceSchedule::create([
        'equipment_id' => $equipment->id,
        'name' => 'Revisión Anual',
        'frequency_days' => 365,
        'next_run_at' => now()->addDays(10),
        'is_active' => true,
    ]);

    Artisan::call('app:generate-preventive-maintenance');

    $this->assertDatabaseMissing('tickets', [
        'equipment_id' => $equipment->id,
        'maintenance_type' => 'Preventivo',
    ]);
});

test('el calendario renderiza la leyenda de mantenimiento', function () {
    $user = User::factory()->create()->assignRole('Cliente');
    $this->actingAs($user);

    Livewire\Livewire::test(\App\Livewire\MaintenanceCalendar::class)
        ->assertOk()
        ->assertSee('Preventivo')
        ->assertSee('Emergencia');
});

test('el calendario muestra rutinas preventivas a los agentes', function () {
    $agent = User::factory()->create()->assignRole('Agente');
    $equipment = Equipment::factory()->create(['user_id' => $agent->id]);

    MaintenanceSchedule::factory()->create([
        'equipment_id' => $equipment->id,
        'name' => 'Revisión de almacenamiento',
        'next_run_at' => now()->addDay(),
        'is_active' => true,
    ]);

    $this->actingAs($agent);

    Livewire\Livewire::test(\App\Livewire\MaintenanceCalendar::class)
        ->assertSee('Revisión de almacenamiento');
});

test('el boton hoy vuelve al mes y anio actuales', function () {
    $user = User::factory()->create()->assignRole('Cliente');
    $this->actingAs($user);

    Livewire\Livewire::test(\App\Livewire\MaintenanceCalendar::class)
        ->call('nextMonth')
        ->call('goToToday')
        ->assertSet('month', now()->month)
        ->assertSet('year', now()->year);
});
