<?php

namespace App\Filament\Forms;

use Filament\Forms;

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
}
