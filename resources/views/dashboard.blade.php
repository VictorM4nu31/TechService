<x-layouts::app :title="__('Dashboard')">
    <div class="flex flex-col gap-8">
        {{-- Header --}}
        <div class="flex items-center justify-between bg-zinc-900/50 p-4 -m-4 mb-4 border-b border-zinc-800">
            <flux:heading size="xl" level="1">{{ __('Panel de Control') }}</flux:heading>
            
            <div class="flex items-center gap-4">
                <div class="relative hidden md:block">
                    <flux:input variant="filled" placeholder="{{ __('Buscar tickets...') }}" class="w-64" icon="magnifying-glass" />
                </div>
                <flux:button variant="primary" icon="plus" color="blue" :href="route('tickets.create')" wire:navigate>
                    {{ __('Nuevo Ticket') }}
                </flux:button>
                <div class="relative">
                    <flux:button variant="ghost" icon="bell" />
                    <span class="absolute top-0 right-0 size-4 bg-red-500 text-white text-[10px] flex items-center justify-center rounded-full border-2 border-zinc-900">{{ $stats['open'] }}</span>
                </div>
            </div>
        </div>

        {{-- Top Charts Row --}}
        <div class="grid gap-6 md:grid-cols-3">
            {{-- Line Chart Placeholder --}}
            <flux:card x-data="{ shown: false }" x-intersect.once.margin.-10%.0px="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="flex flex-col gap-4 bg-zinc-900 border-zinc-800 transition-all duration-700 ease-out">
                <div class="flex items-center justify-between">
                    <flux:text size="sm" class="font-medium text-zinc-400">{{ __('Tickets Creados vs Resueltos') }}</flux:text>
                </div>
                <div class="h-48 w-full relative">
                    <svg viewBox="0 0 400 200" class="w-full h-full">
                        <path d="M0,150 Q100,50 200,100 T400,80" fill="none" stroke="#3b82f6" stroke-width="3" />
                        <path d="M0,160 Q100,120 200,140 T400,120" fill="none" stroke="#10b981" stroke-width="3" />
                        {{-- Gradients and Area --}}
                        <path d="M0,150 Q100,50 200,100 T400,80 L400,200 L0,200 Z" fill="url(#blue-grad)" opacity="0.1" />
                    </svg>
                    <defs>
                        <linearGradient id="blue-grad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#3b82f6" />
                            <stop offset="100%" stop-color="#3b82f6" stop-opacity="0" />
                        </linearGradient>
                    </defs>
                </div>
                <div class="flex gap-4 mt-auto">
                    <div class="flex items-center gap-2">
                        <div class="size-2 rounded-full bg-blue-500"></div>
                        <flux:text size="xs">{{ __('Creados') }} ({{ $stats['total'] }})</flux:text>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="size-2 rounded-full bg-green-500"></div>
                        <flux:text size="xs">{{ __('Resueltos') }} ({{ $stats['resolved'] }})</flux:text>
                    </div>
                </div>
            </flux:card>

            {{-- Donut Chart --}}
            <flux:card x-data="{ shown: false }" x-intersect.once.margin.-10%.0px="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="flex flex-col gap-4 bg-zinc-900 border-zinc-800 transition-all duration-700 delay-100 ease-out">
                <flux:text size="sm" class="font-medium text-zinc-400">{{ __('Distribución por Categoría') }}</flux:text>
                <div class="flex items-center justify-center p-4">
                    <div class="relative size-40">
                        <svg viewBox="0 0 36 36" class="size-full transform -rotate-90">
                            @php 
                                $totalTickets = $stats['total'] ?: 1;
                                $offset = 0;
                                $colors = ['#10b981', '#3b82f6', '#ef4444'];
                            @endphp
                            @foreach($categories as $index => $category)
                                @php 
                                    $percent = ($category->tickets_count / $totalTickets) * 100;
                                    $dash = $percent . ", 100";
                                @endphp
                                <circle cx="18" cy="18" r="16" fill="none" stroke="{{ $colors[$index % 3] }}" stroke-width="4" stroke-dasharray="{{ $dash }}" stroke-dashoffset="-{{ $offset }}" />
                                @php $offset += $percent @endphp
                            @endforeach
                        </svg>
                    </div>
                </div>
                <div class="grid gap-2">
                    @foreach($categories as $index => $category)
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <div class="size-2 rounded-full" style="background-color: {{ $colors[$index % 3] }}"></div>
                                <flux:text>{{ $category->name }}</flux:text>
                            </div>
                            <flux:text class="font-bold">{{ $category->tickets_count }}</flux:text>
                        </div>
                    @endforeach
                </div>
            </flux:card>

            {{-- Bar Chart --}}
            <flux:card x-data="{ shown: false }" x-intersect.once.margin.-10%.0px="shown = true" :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'" class="flex flex-col gap-4 bg-zinc-900 border-zinc-800 transition-all duration-700 delay-200 ease-out">
                <flux:heading size="md">{{ __('Tickets por Estado') }}</flux:heading>
                <div class="flex flex-col gap-3 mt-4">
                    @php 
                        $stateStats = [
                            ['label' => 'Abierto', 'count' => $stats['open'], 'color' => 'bg-blue-500', 'percent' => ($stats['open'] / $totalTickets) * 100],
                            ['label' => 'En Progreso', 'count' => $stats['in_progress'], 'color' => 'bg-yellow-500', 'percent' => ($stats['in_progress'] / $totalTickets) * 100],
                            ['label' => 'Cerrado', 'count' => $stats['resolved'], 'color' => 'bg-green-500', 'percent' => ($stats['resolved'] / $totalTickets) * 100],
                        ];
                    @endphp
                    @foreach($stateStats as $stat)
                        <div class="space-y-1">
                            <div class="flex justify-between text-xs mb-1">
                                <flux:text>{{ $stat['label'] }}</flux:text>
                                <flux:text>{{ round($stat['percent']) }}%</flux:text>
                            </div>
                            <div class="h-3 w-full bg-zinc-800 rounded-full overflow-hidden">
                                <div class="h-full {{ $stat['color'] }}" style="width: {{ $stat['percent'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </flux:card>
        </div>

        {{-- Botom Grid --}}
        <div class="grid gap-6 md:grid-cols-2">
            {{-- Tickets Grid --}}
            <div class="flex flex-col gap-6">
                <div>
                    <flux:heading size="lg">{{ __('Tickets Críticos') }}</flux:heading>
                </div>
                @forelse($criticalTickets as $ticket)
                    <flux:card class="flex gap-4 items-start bg-zinc-900 border-zinc-800 hover:border-red-500/50 transition-colors pointer-cursor" :href="route('tickets.show', $ticket)" wire:navigate>
                        <div class="p-2 rounded-lg bg-blue-500/10 text-blue-500">
                            <flux:icon name="wrench" size="sm" />
                        </div>
                        <div class="flex-1 space-y-1">
                            <div class="flex items-center gap-2">
                                <flux:text size="xs" class="text-zinc-500">#{{ $ticket->id }}</flux:text>
                                <flux:badge color="red" size="sm" variant="solid">{{ $ticket->priority->name }}</flux:badge>
                                <flux:spacer />
                                <flux:badge :color="match($ticket->status->name){'Abierto'=>'blue','En Progreso'=>'yellow','Cerrado'=>'green',default=>'zinc'}" size="sm">{{ $ticket->status->name }}</flux:badge>
                            </div>
                            <flux:heading size="md" class="mt-1">{{ $ticket->title }}</flux:heading>
                            <div class="flex items-center gap-4 text-xs text-zinc-500">
                                <span class="flex items-center gap-1"><flux:icon name="map-pin" size="xs" /> {{ $ticket->location ?? __('Sin ubicación') }}</span>
                                <span class="flex items-center gap-1"><flux:icon name="clock" size="xs" /> {{ $ticket->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </flux:card>
                @empty
                    <flux:card class="bg-zinc-900 border-zinc-800 text-center py-8">
                        <flux:text class="text-zinc-500">{{ __('No hay tickets críticos pendientes') }}</flux:text>
                    </flux:card>
                @endforelse

                <div class="flex items-center justify-between mt-4">
                    <flux:heading size="lg">{{ __('Tickets Recientes') }}</flux:heading>
                    <flux:link :href="route('tickets.index')" size="sm">{{ __('Ver todos') }} →</flux:link>
                </div>
                @foreach($recentTickets as $ticket)
                    <flux:card class="flex gap-4 items-start bg-zinc-900 border-zinc-800 hover:border-zinc-700 transition-colors pointer-cursor" :href="route('tickets.show', $ticket)" wire:navigate>
                        <div class="p-2 rounded-lg bg-blue-500/10 text-blue-500">
                            <flux:icon name="wrench" size="sm" />
                        </div>
                        <div class="flex-1 space-y-1">
                            <div class="flex items-center gap-2">
                                <flux:text size="xs" class="text-zinc-500">#{{ $ticket->id }}</flux:text>
                                <flux:badge size="sm" variant="outline">{{ $ticket->priority->name }}</flux:badge>
                                <flux:spacer />
                                <flux:badge :color="match($ticket->status->name){'Abierto'=>'blue','En Progreso'=>'yellow','Cerrado'=>'green',default=>'zinc'}" size="sm">{{ $ticket->status->name }}</flux:badge>
                            </div>
                            <flux:heading size="md" class="mt-1">{{ $ticket->title }}</flux:heading>
                            <div class="flex items-center gap-4 text-xs text-zinc-500">
                                <span class="flex items-center gap-1"><flux:icon name="map-pin" size="xs" /> {{ $ticket->location ?? __('Sin ubicación') }}</span>
                                <span class="flex items-center gap-1"><flux:icon name="clock" size="xs" /> {{ $ticket->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </flux:card>
                @endforeach
            </div>

            {{-- Recent Activity --}}
            <flux:card class="bg-zinc-900 border-zinc-800 flex flex-col gap-6">
                <flux:heading size="lg">{{ __('Actividad Reciente') }}</flux:heading>
                
                <div class="relative space-y-8 before:absolute before:inset-0 before:ml-5 before:-translate-x-px before:h-full before:w-0.5 before:bg-zinc-800">
                    @forelse($activities as $activity)
                        <div class="relative flex items-start gap-4">
                            <div class="flex size-10 items-center justify-center rounded-full bg-zinc-900 ring-4 ring-zinc-900 z-10">
                                @php 
                                    $actConfig = match($activity->type) {
                                        'created' => ['icon' => 'plus-circle', 'color' => 'blue'],
                                        'assigned' => ['icon' => 'user-plus', 'color' => 'purple'],
                                        'commented' => ['icon' => 'chat-bubble-left-ellipsis', 'color' => 'gray'],
                                        'status_updated', 'resolved' => ['icon' => 'check-circle', 'color' => 'green'],
                                        default => ['icon' => 'information-circle', 'color' => 'zinc'],
                                    };
                                @endphp
                                <div class="size-8 rounded-full bg-{{ $actConfig['color'] }}-500/20 text-{{ $actConfig['color'] }}-500 flex items-center justify-center">
                                    <flux:icon :name="$actConfig['icon']" size="xs" />
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-center text-sm">
                                    <flux:text><span class="font-bold text-zinc-100">{{ $activity->user->name }}</span> {{ $activity->description }}</flux:text>
                                </div>
                                @if($activity->ticket)
                                    <flux:link :href="route('tickets.show', $activity->ticket)" size="sm" class="text-blue-500 font-medium mt-0.5" wire:navigate>
                                        {{ $activity->ticket->title }}
                                    </flux:link>
                                @endif
                                <flux:text size="xs" class="text-zinc-500 mt-2">{{ $activity->created_at->diffForHumans() }}</flux:text>
                            </div>
                        </div>
                    @empty
                        <flux:text class="text-zinc-500 text-center py-4">{{ __('No hay actividad reciente') }}</flux:text>
                    @endforelse
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts::app>
