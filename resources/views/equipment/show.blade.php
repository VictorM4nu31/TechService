<x-layouts::app :title="$equipment->name">
    <div class="flex flex-col gap-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-zinc-500">
            <a href="{{ route('dashboard') }}" wire:navigate
                class="hover:text-zinc-300 transition-colors">{{ __('Panel') }}</a>
            <span>/</span>
            <a href="{{ route('equipment.index') }}" wire:navigate
                class="hover:text-zinc-300 transition-colors">{{ __('Mis Equipos') }}</a>
            <span>/</span>
            <span class="text-zinc-300 truncate max-w-xs">{{ $equipment->name }}</span>
        </nav>

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-800 pb-6">
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <flux:heading size="xl" level="1">{{ $equipment->name }}</flux:heading>
                    <flux:badge variant="outline" size="sm">{{ $equipment->type }}</flux:badge>
                </div>
                <flux:subheading>
                    {{ __('Registrado por') }} <span
                        class="font-medium text-zinc-200">{{ $equipment->owner->name }}</span>
                </flux:subheading>
            </div>
            <div class="flex items-center gap-3">
                <flux:button :href="route('equipment.edit', $equipment)" wire:navigate variant="ghost"
                    icon="pencil-square">
                    {{ __('Editar') }}
                </flux:button>
                <form method="POST" action="{{ route('equipment.destroy', $equipment) }}"
                    onsubmit="return confirm('{{ __('¿Eliminar este equipo? Esta acción no se puede deshacer.') }}')">
                    @csrf
                    @method('DELETE')
                    <flux:button type="submit" variant="ghost" icon="trash" class="text-red-500 hover:text-red-400">
                        {{ __('Eliminar') }}
                    </flux:button>
                </form>
                <flux:button :href="route('equipment.index')" wire:navigate variant="ghost" icon="arrow-left"
                    size="sm" />
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Tickets asociados --}}
                <flux:card class="bg-zinc-900 border-zinc-800 space-y-4">
                    <flux:heading size="lg">{{ __('Tickets Asociados') }}</flux:heading>
                    @forelse($equipment->tickets as $ticket)
                        <a href="{{ route('tickets.show', $ticket) }}" wire:navigate
                            class="flex items-center justify-between p-3 rounded-lg bg-zinc-800/50 hover:bg-zinc-800 transition-colors border border-zinc-700 group">
                            <div class="space-y-0.5">
                                <flux:text class="font-medium text-zinc-100 group-hover:text-blue-400 transition-colors">
                                    #{{ $ticket->id }} — {{ $ticket->title }}
                                </flux:text>
                                <flux:text size="xs" class="text-zinc-500">{{ $ticket->created_at->format('d/m/Y') }}
                                </flux:text>
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:badge size="sm" :color="$ticket->status->color()">
                                    {{ $ticket->status->label() }}
                                </flux:badge>
                                <flux:text size="sm" class="text-zinc-400">
                                    [{{ $ticket->category->label() }}]
                                </flux:text>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-8 text-zinc-500">
                            <flux:icon name="ticket" size="lg" class="mx-auto mb-2 opacity-40" />
                            <flux:text>{{ __('Sin intervención técnica registrada.') }}</flux:text>
                        </div>
                    @endforelse
                </flux:card>
            </div>

            {{-- Sidebar: Ficha Técnica --}}
            <div class="space-y-6">
                <flux:card class="bg-zinc-900 border-zinc-800 space-y-5">
                    <flux:heading size="md" class="border-b border-zinc-800 pb-3">{{ __('Ficha Técnica') }}
                    </flux:heading>
                    <div class="space-y-4">
                        <div>
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">{{ __('Tipo') }}</flux:text>
                            <flux:text class="mt-1 font-medium">{{ $equipment->type }}</flux:text>
                        </div>
                        <div>
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">{{ __('Marca') }}</flux:text>
                            <flux:text class="mt-1 font-medium">{{ $equipment->brand ?? '—' }}</flux:text>
                        </div>
                        <div>
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">{{ __('Modelo') }}
                            </flux:text>
                            <flux:text class="mt-1 font-medium">{{ $equipment->model ?? '—' }}</flux:text>
                        </div>
                        <div>
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">{{ __('N° de Serie') }}
                            </flux:text>
                            <flux:text class="mt-1 font-medium font-mono text-sm">{{ $equipment->serial_number ?? '—' }}
                            </flux:text>
                        </div>
                        <div class="pt-2 border-t border-zinc-800">
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">{{ __('Cliente Externo') }}
                            </flux:text>
                            <flux:text class="mt-1 font-medium text-blue-400">
                                {{ $equipment->client->name ?? __('Sin Cliente') }}</flux:text>
                        </div>
                        <div>
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">{{ __('Contacto / Dueño') }}
                            </flux:text>
                            <flux:text class="mt-1 font-medium">{{ $equipment->owner->name }}</flux:text>
                        </div>
                        <div>
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">{{ __('Fecha Registro') }}
                            </flux:text>
                            <flux:text class="mt-1 text-sm" x-data
                                x-tooltip="'{{ $equipment->created_at->format('d/m/Y H:i') }}'">
                                {{ $equipment->created_at->diffForHumans() }}
                            </flux:text>
                        </div>
                    </div>
                </flux:card>

                <flux:button variant="primary" :href="route('tickets.create')" wire:navigate class="w-full" icon="plus">
                    {{ __('Abrir Ticket para este Equipo') }}
                </flux:button>
            </div>
        </div>
    </div>
</x-layouts::app>