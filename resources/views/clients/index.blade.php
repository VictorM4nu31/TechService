<x-layouts::app :title="__('Clientes')">
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1">{{ __('Clientes Externos') }}</flux:heading>
                <flux:subheading>
                    {{ __('Gestiona las empresas y personas que reciben tus servicios de mantenimiento.') }}
                </flux:subheading>
            </div>
            <flux:button icon="plus" variant="primary" :href="route('clients.create')" wire:navigate>
                {{ __('Nuevo Cliente') }}
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
                    <flux:table.column>{{ __('Email') }}</flux:table.column>
                    <flux:table.column>{{ __('Teléfono') }}</flux:table.column>
                    <flux:table.column>{{ __('RFC / Tax ID') }}</flux:table.column>
                    <flux:table.column>{{ __('Equipos') }}</flux:table.column>
                    <flux:table.column>{{ __('Acciones') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($clients as $client)
                        <flux:table.row :key="$client->id">
                            <flux:table.cell class="font-medium">
                                <flux:link :href="route('clients.show', $client)" wire:navigate>
                                    {{ $client->name }}
                                </flux:link>
                            </flux:table.cell>
                            <flux:table.cell class="text-zinc-400">
                                {{ $client->contact_email ?? '—' }}
                            </flux:table.cell>
                            <flux:table.cell class="text-zinc-400">
                                {{ $client->contact_phone ?? '—' }}
                            </flux:table.cell>
                            <flux:table.cell class="text-zinc-400 font-mono text-xs">
                                {{ $client->tax_id ?? '—' }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="blue" size="sm">{{ $client->equipment_count }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square"
                                        :href="route('clients.edit', $client)" wire:navigate x-data
                                        x-tooltip="'{{ __('Editar') }}'" />
                                    <form method="POST" action="{{ route('clients.destroy', $client) }}"
                                        onsubmit="return confirm('{{ __('¿Eliminar este cliente? Se desvinculará de todos sus equipos.') }}')">
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
                                    <flux:icon name="building-office" size="lg" />
                                    <flux:text>{{ __('No hay clientes registrados.') }}</flux:text>
                                    <flux:button size="sm" variant="primary" :href="route('clients.create')" wire:navigate>
                                        {{ __('Registrar el primero') }}
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>

        @if($clients->hasPages())
            <div class="mt-2">{{ $clients->links() }}</div>
        @endif
    </div>
</x-layouts::app>