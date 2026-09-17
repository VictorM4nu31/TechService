<?php

namespace App\Enums;

enum TicketImpact: string
{
    case Bajo = 'Bajo';
    case Medio = 'Medio';
    case Alto = 'Alto';

    public function label(): string
    {
        return __($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Bajo => 'green',
            self::Medio => 'yellow',
            self::Alto => 'red',
        };
    }
}
