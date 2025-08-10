<?php

namespace App\Filament\Resources\ServiceResource\Pages;

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
            Actions\Action::make('create')
                ->modalAlignment(Alignment::Center)
                ->modalWidth(MaxWidth::Medium)
                ->modalSubmitActionLabel('Add')
                ->closeModalByClickingAway(false)
                ->modalDescription('Fill in the details of the new service.')
                ->form([
                    'name' => TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    'description123' => Select::make('description123')
                        ->options([
                            'Health' => 'q21312',
                            'Education' => 'adwbda',
                            'Financial' => 'Financial',
                            'Social' => 'Social',
                        ])
                        ->required()
                ])
                ->action(fn(Service $service, $data) => $service->create($data))
                ->sendSuccessNotification()
                ->successNotificationTitle('Service added successfully')
                ->icon('heroicon-o-plus')
                ->label('Add Service'),

        ];
    }
}
