<?php

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;

test('TicketStatus enum has correct values', function () {
    expect(TicketStatus::Abierto->value)->toBe('Abierto');
    expect(TicketStatus::EnProgreso->value)->toBe('En Progreso');
    expect(TicketStatus::Cerrado->value)->toBe('Cerrado');
});

test('TicketStatus enum has correct colors', function () {
    expect(TicketStatus::Abierto->color())->toBe('blue');
    expect(TicketStatus::EnProgreso->color())->toBe('yellow');
    expect(TicketStatus::Cerrado->color())->toBe('green');
});

test('TicketPriority enum has correct values', function () {
    expect(TicketPriority::Baja->value)->toBe('Baja');
    expect(TicketPriority::Media->value)->toBe('Media');
    expect(TicketPriority::Alta->value)->toBe('Alta');
});

test('TicketPriority enum has correct colors', function () {
    expect(TicketPriority::Baja->color())->toBe('green');
    expect(TicketPriority::Media->color())->toBe('yellow');
    expect(TicketPriority::Alta->color())->toBe('red');
});

test('TicketCategory enum has correct values', function () {
    expect(TicketCategory::Preventivo->value)->toBe('Preventivo');
    expect(TicketCategory::Correctivo->value)->toBe('Correctivo');
    expect(TicketCategory::Emergencia->value)->toBe('Emergencia');
});

test('TicketCategory enum has correct icons', function () {
    expect(TicketCategory::Preventivo->icon())->toBe('shield-check');
    expect(TicketCategory::Correctivo->icon())->toBe('wrench-screwdriver');
    expect(TicketCategory::Emergencia->icon())->toBe('exclamation-triangle');
});

test('TicketStatus can be created from string', function () {
    $status = TicketStatus::from('Abierto');
    expect($status)->toBe(TicketStatus::Abierto);
});

test('TicketPriority can be created from string', function () {
    $priority = TicketPriority::from('Alta');
    expect($priority)->toBe(TicketPriority::Alta);
});

test('TicketCategory can be created from string', function () {
    $category = TicketCategory::from('Emergencia');
    expect($category)->toBe(TicketCategory::Emergencia);
});
