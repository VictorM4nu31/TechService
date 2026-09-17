@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="{{ config('app.name', 'TechService') }}" subtitle="Mesa de señales" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-lg bg-signal-accent text-signal-ink font-mono text-xs font-bold">
            TS<span class="sr-only"> TechService</span>
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="{{ config('app.name', 'TechService') }}" subtitle="Mesa de señales" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-lg bg-signal-accent text-signal-ink font-mono text-xs font-bold">
            TS<span class="sr-only"> TechService</span>
        </x-slot>
    </flux:brand>
@endif
