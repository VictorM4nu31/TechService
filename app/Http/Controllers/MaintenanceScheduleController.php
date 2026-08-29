<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenanceScheduleRequest;
use App\Http\Requests\UpdateMaintenanceScheduleRequest;
use App\Models\Equipment;
use App\Models\MaintenanceSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MaintenanceScheduleController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', MaintenanceSchedule::class);

        $schedules = MaintenanceSchedule::with(['equipment'])
            ->latest()
            ->paginate(15);

        return view('maintenance-schedules.index', compact('schedules'));
    }

    public function create(): View
    {
        $this->authorize('create', MaintenanceSchedule::class);

        $equipments = Equipment::with('owner')->get();

        return view('maintenance-schedules.create', compact('equipments'));
    }

    public function store(StoreMaintenanceScheduleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        MaintenanceSchedule::create($data);

        session()->flash('status', __('Programación creada con éxito.'));

        return redirect()->route('maintenance-schedules.index');
    }

    public function edit(MaintenanceSchedule $maintenanceSchedule): View
    {
        $this->authorize('update', $maintenanceSchedule);

        $equipments = Equipment::with('owner')->get();

        return view('maintenance-schedules.edit', compact('maintenanceSchedule', 'equipments'));
    }

    public function update(UpdateMaintenanceScheduleRequest $request, MaintenanceSchedule $maintenanceSchedule): RedirectResponse
    {
        $this->authorize('update', $maintenanceSchedule);

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $maintenanceSchedule->update($data);

        session()->flash('status', __('Programación actualizada con éxito.'));

        return redirect()->route('maintenance-schedules.index');
    }

    public function destroy(MaintenanceSchedule $maintenanceSchedule): RedirectResponse
    {
        $this->authorize('delete', $maintenanceSchedule);

        $maintenanceSchedule->delete();

        session()->flash('status', __('Programación eliminada.'));

        return redirect()->route('maintenance-schedules.index');
    }
}
