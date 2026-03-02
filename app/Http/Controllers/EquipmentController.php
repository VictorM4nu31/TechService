<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $equipment = Equipment::query()
            ->when(!$user->hasRole('Admin'), fn($q) => $q->where('user_id', $user->id))
            ->with('owner')
            ->latest()
            ->paginate(15);

        return view('equipment.index', compact('equipment'));
    }

    public function create()
    {
        return view('equipment.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'brand'         => 'nullable|string|max:100',
            'model'         => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:150',
            'type'          => 'required|string|in:Computadora,Impresora,Red,Servidor,Teléfono,Otro',
            'client_id'     => 'nullable|exists:clients,id',
        ]);

        $validated['user_id'] = Auth::id();

        Equipment::create($validated);

        session()->flash('status', __('Equipo registrado con éxito.'));
        return redirect()->route('equipment.index');
    }

    public function show(Equipment $equipment)
    {
        $this->authorizeAccess($equipment);
        $equipment->load(['tickets.status', 'tickets.priority', 'owner', 'client', 'tickets.category']);
        return view('equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment)
    {
        $this->authorizeAccess($equipment);
        return view('equipment.edit', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $this->authorizeAccess($equipment);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'brand'         => 'nullable|string|max:100',
            'model'         => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:150',
            'type'          => 'required|string|in:Computadora,Impresora,Red,Servidor,Teléfono,Otro',
        ]);

        $equipment->update($validated);

        session()->flash('status', __('Equipo actualizado con éxito.'));
        return redirect()->route('equipment.show', $equipment);
    }

    public function destroy(Equipment $equipment)
    {
        $this->authorizeAccess($equipment);
        $equipment->delete();

        session()->flash('status', __('Equipo eliminado.'));
        return redirect()->route('equipment.index');
    }

    private function authorizeAccess(Equipment $equipment): void
    {
        $user = Auth::user();
        if (!$user->hasRole('Admin') && $equipment->user_id !== $user->id) {
            abort(403, __('No tienes permiso para acceder a este equipo.'));
        }
    }
}
