@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="TechSupport Pro" subtitle="Sistema de Tickets" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-lg bg-accent text-white font-bold">
            TS
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="TechSupport Pro" subtitle="Sistema de Tickets" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-lg bg-accent text-white font-bold">
            TS
        </x-slot>
    </flux:brand>
@endif
