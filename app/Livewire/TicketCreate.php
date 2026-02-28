<?php

namespace App\Livewire;

use App\Models\Priority;
use App\Models\Category;
use App\Models\Status;
use App\Services\TicketService;
use Livewire\Component;
use Livewire\WithFileUploads;

class TicketCreate extends Component
{
    use WithFileUploads;

    public $title = '';
    public $description = '';
    public $equipment_id = '';
    public $equipment = '';
    public $location = '';
    public $priority_id = '';
    public $category_id = '';
    public $maintenance_type = 'Hardware';
    public $attachments = [];

    protected $rules = [
        'title' => 'required|min:5',
        'description' => 'required|min:10',
        'priority_id' => 'required|exists:priorities,id',
        'category_id' => 'required|exists:categories,id',
        'equipment_id' => 'nullable|exists:equipment,id',
        'equipment' => 'nullable|string',
        'location' => 'nullable|string',
        'maintenance_type' => 'required|string',
        'attachments.*' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:10240', // 10MB
    ];

    public function removeAttachment($index)
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    public function save(TicketService $ticketService)
    {
        $this->validate();

        $openStatus = Status::where('name', 'Abierto')->first();

        $ticket = $ticketService->createTicket([
            'title' => $this->title,
            'description' => $this->description,
            'equipment_id' => $this->equipment_id ?: null,
            'equipment' => $this->equipment,
            'location' => $this->location,
            'maintenance_type' => $this->maintenance_type,
            'status_id' => $openStatus->id,
            'priority_id' => $this->priority_id,
            'category_id' => $this->category_id,
            'created_by' => auth()->id(),
        ]);

        foreach ($this->attachments as $attachment) {
            $ticket->addMedia($attachment)->toMediaCollection('attachments');
        }

        session()->flash('status', 'Ticket creado con éxito.');
        return redirect()->route('tickets.index');
    }

    public function render()
    {
        $priorities = Priority::orderBy('level', 'desc')->get();
        $categories = Category::all();
        $equipments = \App\Models\Equipment::where('user_id', auth()->id())->get();

        return view('livewire.ticket-create', compact('priorities', 'categories', 'equipments'));
    }
}
