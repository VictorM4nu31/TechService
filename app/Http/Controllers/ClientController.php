<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = Client::withCount('equipment')
            ->latest()
            ->paginate(15);

        return view('clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        Client::create($request->validated());

        session()->flash('status', __('Cliente registrado con éxito.'));

        return redirect()->route('clients.index');
    }

    public function show(Client $client): View
    {
        $client->load(['equipment.owner', 'equipment.tickets']);

        return view('clients.show', compact('client'));
    }

    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());

        session()->flash('status', __('Cliente actualizado con éxito.'));

        return redirect()->route('clients.show', $client);
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        session()->flash('status', __('Cliente eliminado.'));

        return redirect()->route('clients.index');
    }
}
