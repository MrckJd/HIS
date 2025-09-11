<?php

namespace App\Filament\Resources\RequestActionResource\Pages;

use App\Enum\RequestActionStatus;
use App\Filament\Resources\RequestActionResource;
use App\Models\RequestAction;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListRequestActions extends ListRecords
{
    protected static string $resource = RequestActionResource::class;

    // protected function getHeaderActions(): array
    // {

    // }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'pending' => Tab::make()
                            ->query(fn ($query) => $query->where('status', RequestActionStatus::PENDING->value))
                            ->badge(RequestAction::query()->where('status', RequestActionStatus::PENDING->value)->count()),
            'approved' => Tab::make()
                            ->query(fn ($query) => $query->where('status', RequestActionStatus::APPROVED->value))
                            ->badge(RequestAction::query()->where('status', RequestActionStatus::APPROVED->value)->count()),
            'cancelled' => Tab::make()
                            ->query(fn ($query) => $query->where('status', RequestActionStatus::CANCELLED->value))
                            ->badge(RequestAction::query()->where('status', RequestActionStatus::CANCELLED->value)->count()),
            'rejected' => Tab::make()
                            ->query(fn ($query) => $query->where('status', RequestActionStatus::REJECTED->value))
                            ->badge(RequestAction::query()->where('status', RequestActionStatus::REJECTED->value)->count())
                            ->badgeColor('danger'),
        ];
    }
}
