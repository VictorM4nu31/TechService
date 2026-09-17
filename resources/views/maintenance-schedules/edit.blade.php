<x-layouts::app :title="__('Editar Programación')">
    <div class="flex flex-col gap-6 max-w-2xl">
        <div>
            <flux:heading size="xl" level="1">{{ __('Editar Programación de Mantenimiento') }}</flux:heading>
            <flux:subheading>{{ __('Actualiza los datos de la tarea preventiva.') }}</flux:subheading>
        </div>

        <flux:card class="bg-zinc-900 border-zinc-800 space-y-6">
            <form method="POST" action="{{ route('maintenance-schedules.update', $maintenanceSchedule) }}"
                class="space-y-5">
                @csrf
                @method('PUT')

                <flux:select name="equipment_id" label="{{ __('Equipo *') }}" required>
                    @foreach($equipments as $equipment)
                        <flux:select.option value="{{ $equipment->id }}"
                            :selected="old('equipment_id', $maintenanceSchedule->equipment_id) == $equipment->id">
                            {{ $equipment->name }} — {{ $equipment->owner?->name ?? 'Sin responsable' }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                @error('equipment_id') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text> @enderror

                <flux:input name="name" label="{{ __('Nombre de la Tarea *') }}"
                    value="{{ old('name', $maintenanceSchedule->name) }}" required />
                @error('name') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text> @enderror

                <flux:textarea name="description" label="{{ __('Descripción') }}" rows="4">{{ old('description', $maintenanceSchedule->description) }}</flux:textarea>
                @error('description') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text> @enderror

                <flux:textarea name="checklist" label="{{ __('Checklist técnico') }}" rows="5"
                    placeholder="{{ __('Una verificación por línea') }}">{{ old('checklist', implode("\n", $maintenanceSchedule->checklist ?? [])) }}</flux:textarea>
                <flux:text size="xs" class="text-signal-muted">{{ __('Una verificación por línea.') }}</flux:text>
                @error('checklist') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text> @enderror

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <flux:input name="frequency_days" type="number" min="1"
                            label="{{ __('Frecuencia (días) *') }}"
                            value="{{ old('frequency_days', $maintenanceSchedule->frequency_days) }}" required />
                        @error('frequency_days') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text>
                        @enderror
                    </div>
                    <div>
                        <flux:input name="next_run_at" type="date" label="{{ __('Próxima ejecución') }}"
                            value="{{ old('next_run_at', $maintenanceSchedule->next_run_at?->format('Y-m-d')) }}" />
                        @error('next_run_at') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text>
                        @enderror
                    </div>
                </div>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                        @checked(old('is_active', $maintenanceSchedule->is_active))
                        class="rounded border-zinc-700 bg-zinc-800 text-blue-500 focus:ring-blue-500">
                    <flux:text>{{ __('Programación activa') }}</flux:text>
                </label>

                <div class="flex gap-3 pt-2">
                    <flux:button type="submit" variant="primary" class="flex-1">
                        {{ __('Guardar Cambios') }}
                    </flux:button>
                    <flux:button :href="route('maintenance-schedules.index')" wire:navigate variant="ghost">
                        {{ __('Cancelar') }}
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>
