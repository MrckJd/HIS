<?php

namespace App\Filament\Forms;

use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;

class AddMember
{
    public static function form(): array
    {
        return [
            Group::make([
                FileUpload::make('avatar')
                    ->avatar()
                    ->directory('avatars')
                    ->visibility('public')
                    ->imageEditor()
                    ->label('Profile Picture')
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetHeight('500')
                    ->imageResizeTargetWidth('500')
                    ->acceptedFileTypes(['image/jpeg', 'image/png']),
                ])
                ->columnSpanFull()
                ->extraAttributes(['class'=>'flex flex-col items-center justify-center w-full']),
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
            Forms\Components\Select::make('sex')
                ->options([
                    'Male' => 'Male',
                    'Female' => 'Female',
                ])
                ->native(false)
                ->required(),
            Forms\Components\Select::make('role')
                ->label('Relationship')
                ->default(fn($get)=> $get('is_leader') ? 'Head' : '')
                ->hidden(function($get) {
                    return $get('is_leader');
                })
                ->dehydratedWhenHidden()
                ->options(
                    function($get){
                        return $get('is_leader') ? [
                            'Head' => 'Head of Household',
                        ] : [
                            'Spouse' => 'Spouse',
                            'Child' => 'Child',
                            'Parent' => 'Parent',
                            'Sibling' => 'Sibling',
                            'Other' => 'Other Relative',
                        ];
                    })
                ->required(),
            Forms\Components\TextInput::make('precinct_no')
                ->maxLength(255),
            Forms\Components\TextInput::make('cluster_no')
                ->numeric()
                ->maxLength(255),
        ];
    }

    public static function memberServicesForm(): array
    {
        return [
            TableRepeater::make('memberServices')
                ->defaultItems(0)
                ->schema([
                    Select::make('service_id')
                        ->label('Service')
                        ->options(Service::all()->pluck('name', 'id'))
                        ->live()
                        ->searchable(),
                    DatePicker::make('date_received')
                        ->closeOnDateSelection()
                        ->helperText('Date Format: dd/mm/YYYY')
                        ->label('Date Received'),
                ]),
        ];
    }
}
