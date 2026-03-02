<x-layouts::app :title="$client->name">
    <div class="flex flex-col gap-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-zinc-500">
            <a href="{{ route('dashboard') }}" wire:navigate
                class="hover:text-zinc-300 transition-colors">{{ __('Panel') }}</a>
            <span>/</span>
            <a href="{{ route('clients.index') }}" wire:navigate
                class="hover:text-zinc-300 transition-colors">{{ __('Clientes') }}</a>
            <span>/</span>
            <span class="text-zinc-300 truncate max-w-xs">{{ $client->name }}</span>
        </nav>

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-800 pb-6">
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <flux:heading size="xl" level="1">{{ $client->name }}</flux:heading>
                </div>
                <flux:subheading>
                    {{ $client->contact_email ?? __('Sin email') }} · {{ $client->contact_phone ?? __('Sin teléfono') }}
                </flux:subheading>
            </div>
            <div class="flex items-center gap-3">
                <flux:button :href="route('clients.edit', $client)" wire:navigate variant="ghost" icon="pencil-square">
                    {{ __('Editar') }}
                </flux:button>
                <form method="POST" action="{{ route('clients.destroy', $client) }}"
                    onsubmit="return confirm('{{ __('¿Eliminar este cliente? Se desvinculará de todos sus equipos.') }}')">
                    @csrf
                    @method('DELETE')
                    <flux:button type="submit" variant="ghost" icon="trash" class="text-red-500 hover:text-red-400">
                        {{ __('Eliminar') }}
                    </flux:button>
                </form>
                <flux:button :href="route('clients.index')" wire:navigate variant="ghost" icon="arrow-left" size="sm" />
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Main Content: Equipos del Cliente --}}
            <div class="lg:col-span-2 space-y-6">
                <flux:card class="bg-zinc-900 border-zinc-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <flux:heading size="lg">{{ __('Equipos del Cliente') }}</flux:heading>
                        <flux:badge color="blue" size="sm">{{ $client->equipment->count() }}</flux:badge>
                    </div>
                    @forelse($client->equipment as $equip)
                        <a href="{{ route('equipment.show', $equip) }}" wire:navigate
                            class="flex items-center justify-between p-3 rounded-lg bg-zinc-800/50 hover:bg-zinc-800 transition-colors border border-zinc-700 group">
                            <div class="space-y-0.5">
                                <flux:text class="font-medium text-zinc-100 group-hover:text-blue-400 transition-colors">
                                    {{ $equip->name }}
                                </flux:text>
                                <flux:text size="xs" class="text-zinc-500">
                                    {{ $equip->brand ?? '' }} {{ $equip->model ?? '' }} · {{ $equip->type }}
                                </flux:text>
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:badge size="sm" variant="outline">{{ $equip->tickets->count() }} {{ __('tickets') }}
                                </flux:badge>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-8 text-zinc-500">
                            <flux:icon name="cpu-chip" size="lg" class="mx-auto mb-2 opacity-40" />
                            <flux:text>{{ __('Este cliente no tiene equipos registrados.') }}</flux:text>
                        </div>
                    @endforelse
                </flux:card>
            </div>

            {{-- Sidebar: Datos del Cliente --}}
            <div class="space-y-6">
                <flux:card class="bg-zinc-900 border-zinc-800 space-y-5">
                    <flux:heading size="md" class="border-b border-zinc-800 pb-3">{{ __('Datos del Cliente') }}
                    </flux:heading>
                    <div class="space-y-4">
                        <div>
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">{{ __('Email') }}</flux:text>
                            <flux:text class="mt-1 font-medium">{{ $client->contact_email ?? '—' }}</flux:text>
                        </div>
                        <div>
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">{{ __('Teléfono') }}
                            </flux:text>
                            <flux:text class="mt-1 font-medium">{{ $client->contact_phone ?? '—' }}</flux:text>
                        </div>
                        <div>
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">{{ __('RFC / Tax ID') }}
                            </flux:text>
                            <flux:text class="mt-1 font-medium font-mono text-sm">{{ $client->tax_id ?? '—' }}
                            </flux:text>
                        </div>
                        <div>
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">{{ __('Dirección') }}
                            </flux:text>
                            <flux:text class="mt-1 text-sm">{{ $client->address ?? '—' }}</flux:text>
                        </div>
                        <div class="pt-2 border-t border-zinc-800">
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">{{ __('Registro') }}
                            </flux:text>
                            <flux:text class="mt-1 text-sm" x-data
                                x-tooltip="'{{ $client->created_at->format('d/m/Y H:i') }}'">
                                {{ $client->created_at->diffForHumans() }}
                            </flux:text>
                        </div>
                    </div>
                </flux:card>
            </div>
        </div>
    </div>
</x-layouts::app>