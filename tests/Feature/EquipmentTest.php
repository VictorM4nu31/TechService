<?php

use App\Models\User;
use App\Models\Equipment;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('user can create their own equipment', function () {
    $user = User::factory()->create()->assignRole('Cliente');

    $this->actingAs($user)->post(route('equipment.store'), [
        'name'   => 'Mi Laptop HP',
        'type'   => 'Computadora',
        'brand'  => 'HP',
        'model'  => 'ProBook 440',
    ])->assertRedirect(route('equipment.index'));

    $this->assertDatabaseHas('equipment', ['name' => 'Mi Laptop HP', 'user_id' => $user->id]);
});

test('user cannot edit equipment belonging to another user', function () {
    $owner = User::factory()->create()->assignRole('Cliente');
    $other = User::factory()->create()->assignRole('Cliente');

    $equipment = Equipment::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($other)
        ->get(route('equipment.edit', $equipment))
        ->assertForbidden();
});

test('user cannot delete equipment belonging to another user', function () {
    $owner = User::factory()->create()->assignRole('Cliente');
    $other = User::factory()->create()->assignRole('Cliente');

    $equipment = Equipment::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($other)
        ->delete(route('equipment.destroy', $equipment))
        ->assertForbidden();
});

test('admin can view equipment from any user', function () {
    $admin = User::factory()->create()->assignRole('Admin');
    $user  = User::factory()->create()->assignRole('Cliente');

    $equipment = Equipment::factory()->create(['user_id' => $user->id]);

    $this->actingAs($admin)
        ->get(route('equipment.show', $equipment))
        ->assertOk();
});

test('user only sees their own equipment in index', function () {
    $owner = User::factory()->create()->assignRole('Cliente');
    $other = User::factory()->create()->assignRole('Cliente');

    $mine   = Equipment::factory()->create(['user_id' => $owner->id, 'name' => 'Equipo Propio']);
    $theirs = Equipment::factory()->create(['user_id' => $other->id, 'name' => 'Equipo Ajeno']);

    $this->actingAs($owner)->get(route('equipment.index'))
        ->assertSee('Equipo Propio')
        ->assertDontSee('Equipo Ajeno');
});
