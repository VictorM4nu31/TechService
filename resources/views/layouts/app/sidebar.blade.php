<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-signal-canvas text-signal-ink dark:bg-signal-canvas"
    x-data="{ commandOpen: false, commandQuery: '', openCommand() { this.commandOpen = true; this.commandQuery = ''; this.$nextTick(() => this.$refs.commandInput?.focus()); }, closeCommand() { this.commandOpen = false; } }"
    @keydown.window.meta.k.prevent="openCommand()"
    @keydown.window.ctrl.k.prevent="openCommand()"
    @keydown.window.escape="closeCommand()">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-signal-border bg-signal-surface dark:border-signal-border dark:bg-signal-surface">
        <flux:sidebar.header class="pb-2!">
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

        <div class="px-3 pb-3">
            <div class="flex items-center gap-2 rounded-lg border border-signal-border bg-signal-canvas px-3 py-2 text-xs text-signal-muted">
                <span class="size-2 rounded-full bg-signal-success shadow-[0_0_0_3px_rgba(98,211,154,0.12)]"></span>
                <span>{{ __('Operación estable') }}</span>
                <span class="ml-auto font-mono text-[10px] text-signal-muted">{{ now()->format('H:i') }}</span>
            </div>
        </div>

        <div class="px-3 pb-2">
            <button type="button" @click="openCommand()" class="signal-focus flex w-full items-center justify-between gap-3 rounded-lg border border-signal-border bg-signal-canvas px-3 py-2 text-left text-xs text-signal-muted transition-colors hover:border-signal-accent/60 hover:text-signal-ink">
                <span class="flex items-center gap-2"><flux:icon name="magnifying-glass" size="xs" /> {{ __('Buscar o ejecutar') }}</span>
                <kbd class="rounded border border-signal-border px-1.5 py-0.5 font-mono text-[10px]">⌘K</kbd>
            </button>
        </div>

        <flux:sidebar.nav class="gap-y-1">
            {{-- Sección: General --}}
            <flux:sidebar.group :heading="__('General')" class="grid text-xs font-semibold uppercase tracking-wider text-signal-muted">
                <flux:sidebar.item icon="squares-2x2" :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Panel de Control') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

            {{-- Sección: Soporte Técnico --}}
            <flux:sidebar.group :heading="auth()->user()->hasRole('Cliente') ? __('Soporte') : __('Soporte Técnico')" class="mt-2 grid text-xs font-semibold uppercase tracking-wider text-signal-muted">
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
                class="mt-2 grid text-xs font-semibold uppercase tracking-wider text-signal-muted">
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
                class="mt-2 grid text-xs font-semibold uppercase tracking-wider text-signal-muted">
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
                class="mt-2 grid text-xs font-semibold uppercase tracking-wider text-signal-muted">
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
    <flux:header class="border-b border-signal-border bg-signal-surface lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-3" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu class="border-signal-border bg-signal-elevated">
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

    <div x-cloak x-show="commandOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-start justify-center bg-signal-canvas/80 px-4 pt-[12vh] backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="command-title">
        <button type="button" class="absolute inset-0 cursor-default" aria-label="{{ __('Cerrar búsqueda') }}" @click="closeCommand()"></button>
        <div class="relative w-full max-w-xl overflow-hidden rounded-xl border border-signal-border bg-signal-elevated shadow-2xl" @click.stop>
            <div class="border-b border-signal-border p-4">
                <div class="flex items-center gap-3">
                    <flux:icon name="magnifying-glass" size="sm" class="text-signal-accent" />
                    <input x-ref="commandInput" x-model="commandQuery" type="search" id="command-title" autocomplete="off"
                        placeholder="{{ __('Buscar una acción o pantalla...') }}"
                        class="min-w-0 flex-1 border-0 bg-transparent text-sm text-signal-ink outline-hidden placeholder:text-signal-muted"
                        aria-label="{{ __('Buscar una acción o pantalla') }}" />
                    <kbd class="rounded border border-signal-border px-2 py-1 font-mono text-[10px] text-signal-muted">ESC</kbd>
                </div>
            </div>
            <nav class="max-h-[min(28rem,60vh)] overflow-y-auto p-2" aria-label="{{ __('Acciones rápidas') }}">
                <p class="px-3 pb-2 pt-1 text-[10px] font-semibold uppercase tracking-wider text-signal-muted">{{ __('Ir a') }}</p>
                <a x-show="!commandQuery || '{{ __('Panel de Control') }}'.toLowerCase().includes(commandQuery.toLowerCase())" href="{{ route('dashboard') }}" wire:navigate @click="closeCommand()" class="signal-focus flex items-center gap-3 rounded-lg px-3 py-3 text-sm text-signal-ink hover:bg-signal-canvas">
                    <flux:icon name="squares-2x2" size="sm" class="text-signal-info" /> {{ __('Panel de Control') }}
                </a>
                <a x-show="!commandQuery || '{{ __('Todos los Tickets') }}'.toLowerCase().includes(commandQuery.toLowerCase())" href="{{ route('tickets.index') }}" wire:navigate @click="closeCommand()" class="signal-focus flex items-center gap-3 rounded-lg px-3 py-3 text-sm text-signal-ink hover:bg-signal-canvas">
                    <flux:icon name="ticket" size="sm" class="text-signal-warning" /> {{ __('Todos los Tickets') }}
                </a>
                <a x-show="!commandQuery || '{{ __('Reportar incidencia') }}'.toLowerCase().includes(commandQuery.toLowerCase())" href="{{ route('tickets.create') }}" wire:navigate @click="closeCommand()" class="signal-focus flex items-center gap-3 rounded-lg px-3 py-3 text-sm text-signal-ink hover:bg-signal-canvas">
                    <flux:icon name="plus-circle" size="sm" class="text-signal-accent" /> {{ __('Reportar incidencia') }}
                </a>
                <a x-show="!commandQuery || '{{ __('Inventario de Equipos') }}'.toLowerCase().includes(commandQuery.toLowerCase())" href="{{ route('equipment.index') }}" wire:navigate @click="closeCommand()" class="signal-focus flex items-center gap-3 rounded-lg px-3 py-3 text-sm text-signal-ink hover:bg-signal-canvas">
                    <flux:icon name="cpu-chip" size="sm" class="text-signal-success" /> {{ __('Inventario de Equipos') }}
                </a>
                <a x-show="!commandQuery || '{{ __('Calendario') }}'.toLowerCase().includes(commandQuery.toLowerCase())" href="{{ route('calendar') }}" wire:navigate @click="closeCommand()" class="signal-focus flex items-center gap-3 rounded-lg px-3 py-3 text-sm text-signal-ink hover:bg-signal-canvas">
                    <flux:icon name="calendar" size="sm" class="text-signal-secondary" /> {{ __('Calendario') }}
                </a>
                @hasanyrole('Admin|Agente')
                    <a x-show="!commandQuery || '{{ __('Programaciones') }}'.toLowerCase().includes(commandQuery.toLowerCase())" href="{{ route('maintenance-schedules.index') }}" wire:navigate @click="closeCommand()" class="signal-focus flex items-center gap-3 rounded-lg px-3 py-3 text-sm text-signal-ink hover:bg-signal-canvas">
                        <flux:icon name="clipboard-document-list" size="sm" class="text-signal-info" /> {{ __('Programaciones') }}
                    </a>
                @endhasanyrole
            </nav>
            <div class="border-t border-signal-border px-4 py-3 text-xs text-signal-muted">{{ __('Usa ⌘K o Ctrl+K para abrir esta búsqueda desde cualquier pantalla.') }}</div>
        </div>
    </div>

    @fluxScripts
</body>

</html>
