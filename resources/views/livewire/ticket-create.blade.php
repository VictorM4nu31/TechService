<div>
    <div class="flex flex-col gap-6">
        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-zinc-800 pb-4">
            <div>
                <flux:heading size="xl" level="1">{{ __('Nuevo Ticket') }}</flux:heading>
                <flux:subheading>
                    {{ __('Complete el formulario para registrar una solicitud de soporte técnico o mantenimiento.') }}
                </flux:subheading>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Main Form Content --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Información del Ticket --}}
                <flux:card class="bg-zinc-900 border-zinc-800 space-y-6">
                    <flux:heading size="lg">{{ __('Información del Ticket') }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">
                        {{ __('Complete los detalles del problema o solicitud de mantenimiento.') }}
                    </flux:text>

                    <div class="space-y-4 pt-4">
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
                        <flux:input wire:model="location" label="{{ __('Ubicación *') }}"
                            placeholder="{{ __('Ej: Piso 3, Oficina 301') }}" />
                    </div>
                </flux:card>

                {{-- Archivos Adjuntos --}}
                <flux:card class="bg-zinc-900 border-zinc-800 space-y-6">
                    <flux:heading size="lg">{{ __('Archivos Adjuntos') }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-400 mt-1">
                        {{ __('Adjunte capturas de pantalla o documentos relevantes (opcional)') }}
                    </flux:text>

                    <div
                        class="mt-4 border-2 border-dashed border-zinc-800 rounded-xl p-12 flex flex-col items-center justify-center gap-4 hover:border-zinc-700 transition-colors cursor-pointer relative">
                        <input type="file" wire:model="attachments" multiple
                            class="absolute inset-0 opacity-0 cursor-pointer" />
                        <div class="p-4 rounded-full bg-zinc-800 text-zinc-400">
                            <flux:icon name="arrow-up-tray" size="md" />
                        </div>
                        <div class="text-center">
                            <flux:text class="font-medium text-zinc-300">
                                {{ __('Arrastra archivos aquí o haz clic para seleccionar') }}
                            </flux:text>
                            <flux:text size="xs" class="text-zinc-500 mt-1">{{ __('PNG, JPG, PDF hasta 10MB') }}
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
                <flux:card class="bg-zinc-900 border-zinc-800 space-y-4">
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

                {{-- Categoría --}}
                <flux:card class="bg-zinc-900 border-zinc-800 space-y-4">
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
                <flux:card class="bg-zinc-900 border-zinc-800 space-y-4">
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
                    <flux:button wire:click="save" wire:loading.attr="disabled" wire:target="save" variant="primary"
                        class="w-full font-bold shadow-lg shadow-blue-500/20">
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
