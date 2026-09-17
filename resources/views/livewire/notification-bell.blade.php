<div wire:poll.30s>
    <flux:dropdown position="right" align="start">
        <flux:button variant="ghost" icon="bell" class="relative w-full justify-start text-signal-muted hover:text-signal-ink">
            {{ __('Notificaciones') }}
            @if($unreadCount > 0)
                <span class="ml-auto flex size-5 items-center justify-center rounded-full bg-signal-error font-mono text-[10px] text-white">{{ min(9, $unreadCount) }}</span>
            @endif
        </flux:button>
        <flux:menu class="w-80 border-signal-border bg-signal-elevated">
            <div class="flex items-center justify-between px-3 py-2">
                <flux:heading size="sm">{{ __('Notificaciones') }}</flux:heading>
                @if($unreadCount > 0)
                    <button type="button" wire:click="markAllRead" class="text-xs text-signal-accent hover:text-signal-ink">{{ __('Marcar leídas') }}</button>
                @endif
            </div>
            <flux:menu.separator />
            @forelse($notifications as $notification)
                <a href="{{ $notification->data['url'] ?? route('dashboard') }}" wire:navigate class="block px-3 py-2 hover:bg-signal-canvas">
                    <div class="flex items-start gap-2">
                        <span class="mt-1.5 size-2 shrink-0 rounded-full {{ $notification->read_at ? 'bg-signal-muted' : 'bg-signal-accent' }}"></span>
                        <div class="min-w-0">
                            <span class="block truncate text-xs font-medium text-signal-ink">{{ $notification->data['title'] ?? __('Actualización') }}</span>
                            <span class="mt-0.5 block text-xs text-signal-muted">{{ $notification->data['message'] ?? '' }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-3 py-5 text-center text-xs text-signal-muted">{{ __('No hay notificaciones todavía.') }}</div>
            @endforelse
        </flux:menu>
    </flux:dropdown>
</div>
