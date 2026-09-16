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

    public string $title = '';

    public string $description = '';

    public ?int $equipment_id = null;

    public ?string $location = null;

    public string $priority = 'Media';

    public string $category = 'Correctivo';

    public string $maintenance_type = 'Hardware';

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile[] */
    public array $attachments = [];

    public function mount(?int $equipment = null): void
    {
        if ($equipment !== null) {
            $equipmentModel = Equipment::find($equipment);

            if ($equipmentModel !== null && $equipmentModel->isVisibleTo(auth()->user())) {
                $this->equipment_id = $equipmentModel->id;
            }
        }
    }

    protected function rules(): array
    {
        return [
            'title' => 'required|min:5',
            'description' => 'required|min:10',
            'priority' => ['required', 'in:'.implode(',', array_column(TicketPriority::cases(), 'value'))],
            'category' => ['required', 'in:'.implode(',', array_column(TicketCategory::cases(), 'value'))],
            'equipment_id' => [
                'nullable',
                fn (string $attribute, mixed $value, \Closure $fail) => $this->validateEquipment($value, $fail),
            ],
            'location' => 'nullable|string',
            'maintenance_type' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:10240',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.min' => 'El título debe tener al menos :min caracteres.',
            'description.required' => 'La descripción es obligatoria.',
            'description.min' => 'La descripción debe tener al menos :min caracteres.',
            'priority.required' => 'Selecciona una prioridad.',
            'priority.in' => 'La prioridad seleccionada no es válida.',
            'category.required' => 'Selecciona una categoría.',
            'category.in' => 'La categoría seleccionada no es válida.',
            'equipment_id.invalid' => 'El equipo seleccionado no es válido.',
            'maintenance_type.required' => 'Selecciona el tipo de mantenimiento.',
            'attachments.*.mimes' => 'Los archivos adjuntos deben ser PNG, JPG o PDF.',
            'attachments.*.max' => 'Cada archivo adjunto no debe superar los 10MB.',
        ];
    }

    protected function validateEquipment(mixed $value, \Closure $fail): void
    {
        $equipment = Equipment::find($value);

        if ($equipment === null || ! $equipment->isVisibleTo(auth()->user())) {
            $fail('El equipo seleccionado no es válido.');
        }
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
        $equipments = Equipment::query()->visibleToUser(auth()->user())->orderBy('name')->get();

        return view('livewire.ticket-create', compact('priorities', 'categories', 'equipments'));
    }
}
