<?php

namespace App\Filament\Resources;

use App\Enum\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Municipality;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 13;

    public static function canAccess(): bool
    {
        return in_array(Filament::getCurrentPanel()->getId(), ['root', 'admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique()
                    ->maxLength(255),
                Forms\Components\TextInput::make('contact_number')
                    ->label('Contact Number')
                    ->required()
                    ->prefix('+63 ')
                    ->suffixIcon('heroicon-o-phone')
                    ->mask('99 9999 9999')
                    ->placeholder('9XX XXX XXXX')
                    ->mutateDehydratedStateUsing(fn ($state) => str_replace(' ', '', $state)),
                Forms\Components\Select::make('role')
                    ->options(UserRole::options())
                    ->required()
                    ->native(false)
                    ->live(),
                Section::make('Area of Responsibility')
                    ->description("This will be the area of resposibility of the user's.")
                    ->hidden(fn($get) => $get('role') !== UserRole::SUPERVISOR->value)
                    ->schema([
                        Repeater::make('address')
                            ->columns(['sm' => 1, 'md' => 3])
                            ->reorderable(false)
                            ->schema([
                                Forms\Components\Select::make('municipality')
                                    ->required()
                                    ->native(false)
                                    ->loadingMessage('Loading municipalities...')
                                    ->noSearchResultsMessage('No Municipalities found.')
                                    ->searchable('name')
                                    ->searchPrompt('Search municipalities...')
                                    ->options(Municipality::orderBy('name')->pluck('name', 'code'))
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                Forms\Components\Select::make('barangay')
                                    ->label('Barangay')
                                    ->options(function($get){
                                        $municipality = $get('municipality');
                                        if(!$municipality){
                                            return [];
                                        }
                                        return Municipality::where('code', $municipality)->first()?->barangays()->pluck('name', 'code') ?? [];
                                    })
                                    ->required()
                                    ->native(false)
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                Forms\Components\TextInput::make('purok')
                                    ->label('Purok')
                                    ->required()
                                    ->maxLength(255),
                            ])
                    ]),
                Section::make('Address')
                    ->description('This address will be used for identification and contact purposes.')
                    ->columns(['sm' => 1, 'md' => 3])
                    ->schema([
                        Forms\Components\Select::make('municipality')
                            ->required()
                            ->native(false)
                            ->loadingMessage('Loading municipalities...')
                            ->noSearchResultsMessage('No Municipalities found.')
                            ->searchable('name')
                            ->searchPrompt('Search municipalities...')
                            ->options(Municipality::orderBy('name')->pluck('name', 'code')),
                        Forms\Components\Select::make('barangay')
                            ->label('Barangay')
                            ->options(function($get){
                                $municipality = $get('municipality');
                                if(!$municipality){
                                    return [];
                                }
                                return Municipality::where('code', $municipality)->first()?->barangays()->pluck('name', 'code') ?? [];
                            })
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('purok')
                            ->label('Purok')
                            ->required()
                            ->maxLength(255),
                    ]),
                Section::make('Password')
                    ->visible(fn (Forms\Get $get) => !$get('id'))
                    ->description('Set the password for the user.')
                    ->columns(['sm' => 1, 'md' => 2])
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('retype_password')
                            ->password()
                            ->required()
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatarUrl')
                    ->label('Avatar')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_active')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ActionGroup::make([
                    Action::make('change_password')
                        ->label('Change Password')
                        ->form([
                            Forms\Components\TextInput::make('password')
                                ->password()
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('retype_password')
                                ->password()
                                ->required()
                                ->maxLength(255),
                        ])
                        ->action(function (User $record, array $data): void {
                            if ($data['password'] !== $data['retype_password']) {
                                Notification::make()
                                    ->title('Password did not match.')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            $record->update([
                                'password' => bcrypt($data['password']),
                            ]);
                            Notification::make()
                                ->title('Password changed successfully.')
                                ->success()
                                ->send();
                        })
                        ->color('warning')
                        ->icon('heroicon-o-key')
                        ->requiresConfirmation()
                        ->slideOver(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id', '!=', request()->user()->id)
            ->where('role','!=','root');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
