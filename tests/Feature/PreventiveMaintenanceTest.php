<?php

use App\Models\User;
use App\Models\Equipment;
use App\Models\MaintenanceSchedule;
use App\Models\Ticket;
use App\Models\Status;
use App\Models\Priority;
use App\Models\Category;
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
        'next_run_at' => now()->subDay(), // Due yesterday
        'is_active' => true,
    ]);

    Artisan::call('app:generate-preventive-maintenance');

    $this->assertDatabaseHas('tickets', [
        'equipment_id' => $equipment->id,
        'maintenance_type' => 'Preventivo',
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
        'next_run_at' => now()->addDays(10), // Future
        'is_active' => true,
    ]);

    Artisan::call('app:generate-preventive-maintenance');

    $this->assertDatabaseMissing('tickets', [
        'equipment_id' => $equipment->id,
        'maintenance_type' => 'Preventivo',
    ]);
});
