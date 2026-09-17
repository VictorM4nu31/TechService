<?php

use App\Enums\TicketCategory;
use App\Enums\TicketImpact;
use App\Enums\TicketPriority;
use App\Enums\TicketUrgency;
use App\Models\Ticket;
use App\Models\Equipment;
use App\Services\TicketService;
use function Livewire\Volt\{state, rules, computed};

state([
    'ticket',
    'title' => fn ($ticket) => $ticket->title,
    'description' => fn ($ticket) => $ticket->description,
    'equipment_id' => fn ($ticket) => $ticket->equipment_id,
    'location' => fn ($ticket) => $ticket->location,
    'priority' => fn ($ticket) => $ticket->priority->value,
    'impact' => fn ($ticket) => $ticket->impact?->value ?? 'Medio',
    'urgency' => fn ($ticket) => $ticket->urgency?->value ?? 'Medio',
    'category' => fn ($ticket) => $ticket->category->value,
    'maintenance_type' => fn ($ticket) => $ticket->maintenance_type,
]);

rules([
    'title' => 'required|min:5',
    'description' => 'required|min:10',
    'priority' => ['required', 'in:'.implode(',', array_column(TicketPriority::cases(), 'value'))],
    'category' => ['required', 'in:'.implode(',', array_column(TicketCategory::cases(), 'value'))],
    'equipment_id' => [
        'nullable',
        function (string $attribute, mixed $value, Closure $fail) {
            $equipment = Equipment::find($value);

            if ($equipment === null || ! $equipment->isVisibleTo(auth()->user())) {
                $fail('El equipo seleccionado no es válido.');
            }
        },
    ],
])->messages([
    'title.required' => 'El título es obligatorio.',
    'title.min' => 'El título debe tener al menos :min caracteres.',
    'description.required' => 'La descripción es obligatoria.',
    'description.min' => 'La descripción debe tener al menos :min caracteres.',
    'priority.required' => 'Selecciona una prioridad.',
    'priority.in' => 'La prioridad seleccionada no es válida.',
    'category.required' => 'Selecciona una categoría.',
    'category.in' => 'La categoría seleccionada no es válida.',
]);

$save = function (TicketService $ticketService) {
    $this->authorize('update', $this->ticket);

    $this->validate();

    $this->ticket->update([
        'title' => $this->title,
        'description' => $this->description,
        'equipment_id' => $this->equipment_id ?: null,
        'location' => $this->location,
        'priority' => $this->priority,
        'impact' => $this->impact,
        'urgency' => $this->urgency,
        'category' => $this->category,
        'maintenance_type' => $this->maintenance_type,
    ]);

    $ticketService->logActivity($this->ticket, 'updated', 'actualizó los detalles del ticket');

    return redirect()->route('tickets.show', $this->ticket)->with('status', 'Ticket actualizado con éxito.');
};

$priorities = computed(fn () => TicketPriority::cases());
$categories = computed(fn () => TicketCategory::cases());
$equipments = computed(fn () => Equipment::query()->visibleToUser(auth()->user())->orderBy('name')->get());

