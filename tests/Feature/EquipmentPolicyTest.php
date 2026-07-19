<?php

use App\Models\Equipment;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('admin can view any equipment', function () {
    $admin = User::factory()->create()->assignRole('Admin');
    $user = User::factory()->create()->assignRole('Cliente');
    $equipment = Equipment::factory()->create(['user_id' => $user->id]);

    $this->assertTrue($admin->can('view', $equipment));
});

test('user can view their own equipment', function () {
    $user = User::factory()->create()->assignRole('Cliente');
    $equipment = Equipment::factory()->create(['user_id' => $user->id]);

    $this->assertTrue($user->can('view', $equipment));
});

test('user cannot view equipment belonging to another user', function () {
    $owner = User::factory()->create()->assignRole('Cliente');
    $other = User::factory()->create()->assignRole('Cliente');
    $equipment = Equipment::factory()->create(['user_id' => $owner->id]);

    $this->assertFalse($other->can('view', $equipment));
});

test('user can update their own equipment', function () {
    $user = User::factory()->create()->assignRole('Cliente');
    $equipment = Equipment::factory()->create(['user_id' => $user->id]);

    $this->assertTrue($user->can('update', $equipment));
});

test('user cannot update equipment belonging to another user', function () {
    $owner = User::factory()->create()->assignRole('Cliente');
    $other = User::factory()->create()->assignRole('Cliente');
    $equipment = Equipment::factory()->create(['user_id' => $owner->id]);

    $this->assertFalse($other->can('update', $equipment));
});

test('user can delete their own equipment', function () {
    $user = User::factory()->create()->assignRole('Cliente');
    $equipment = Equipment::factory()->create(['user_id' => $user->id]);

    $this->assertTrue($user->can('delete', $equipment));
});

test('user cannot delete equipment belonging to another user', function () {
    $owner = User::factory()->create()->assignRole('Cliente');
    $other = User::factory()->create()->assignRole('Cliente');
    $equipment = Equipment::factory()->create(['user_id' => $owner->id]);

    $this->assertFalse($other->can('delete', $equipment));
});

test('admin can update any equipment', function () {
    $admin = User::factory()->create()->assignRole('Admin');
    $user = User::factory()->create()->assignRole('Cliente');
    $equipment = Equipment::factory()->create(['user_id' => $user->id]);

    $this->assertTrue($admin->can('update', $equipment));
});

test('admin can delete any equipment', function () {
    $admin = User::factory()->create()->assignRole('Admin');
    $user = User::factory()->create()->assignRole('Cliente');
    $equipment = Equipment::factory()->create(['user_id' => $user->id]);

    $this->assertTrue($admin->can('delete', $equipment));
});
