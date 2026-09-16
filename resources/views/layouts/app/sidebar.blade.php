<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header class="pb-0!">
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <style>
            /* Hide scrollbar for Chrome, Safari and Opera */
            [data-flux-sidebar] nav::-webkit-scrollbar,
            [data-flux-sidebar]::-webkit-scrollbar {
                display: none;
            }
            /* Hide scrollbar for IE, Edge and Firefox */
            [data-flux-sidebar] nav,
            [data-flux-sidebar] {
                -ms-overflow-style: none;  /* IE and Edge */
                scrollbar-width: none;  /* Firefox */
            }
        </style>

        <flux:sidebar.nav class="gap-y-1">
            {{-- Sección: General --}}
            <flux:sidebar.group :heading="__('General')" class="grid text-xs font-semibold uppercase text-zinc-500">
                <flux:sidebar.item icon="squares-2x2" :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Panel de Control') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

            {{-- Sección: Soporte Técnico --}}
            <flux:sidebar.group :heading="auth()->user()->hasRole('Cliente') ? __('Soporte') : __('Soporte Técnico')" class="grid text-xs font-semibold uppercase text-zinc-500 mt-2">
                @role('Cliente')
                <flux:sidebar.item icon="ticket" :href="route('tickets.index')"
                    :current="request()->routeIs('tickets.index') && !request()->hasAny(['status', 'category', 'search'])" wire:navigate>
                    {{ __('Mis Solicitudes') }}
                    <flux:badge variant="light" color="blue" class="ml-auto" size="sm">{{ $sidebarStats['total'] }}
                    </flux:badge>
                </flux:sidebar.item>
                <flux:sidebar.item icon="plus-circle" :href="route('tickets.create')"
                    :current="request()->routeIs('tickets.create')" wire:navigate>
                    {{ __('Nueva Solicitud') }}
                </flux:sidebar.item>
                @else
                <flux:sidebar.item icon="ticket" :href="route('tickets.index')"
                    :current="request()->routeIs('tickets.index') && !request()->hasAny(['status', 'category', 'search'])" wire:navigate>
                    {{ __('Todos los Tickets') }}
                    <flux:badge variant="light" color="blue" class="ml-auto" size="sm">{{ $sidebarStats['total'] }}
                    </flux:badge>
                </flux:sidebar.item>
                <flux:sidebar.item icon="plus" :href="route('tickets.create')"
                    :current="request()->routeIs('tickets.create')" wire:navigate>
                    {{ __('Nuevo Ticket') }}
                </flux:sidebar.item>
                @endrole
            </flux:sidebar.group>

            {{-- Sección de Accesos Directos --}}
            <flux:sidebar.group :heading="auth()->user()->hasRole('Cliente') ? __('Mis Pendientes') : __('Accesos Directos')"
                class="grid text-xs font-semibold uppercase text-zinc-500 mt-2">
                @role('Cliente')
                <flux:sidebar.item icon="clock" :href="route('tickets.index', ['status' => 'Abierto'])" wire:navigate>
                    {{ __('Tickets Abiertos') }}
                    <flux:badge variant="light" color="blue" class="ml-auto" size="sm">{{ $sidebarStats['open'] }}
                    </flux:badge>
                </flux:sidebar.item>
                @else
                <flux:sidebar.item icon="clock" :href="route('tickets.index', ['status' => 'Abierto'])" wire:navigate>
                    {{ __('Tickets Abiertos') }}
                    <flux:badge variant="light" color="blue" class="ml-auto" size="sm">{{ $sidebarStats['open'] }}
                    </flux:badge>
                </flux:sidebar.item>
                <flux:sidebar.item icon="fire" :href="route('tickets.index', ['category' => 'Emergencia'])" wire:navigate>
                    {{ __('Atención Urgente') }}
                </flux:sidebar.item>
                @endrole
            </flux:sidebar.group>

            {{-- Sección: Gestión CMMS / Equipos y Mantenimiento --}}
            <flux:sidebar.group :heading="auth()->user()->hasRole('Cliente') ? __('Equipos y Mantenimiento') : __('Gestión CMMS')"
                class="grid text-xs font-semibold uppercase text-zinc-500 mt-2">
                <flux:sidebar.item icon="cpu-chip" :href="route('equipment.index')"
                    :current="request()->routeIs('equipment.*')" wire:navigate>
                    {{ auth()->user()->hasRole('Cliente') ? __('Mis Equipos') : __('Inventario de Equipos') }}
                </flux:sidebar.item>

                @hasanyrole('Admin|Agente')
                <flux:sidebar.item icon="building-office" :href="route('clients.index')"
                    :current="request()->routeIs('clients.*')" wire:navigate>
                    {{ __('Directorio de Clientes') }}
                </flux:sidebar.item>
                @endhasanyrole

                <flux:sidebar.item icon="calendar" :href="route('calendar')" :current="request()->routeIs('calendar')"
                    wire:navigate>
                    {{ auth()->user()->hasRole('Cliente') ? __('Mi Calendario') : __('Calendario de Mtto.') }}
                </flux:sidebar.item>

                @hasanyrole('Admin|Agente')
                <flux:sidebar.item icon="clipboard-document-list" :href="route('maintenance-schedules.index')"
                    :current="request()->routeIs('maintenance-schedules.*')" wire:navigate>
                    {{ __('Programaciones') }}
                </flux:sidebar.item>
                @endhasanyrole
            </flux:sidebar.group>

            {{-- Sección: Administración --}}
            @hasanyrole('Admin|Agente')
            <flux:sidebar.group :heading="__('Administración')"
                class="grid text-xs font-semibold uppercase text-zinc-500 mt-2">
                <flux:sidebar.item icon="user-group" :href="route('teams.index')"
                    :current="request()->routeIs('teams.index')" wire:navigate>
                    {{ __('Plantilla Técnica') }}
                </flux:sidebar.item>
                @role('Admin')
                <flux:sidebar.item icon="cog-6-tooth" :href="route('profile.edit')" :current="request()->routeIs('profile.edit')" wire:navigate>
                    {{ __('Mi Perfil') }}
                </flux:sidebar.item>
                @endrole
            </flux:sidebar.group>
            @endhasanyrole
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