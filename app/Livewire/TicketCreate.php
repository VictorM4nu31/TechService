<?php

namespace App\Livewire;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Equipment;
use App\Models\TicketDraft;
use App\Services\TicketService;
use Carbon\CarbonImmutable;
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

    public string $impact = 'Medio';

    public string $urgency = 'Medio';

    public string $category = 'Correctivo';

    public string $maintenance_type = 'Hardware';

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile[] */
    public array $attachments = [];

    public ?int $draftId = null;

    public function mount(?int $equipment = null): void
    {
        $draft = TicketDraft::query()
            ->where('user_id', auth()->id())
            ->where('saved_at', '>=', now()->subDays(7))
            ->latest('saved_at')
            ->first();

        if ($draft !== null) {
            $payload = $draft->payload;
            $this->draftId = $draft->id;
            $this->title = $payload['title'] ?? $this->title;
            $this->description = $payload['description'] ?? $this->description;
            $this->equipment_id = $payload['equipment_id'] ?? $this->equipment_id;
            $this->location = $payload['location'] ?? $this->location;
            $this->priority = $payload['priority'] ?? $this->priority;
            $this->impact = $payload['impact'] ?? $this->impact;
            $this->urgency = $payload['urgency'] ?? $this->urgency;
            $this->category = $payload['category'] ?? $this->category;
            $this->maintenance_type = $payload['maintenance_type'] ?? $this->maintenance_type;
        }

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
            'impact' => ['required', 'in:Bajo,Medio,Alto'],
            'urgency' => ['required', 'in:Bajo,Medio,Alto'],
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
            'impact.required' => 'Selecciona el impacto operativo.',
            'urgency.required' => 'Selecciona la urgencia.',
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

    public function saveDraft(): void
    {
        $draft = TicketDraft::updateOrCreate(
            ['id' => $this->draftId, 'user_id' => auth()->id()],
            [
                'payload' => [
                    'title' => $this->title,
                    'description' => $this->description,
                    'equipment_id' => $this->equipment_id,
                    'location' => $this->location,
                    'priority' => $this->priority,
                    'impact' => $this->impact,
                    'urgency' => $this->urgency,
                    'category' => $this->category,
                    'maintenance_type' => $this->maintenance_type,
                ],
                'saved_at' => now(),
            ],
        );

        $this->draftId = $draft->id;
        session()->flash('draft-saved', __('Borrador guardado.'));
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
            'impact' => $this->impact,
            'urgency' => $this->urgency,
            'category' => TicketCategory::from($this->category),
            'created_by' => auth()->id(),
            'sla_due_at' => $this->slaDueAt(),
        ]);

        foreach ($this->attachments as $attachment) {
            $ticket->addMedia($attachment)->toMediaCollection('attachments');
        }

        if ($this->draftId !== null) {
            TicketDraft::whereKey($this->draftId)->where('user_id', auth()->id())->delete();
        }

        session()->flash('status', 'Ticket creado con éxito.');

        $this->redirect(route('tickets.index'));
    }

    protected function slaDueAt(): CarbonImmutable
    {
        return now()->toImmutable()->addHours(match ($this->urgency) {
            'Alto' => 4,
            'Medio' => 24,
            default => 72,
        });
    }

    public function render(): View
    {
        $priorities = TicketPriority::cases();
        $categories = TicketCategory::cases();
        $equipments = Equipment::query()->visibleToUser(auth()->user())->orderBy('name')->get();

        return view('livewire.ticket-create', compact('priorities', 'categories', 'equipments'));
    }
}
