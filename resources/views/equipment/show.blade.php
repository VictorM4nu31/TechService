<x-layouts::app :title="$equipment->name">
    @php
        $activeTickets = $equipment->tickets->where('status.value', '!=', 'Cerrado')->count();
        $activeSchedules = $equipment->maintenanceSchedules->where('is_active', true)->count();
        $lastTicket = $equipment->tickets->sortByDesc('created_at')->first();
    @endphp

    <div class="mx-auto flex max-w-6xl flex-col gap-8">
        <nav class="flex items-center gap-2 text-sm text-signal-muted" aria-label="{{ __('Breadcrumb') }}">
            <a href="{{ route('dashboard') }}" wire:navigate class="signal-focus hover:text-signal-ink">{{ __('Panel') }}</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('equipment.index') }}" wire:navigate class="signal-focus hover:text-signal-ink">{{ __('Equipos') }}</a>
            <span aria-hidden="true">/</span>
            <span class="truncate text-signal-ink">{{ $equipment->name }}</span>
        </nav>

        <header class="flex flex-col gap-5 border-b border-signal-border pb-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="signal-kicker">{{ __('Pasaporte del activo') }}</p>
                <div class="flex flex-wrap items-center gap-3">
                    <flux:heading size="xl" level="1" class="tracking-tight">{{ $equipment->name }}</flux:heading>
                    <flux:badge variant="outline" size="sm">{{ $equipment->type }}</flux:badge>
                </div>
                <flux:subheading class="text-signal-muted">
                    {{ $equipment->brand ?? __('Marca no especificada') }} {{ $equipment->model ? '· '.$equipment->model : '' }}
                    <span class="mx-1">·</span>
                    <span class="font-mono text-xs">{{ $equipment->serial_number ?? __('SIN SERIE') }}</span>
                </flux:subheading>
            </div>

            <div class="flex flex-wrap gap-3">
                <flux:button :href="route('equipment.edit', $equipment)" wire:navigate variant="ghost" icon="pencil-square">
                    {{ __('Editar ficha') }}
                </flux:button>
                <flux:button variant="primary" :href="route('tickets.create', ['equipment' => $equipment->id])" wire:navigate icon="plus"
                    class="bg-signal-accent text-signal-ink hover:bg-signal-accent/85">
                    {{ __('Abrir incidencia') }}
                </flux:button>
            </div>
        </header>

        <section class="grid gap-3 sm:grid-cols-3" aria-label="{{ __('Resumen del activo') }}">
            <div class="signal-panel p-5">
                <p class="signal-kicker text-signal-error">{{ __('Atención') }}</p>
                <div class="mt-4 flex items-end justify-between gap-4">
                    <span class="font-mono text-3xl text-signal-ink">{{ $activeTickets }}</span>
                    <span class="text-right text-xs text-signal-muted">{{ __('tickets activos') }}</span>
                </div>
            </div>
            <div class="signal-panel p-5">
                <p class="signal-kicker text-signal-success">{{ __('Prevención') }}</p>
                <div class="mt-4 flex items-end justify-between gap-4">
                    <span class="font-mono text-3xl text-signal-ink">{{ $activeSchedules }}</span>
                    <span class="text-right text-xs text-signal-muted">{{ __('rutinas activas') }}</span>
                </div>
            </div>
            <div class="signal-panel p-5">
                <p class="signal-kicker text-signal-info">{{ __('Última señal') }}</p>
                <div class="mt-4 flex items-end justify-between gap-4">
                    <span class="font-mono text-lg text-signal-ink">{{ $lastTicket?->created_at?->diffForHumans() ?? __('Sin historial') }}</span>
                    <span class="text-right text-xs text-signal-muted">{{ __('actividad') }}</span>
                </div>
            </div>
        </section>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1.45fr)_minmax(18rem,0.75fr)]">
            <section class="signal-panel overflow-hidden" aria-labelledby="interventions-heading">
                <div class="border-b border-signal-border p-5">
                    <p class="signal-kicker">{{ __('Historial operativo') }}</p>
                    <flux:heading id="interventions-heading" size="lg" class="mt-2">{{ __('Intervenciones') }}</flux:heading>
                    <flux:text size="sm" class="mt-1 text-signal-muted">{{ __('Cada incidencia deja contexto para la siguiente decisión.') }}</flux:text>
                </div>

                <div class="divide-y divide-signal-border">
                    @forelse($equipment->tickets->sortByDesc('created_at') as $ticket)
                        <a href="{{ route('tickets.show', $ticket) }}" wire:navigate class="signal-focus group flex gap-4 p-5 transition-colors hover:bg-signal-elevated/60">
                            <div class="relative flex w-6 shrink-0 justify-center">
                                <span class="mt-1 size-2.5 rounded-full {{ $ticket->status->value === 'Cerrado' ? 'bg-signal-success' : ($ticket->priority->value === 'Alta' ? 'bg-signal-error' : 'bg-signal-warning') }}"></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-mono text-xs text-signal-muted">#{{ $ticket->id }}</span>
                                    <flux:badge :color="$ticket->status->color()" size="sm">{{ $ticket->status->label() }}</flux:badge>
                                    <flux:badge :color="$ticket->priority->color()" variant="outline" size="sm">{{ $ticket->priority->label() }}</flux:badge>
                                </div>
                                <flux:heading size="sm" class="mt-2 truncate text-signal-ink group-hover:text-signal-accent">{{ $ticket->title }}</flux:heading>
                                <flux:text size="xs" class="mt-1 text-signal-muted">{{ $ticket->created_at->diffForHumans() }} · {{ $ticket->category->label() }}</flux:text>
                            </div>
                            <flux:icon name="chevron-right" size="xs" class="mt-1 shrink-0 text-signal-muted" />
                        </a>
                    @empty
                        <div class="p-10 text-center">
                            <flux:icon name="ticket" size="lg" class="mx-auto text-signal-muted" />
                            <flux:heading size="md" class="mt-3">{{ __('Sin intervención registrada') }}</flux:heading>
                            <flux:text size="sm" class="mt-1 text-signal-muted">{{ __('Este activo todavía no tiene tickets asociados.') }}</flux:text>
                        </div>
                    @endforelse
                </div>
            </section>

            <aside class="flex flex-col gap-8">
                <section class="signal-panel p-5" aria-labelledby="details-heading">
                    <p class="signal-kicker">{{ __('Identidad') }}</p>
                    <flux:heading id="details-heading" size="lg" class="mt-2">{{ __('Datos del activo') }}</flux:heading>
                    <dl class="mt-6 space-y-4">
                        <div><dt class="text-xs uppercase tracking-wider text-signal-muted">{{ __('Cliente externo') }}</dt><dd class="mt-1 text-sm text-signal-ink">{{ $equipment->client->name ?? __('Sin cliente') }}</dd></div>
                        <div><dt class="text-xs uppercase tracking-wider text-signal-muted">{{ __('Responsable') }}</dt><dd class="mt-1 text-sm text-signal-ink">{{ $equipment->owner->name ?? __('Sin responsable') }}</dd></div>
                        <div><dt class="text-xs uppercase tracking-wider text-signal-muted">{{ __('Registrado') }}</dt><dd class="mt-1 text-sm text-signal-ink">{{ $equipment->created_at->format('d/m/Y') }}</dd></div>
                    </dl>
                </section>

                <section class="signal-panel p-5" aria-labelledby="maintenance-heading">
                    <p class="signal-kicker">{{ __('Prevención') }}</p>
                    <flux:heading id="maintenance-heading" size="lg" class="mt-2">{{ __('Rutinas de mantenimiento') }}</flux:heading>
                    <div class="mt-6 space-y-3">
                        @forelse($equipment->maintenanceSchedules as $schedule)
                            <div class="rounded-lg border border-signal-border bg-signal-canvas p-3">
                                <div class="flex items-start justify-between gap-3">
                                    <span class="text-sm text-signal-ink">{{ $schedule->name }}</span>
                                    <span class="size-2 shrink-0 rounded-full {{ $schedule->is_active ? 'bg-signal-success' : 'bg-signal-muted' }}"></span>
                                </div>
                                <span class="mt-2 block text-xs text-signal-muted">{{ $schedule->next_run_at?->diffForHumans() ?? __('Sin fecha programada') }}</span>
                            </div>
                        @empty
                            <flux:text size="sm" class="text-signal-muted">{{ __('No hay rutinas asociadas a este activo.') }}</flux:text>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>
</x-layouts::app>
