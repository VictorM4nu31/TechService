<!DOCTYPE html>
<html lang="es" class="dark">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>404 — Página no encontrada | TechService</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-zinc-950 flex items-center justify-center p-6">
    <div class="text-center space-y-6 max-w-md">
        <div class="relative inline-block">
            <span class="text-9xl font-black text-zinc-800 select-none">404</span>
            <div class="absolute inset-0 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-14 text-blue-500 opacity-80" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl font-bold text-zinc-100">Página no encontrada</h1>
            <p class="text-zinc-400 text-sm leading-relaxed">
                El recurso que buscas no existe o fue eliminado. Verifica la dirección o regresa al inicio.
            </p>
        </div>

        <div class="flex items-center justify-center gap-3">
            <a href="{{ url('/dashboard') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Ir al Panel
            </a>
            <a href="javascript:history.back()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-sm font-medium transition-colors">
                Regresar
            </a>
        </div>
    </div>
</body>

</html>