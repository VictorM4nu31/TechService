<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSavedViewRequest;
use App\Models\SavedView;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = Ticket::query()
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->category, fn ($q) => $q->where('category', $request->category))
            ->when($request->search, function ($query, string $search): void {
                $query->where(function ($searchQuery) use ($search): void {
                    $searchQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('id', $search)
                        ->orWhereHas('equipment', fn ($equipmentQuery) => $equipmentQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('serial_number', 'like', "%{$search}%"));
                });
            })
            ->visibleTo(auth()->user())
            ->with(['creator', 'assignee', 'equipment'])
            ->latest()
            ->paginate(15);

        $savedViews = SavedView::query()
            ->where(function ($query): void {
                $query->where('user_id', auth()->id())->orWhere('is_shared', true);
            })
            ->latest()
            ->get();

        return view('tickets.index', compact('tickets', 'savedViews'));
    }

    public function storeView(StoreSavedViewRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $filters = array_filter($data['filters']);

        SavedView::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'filters' => $filters,
        ]);

        return redirect()->route('tickets.index', $filters)
            ->with('status', __('Vista guardada.'));
    }

    public function create(): View
    {
        return view('tickets.create');
    }

    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'creator',
            'assignee',
            'equipment',
            'comments' => fn ($query) => $query->with(['user', 'media'])->latest()->limit(50),
        ]);

        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket): View
    {
        $this->authorize('update', $ticket);

        return view('tickets.edit', compact('ticket'));
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        session()->flash('status', __('Ticket eliminado.'));

        return redirect()->route('tickets.index');
    }
}
