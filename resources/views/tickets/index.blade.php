<x-layouts::app :title="__('Tickets')">
    @php
        $filtersActive = request()->filled('search') || request()->filled('status') || request()->filled('category');
    @endphp

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1">
                    @if (request()->filled('search'))
                        {{ __('Resultados para') }} "{{ request('search') }}"
                    @elseif ($filtersActive)
                        {{ __('Tickets Filtrados') }}
                    @else
                        {{ __('Todos los Tickets') }}
                    @endif
                </flux:heading>
                <flux:subheading>
                    @if ($filtersActive)
                        {{ $tickets->total() }} {{ $tickets->total() === 1 ? __('ticket encontrado') : __('tickets encontrados') }} ·
                        <a href="{{ route('tickets.index') }}" class="text-blue-500 hover:text-blue-400"
                            wire:navigate>{{ __('Limpiar filtros') }}</a>
                    @else
                        {{ __('Gestiona las solicitudes de soporte técnico.') }}
                    @endif
                </flux:subheading>
            </div>
            <flux:button icon="plus" variant="primary" :href="route('tickets.create')" wire:navigate>
                {{ __('Nuevo Ticket') }}
            </flux:button>
        </div>

        <flux:card class="p-4">
            <form method="GET" action="{{ route('tickets.index') }}"
                class="flex flex-col md:flex-row gap-3 md:items-end">
                <flux:input name="search" :label="__('Buscar')" icon="magnifying-glass"
                    placeholder="{{ __('Buscar por título...') }}" :value="request('search')" class="flex-1" />
                <flux:select name="status" :label="__('Estado')">
                    <option value="">{{ __('Todos') }}</option>
                    @foreach (\App\Enums\TicketStatus::cases() as $statusOption)
                        <option value="{{ $statusOption->value }}" @selected(request('status') === $statusOption->value)>
                            {{ $statusOption->label() }}
                        </option>
                    @endforeach
                </flux:select>
                <flux:select name="category" :label="__('Categoría')">
                    <option value="">{{ __('Todas') }}</option>
                    @foreach (\App\Enums\TicketCategory::cases() as $categoryOption)
                        <option value="{{ $categoryOption->value }}" @selected(request('category') === $categoryOption->value)>
                            {{ $categoryOption->label() }}
                        </option>
                    @endforeach
                </flux:select>
                <flux:button variant="primary" type="submit" icon="funnel">{{ __('Filtrar') }}</flux:button>
                @if ($filtersActive)
                    <flux:button :href="route('tickets.index')" variant="ghost" icon="x-mark"
                        wire:navigate>{{ __('Limpiar') }}</flux:button>
                @endif
            </form>
        </flux:card>

        @if (session('status'))
            <div
                class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg text-sm">
                <flux:icon name="check-circle" size="sm" />
                {{ session('status') }}
            </div>
        @endif

        <flux:card class="p-0 overflow-x-auto">
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
