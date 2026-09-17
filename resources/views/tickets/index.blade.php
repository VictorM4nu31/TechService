<x-layouts::app :title="__('Tickets')">
    @php
        $filtersActive = request()->filled('search') || request()->filled('status') || request()->filled('category');
    @endphp

    <div class="mx-auto flex max-w-[1600px] flex-col gap-8">
        <div class="flex flex-col gap-5 border-b border-signal-border pb-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="signal-kicker">{{ __('Cola operativa') }}</p>
                <flux:heading size="xl" level="1">
                    @if (request()->filled('search'))
                        {{ __('Resultados para') }} "{{ request('search') }}"
                    @elseif ($filtersActive)
                        {{ __('Tickets Filtrados') }}
                    @else
                        {{ __('Todos los Tickets') }}
                    @endif
                </flux:heading>
                <flux:subheading class="max-w-2xl text-signal-muted">
                    @if ($filtersActive)
                        {{ $tickets->total() }} {{ $tickets->total() === 1 ? __('ticket encontrado') : __('tickets encontrados') }} ·
                        <a href="{{ route('tickets.index') }}" class="text-blue-500 hover:text-blue-400"
                            wire:navigate>{{ __('Limpiar filtros') }}</a>
                    @else
                        {{ __('Gestiona las solicitudes de soporte técnico.') }}
                    @endif
                </flux:subheading>
            </div>
            <flux:button icon="plus" variant="primary" :href="route('tickets.create')" wire:navigate class="bg-signal-accent text-signal-ink hover:bg-signal-accent/85">
                {{ __('Reportar incidencia') }}
            </flux:button>
        </div>

        <flux:card class="signal-panel p-4">
            <form method="GET" action="{{ route('tickets.index') }}"
                class="flex flex-col md:flex-row gap-3 md:items-end">
                <flux:input name="search" :label="__('Buscar en la cola')" icon="magnifying-glass"
                    placeholder="{{ __('Título, equipo, serie, ubicación o #ID...') }}" :value="request('search')" class="min-w-0 flex-1" />
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
                <flux:button variant="primary" type="submit" icon="funnel" class="bg-signal-primary hover:bg-signal-primary-strong">{{ __('Aplicar') }}</flux:button>
                @if ($filtersActive)
                    <flux:button :href="route('tickets.index')" variant="ghost" icon="x-mark"
                        wire:navigate>{{ __('Limpiar') }}</flux:button>
                @endif
            </form>
        </flux:card>

        @if (session('status'))
            <div
                class="flex items-center gap-3 rounded-lg border border-signal-success/30 bg-signal-success/10 px-4 py-3 text-sm text-signal-success">
                <flux:icon name="check-circle" size="sm" />
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-signal-muted">
            <span>{{ $tickets->total() }} {{ $tickets->total() === 1 ? __('resultado') : __('resultados') }}</span>
            <span class="font-mono uppercase tracking-wider">{{ __('Ordenado por actividad reciente') }}</span>
        </div>

        <div class="signal-panel divide-y divide-signal-border overflow-hidden lg:hidden">
            @forelse ($tickets as $ticket)
                <a href="{{ route('tickets.show', $ticket) }}" wire:navigate class="signal-focus block p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 text-xs text-signal-muted">
                                <span class="font-mono">#{{ $ticket->id }}</span>
                                <flux:badge :color="$ticket->status->color()" size="sm">{{ $ticket->status->label() }}</flux:badge>
                            </div>
                            <flux:heading size="sm" class="mt-2 truncate text-signal-ink">{{ $ticket->title }}</flux:heading>
                        </div>
                        <flux:badge :color="$ticket->priority->color()" size="sm" variant="outline">{{ $ticket->priority->label() }}</flux:badge>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-signal-muted">
                        <span>{{ $ticket->category->label() }}</span>
                        <span>{{ $ticket->equipment?->name ?? __('Sin equipo') }}</span>
                        <span>{{ $ticket->created_at->diffForHumans() }}</span>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center text-sm text-signal-muted">{{ __('No hay tickets que coincidan con los criterios.') }}</div>
            @endforelse
        </div>

        <flux:card class="signal-panel hidden overflow-hidden p-0 lg:block">
            <div class="overflow-x-auto">
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
                            <flux:table.cell class="font-medium text-signal-accent hover:text-signal-ink">
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
            </div>
        </flux:card>

        @if($tickets->hasPages())
            <div class="mt-2">{{ $tickets->links() }}</div>
        @endif
    </div>
</x-layouts::app>
