<x-layouts::app :title="__('Panel de Control')">
    <div class="mx-auto flex max-w-[1600px] flex-col gap-8">
        <header class="flex flex-col gap-5 border-b border-signal-border pb-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <p class="signal-kicker">{{ __('Centro de señales') }}</p>
                <div>
                    <flux:heading size="xl" level="1" class="tracking-tight">{{ __('Qué necesita atención') }}</flux:heading>
                    <flux:subheading class="mt-2 max-w-2xl text-signal-muted">
                        {{ __('Una vista operativa de incidencias abiertas, riesgo y próximas acciones.') }}
                    </flux:subheading>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <form method="GET" action="{{ route('tickets.index') }}" class="min-w-0 sm:w-72">
                    <flux:input name="search" variant="filled" placeholder="{{ __('Buscar por ticket o equipo...') }}"
                        icon="magnifying-glass" aria-label="{{ __('Buscar tickets') }}" />
                </form>
                <flux:button variant="primary" icon="plus" :href="route('tickets.create')" wire:navigate
                    class="bg-signal-accent text-signal-ink hover:bg-signal-accent/85">
                    {{ __('Reportar incidencia') }}
                </flux:button>
            </div>
        </header>

        <section aria-label="{{ __('Resumen operativo') }}" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('tickets.index', ['status' => 'Abierto']) }}" wire:navigate class="signal-panel signal-focus group p-5 transition-colors hover:border-signal-accent/60">
                <div class="flex items-start justify-between gap-3">
                    <span class="signal-kicker text-signal-info">{{ __('Entrada') }}</span>
                    <flux:icon name="arrow-up-right" size="xs" class="text-signal-muted transition-transform group-hover:-translate-y-0.5 group-hover:translate-x-0.5" />
                </div>
                <div class="mt-5 flex items-end justify-between gap-4">
                    <span class="font-mono text-4xl font-medium tracking-tight text-signal-ink">{{ $stats['open'] }}</span>
                    <span class="pb-1 text-right text-xs text-signal-muted">{{ __('tickets abiertos') }}</span>
                </div>
            </a>

            <a href="{{ route('tickets.index', ['status' => 'En Progreso']) }}" wire:navigate class="signal-panel signal-focus group p-5 transition-colors hover:border-signal-accent/60">
                <div class="flex items-start justify-between gap-3">
                    <span class="signal-kicker text-signal-secondary">{{ __('En curso') }}</span>
                    <flux:icon name="arrow-up-right" size="xs" class="text-signal-muted transition-transform group-hover:-translate-y-0.5 group-hover:translate-x-0.5" />
                </div>
                <div class="mt-5 flex items-end justify-between gap-4">
                    <span class="font-mono text-4xl font-medium tracking-tight text-signal-ink">{{ $stats['in_progress'] }}</span>
                    <span class="pb-1 text-right text-xs text-signal-muted">{{ __('en progreso') }}</span>
                </div>
            </a>

            <a href="{{ route('tickets.index', ['category' => 'Emergencia']) }}" wire:navigate class="signal-panel signal-focus group border-signal-error/40 p-5 transition-colors hover:border-signal-error">
                <div class="flex items-start justify-between gap-3">
                    <span class="signal-kicker text-signal-error">{{ __('Riesgo') }}</span>
                    <flux:icon name="exclamation-triangle" size="xs" class="text-signal-error" />
                </div>
                <div class="mt-5 flex items-end justify-between gap-4">
                    <span class="font-mono text-4xl font-medium tracking-tight text-signal-ink">{{ $stats['overdue'] }}</span>
                    <span class="pb-1 text-right text-xs text-signal-muted">{{ __('SLA vencido') }}</span>
                </div>
            </a>

            <div class="signal-panel p-5">
                <div class="flex items-start justify-between gap-3">
                    <span class="signal-kicker text-signal-success">{{ __('Flujo') }}</span>
                    <flux:icon name="check-circle" size="xs" class="text-signal-success" />
                </div>
                <div class="mt-5 flex items-end justify-between gap-4">
                    <span class="font-mono text-4xl font-medium tracking-tight text-signal-ink">{{ $stats['resolved'] }}</span>
                    <span class="pb-1 text-right text-xs text-signal-muted">{{ __('resueltos') }}</span>
                </div>
            </div>
        </section>

        <div class="grid gap-8 xl:grid-cols-[minmax(0,1.5fr)_minmax(20rem,0.75fr)]">
            <section class="signal-panel overflow-hidden" aria-labelledby="next-actions-heading">
                <div class="flex flex-col gap-4 border-b border-signal-border p-5 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="signal-kicker">{{ __('Orden de trabajo') }}</p>
                        <flux:heading id="next-actions-heading" size="lg" class="mt-2">{{ __('Próximas acciones') }}</flux:heading>
                        <flux:text size="sm" class="mt-1 text-signal-muted">{{ __('La cola se ordena por riesgo, vencimiento y prioridad.') }}</flux:text>
                    </div>
                    <flux:link :href="route('tickets.index')" wire:navigate class="text-signal-accent">{{ __('Abrir cola completa') }} <span aria-hidden="true">→</span></flux:link>
                </div>

                <div class="divide-y divide-signal-border">
                    @foreach($criticalTickets as $ticket)
                        <a href="{{ route('tickets.show', $ticket) }}" wire:navigate class="signal-focus group flex flex-col gap-4 border-s-2 border-signal-error bg-signal-error/5 p-5 transition-colors hover:bg-signal-error/10 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-start gap-4">
                                <span class="mt-1 flex size-8 shrink-0 items-center justify-center rounded-md bg-signal-error/15 text-signal-error"><flux:icon name="exclamation-triangle" size="xs" /></span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="signal-kicker text-signal-error">{{ __('Atención inmediata') }}</span>
                                        <span class="font-mono text-[11px] text-signal-muted">#{{ $ticket->id }}</span>
                                    </div>
                                    <flux:heading size="sm" class="mt-2 truncate text-signal-ink group-hover:text-signal-accent">{{ $ticket->title }}</flux:heading>
                                    <flux:text size="xs" class="mt-2 text-signal-muted">{{ $ticket->location ?? __('Ubicación no especificada') }} · {{ $ticket->created_at->diffForHumans() }}</flux:text>
                                </div>
                            </div>
                            <flux:badge color="red" size="sm">{{ $ticket->status->label() }}</flux:badge>
                        </a>
                    @endforeach

                    @forelse($nextActions as $ticket)
                        <a href="{{ route('tickets.show', $ticket) }}" wire:navigate class="signal-focus group flex flex-col gap-4 p-5 transition-colors hover:bg-signal-elevated/60 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-start gap-4">
                                <span class="mt-1 flex size-8 shrink-0 items-center justify-center rounded-md bg-signal-canvas font-mono text-[10px] text-signal-muted">#{{ $ticket->id }}</span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <flux:badge :color="$ticket->priority->color()" size="sm">{{ $ticket->priority->label() }}</flux:badge>
                                        <span class="font-mono text-[11px] uppercase tracking-wider text-signal-muted">{{ $ticket->category->label() }}</span>
                                    </div>
                                    <flux:heading size="sm" class="mt-2 truncate text-signal-ink group-hover:text-signal-accent">{{ $ticket->title }}</flux:heading>
                                    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-signal-muted">
                                        <span class="inline-flex items-center gap-1"><flux:icon name="cpu-chip" size="xs" /> {{ $ticket->equipment?->name ?? __('Sin equipo') }}</span>
                                        <span class="inline-flex items-center gap-1"><flux:icon name="user" size="xs" /> {{ $ticket->assignee?->name ?? __('Sin asignar') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center justify-between gap-4 sm:block sm:text-right">
                                <flux:badge :color="$ticket->status->color()" size="sm">{{ $ticket->status->label() }}</flux:badge>
                                <flux:text size="xs" class="mt-1 block text-signal-muted">
                                    {{ $ticket->due_date ? __('Vence :date', ['date' => $ticket->due_date->diffForHumans()]) : $ticket->created_at->diffForHumans() }}
                                </flux:text>
                            </div>
                        </a>
                    @empty
                        <div class="p-10 text-center">
                            <flux:icon name="check-circle" size="lg" class="mx-auto text-signal-success" />
                            <flux:heading size="md" class="mt-3">{{ __('No hay acciones pendientes') }}</flux:heading>
                            <flux:text size="sm" class="mt-1 text-signal-muted">{{ __('La cola está limpia por ahora.') }}</flux:text>
                        </div>
                    @endforelse
                </div>
            </section>

            <aside class="flex flex-col gap-8">
                <section class="signal-panel p-5" aria-labelledby="pulse-heading">
                    <p class="signal-kicker">{{ __('Pulso del sistema') }}</p>
                    <flux:heading id="pulse-heading" size="lg" class="mt-2">{{ __('Señales que requieren contexto') }}</flux:heading>
                    <div class="mt-6 space-y-4">
                        <a href="{{ route('tickets.index', ['category' => 'Emergencia']) }}" wire:navigate class="signal-focus flex items-center justify-between gap-4 rounded-lg border border-signal-border bg-signal-canvas p-4 hover:border-signal-error/70">
                            <span class="flex items-center gap-3 text-sm"><span class="size-2 rounded-full bg-signal-error"></span>{{ __('Emergencias activas') }}</span>
                            <span class="font-mono text-sm text-signal-error">{{ $categories->firstWhere('category.value', 'Emergencia')?->tickets_count ?? 0 }}</span>
                        </a>
                        <a href="{{ route('tickets.index', ['status' => 'Abierto']) }}" wire:navigate class="signal-focus flex items-center justify-between gap-4 rounded-lg border border-signal-border bg-signal-canvas p-4 hover:border-signal-warning/70">
                            <span class="flex items-center gap-3 text-sm"><span class="size-2 rounded-full bg-signal-warning"></span>{{ __('Sin responsable') }}</span>
                            <span class="font-mono text-sm text-signal-warning">{{ $stats['unassigned'] }}</span>
                        </a>
                    </div>
                    <div class="mt-6 border-t border-signal-border pt-5">
                        <div class="flex items-center justify-between text-xs text-signal-muted">
                            <span>{{ __('Total histórico') }}</span>
                            <span class="font-mono text-signal-ink">{{ $stats['total'] }}</span>
                        </div>
                        <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-signal-canvas">
                            <div class="h-full rounded-full bg-signal-accent" style="width: {{ $stats['total'] > 0 ? min(100, ($stats['resolved'] / $stats['total']) * 100) : 0 }}%"></div>
                        </div>
                        <div class="mt-2 text-right text-[11px] text-signal-muted">{{ __(':percent% resueltos', ['percent' => $stats['total'] > 0 ? round(($stats['resolved'] / $stats['total']) * 100) : 0]) }}</div>
                    </div>
                </section>

                <section class="signal-panel p-5" aria-labelledby="activity-heading">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="signal-kicker">{{ __('Trazabilidad') }}</p>
                            <flux:heading id="activity-heading" size="lg" class="mt-2">{{ __('Actividad reciente') }}</flux:heading>
                        </div>
                        <flux:icon name="clock" size="sm" class="text-signal-muted" />
                    </div>
                    <div class="mt-6 space-y-5">
                        @forelse($activities->take(5) as $activity)
                            <div class="flex gap-3">
                                <span class="mt-1 size-2 shrink-0 rounded-full bg-signal-accent"></span>
                                <div class="min-w-0">
                                    <flux:text size="sm" class="leading-snug"><strong class="text-signal-ink">{{ $activity->user->name }}</strong> {{ $activity->description }}</flux:text>
                                    @if($activity->ticket)
                                        <flux:link :href="route('tickets.show', $activity->ticket)" wire:navigate class="mt-1 block truncate text-xs text-signal-info">{{ $activity->ticket->title }}</flux:link>
                                    @endif
                                    <flux:text size="xs" class="mt-1 text-signal-muted">{{ $activity->created_at->diffForHumans() }}</flux:text>
                                </div>
                            </div>
                        @empty
                            <flux:text class="text-signal-muted">{{ __('No hay actividad reciente.') }}</flux:text>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>
</x-layouts::app>
