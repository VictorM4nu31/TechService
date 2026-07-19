<?php

namespace App\Livewire;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Equipment;
use App\Services\TicketService;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class TicketCreate extends Component
{
    use WithFileUploads;

    public $title = '';

    public $description = '';

    public $equipment_id = '';

    public $location = '';

    public string $priority = 'Media';

    public string $category = 'Correctivo';

    public $maintenance_type = 'Hardware';

    public $attachments = [];

    protected function rules(): array
    {
        return [
            'title' => 'required|min:5',
            'description' => 'required|min:10',
            'priority' => ['required', 'in:'.implode(',', array_column(TicketPriority::cases(), 'value'))],
            'category' => ['required', 'in:'.implode(',', array_column(TicketCategory::cases(), 'value'))],
            'equipment_id' => 'nullable|exists:equipment,id',
            'location' => 'nullable|string',
            'maintenance_type' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:10240',
        ];
    }

    public function removeAttachment(int $index): void
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    public function save(TicketService $ticketService): void
    {
        $this->validate();

        $ticket = $ticketService->createTicket([
            'title' => $this->title,
            'description' => $this->description,
            'equipment_id' => $this->equipment_id ?: null,
            'location' => $this->location,
            'maintenance_type' => $this->maintenance_type,
            'status' => TicketStatus::Abierto,
            'priority' => TicketPriority::from($this->priority),
            'category' => TicketCategory::from($this->category),
            'created_by' => auth()->id(),
        ]);

        foreach ($this->attachments as $attachment) {
            $ticket->addMedia($attachment)->toMediaCollection('attachments');
        }

        session()->flash('status', 'Ticket creado con éxito.');

        $this->redirect(route('tickets.index'));
    }

    public function render(): View
    {
        $priorities = TicketPriority::cases();
        $categories = TicketCategory::cases();
        $equipments = Equipment::where('user_id', auth()->id())->get();

        return view('livewire.ticket-create', compact('priorities', 'categories', 'equipments'));
    }
}
