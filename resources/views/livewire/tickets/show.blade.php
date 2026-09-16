<?php

use App\Enums\TicketStatus;
use App\Models\User;
use App\Models\Ticket;
use App\Services\TicketService;
use function Livewire\Volt\{state, on, computed};

state(['ticket', 'newComment' => '', 'attachments' => []]);

$addComment = function (TicketService $ticketService) {
    $this->authorize('view', $this->ticket);

    $this->validate([
        'newComment' => ['required', 'string', 'min:3', 'max:5000'],
        'attachments' => ['nullable', 'array', 'max:5'],
        'attachments.*' => ['file', 'mimes:png,jpg,jpeg,pdf', 'max:10240'],
    ], [
        'newComment.required' => 'Escribe un comentario antes de publicar.',
        'newComment.min' => 'El comentario debe tener al menos :min caracteres.',
        'newComment.max' => 'El comentario no puede superar los :max caracteres.',
        'attachments.max' => 'Puedes adjuntar hasta :max archivos.',
        'attachments.*.mimes' => 'Los archivos adjuntos deben ser PNG, JPG o PDF.',
        'attachments.*.max' => 'Cada archivo adjunto no debe superar los 10MB.',
    ]);

    $ticketService->addComment($this->ticket, $this->newComment, $this->attachments);
    $this->newComment = '';
    $this->attachments = [];
    $this->ticket->refresh();
};

$removeAttachment = function (int $index) {
    unset($this->attachments[$index]);
    $this->attachments = array_values($this->attachments);
};

$updateStatus = function (string $statusValue, TicketService $ticketService) {
    abort_unless(auth()->user()->hasAnyRole(['Admin', 'Agente']), 403);

    $status = TicketStatus::tryFrom($statusValue);
    abort_unless($status !== null, 422);

    $ticketService->updateStatus($this->ticket, $status);
    $this->ticket->refresh();
};

$assignTo = function ($userId, TicketService $ticketService) {
    abort_unless(auth()->user()->hasAnyRole(['Admin', 'Agente']), 403);

    abort_unless(is_numeric($userId), 422);

    $ticketService->assignTicket($this->ticket, (int) $userId);
    $this->ticket->refresh();
};

$statuses = computed(fn () => TicketStatus::cases());
$agents = computed(fn () => User::role('Agente')->get());
$recentActivities = computed(fn () => $this->ticket->activities()->latest()->take(5)->get());

