<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Actions\CreateServiceAction;
use App\Filament\Resources\ServiceResource;
use App\Models\Service;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateServiceAction::make('create')
                ->icon('heroicon-o-plus')
                ->label('Add Service'),
        ];
    }
}
