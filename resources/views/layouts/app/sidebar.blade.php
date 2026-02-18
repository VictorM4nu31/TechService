<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Principal')" class="grid text-xs font-semibold uppercase text-zinc-500">
                <flux:sidebar.item icon="squares-2x2" :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Panel de Control') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="ticket" :href="route('tickets.index')"
                    :current="request()->routeIs('tickets.index')" wire:navigate>
                    {{ __('Todos los Tickets') }}
                    <flux:badge variant="light" color="blue" class="ml-auto" size="sm">{{ $sidebarStats['total'] }}
                    </flux:badge>
                </flux:sidebar.item>
                <flux:sidebar.item icon="plus" :href="route('tickets.create')"
                    :current="request()->routeIs('tickets.create')" wire:navigate>
                    {{ __('Nuevo Ticket') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

            {{-- Admin & Agente --}}
            @hasanyrole('Admin|Agente')
            <flux:sidebar.group :heading="__('Por Estado')"
                class="grid text-xs font-semibold uppercase text-zinc-500 mt-4">
                <flux:sidebar.item icon="clock" :href="route('tickets.index', ['status' => 'Abierto'])" wire:navigate>
                    {{ __('Abiertos') }}
                    <flux:badge variant="light" color="blue" class="ml-auto" size="sm">{{ $sidebarStats['open'] }}
                    </flux:badge>
                </flux:sidebar.item>
                <flux:sidebar.item icon="exclamation-triangle"
                    :href="route('tickets.index', ['status' => 'En Progreso'])" wire:navigate>
                    {{ __('En Progreso') }}
                    <flux:badge variant="light" color="yellow" class="ml-auto" size="sm">
                        {{ $sidebarStats['in_progress'] }}
                    </flux:badge>
                </flux:sidebar.item>
                <flux:sidebar.item icon="check-circle" :href="route('tickets.index', ['status' => 'Cerrado'])"
                    wire:navigate>
                    {{ __('Resueltos') }}
                    <flux:badge variant="light" color="green" class="ml-auto" size="sm">{{ $sidebarStats['resolved'] }}
                    </flux:badge>
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.group :heading="__('Por Categoría')"
                class="grid text-xs font-semibold uppercase text-zinc-500 mt-4">
                <flux:sidebar.item icon="shield-check" :href="route('tickets.index', ['category' => 'Preventivo'])"
                    wire:navigate>
                    {{ __('Preventivo') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="wrench" :href="route('tickets.index', ['category' => 'Correctivo'])"
                    wire:navigate>
                    {{ __('Correctivo') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="exclamation-circle"
                    :href="route('tickets.index', ['category' => 'Emergencia'])" wire:navigate>
                    {{ __('Emergencia') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
            @endhasanyrole

            {{-- Cliente --}}
            @role('Cliente')
            <flux:sidebar.group :heading="__('Mis Solicitudes')"
                class="grid text-xs font-semibold uppercase text-zinc-500 mt-4">
                <flux:sidebar.item icon="ticket" :href="route('tickets.index')"
                    :current="request()->routeIs('tickets.index')" wire:navigate>
                    {{ __('Mis Tickets') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="plus-circle" :href="route('tickets.create')"
                    :current="request()->routeIs('tickets.create')" wire:navigate>
                    {{ __('Nueva Solicitud') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
            @endrole

            {{-- Solo Admin --}}
            @role('Admin')
            <flux:sidebar.group :heading="__('Gestión')"
                class="grid text-xs font-semibold uppercase text-zinc-500 mt-4">
                <flux:sidebar.item icon="chart-bar" href="#" wire:navigate>
                    {{ __('Reportes') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="user-group" :href="route('teams.index')"
                    :current="request()->routeIs('teams.index')" wire:navigate>
                    {{ __('Equipos') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="cog-6-tooth" :href="route('profile.edit')" wire:navigate>
                    {{ __('Configuración') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
            @endrole
        </flux:sidebar.nav>

        <flux:spacer />

        <x-desktop-user-menu class="hidden lg:block mb-4" />
    </flux:sidebar>


    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-3" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>