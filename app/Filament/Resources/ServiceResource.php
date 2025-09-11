<?php

namespace App\Filament\Resources;

use App\Enum\UserRole;
use App\Filament\Actions\CreateServiceAction;
use App\Filament\Actions\Table\EditServiceAction;
use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Resources\Resource;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'fas-file-alt';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return in_array(Filament::getCurrentPanel()->getId(), ['root', 'admin', 'provider']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('services')
                    ->label('Add Services')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(1)
                    ->defaultItems(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('member_services_count')
                    ->label('Member Services Count')
                    ->counts('memberServices')
                    ->verticalAlignment(VerticalAlignment::Center),
            ])
            ->filters([
            ])
            ->actions([
                EditServiceAction::make('name')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
        ];
    }
}
