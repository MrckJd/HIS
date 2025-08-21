<?php

namespace App\Enum;

enum UserRole: string
{
    case ROOT = 'root';

    case ADMIN = 'admin';

    case ENCODER = 'encoder';

    case PROVIDER = 'service provider';

    public function getLabel(): ?string
    {
        return match ($this) {
            default => mb_ucfirst($this->value),
        };
    }

    public static function options(bool $root = false): array
    {
        $filtered = array_filter(
            self::cases(),
            fn (self $role) => $root || ! in_array($role, [self::ROOT])
        );

        return array_combine(
            array_map(fn (self $role) => $role->value, $filtered),
            array_map(fn (self $role) => $role->getLabel(), $filtered)
        );
    }
}
