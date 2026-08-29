<x-layouts::app :title="__('Tickets')">
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1">{{ __('Todos los Tickets') }}</flux:heading>
                <flux:subheading>{{ __('Gestiona las solicitudes de soporte técnico.') }}</flux:subheading>
            </div>
            <flux:button icon="plus" variant="primary" :href="route('tickets.create')" wire:navigate>
                {{ __('Nuevo Ticket') }}
            </flux:button>
        </div>

        @if (session('status'))
            <div
                class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg text-sm">
                <flux:icon name="check-circle" size="sm" />
                {{ session('status') }}
            </div>
        @endif

        <flux:card class="overflow-hidden p-0">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('ID') }}</flux:table.column>
                    <flux:table.column>{{ __('Título') }}</flux:table.column>
                    <flux:table.column>{{ __('Estado') }}</flux:table.column>
                    <flux:table.column>{{ __('Prioridad') }}</flux:table.column>
                    <flux:table.column>{{ __('Categoría') }}</flux:table.column>
                    <flux:table.column>{{ __('Creado por') }}</flux:table.column>
                    <flux:table.column>{{ __('Asignado a') }}</flux:table.column>
                    <flux:table.column>{{ __('Fecha') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($tickets as $ticket)
                        <flux:table.row :key="$ticket->id" :href="route('tickets.show', $ticket)" wire:navigate
                            class="cursor-pointer">
                            <flux:table.cell>#{{ $ticket->id }}</flux:table.cell>
                            <flux:table.cell class="font-medium text-blue-500 hover:text-blue-400">
                                {{ $ticket->title }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$ticket->status->color()">{{ $ticket->status->label() }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" variant="outline" x-data
                                    x-tooltip="'{{ __('Prioridad: :level', ['level' => $ticket->priority->label()]) }}'">
                                    {{ $ticket->priority->label() }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $ticket->category->label() }}</flux:table.cell>
                            <flux:table.cell>{{ $ticket->creator->name }}</flux:table.cell>
                            <flux:table.cell>{{ $ticket->assignee?->name ?? __('Sin asignar') }}</flux:table.cell>
                            <flux:table.cell>
                                <span class="cursor-help underline decoration-dotted" x-data
                                    x-tooltip="'{{ $ticket->created_at->diffForHumans() }}'">
                                    {{ $ticket->created_at->format('d/m/Y H:i') }}
                                </span>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="8" class="text-center py-8 text-zinc-500">
                                {{ __('No hay tickets que coincidan con los criterios.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>

        @if($tickets->hasPages())
            <div class="mt-2">{{ $tickets->links() }}</div>
        @endif
    </div>
</x-layouts::app>
