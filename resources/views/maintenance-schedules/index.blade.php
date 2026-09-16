<x-layouts::app :title="__('Programaciones de Mantenimiento')">
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1">{{ __('Programaciones de Mantenimiento') }}</flux:heading>
                <flux:subheading>{{ __('Programa tareas preventivas y recurrentes por equipo.') }}</flux:subheading>
            </div>
            <flux:button icon="plus" variant="primary" :href="route('maintenance-schedules.create')" wire:navigate>
                {{ __('Nueva Programación') }}
            </flux:button>
        </div>

        @if (session('status'))
            <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg text-sm">
                <flux:icon name="check-circle" size="sm" />
                {{ session('status') }}
            </div>
        @endif

        <flux:card class="p-0 overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Nombre') }}</flux:table.column>
                    <flux:table.column>{{ __('Equipo') }}</flux:table.column>
                    <flux:table.column>{{ __('Frecuencia (días)') }}</flux:table.column>
                    <flux:table.column>{{ __('Próxima ejecución') }}</flux:table.column>
                    <flux:table.column>{{ __('Estado') }}</flux:table.column>
                    <flux:table.column>{{ __('Acciones') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($schedules as $schedule)
                        <flux:table.row :key="$schedule->id">
                            <flux:table.cell class="font-medium">{{ $schedule->name }}</flux:table.cell>
                            <flux:table.cell class="text-zinc-400">{{ $schedule->equipment->name ?? '—' }}</flux:table.cell>
                            <flux:table.cell class="text-zinc-400">{{ $schedule->frequency_days }}</flux:table.cell>
                            <flux:table.cell class="text-zinc-400">
                                {{ $schedule->next_run_at?->format('d/m/Y') ?? '—' }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$schedule->is_active ? 'green' : 'gray'" size="sm">
                                    {{ $schedule->is_active ? __('Activa') : __('Inactiva') }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square"
                                        :href="route('maintenance-schedules.edit', $schedule)" wire:navigate x-data
                                        x-tooltip="'{{ __('Editar') }}'" />
                                    <form method="POST" action="{{ route('maintenance-schedules.destroy', $schedule) }}"
                                        onsubmit="return confirm('{{ __('¿Eliminar esta programación?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button size="sm" variant="ghost" icon="trash" type="submit"
                                            class="text-red-500 hover:text-red-400" x-data
                                            x-tooltip="'{{ __('Eliminar') }}'" />
                                    </form>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center py-12">
                                <div class="flex flex-col items-center gap-3 text-zinc-500">
                                    <flux:icon name="calendar-days" size="lg" />
                                    <flux:text>{{ __('No hay programaciones de mantenimiento registradas.') }}</flux:text>
                                    <flux:button size="sm" variant="primary"
                                        :href="route('maintenance-schedules.create')" wire:navigate>
                                        {{ __('Crear la primera') }}
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>

        @if($schedules->hasPages())
            <div class="mt-2">{{ $schedules->links() }}</div>
        @endif
    </div>
</x-layouts::app>
