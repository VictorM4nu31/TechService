<?php
use function Livewire\Volt\{state, rules, computed};
use App\Models\Ticket;
use App\Models\Status;
use App\Models\Priority;
use App\Models\Category;
use App\Models\Equipment;

state([
    'ticket',
    'title' => fn($ticket) => $ticket->title,
    'description' => fn($ticket) => $ticket->description,
    'equipment' => fn($ticket) => $ticket->equipment,
    'equipment_id' => fn($ticket) => $ticket->equipment_id,
    'location' => fn($ticket) => $ticket->location,
    'priority_id' => fn($ticket) => $ticket->priority_id,
    'category_id' => fn($ticket) => $ticket->category_id,
    'maintenance_type' => fn($ticket) => $ticket->maintenance_type,
]);

rules([
    'title' => 'required|min:5',
    'description' => 'required',
    'priority_id' => 'required|exists:priorities,id',
    'category_id' => 'required|exists:categories,id',
    'equipment_id' => 'nullable|exists:equipment,id',
]);

$save = function () {
    $this->validate();

    $this->ticket->update([
        'title' => $this->title,
        'description' => $this->description,
        'equipment' => $this->equipment,
        'equipment_id' => $this->equipment_id,
        'location' => $this->location,
        'priority_id' => $this->priority_id,
        'category_id' => $this->category_id,
        'maintenance_type' => $this->maintenance_type,
    ]);

    // Log update activity
    \App\Models\Activity::create([
        'user_id' => auth()->id(),
        'ticket_id' => $this->ticket->id,
        'type' => 'updated',
        'description' => 'actualizó los detalles del ticket',
    ]);

    return redirect()->route('tickets.show', $this->ticket)->with('status', 'Ticket actualizado con éxito.');
};

$priorities = computed(fn() => Priority::orderBy('level', 'desc')->get());
$categories = computed(fn() => Category::all());
$equipments = computed(fn() => Equipment::where('user_id', $this->ticket->created_by)->get());

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
                        <flux:input wire:model="equipment" label="{{ __('Equipo Detalle Textual (Opcional)') }}" />
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
                    <flux:select wire:model="priority_id" label="{{ __('Prioridad') }}">
                        @foreach($this->priorities as $priority)
                            <flux:select.option value="{{ $priority->id }}">{{ $priority->name }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="category_id" label="{{ __('Categoría') }}">
                        @foreach($this->categories as $category)
                            <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
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