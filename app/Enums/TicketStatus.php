<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Abierto = 'Abierto';
    case EnProgreso = 'En Progreso';
    case Cerrado = 'Cerrado';

    public function label(): string
    {
        return match ($this) {
            self::Abierto => __('Abierto'),
            self::EnProgreso => __('En Progreso'),
            self::Cerrado => __('Cerrado'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Abierto => 'blue',
            self::EnProgreso => 'yellow',
            self::Cerrado => 'green',
        };
    }
}
