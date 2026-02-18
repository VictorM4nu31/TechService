<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TechService') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-white dark:bg-zinc-900 antialiased font-sans">
    <div class="relative flex flex-col min-h-screen">
        {{-- Navigation --}}
        <header
            class="w-full px-6 py-4 flex justify-between items-center border-b border-zinc-100 dark:border-zinc-800">
            <x-app-logo />

            <nav class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <flux:button href="{{ url('/dashboard') }}" variant="ghost" wire:navigate>
                            {{ __('Panel de Control') }}
                        </flux:button>
                    @else
                        <flux:button href="{{ route('login') }}" variant="ghost" wire:navigate>
                            {{ __('Iniciar Sesión') }}
                        </flux:button>

                        @if (Route::has('register'))
                            <flux:button href="{{ route('register') }}" variant="primary" wire:navigate>
                                {{ __('Registrarse') }}
                            </flux:button>
                        @endif
                    @endauth
                @endif
            </nav>
        </header>

        {{-- Hero Section --}}
        <main class="flex-1 flex flex-col items-center justify-center px-6 text-center py-12">
            <div class="max-w-3xl space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-1000">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-accent/10 text-accent text-sm font-medium border border-accent/20 mx-auto">
                    <flux:icon name="bolt" class="size-4" />
                    <span>Novedad: Gestión de Tickets Optimizada</span>
                </div>

                <h1 class="text-4xl md:text-6xl font-bold tracking-tight text-zinc-900 dark:text-white">
                    Soporte Técnico <span class="text-accent underline decoration-accent/30 italic">Inteligente</span>
                    para tu Empresa
                </h1>

                <p class="text-lg text-zinc-600 dark:text-zinc-400 max-w-2xl mx-auto">
                    TechService ofrece una plataforma centralizada para gestionar incidencias, equipos y soporte técnico
                    con eficiencia y control total.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    @auth
                        <flux:button href="{{ url('/dashboard') }}" variant="primary" icon-trailing="arrow-right"
                            wire:navigate>
                            {{ __('Ir a mi Panel') }}
                        </flux:button>
                    @else
                        <flux:button href="{{ route('login') }}" variant="primary" icon-trailing="arrow-right"
                            wire:navigate>
                            {{ __('Comenzar Ahora') }}
                        </flux:button>
                        <flux:button href="https://github.com/VictorM4nu31/TechService" target="_blank" variant="ghost"
                            icon="code-bracket">
                            {{ __('Ver Repositorio') }}
                        </flux:button>
                    @endauth
                </div>
            </div>

            {{-- Feature Grid --}}
            <div class="max-w-5xl w-full mt-24 grid grid-cols-1 md:grid-cols-3 gap-8 pb-24">
                <div
                    class="p-8 rounded-2xl border border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/50 text-start space-y-4 hover:shadow-lg transition-shadow duration-300">
                    <div class="size-12 rounded-xl bg-accent/10 flex items-center justify-center text-accent">
                        <flux:icon name="ticket" class="size-7" />
                    </div>
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-white">Gestión de Tickets</h3>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed text-sm">Sistema completo de tickets con
                        prioridades, categorías y adjuntos para un seguimiento preciso de cada incidencia.</p>
                </div>

                <div
                    class="p-8 rounded-2xl border border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/50 text-start space-y-4 hover:shadow-lg transition-shadow duration-300">
                    <div class="size-12 rounded-xl bg-accent/10 flex items-center justify-center text-accent">
                        <flux:icon name="user-group" class="size-7" />
                    </div>
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-white">Equipos y Roles</h3>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed text-sm">Control de acceso granular
                        basado en roles para asegurar que cada miembro vea solo lo que le corresponde.</p>
                </div>

                <div
                    class="p-8 rounded-2xl border border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/50 text-start space-y-4 hover:shadow-lg transition-shadow duration-300">
                    <div class="size-12 rounded-xl bg-accent/10 flex items-center justify-center text-accent">
                        <flux:icon name="chart-bar" class="size-7" />
                    </div>
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-white">Fácil e Intuitivo</h3>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed text-sm">Interfaz diseñada con Flux para
                        una experiencia de usuario fluida, moderna y totalmente adaptativa.</p>
                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer
            class="w-full py-8 px-6 border-t border-zinc-100 dark:border-zinc-800 text-center text-sm text-zinc-500">
            <p>&copy; {{ date('Y') }} TechSupport Pro. {{ __('Todos los derechos reservados.') }}</p>
        </footer>
    </div>

    @fluxScripts
</body>

</html>