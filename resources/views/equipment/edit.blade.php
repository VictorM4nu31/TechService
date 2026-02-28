<x-layouts::app :title="__('Editar Equipo')">
    <div class="flex flex-col gap-6 max-w-2xl">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1">{{ __('Editar Equipo') }}</flux:heading>
                <flux:subheading>{{ $equipment->name }}</flux:subheading>
            </div>
            <flux:button :href="route('equipment.show', $equipment)" wire:navigate variant="ghost" icon="arrow-left"
                size="sm">
                {{ __('Ver Detalle') }}
            </flux:button>
        </div>

        <flux:card class="bg-zinc-900 border-zinc-800 space-y-6">
            <form method="POST" action="{{ route('equipment.update', $equipment) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <flux:input name="name" label="{{ __('Nombre del Equipo *') }}"
                    placeholder="{{ __('Ej: Laptop de Contabilidad') }}" value="{{ old('name', $equipment->name) }}"
                    required />
                @error('name') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text> @enderror

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <flux:input name="brand" label="{{ __('Marca') }}"
                            placeholder="{{ __('Ej: HP, Dell, Lenovo') }}"
                            value="{{ old('brand', $equipment->brand) }}" />
                        @error('brand') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text>
                        @enderror
                    </div>
                    <div>
                        <flux:input name="model" label="{{ __('Modelo') }}" placeholder="{{ __('Ej: ProBook 440 G8') }}"
                            value="{{ old('model', $equipment->model) }}" />
                        @error('model') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text>
                        @enderror
                    </div>
                </div>

                <flux:input name="serial_number" label="{{ __('Número de Serie') }}"
                    placeholder="{{ __('Ej: SN-ABC12345') }}"
                    value="{{ old('serial_number', $equipment->serial_number) }}" />
                @error('serial_number') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text>
                @enderror

                <flux:select name="type" label="{{ __('Tipo de Equipo *') }}" required>
                    @foreach(['Computadora', 'Impresora', 'Red', 'Servidor', 'Teléfono', 'Otro'] as $type)
                        <flux:select.option value="{{ $type }}" :selected="old('type', $equipment->type) === $type">
                            {{ $type }}</flux:select.option>
                    @endforeach
                </flux:select>
                @error('type') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text> @enderror

                <div class="flex gap-3 pt-2">
                    <flux:button type="submit" variant="primary" class="flex-1">
                        {{ __('Guardar Cambios') }}
                    </flux:button>
                    <flux:button :href="route('equipment.index')" wire:navigate variant="ghost">
                        {{ __('Cancelar') }}
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>