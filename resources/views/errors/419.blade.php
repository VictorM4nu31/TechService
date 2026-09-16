<!DOCTYPE html>
<html lang="es" class="dark">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>419 — Página expirada | TechService</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-zinc-950 flex items-center justify-center p-6">
    <div class="text-center space-y-6 max-w-md">
        <div class="relative inline-block">
            <span class="text-9xl font-black text-zinc-800 select-none">419</span>
            <div class="absolute inset-0 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-14 text-blue-500 opacity-80" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl font-bold text-zinc-100">Página expirada</h1>
            <p class="text-zinc-400 text-sm leading-relaxed">
                La sesión expiró por inactividad o la página fue abierta hace demasiado tiempo. Recarga la página
                e intenta de nuevo; si el problema persiste, inicia sesión otra vez.
            </p>
        </div>

        <div class="flex items-center justify-center gap-3">
            <a href="javascript:location.reload()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Recargar página
            </a>
            <a href="{{ url('/login') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-sm font-medium transition-colors">
                Iniciar sesión
            </a>
        </div>
    </div>
</body>

</html>