?>
<div>
    <div class="flex flex-col gap-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-zinc-500">
            <a href="{{ route('dashboard') }}" wire:navigate
                class="hover:text-zinc-300 transition-colors">{{ __('Panel') }}</a>
            <span>/</span>
            <a href="{{ route('tickets.index') }}" wire:navigate
                class="hover:text-zinc-300 transition-colors">{{ __('Tickets') }}</a>
            <span>/</span>
            <span class="text-zinc-300 truncate max-w-xs">#{{ $ticket->id }} {{ $ticket->title }}</span>
        </nav>

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-800 pb-6">
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <flux:heading size="xl" level="1">#{{ $ticket->id }} - {{ $ticket->title }}</flux:heading>
                    <flux:badge :color="$ticket->status->color()" size="sm">
                        {{ $ticket->status->label() }}
                    </flux:badge>
                </div>
                <flux:subheading>
                    {{ __('Creado el') }} {{ $ticket->created_at->format('d/m/Y H:i') }} {{ __('por') }} <span
                        class="font-medium text-zinc-200">{{ $ticket->creator->name }}</span>
                </flux:subheading>
            </div>

            <div class="flex items-center gap-3">
                @role('Admin|Agente')
                <flux:dropdown>
                    <flux:button variant="filled" icon="arrow-path" wire:loading.attr="disabled"
                        wire:target="updateStatus">{{ __('Cambiar Estado') }}</flux:button>
                    <flux:menu>
                        @foreach($this->statuses as $status)
                            <flux:menu.item wire:click="updateStatus('{{ $status->value }}')">{{ $status->label() }}
                            </flux:menu.item>
                        @endforeach
                    </flux:menu>
                </flux:dropdown>

                <flux:dropdown>
                    <flux:button variant="primary" color="blue" icon="user-plus">{{ __('Asignar') }}</flux:button>
                    <flux:menu>
                        @foreach($this->agents as $agent)
                            <flux:menu.item wire:click="assignTo({{ $agent->id }})">{{ $agent->name }}</flux:menu.item>
                        @endforeach
                    </flux:menu>
                </flux:dropdown>
                @endrole

                @can('update', $ticket)
                    <flux:button :href="route('tickets.edit', $ticket)" wire:navigate variant="ghost" icon="pencil-square">
                        {{ __('Editar') }}
                    </flux:button>
                @endcan
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Contenido Principal --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Descripción --}}
                <flux:card class="bg-zinc-900 border-zinc-800 space-y-4">
                    <flux:heading size="lg">{{ __('Descripción') }}</flux:heading>
                    <flux:text class="text-zinc-300 whitespace-pre-wrap leading-relaxed">
                        {{ $ticket->description }}
                    </flux:text>

                    @if($ticket->getMedia('attachments')->count() > 0)
                        <div class="pt-4 border-t border-zinc-800">
                            <flux:heading size="sm" class="mb-3">{{ __('Archivos Adjuntos') }}</flux:heading>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($ticket->getMedia('attachments') as $media)
                                    <a href="{{ $media->getUrl() }}" target="_blank"
                                        class="group relative aspect-square rounded-lg bg-zinc-800 flex items-center justify-center overflow-hidden hover:ring-2 hover:ring-blue-500 transition-all">
                                        @if(str_contains($media->mime_type, 'image'))
                                            <img src="{{ $media->getUrl() }}"
                                                class="object-cover size-full opacity-80 group-hover:opacity-100 transition-opacity" />
                                        @else
                                            <flux:icon name="document" size="lg" class="text-zinc-500" />
                                            <span
                                                class="absolute bottom-0 inset-x-0 bg-black/60 p-1 text-[10px] text-center truncate">{{ $media->file_name }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </flux:card>

                {{-- Comentarios --}}
                <div class="space-y-6">
                    <flux:heading size="lg" class="flex items-center gap-2">
                        {{ __('Comentarios') }}
                        <span
                            class="text-xs bg-zinc-800 px-2 py-0.5 rounded-full text-zinc-400">{{ $ticket->comments->count() }}</span>
                    </flux:heading>

                    <div class="space-y-4">
                        @foreach($ticket->comments as $comment)
                            <div class="flex gap-4">
                                <div
                                    class="size-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold shrink-0">
                                    {{ substr($comment->user->name, 0, 1) }}
                                </div>
                                <div class="flex-1 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <flux:text class="font-bold text-zinc-100">{{ $comment->user->name }}
                                        </flux:text>
                                        <flux:text size="xs" class="text-zinc-500">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </flux:text>
                                    </div>
                                    <flux:card class="bg-zinc-800/50 border-zinc-700">
                                        <flux:text class="text-zinc-300 leading-relaxed">{{ $comment->content }}
                                        </flux:text>
                                    </flux:card>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Nuevo Comentario --}}
                    <flux:card class="bg-zinc-900 border-zinc-800 space-y-4">
                        @if ($errors->any())
                            <div
                                class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg text-sm">
                                <flux:icon name="exclamation-triangle" size="sm" />
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <flux:textarea wire:model="newComment" x-data x-autosize
                            placeholder="{{ __('Escribe un comentario o actualización...') }}" rows="3" />

                        @if ($attachments)
                            <div class="flex flex-wrap gap-2">
                                @foreach ($attachments as $index => $attachment)
                                    <span
                                        class="inline-flex items-center gap-1.5 bg-zinc-800 border border-zinc-700 rounded-full pl-3 pr-1.5 py-1 text-xs text-zinc-300">
                                        <flux:icon name="document" size="xs" class="text-zinc-500" />
                                        <span class="max-w-40 truncate">{{ $attachment->getFilename() }}</span>
                                        <button type="button" wire:click="removeAttachment({{ $index }})"
                                            class="p-0.5 rounded-full text-zinc-500 hover:text-red-400 transition-colors">
                                            <flux:icon name="x-mark" size="xs" />
                                        </button>
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center justify-between">
                            <div class="relative">
                                <flux:button variant="ghost" icon="paper-clip" size="sm"
                                    wire:loading.attr="disabled" wire:target="addComment">
                                    {{ __('Adjuntar') }}
                                </flux:button>
                                <input type="file" wire:model="attachments" multiple
                                    class="absolute inset-0 opacity-0 cursor-pointer" />
                            </div>
                            <flux:button wire:click="addComment" wire:loading.attr="disabled" wire:target="addComment"
                                variant="primary" color="blue" size="sm" icon="paper-airplane">
                                <span wire:loading.remove
                                    wire:target="addComment">{{ __('Publicar Comentario') }}</span>
                                <span wire:loading wire:target="addComment" class="flex items-center gap-2">
                                    <flux:icon name="arrow-path" size="xs" class="animate-spin" />
                                    {{ __('Publicando...') }}
                                </span>
                            </flux:button>
                        </div>
                    </flux:card>
                </div>
            </div>

            {{-- Detalles Lateral --}}
            <div class="space-y-6">
                <flux:card class="bg-zinc-900 border-zinc-800" x-data="{ expanded: true }">
                    <flux:heading size="md"
                        class="border-b border-zinc-800 pb-4 cursor-pointer flex justify-between items-center select-none"
                        @click="expanded = !expanded">
                        {{ __('Detalles de la Solicitud') }}
                        <flux:icon name="chevron-down" size="xs" class="transition-transform duration-200"
                            x-bind:class="expanded ? 'rotate-180' : ''" />
                    </flux:heading>

                    <div class="space-y-4 pt-6" x-show="expanded" x-collapse>
                        <div>
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">{{ __('Responsable') }}
                            </flux:text>
                            <div class="flex items-center gap-2 mt-1">
                                @if($ticket->assignee)
                                    <div
                                        class="size-6 rounded-full bg-blue-500 flex items-center justify-center text-[10px] text-white font-bold">
                                        {{ substr($ticket->assignee->name, 0, 1) }}
                                    </div>
                                    <flux:text class="font-medium">{{ $ticket->assignee->name }}</flux:text>
                                @else
                                    <flux:text class="text-zinc-500 italic">{{ __('No asignado') }}</flux:text>
                                @endif
                            </div>
                        </div>

                        <div>
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">{{ __('Prioridad') }}
                            </flux:text>
                            <div class="mt-1">
                                <flux:badge :color="$ticket->priority->color()" variant="outline">
                                    {{ $ticket->priority->label() }}
                                </flux:badge>
                            </div>
                        </div>

                        <div>
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">{{ __('Categoría') }}
                            </flux:text>
                            <div class="flex items-center gap-2 mt-1">
                                <flux:icon :name="$ticket->category->icon()" size="xs" class="text-zinc-400" />
                                <flux:text>{{ $ticket->category->label() }}</flux:text>
                            </div>
                        </div>

                        <div>
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">
                                {{ __('Tipo de Mantenimiento') }}
                            </flux:text>
                            <div class="flex items-center gap-2 mt-1">
                                <flux:icon
                                    :name="match($ticket->maintenance_type){'Hardware'=>'cpu-chip','Software'=>'code-bracket','Red'=>'wifi','Seguridad'=>'lock-closed',default=>'wrench'}"
                                    size="xs" class="text-zinc-400" />
                                <flux:text>{{ $ticket->maintenance_type ?? __('No especificado') }}</flux:text>
                            </div>
                        </div>

                        <div>
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">{{ __('Equipo') }}
                            </flux:text>
                            <flux:text class="block mt-1 font-medium">
                                @if($ticket->equipment)
                                    {{ $ticket->equipment->name }}
                                    ({{ $ticket->equipment->serial_number ?? 'S/N' }})
                                @else
                                    {{ __('No especificado') }}
                                @endif
                            </flux:text>
                        </div>

                        <div>
                            <flux:text size="xs" class="text-zinc-500 uppercase font-bold">{{ __('Ubicación') }}
                            </flux:text>
                            <flux:text class="block mt-1 font-medium">
                                {{ $ticket->location ?? __('No especificado') }}
                            </flux:text>
                        </div>
                    </div>
                </flux:card>

                {{-- Registro de Actividad Lateral --}}
                <flux:card class="bg-zinc-900 border-zinc-800" x-data="{ expanded: true }">
                    <flux:heading size="md"
                        class="border-b border-zinc-800 pb-4 cursor-pointer flex justify-between items-center select-none"
                        @click="expanded = !expanded">
                        {{ __('Historial Reciente') }}
                        <flux:icon name="chevron-down" size="xs" class="transition-transform duration-200"
                            x-bind:class="expanded ? 'rotate-180' : ''" />
                    </flux:heading>

                    <div x-show="expanded" x-collapse class="pt-6">
                        <div
                            class="space-y-4 relative before:absolute before:inset-0 before:ml-2.5 before:-translate-x-px before:h-full before:w-0.5 before:bg-zinc-800">
                            @foreach($this->recentActivities as $activity)
                                <div class="relative flex items-start gap-4 pl-6">
                                    <div
                                        class="absolute left-0 size-5 rounded-full bg-zinc-900 border-2 border-zinc-800 flex items-center justify-center z-10">
                                        <div class="size-2 rounded-full bg-blue-500"></div>
                                    </div>
                                    <div>
                                        <flux:text size="xs" class="text-zinc-400">
                                            <span class="font-bold text-zinc-100">{{ $activity->user->name }}</span>
                                            {{ $activity->description }}
                                        </flux:text>
                                        <flux:text size="xs" class="text-zinc-600 block mt-0.5">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </flux:text>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </flux:card>
            </div>
        </div>
    </div>
</div>
