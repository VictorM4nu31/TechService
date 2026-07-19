<?php

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
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
    'priority' => fn ($ticket) => $ticket->priority,
    'category' => fn ($ticket) => $ticket->category,
    'maintenance_type' => fn ($ticket) => $ticket->maintenance_type,
]);

rules([
    'title' => 'required|min:5',
    'description' => 'required',
    'priority' => ['required', 'in:'.implode(',', array_column(TicketPriority::cases(), 'value'))],
    'category' => ['required', 'in:'.implode(',', array_column(TicketCategory::cases(), 'value'))],
    'equipment_id' => 'nullable|exists:equipment,id',
]);

$save = function (TicketService $ticketService) {
    $this->validate();

    $this->ticket->update([
        'title' => $this->title,
        'description' => $this->description,
        'equipment_id' => $this->equipment_id,
        'location' => $this->location,
        'priority' => $this->priority,
        'category' => $this->category,
        'maintenance_type' => $this->maintenance_type,
    ]);

    $ticketService->logActivity($this->ticket, 'updated', 'actualizó los detalles del ticket');

    return redirect()->route('tickets.show', $this->ticket)->with('status', 'Ticket actualizado con éxito.');
};

$priorities = computed(fn () => TicketPriority::cases());
$categories = computed(fn () => TicketCategory::cases());
$equipments = computed(fn () => Equipment::where('user_id', $this->ticket->created_by)->get());

?>
<div>
    <div class="flex flex-col gap-6">
        <flux:heading size="xl">{{ __('Editar Ticket') }} #{{ $ticket->id }}</flux:heading>

        <div class="grid lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
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
                        <flux:input wire:model="location" label="{{ __('Ubicación') }}" />
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
                    <flux:select wire:model="priority" label="{{ __('Prioridad') }}">
                        @foreach($this->priorities as $priority)
                            <flux:select.option value="{{ $priority->value }}">{{ $priority->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="category" label="{{ __('Categoría') }}">
                        @foreach($this->categories as $category)
                            <flux:select.option value="{{ $category->value }}">{{ $category->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="maintenance_type" label="{{ __('Tipo de Mantenimiento') }}">
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
