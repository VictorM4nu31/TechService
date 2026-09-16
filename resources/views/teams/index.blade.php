<x-layouts::app :title="__('Equipos')">
    <div class="flex flex-col gap-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('Gestión de Equipos') }}</flux:heading>
            <flux:subheading>{{ __('Organiza a los técnicos en equipos especializados.') }}</flux:subheading>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($teams as $team)
                <flux:card class="flex flex-col gap-4">
                    <flux:heading size="lg">{{ $team->name }}</flux:heading>

                    <flux:text>{{ $team->description }}</flux:text>

                    <div class="mt-2">
                        <flux:text size="sm" class="font-medium mb-1">{{ __('Líder:') }}</flux:text>
                        <div class="flex items-center gap-2">
                            @if ($team->leader)
                                <div
                                    class="size-6 rounded-full bg-blue-500 flex items-center justify-center text-[10px] text-white font-bold">
                                    {{ substr($team->leader->name, 0, 1) }}
                                </div>
                            @else
                                <div
                                    class="size-6 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center text-zinc-500">
                                    <flux:icon name="user" size="xs" />
                                </div>
                            @endif
                            <flux:text size="sm">{{ $team->leader?->name ?? __('Sin líder') }}</flux:text>
                        </div>
                    </div>

                    <div class="mt-2">
                        <flux:text size="sm" class="font-medium mb-1">{{ __('Miembros:') }}</flux:text>
                        <div class="flex -space-x-2 overflow-hidden">
                            @foreach ($team->members as $member)
                                <div class="size-8 rounded-full bg-zinc-800 border-2 border-zinc-900 flex items-center justify-center text-[10px] font-bold"
                                    title="{{ $member->name }}">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </flux:card>
            @empty
                <div class="col-span-full py-12 text-center">
                    <flux:text class="text-zinc-500">{{ __('No hay equipos creados aún.') }}</flux:text>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts::app>