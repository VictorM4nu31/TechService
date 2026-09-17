<?php

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Livewire\TicketCreate;
use App\Models\Equipment;
use App\Models\MaintenanceSchedule;
use App\Models\Ticket;
use App\Models\TicketDraft;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->seed(DatabaseSeeder::class);
});

test('ticket creation stores operational triage and an SLA deadline', function (): void {
    $client = User::factory()->create()->assignRole('Cliente');
    $this->actingAs($client);

    Livewire::test(TicketCreate::class)
        ->set('title', 'Servidor sin conectividad')
        ->set('description', 'El servidor no responde desde ninguna estación de trabajo.')
        ->set('priority', TicketPriority::Alta->value)
        ->set('category', TicketCategory::Emergencia->value)
        ->set('impact', 'Alto')
        ->set('urgency', 'Alto')
        ->call('save')
        ->assertRedirect(route('tickets.index'));

    $ticket = Ticket::query()->where('title', 'Servidor sin conectividad')->firstOrFail();

    expect($ticket->impact->value)->toBe('Alto')
        ->and($ticket->urgency->value)->toBe('Alto')
        ->and($ticket->sla_due_at)->not->toBeNull();
});

test('ticket drafts can be saved and restored for the same user', function (): void {
    $client = User::factory()->create()->assignRole('Cliente');
    $this->actingAs($client);

    Livewire::test(TicketCreate::class)
        ->set('title', 'Borrador de diagnóstico')
        ->set('description', 'Pendiente de adjuntar evidencia del error.')
        ->call('saveDraft');

    expect(TicketDraft::query()->where('user_id', $client->id)->exists())->toBeTrue();

    Livewire::test(TicketCreate::class)
        ->assertSet('title', 'Borrador de diagnóstico')
        ->assertSet('description', 'Pendiente de adjuntar evidencia del error.');
});

test('a saved ticket view preserves filters for the owner', function (): void {
    $admin = User::factory()->create()->assignRole('Admin');

    $this->actingAs($admin)->post(route('ticket-views.store'), [
        'name' => 'Emergencias abiertas',
        'filters' => [
            'status' => 'Abierto',
            'category' => 'Emergencia',
        ],
    ])->assertRedirect(route('tickets.index', [
        'status' => 'Abierto',
        'category' => 'Emergencia',
    ]));

    $this->assertDatabaseHas('saved_views', [
        'user_id' => $admin->id,
        'name' => 'Emergencias abiertas',
    ]);
});

test('ticket activity creates an internal database notification', function (): void {
    $client = User::factory()->create()->assignRole('Cliente');
    $this->actingAs($client);

    Livewire::test(TicketCreate::class)
        ->set('title', 'Notificación de prueba')
        ->set('description', 'Se debe informar al equipo técnico automáticamente.')
        ->call('save');

    $this->assertDatabaseHas('notifications', [
        'type' => 'App\\Notifications\\TicketActivityNotification',
    ]);
});

test('maintenance schedules persist checklist steps', function (): void {
    $agent = User::factory()->create()->assignRole('Agente');
    $equipment = Equipment::factory()->create(['user_id' => $agent->id]);

    $this->actingAs($agent)->post(route('maintenance-schedules.store'), [
        'equipment_id' => $equipment->id,
        'name' => 'Revisión trimestral',
        'frequency_days' => 90,
        'checklist' => "Revisar ventiladores\nValidar respaldos",
        'is_active' => true,
    ])->assertRedirect(route('maintenance-schedules.index'));

    $schedule = MaintenanceSchedule::query()->where('name', 'Revisión trimestral')->firstOrFail();

    expect($schedule->checklist)->toBe(['Revisar ventiladores', 'Validar respaldos']);
});
