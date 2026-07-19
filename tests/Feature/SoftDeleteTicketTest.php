<?php

use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('ticket is soft deleted', function () {
    $admin = User::factory()->create()->assignRole('Admin');
    $ticket = Ticket::factory()->create();

    $this->actingAs($admin)->delete(route('tickets.destroy', $ticket));

    $this->assertSoftDeleted('tickets', ['id' => $ticket->id]);
});

test('soft deleted ticket is not visible in index', function () {
    $user = User::factory()->create()->assignRole('Cliente');
    $ticket = Ticket::factory()->create(['created_by' => $user->id]);

    $ticket->delete();

    $this->actingAs($user)->get(route('tickets.index'))
        ->assertDontSee($ticket->title);
});

test('soft deleted ticket can be restored by admin', function () {
    $admin = User::factory()->create()->assignRole('Admin');
    $ticket = Ticket::factory()->create();

    $ticket->delete();
    $this->assertSoftDeleted('tickets', ['id' => $ticket->id]);

    $ticket->restore();
    $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'deleted_at' => null]);
});

test('ticket can be force deleted by admin', function () {
    $admin = User::factory()->create()->assignRole('Admin');
    $ticket = Ticket::factory()->create();

    $ticket->forceDelete();

    $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
});
