<?php

namespace App\Enums;

enum TicketPriority: string
{
    case Baja = 'Baja';
    case Media = 'Media';
    case Alta = 'Alta';

    public function label(): string
    {
        return match ($this) {
            self::Baja => __('Baja'),
            self::Media => __('Media'),
            self::Alta => __('Alta'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Baja => 'green',
            self::Media => 'yellow',
            self::Alta => 'red',
        };
    }
}
