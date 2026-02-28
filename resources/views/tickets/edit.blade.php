<x-layouts::app :title="__('Editar Ticket') . ' #' . $ticket->id">
    <livewire:tickets.edit :ticket="$ticket" />
</x-layouts::app>