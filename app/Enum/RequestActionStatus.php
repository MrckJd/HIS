<?php

namespace App\Enum;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum RequestActionStatus : string implements HasColor, HasLabel, HasDescription
{
    case PENDING = 'Pending';
    case APPROVED = 'Approved';
    case CANCELLED = 'Cancelled';
    case REJECTED = 'Rejected';

    public static function getStatuses(): array
    {
        return [
            self::PENDING,
            self::APPROVED,
            self::REJECTED,
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            default => mb_ucfirst($this->value),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::CANCELLED => 'gray',
            self::REJECTED => 'danger',
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::PENDING => 'The request is still pending and awaiting action.',
            self::APPROVED => 'The request has been approved successfully.',
            self::CANCELLED => 'The request has been cancelled by the user.',
            self::REJECTED => 'The request has been rejected due to certain reasons.',
        };
    }
}
