<?php

namespace App\Enums;

enum SubscriptionPlan: string
{
    case Free = 'free';
    case Pro = 'pro';

    public function label(): string
    {
        return match ($this) {
            self::Free => 'Gratis (Free)',
            self::Pro => 'Profesional (Pro)',
        };
    }
}

