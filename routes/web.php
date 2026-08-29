<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('tickets', [\App\Http\Controllers\TicketController::class, 'index'])->name('tickets.index');
    Route::get('tickets/create', [\App\Http\Controllers\TicketController::class, 'create'])->name('tickets.create');
    Route::get('tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'show'])->name('tickets.show');
    Route::get('tickets/{ticket}/edit', [\App\Http\Controllers\TicketController::class, 'edit'])->name('tickets.edit');
    Route::delete('tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'destroy'])->name('tickets.destroy');

    Route::get('teams', [\App\Http\Controllers\TeamController::class, 'index'])->name('teams.index');

    Route::resource('equipment', \App\Http\Controllers\EquipmentController::class);
    Route::resource('clients', \App\Http\Controllers\ClientController::class);
    Route::resource('maintenance-schedules', \App\Http\Controllers\MaintenanceScheduleController::class)->except(['show']);
    Route::get('calendar', function () {
        return view('calendar');
    })->name('calendar');
});

require __DIR__.'/settings.php';
