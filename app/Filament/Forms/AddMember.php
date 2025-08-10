<?php

namespace App\Filament\Forms;

use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;

class AddMember
{
    public static function form(): array
    {
        return [
            Forms\Components\TextInput::make('first_name')
                ->required()
                ->live()
                ->maxLength(255),
            Forms\Components\TextInput::make('surname')
                ->required()
                ->live()
                ->maxLength(255),
            Forms\Components\TextInput::make('middle_name')
                ->maxLength(255),
            Forms\Components\TextInput::make('suffix')
                ->maxLength(255),
            Forms\Components\DatePicker::make('birth_date')
                ->required(),
            Forms\Components\Select::make('gender')
                ->options([
                    'Male' => 'Male',
                    'Female' => 'Female',
                ])
                ->required(),
            Forms\Components\Select::make('role')
                ->options([
                    'Head' => 'Head of Household',
                    'Spouse' => 'Spouse',
                    'Child' => 'Child',
                    'Parent' => 'Parent',
                    'Sibling' => 'Sibling',
                    'Other' => 'Other Relative',
                ])
                ->required(),
            Forms\Components\TextInput::make('precinct_no')
                ->numeric()
                ->maxLength(255),
            Forms\Components\TextInput::make('cluster_no')
                ->numeric()
                ->maxLength(255),
        ];
    }

    public static function memberServicesForm(): array
    {
        return [
            TableRepeater::make('members.member_services')
                ->columnSpanFull()
                ->defaultItems(1)
                ->grid(3)
                ->schema([
                    Select::make('service_id')
                        ->label('Service')
                        ->options(function($get) {
                            $allSelectedServices = collect($get('../../members.member_services'))
                                ->pluck('service_id')
                                ->filter()
                                ->toArray();
                            $currentServiceId = $get('service_id');
                            $excludedServices = array_diff($allSelectedServices, [$currentServiceId]);
                            return Service::whereNotIn('id', $excludedServices)
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->live()
                        ->searchable(),
                    DatePicker::make('date_received')
                        ->label('Date Received'),
                ]),
        ];
    }
}
