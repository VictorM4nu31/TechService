<x-layouts::app :title="__('Editar Cliente')">
    <div class="flex flex-col gap-6 max-w-2xl">
        <div>
            <flux:heading size="xl" level="1">{{ __('Editar Cliente') }}</flux:heading>
            <flux:subheading>{{ $client->name }}</flux:subheading>
        </div>

        <flux:card class="bg-zinc-900 border-zinc-800 space-y-6">
            <form method="POST" action="{{ route('clients.update', $client) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <flux:input name="name" label="{{ __('Nombre del Cliente / Empresa *') }}"
                    value="{{ old('name', $client->name) }}" required />
                @error('name') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text> @enderror

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <flux:input name="contact_email" type="email" label="{{ __('Email de Contacto') }}"
                            value="{{ old('contact_email', $client->contact_email) }}" />
                        @error('contact_email') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text>
                        @enderror
                    </div>
                    <div>
                        <flux:input name="contact_phone" label="{{ __('Teléfono') }}"
                            value="{{ old('contact_phone', $client->contact_phone) }}" />
                        @error('contact_phone') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text>
                        @enderror
                    </div>
                </div>

                <flux:input name="tax_id" label="{{ __('RFC / Tax ID') }}"
                    value="{{ old('tax_id', $client->tax_id) }}" />
                @error('tax_id') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text> @enderror

                <flux:textarea name="address" label="{{ __('Dirección') }}" rows="3">
                    {{ old('address', $client->address) }}</flux:textarea>
                @error('address') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text> @enderror

                <div class="flex gap-3 pt-2">
                    <flux:button type="submit" variant="primary" class="flex-1">
                        {{ __('Guardar Cambios') }}
                    </flux:button>
                    <flux:button :href="route('clients.show', $client)" wire:navigate variant="ghost">
                        {{ __('Cancelar') }}
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>