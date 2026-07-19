<?php

namespace App\Enums;

enum TicketCategory: string
{
    case Preventivo = 'Preventivo';
    case Correctivo = 'Correctivo';
    case Emergencia = 'Emergencia';

    public function label(): string
    {
        return match ($this) {
            self::Preventivo => __('Preventivo'),
            self::Correctivo => __('Correctivo'),
            self::Emergencia => __('Emergencia'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Preventivo => 'shield-check',
            self::Correctivo => 'wrench-screwdriver',
            self::Emergencia => 'exclamation-triangle',
        };
    }
}
