<x-layouts::app :title="'Ticket #' . $ticket->id">
    <livewire:tickets.show :ticket="$ticket" />
</x-layouts::app>