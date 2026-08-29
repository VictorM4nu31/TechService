<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEquipmentRequest;
use App\Http\Requests\UpdateEquipmentRequest;
use App\Models\Equipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EquipmentController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $equipment = Equipment::query()
            ->when(! $user->hasRole('Admin'), fn ($q) => $q->where('user_id', $user->id))
            ->withCount('tickets')
            ->with('owner')
            ->latest()
            ->paginate(15);

        return view('equipment.index', compact('equipment'));
    }

    public function create(): View
    {
        return view('equipment.create');
    }

    public function store(StoreEquipmentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        Equipment::create($validated);

        session()->flash('status', __('Equipo registrado con éxito.'));

        return redirect()->route('equipment.index');
    }

    public function show(Equipment $equipment): View
    {
        $this->authorize('view', $equipment);
        $equipment->load(['tickets.status', 'tickets.priority', 'owner', 'client', 'tickets.category']);

        return view('equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment): View
    {
        $this->authorize('update', $equipment);

        return view('equipment.edit', compact('equipment'));
    }

    public function update(UpdateEquipmentRequest $request, Equipment $equipment): RedirectResponse
    {
        $this->authorize('update', $equipment);

        $equipment->update($request->validated());

        session()->flash('status', __('Equipo actualizado con éxito.'));

        return redirect()->route('equipment.show', $equipment);
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        $this->authorize('delete', $equipment);
        $equipment->delete();

        session()->flash('status', __('Equipo eliminado.'));

        return redirect()->route('equipment.index');
    }
}
