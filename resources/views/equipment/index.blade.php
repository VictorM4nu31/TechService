<x-layouts::app :title="__('Mis Equipos')">
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1">{{ __('Inventario de Equipos') }}</flux:heading>
                <flux:subheading>{{ __('Gestiona los equipos registrados para tus tickets de soporte.') }}
                </flux:subheading>
            </div>
            <flux:button icon="plus" variant="primary" :href="route('equipment.create')" wire:navigate>
                {{ __('Nuevo Equipo') }}
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
                    <flux:table.column>{{ __('Nombre') }}</flux:table.column>
                    <flux:table.column>{{ __('Tipo') }}</flux:table.column>
                    <flux:table.column>{{ __('Marca / Modelo') }}</flux:table.column>
                    <flux:table.column>{{ __('N° de Serie') }}</flux:table.column>
                    @role('Admin')
                    <flux:table.column>{{ __('Propietario') }}</flux:table.column>
                    @endrole
                    <flux:table.column>{{ __('Tickets') }}</flux:table.column>
                    <flux:table.column>{{ __('Acciones') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($equipment as $item)
                        <flux:table.row :key="$item->id">
                            <flux:table.cell class="font-medium">
                                <flux:link :href="route('equipment.show', $item)" wire:navigate>
                                    {{ $item->name }}
                                </flux:link>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge variant="outline" size="sm">{{ $item->type }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-zinc-400">
                                {{ $item->brand ?? '—' }} {{ $item->model ? '/ ' . $item->model : '' }}
                            </flux:table.cell>
                            <flux:table.cell class="text-zinc-400 font-mono text-xs">
                                {{ $item->serial_number ?? '—' }}
                            </flux:table.cell>
                            @role('Admin')
                            <flux:table.cell class="text-zinc-400">
                                {{ $item->owner->name ?? '—' }}
                            </flux:table.cell>
                            @endrole
                            <flux:table.cell>
                                <flux:badge color="blue" size="sm">{{ $item->tickets_count }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square"
                                        :href="route('equipment.edit', $item)" wire:navigate x-data
                                        x-tooltip="'{{ __('Editar') }}'" />
                                    <form method="POST" action="{{ route('equipment.destroy', $item) }}"
                                        onsubmit="return confirm('{{ __('¿Eliminar este equipo?') }}')">
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
                            <flux:table.cell colspan="7" class="text-center py-12">
                                <div class="flex flex-col items-center gap-3 text-zinc-500">
                                    <flux:icon name="cpu-chip" size="lg" />
                                    <flux:text>{{ __('No hay equipos registrados.') }}</flux:text>
                                    <flux:button size="sm" variant="primary" :href="route('equipment.create')"
                                        wire:navigate>
                                        {{ __('Registrar el primero') }}
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>

        @if($equipment->hasPages())
            <div class="mt-2">{{ $equipment->links() }}</div>
        @endif
    </div>
</x-layouts::app>