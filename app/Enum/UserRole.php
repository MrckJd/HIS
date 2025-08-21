<?php

namespace App\Enum;

enum UserRole: string
{
    case root = 'Root';

    case admin = 'Admin';

    case encoder = 'Encoder';

    case provider = 'Service Provider';

    public function getLabel(): ?string
    {
        return match ($this) {
            default => mb_ucfirst($this->value),
        };
    }
}
