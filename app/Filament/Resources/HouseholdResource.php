<?php

namespace App\Filament\Resources;

use App\Filament\Forms\AddMember;
use App\Filament\Resources\HouseholdResource\Pages;
use App\Filament\Services\PSGCService;
use App\Models\Household;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HouseholdResource extends Resource
{
    protected static ?string $model = Household::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Household Information')
                    ->contained(false)
                    ->columnSpanFull()
                    ->columns(2)
                    ->tabs([
                        Tab::make('Basic Information')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('municipality')
                                    ->required()
                                    ->native(false)
                                    ->loadingMessage('Loading municipalities...')
                                    ->noSearchResultsMessage('No Municipalities found.')
                                    ->searchable('name')
                                    ->reactive()
                                    ->searchPrompt('Search municipalities...')
                                    ->options(fn() => PSGCService::getMunicipalities()),
                                Forms\Components\Select::make('baranggay')
                                    ->live()
                                    ->loadingMessage('Loading branggays...')
                                    ->noSearchResultsMessage('No Baranggays found.')
                                    ->searchable('name')
                                    ->native(false)
                                    ->required()
                                    ->options(function($get){
                                        $municipality = $get('municipality');
                                        if (!$municipality) {
                                            return [];
                                        }

                                        return PSGCService::getBarangays($municipality);
                                    }),
                                Forms\Components\TextInput::make('purok')
                                    ->required()
                                    ->maxLength(255),
                                            ]),
                        Tab::make('Member')
                            ->schema([
                                Repeater::make('members')
                                    ->relationship('members')
                                    ->schema([
                                        ...AddMember::form(),
                                        Select::make('member_service')
                                            ->label('Services')
                                            ->multiple()
                                            ->preload()
                                            ->columnSpan(2)
                                            ->searchable()
                                            ->options(fn() => Service::all()->pluck('name', 'id')),
                                        ])
                                    ->columns(4)
                                    ->columnSpanFull()
                                    ->defaultItems(1)
                                    ->collapsible()
                                    ->addActionLabel('Add Member')
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('leader_name')
                    ->label('Leader')
                    ->sortable(),
                TextColumn::make('address')
                    ->label('Complete Address')
                    ->state(fn( $record) => $record->purok . ', ' . PSGCService::getBarangayName($record->baranggay, $record->municipality) . ', ' . PSGCService::getMunicipalityName($record->municipality)),
                TextColumn::make('members_count')
                    ->label('Members')
                    ->counts('members')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->recordAction(null)
            ->recordUrl(fn ($record) => static::getUrl('list_member', ['record' => $record]));
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHouseholds::route('/'),
            'create' => Pages\CreateHousehold::route('/create'),
            'edit' => Pages\EditHousehold::route('/{record}/edit'),
            'list_member' => Pages\ListMember::route('/{record}/members'),
        ];
    }
}
