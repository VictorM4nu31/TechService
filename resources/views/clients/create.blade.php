<x-layouts::app :title="__('Nuevo Cliente')">
    <div class="flex flex-col gap-6 max-w-2xl">
        <div>
            <flux:heading size="xl" level="1">{{ __('Registrar Cliente') }}</flux:heading>
            <flux:subheading>{{ __('Agrega una empresa o persona externa que recibirá servicios de mantenimiento.') }}
            </flux:subheading>
        </div>

        <flux:card class="bg-zinc-900 border-zinc-800 space-y-6">
            <form method="POST" action="{{ route('clients.store') }}" class="space-y-5">
                @csrf

                <flux:input name="name" label="{{ __('Nombre del Cliente / Empresa *') }}"
                    placeholder="{{ __('Ej: Corporación Acme S.A.') }}" value="{{ old('name') }}" required />
                @error('name') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text> @enderror

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <flux:input name="contact_email" type="email" label="{{ __('Email de Contacto') }}"
                            placeholder="{{ __('contacto@empresa.com') }}" value="{{ old('contact_email') }}" />
                        @error('contact_email') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text>
                        @enderror
                    </div>
                    <div>
                        <flux:input name="contact_phone" label="{{ __('Teléfono') }}"
                            placeholder="{{ __('Ej: +504 9999-9999') }}" value="{{ old('contact_phone') }}" />
                        @error('contact_phone') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text>
                        @enderror
                    </div>
                </div>

                <flux:input name="tax_id" label="{{ __('RFC / Tax ID') }}" placeholder="{{ __('Ej: XAXX010101000') }}"
                    value="{{ old('tax_id') }}" />
                @error('tax_id') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text> @enderror

                <flux:textarea name="address" label="{{ __('Dirección') }}"
                    placeholder="{{ __('Dirección completa del cliente...') }}" rows="3">{{ old('address') }}
                </flux:textarea>
                @error('address') <flux:text class="text-red-400 text-xs mt-1">{{ $message }}</flux:text> @enderror

                <div class="flex gap-3 pt-2">
                    <flux:button type="submit" variant="primary" class="flex-1">
                        {{ __('Registrar Cliente') }}
                    </flux:button>
                    <flux:button :href="route('clients.index')" wire:navigate variant="ghost">
                        {{ __('Cancelar') }}
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts::app>