<?php

namespace App\Filament\Resources\HouseholdResource\Pages;

use App\Enum\UserRole;
use App\Filament\Resources\HouseholdResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHousehold extends EditRecord
{
    protected static string $resource = HouseholdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn ($record) => auth()->check() && (auth()->user()->role === UserRole::ADMIN->getLabel() || auth()->user()->role === UserRole::ROOT->getLabel()))
                ->successNotificationTitle('Household deleted successfully.'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
