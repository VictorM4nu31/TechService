<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::withCount('equipment')
            ->latest()
            ->paginate(15);

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'address'       => 'nullable|string|max:500',
            'tax_id'        => 'nullable|string|max:50|unique:clients,tax_id',
        ]);

        Client::create($validated);

        session()->flash('status', __('Cliente registrado con éxito.'));
        return redirect()->route('clients.index');
    }

    public function show(Client $client)
    {
        $client->load(['equipment.owner', 'equipment.tickets']);
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'address'       => 'nullable|string|max:500',
            'tax_id'        => 'nullable|string|max:50|unique:clients,tax_id,' . $client->id,
        ]);

        $client->update($validated);

        session()->flash('status', __('Cliente actualizado con éxito.'));
        return redirect()->route('clients.show', $client);
    }

    public function destroy(Client $client)
    {
        $client->delete();

        session()->flash('status', __('Cliente eliminado.'));
        return redirect()->route('clients.index');
    }
}
