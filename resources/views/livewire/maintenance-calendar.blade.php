<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ $monthName }} {{ $year }}</flux:heading>
            <flux:subheading>{{ __('Calendario de Órdenes de Trabajo y Mantenimientos') }}</flux:subheading>
        </div>
        <div class="flex items-center gap-2">
            <flux:button icon="chevron-left" variant="ghost" wire:click="previousMonth" />
            <flux:button variant="outline" wire:click="goToToday">
                {{ __('Hoy') }}
            </flux:button>
            <flux:button icon="chevron-right" variant="ghost" wire:click="nextMonth" />
        </div>
    </div>

    <div class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden">
        {{-- Days of week header --}}
        <div class="grid grid-cols-7 border-b border-zinc-800 bg-zinc-800/30">
            @foreach(['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] as $dayName)
                <div class="py-2 text-center text-xs font-bold text-zinc-500 uppercase">
                    {{ $dayName }}
                </div>
            @endforeach
        </div>

        {{-- Calendar Grid --}}
        <div class="grid grid-cols-7 auto-rows-[120px]">
            @foreach($daysInMonth as $day)
                <div class="border-r border-b border-zinc-800 p-2 {{ $day ? '' : 'bg-zinc-950/20' }}">
                    @if($day)
                        <div class="flex items-center justify-between mb-1">
                            <span
                                class="text-sm font-medium {{ $day == now()->day && $month == now()->month && $year == now()->year ? 'bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center' : 'text-zinc-400' }}">
                                {{ $day }}
                            </span>
                        </div>

                        <div class="space-y-1 overflow-y-auto max-h-[85px] no-scrollbar">
                            @if(isset($events[$day]))
                                @foreach($events[$day] as $event)
                                        <a href="{{ $event['kind'] === 'schedule' ? route('maintenance-schedules.edit', $event['id']) : route('tickets.show', $event['id']) }}" wire:navigate class="block truncate rounded border px-1.5 py-0.5 text-[10px] transition-colors hover:bg-zinc-800
                                                                  {{ match ($event['category'] ?? '') {
                                         'Preventivo' => 'bg-green-500/10 text-green-400 border-green-500/20',
                                         'Emergencia' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                         default => 'bg-blue-500/10 text-blue-400 border-blue-500/20'
                                     } }}">
                                            {{ $event['kind'] === 'schedule' ? '[P] ' : '' }}{{ $event['title'] }}
                                        </a>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex gap-4 text-xs">
        <div class="flex items-center gap-1.5 text-zinc-400">
            <span class="w-2.5 h-2.5 rounded bg-green-500/20 border border-green-500/30"></span>
            {{ __('Preventivo') }}
        </div>
        <div class="flex items-center gap-1.5 text-zinc-400">
            <span class="w-2.5 h-2.5 rounded bg-red-500/20 border border-red-500/30"></span>
            {{ __('Emergencia') }}
        </div>
        <div class="flex items-center gap-1.5 text-zinc-400">
            <span class="w-2.5 h-2.5 rounded bg-blue-500/20 border border-blue-500/30"></span>
            {{ __('Otros') }}
        </div>
    </div>
</div>
