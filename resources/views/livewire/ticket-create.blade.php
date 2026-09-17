<div>
    <div class="mx-auto flex max-w-6xl flex-col gap-8">
        {{-- Header --}}
        <div class="flex flex-col gap-4 border-b border-signal-border pb-6 sm:flex-row sm:items-end sm:justify-between">
            <div class="space-y-3">
                <p class="signal-kicker">{{ __('Entrada guiada') }}</p>
                <flux:heading size="xl" level="1">{{ __('Reportar una incidencia') }}</flux:heading>
                <flux:subheading class="max-w-2xl text-signal-muted">
                    {{ __('Describe el síntoma, añade contexto y deja que el equipo técnico tome el siguiente paso.') }}
                </flux:subheading>
            </div>
            <div class="font-mono text-[11px] uppercase tracking-wider text-signal-muted">{{ __('Paso 1 de 1 · Guardado al enviar') }}</div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Main Form Content --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Información del Ticket --}}
                <flux:card class="signal-panel space-y-6">
                    <div>
                        <p class="signal-kicker">{{ __('Señal principal') }}</p>
                        <flux:heading size="lg" class="mt-2">{{ __('Qué está ocurriendo') }}</flux:heading>
                        <flux:text size="sm" class="mt-1 text-signal-muted">
                            {{ __('Cuanto más concreto sea el síntoma, más rápido podrá diagnosticarse.') }}
                        </flux:text>
                    </div>

                    <div class="space-y-4 pt-4">
                        @if ($errors->any())
                            <div
                                class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg text-sm">
                                <flux:icon name="exclamation-triangle" size="sm" />
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <flux:input wire:model="title" label="{{ __('Título del Ticket *') }}"
                            placeholder="{{ __('Ej: No funciona la impresora') }}" required />
                        <flux:textarea wire:model="description" label="{{ __('Descripción Detallada *') }}" x-data
                            x-autosize
                            placeholder="{{ __('Proporcione todos los detalles relevantes sobre el problema, incluyendo mensajes de error, pasos para reproducir el problema, etc.') }}"
                            required rows="4" />
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <flux:select wire:model="equipment_id" label="{{ __('Equipo de Inventario') }}">
                            <flux:select.option value="">{{ __('No asignado') }}</flux:select.option>
                            @foreach($equipments as $eq)
                                <flux:select.option value="{{ $eq->id }}">{{ $eq->name }} ({{ $eq->serial_number }})
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:input wire:model="location" label="{{ __('Ubicación') }}"
                            placeholder="{{ __('Ej: Piso 3, Oficina 301 (opcional)') }}" />
                    </div>
                </flux:card>

                {{-- Archivos Adjuntos --}}
                <flux:card class="signal-panel space-y-6">
                    <flux:heading size="lg">{{ __('Evidencia') }}</flux:heading>
                    <flux:text size="sm" class="text-signal-muted">
                        {{ __('Adjunte capturas de pantalla o documentos relevantes (opcional)') }}
                    </flux:text>

                    <div
                        class="relative mt-4 flex cursor-pointer flex-col items-center justify-center gap-4 rounded-xl border-2 border-dashed border-signal-border p-12 transition-colors hover:border-signal-accent/60">
                        <input type="file" wire:model="attachments" multiple
                            class="absolute inset-0 opacity-0 cursor-pointer" />
                        <div class="rounded-lg bg-signal-canvas p-4 text-signal-accent">
                            <flux:icon name="arrow-up-tray" size="md" />
                        </div>
                        <div class="text-center">
                            <flux:text class="font-medium text-signal-ink">
                                {{ __('Arrastra archivos aquí o haz clic para seleccionar') }}
                            </flux:text>
                            <flux:text size="xs" class="mt-1 text-signal-muted">{{ __('PNG, JPG, PDF hasta 10MB') }}
                            </flux:text>
                        </div>
                    </div>

                    @if ($attachments)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                            @foreach ($attachments as $index => $attachment)
                                <div class="relative group">
                                    <div
                                        class="aspect-square rounded-lg bg-zinc-800 flex items-center justify-center p-2 text-zinc-500 overflow-hidden">
                                        @if (str_contains($attachment->getMimeType(), 'image'))
                                            <img src="{{ $attachment->temporaryUrl() }}" class="object-cover size-full" />
                                        @else
                                            <flux:icon name="document" size="lg" />
                                        @endif
                                    </div>
                                    <button type="button" wire:click="removeAttachment({{ $index }})"
                                        class="absolute -top-2 -right-2 bg-zinc-900 shadow-lg text-red-500 rounded-full p-1">
                                        <flux:icon name="x-mark" size="sm" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </flux:card>
            </div>

            {{-- Sidebar Content --}}
            <div class="space-y-8">
                {{-- Prioridad --}}
                <flux:card class="signal-panel space-y-4">
                    <flux:heading size="md">{{ __('Prioridad') }}</flux:heading>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($priorities as $priority)
                            <button type="button" wire:click="$set('priority', '{{ $priority->value }}')"
                                class="px-4 py-2 rounded-lg text-xs font-bold transition-all border-2 {{ $priority->value === $this->priority ? 'scale-105 shadow-lg' : 'opacity-50' }}
                                {{ match ($priority) {
                                    \App\Enums\TicketPriority::Alta => 'bg-red-500/10 text-red-500 border-red-500',
                                    \App\Enums\TicketPriority::Media => 'bg-yellow-500/10 text-yellow-500 border-yellow-500',
                                    \App\Enums\TicketPriority::Baja => 'bg-green-500/10 text-green-500 border-green-500',
                                } }}">
                                {{ strtoupper($priority->label()) }}
                            </button>
                        @endforeach
                    </div>
                </flux:card>

                <flux:card class="signal-panel space-y-4">
                    <div>
                        <p class="signal-kicker">{{ __('Triage') }}</p>
                        <flux:heading size="md" class="mt-2">{{ __('Impacto y urgencia') }}</flux:heading>
                        <flux:text size="sm" class="mt-1 text-signal-muted">{{ __('Ayuda a ordenar la atención sin confundir prioridad con alcance.') }}</flux:text>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <flux:select wire:model="impact" label="{{ __('Impacto') }}">
                            <flux:select.option value="Bajo">{{ __('Bajo · una persona o equipo') }}</flux:select.option>
                            <flux:select.option value="Medio">{{ __('Medio · un área') }}</flux:select.option>
                            <flux:select.option value="Alto">{{ __('Alto · operación detenida') }}</flux:select.option>
                        </flux:select>
                        <flux:select wire:model="urgency" label="{{ __('Urgencia') }}">
                            <flux:select.option value="Bajo">{{ __('Baja · puede esperar') }}</flux:select.option>
                            <flux:select.option value="Medio">{{ __('Media · atender hoy') }}</flux:select.option>
                            <flux:select.option value="Alto">{{ __('Alta · atención inmediata') }}</flux:select.option>
                        </flux:select>
                    </div>
                </flux:card>

                {{-- Categoría --}}
                <flux:card class="signal-panel space-y-4">
                    <flux:heading size="md">{{ __('Categoría') }}</flux:heading>
                    <div class="grid gap-3">
                        @foreach ($categories as $category)
                            <button type="button" wire:click="$set('category', '{{ $category->value }}')"
                                class="flex items-center gap-3 p-4 rounded-xl border-2 transition-all text-left {{ $category->value === $this->category ? 'bg-blue-500/10 border-blue-500 shadow-[0_0_15px_rgba(59,130,246,0.2)]' : 'bg-transparent border-zinc-800 hover:border-zinc-700' }}">
                                <div
                                    class="p-2 rounded-lg bg-zinc-800 {{ $category->value === $this->category ? 'text-blue-500' : 'text-zinc-500' }}">
                                    <flux:icon :name="$category->icon()" size="sm" />
                                </div>
                                <flux:text
                                    class="font-medium {{ $category->value === $this->category ? 'text-zinc-100' : 'text-zinc-400' }}">
                                    {{ $category->label() }}
                                </flux:text>
                            </button>
                        @endforeach
                    </div>
                </flux:card>

                {{-- Tipo de Mantenimiento --}}
                <flux:card class="signal-panel space-y-4">
                    <flux:heading size="md">{{ __('Tipo de Mantenimiento') }}</flux:heading>
                    <flux:select wire:model="maintenance_type">
                        <x-slot name="prefix">
                            <flux:icon :name="match($maintenance_type) {
                                'Hardware' => 'cpu-chip',
                                'Software' => 'code-bracket',
                                'Red' => 'wifi',
                                'Seguridad' => 'lock-closed',
                                default => 'question-mark-circle',
                            }" size="sm" />
                        </x-slot>
                        <flux:select.option value="Hardware">{{ __('Hardware') }}</flux:select.option>
                        <flux:select.option value="Software">{{ __('Software') }}</flux:select.option>
                        <flux:select.option value="Red">{{ __('Red') }}</flux:select.option>
                        <flux:select.option value="Seguridad">{{ __('Seguridad') }}</flux:select.option>
                        <flux:select.option value="Otro">{{ __('Otro') }}</flux:select.option>
                    </flux:select>
                </flux:card>

                {{-- Actions --}}
                <div class="grid gap-3 pt-4">
                    @if (session('draft-saved'))
                        <div class="rounded-lg border border-signal-success/30 bg-signal-success/10 px-3 py-2 text-xs text-signal-success" role="status">
                            {{ session('draft-saved') }}
                        </div>
                    @endif
                    <flux:button wire:click="saveDraft" wire:loading.attr="disabled" wire:target="saveDraft" variant="ghost" icon="bookmark" class="w-full">
                        <span wire:loading.remove wire:target="saveDraft">{{ __('Guardar borrador') }}</span>
                        <span wire:loading wire:target="saveDraft">{{ __('Guardando borrador...') }}</span>
                    </flux:button>
                    <flux:button wire:click="save" wire:loading.attr="disabled" wire:target="save" variant="primary"
                        class="w-full bg-signal-accent font-bold text-signal-ink shadow-lg shadow-signal-accent/10 hover:bg-signal-accent/85">
                        <span wire:loading.remove wire:target="save">{{ __('Crear Ticket') }}</span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <flux:icon name="arrow-path" size="xs" class="animate-spin" />
                            {{ __('Guardando...') }}
                        </span>
                    </flux:button>
                    <flux:button :href="route('tickets.index')" wire:navigate variant="ghost" class="w-full">
                        {{ __('Cancelar') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
</div>