?>
<div>
    <div class="flex flex-col gap-6">
        <flux:heading size="xl">{{ __('Editar Ticket') }} #{{ $ticket->id }}</flux:heading>

        <div class="grid lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                @if ($errors->any())
                    <div
                        class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg text-sm">
                        <flux:icon name="exclamation-triangle" size="sm" />
                        {{ $errors->first() }}
                    </div>
                @endif

                <flux:card class="bg-zinc-900 border-zinc-800 space-y-4">
                    <flux:input wire:model="title" label="{{ __('Título') }}" required />
                    <flux:textarea wire:model="description" label="{{ __('Descripción') }}" rows="6" required />

                    <div class="grid md:grid-cols-2 gap-4">
                        <flux:select wire:model="equipment_id" label="{{ __('Equipo de Inventario') }}">
                            <flux:select.option value="">{{ __('No asignado') }}</flux:select.option>
                            @foreach($this->equipments as $eq)
                                <flux:select.option value="{{ $eq->id }}">{{ $eq->name }} ({{ $eq->serial_number }})
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:input wire:model="location" label="{{ __('Ubicación') }}"
                            placeholder="{{ __('Ej: Piso 3, Oficina 301') }}" />
                    </div>
                </flux:card>

                <div class="flex gap-3">
                    <flux:button wire:click="save" variant="primary" color="blue">{{ __('Guardar Cambios') }}
                    </flux:button>
                    <flux:button :href="route('tickets.show', $ticket)" wire:navigate variant="ghost">
                        {{ __('Cancelar') }}
                    </flux:button>
                </div>
            </div>

            <div class="space-y-6">
                <flux:card class="bg-zinc-900 border-zinc-800 space-y-4">
                    <flux:heading size="md">{{ __('Prioridad') }}</flux:heading>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($this->priorities as $priorityOption)
                            <button type="button"
                                wire:click="$set('priority', '{{ $priorityOption->value }}')"
                                class="px-4 py-2 rounded-lg text-xs font-bold transition-all border-2 {{ $priorityOption->value === $this->priority ? 'scale-105 shadow-lg' : 'opacity-50' }}
                                {{ match ($priorityOption) {
                                    TicketPriority::Alta => 'bg-red-500/10 text-red-500 border-red-500',
                                    TicketPriority::Media => 'bg-yellow-500/10 text-yellow-500 border-yellow-500',
                                    TicketPriority::Baja => 'bg-green-500/10 text-green-500 border-green-500',
                                } }}">
                                {{ strtoupper($priorityOption->label()) }}
                            </button>
                        @endforeach
                    </div>
                </flux:card>

                <flux:card class="bg-zinc-900 border-zinc-800 space-y-4">
                    <flux:heading size="md">{{ __('Categoría') }}</flux:heading>
                    <div class="grid gap-3">
                        @foreach ($this->categories as $categoryOption)
                            <button type="button"
                                wire:click="$set('category', '{{ $categoryOption->value }}')"
                                class="flex items-center gap-3 p-4 rounded-xl border-2 transition-all text-left {{ $categoryOption->value === $this->category ? 'bg-blue-500/10 border-blue-500 shadow-[0_0_15px_rgba(59,130,246,0.2)]' : 'bg-transparent border-zinc-800 hover:border-zinc-700' }}">
                                <div
                                    class="p-2 rounded-lg bg-zinc-800 {{ $categoryOption->value === $this->category ? 'text-blue-500' : 'text-zinc-500' }}">
                                    <flux:icon :name="$categoryOption->icon()" size="sm" />
                                </div>
                                <flux:text
                                    class="font-medium {{ $categoryOption->value === $this->category ? 'text-zinc-100' : 'text-zinc-400' }}">
                                    {{ $categoryOption->label() }}
                                </flux:text>
                            </button>
                        @endforeach
                    </div>
                </flux:card>

                <flux:card class="bg-zinc-900 border-zinc-800 space-y-4">
                    <flux:heading size="md">{{ __('Triage operativo') }}</flux:heading>
                    <flux:select wire:model="impact" label="{{ __('Impacto') }}">
                        @foreach (TicketImpact::cases() as $impactOption)
                            <flux:select.option value="{{ $impactOption->value }}">{{ $impactOption->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model="urgency" label="{{ __('Urgencia') }}">
                        @foreach (TicketUrgency::cases() as $urgencyOption)
                            <flux:select.option value="{{ $urgencyOption->value }}">{{ $urgencyOption->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:card>

                <flux:card class="bg-zinc-900 border-zinc-800 space-y-4">
                    <flux:heading size="md">{{ __('Tipo de Mantenimiento') }}</flux:heading>
                    <flux:select wire:model="maintenance_type">
                        <x-slot name="prefix">
                            <flux:icon :name="match($maintenance_type) {
                                'Hardware' => 'cpu-chip',
                                'Software' => 'code-bracket',
                                'Red' => 'wifi',
                                'Seguridad' => 'lock-closed',
                                default => 'question-mark-circle',
                            }" size="sm" />
                        </x-slot>
                        <flux:select.option value="Hardware">{{ __('Hardware') }}</flux:select.option>
                        <flux:select.option value="Software">{{ __('Software') }}</flux:select.option>
                        <flux:select.option value="Red">{{ __('Red') }}</flux:select.option>
                        <flux:select.option value="Seguridad">{{ __('Seguridad') }}</flux:select.option>
                        <flux:select.option value="Otro">{{ __('Otro') }}</flux:select.option>
                    </flux:select>
                </flux:card>
            </div>
        </div>
    </div>
</div>
