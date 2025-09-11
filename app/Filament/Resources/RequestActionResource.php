<?php

namespace App\Filament\Resources;

use App\Enum\RequestActionStatus;
use App\Filament\Actions\Table\ApprovedDeleteAction;
use App\Filament\Actions\Table\CancelDeleteMemberAction;
use App\Filament\Actions\Table\RejectDeleteAction;
use App\Filament\Resources\RequestActionResource\Pages;
use App\Models\RequestAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;

class RequestActionResource extends Resource
{
    protected static ?string $model = RequestAction::class;

    protected static ?string $navigationIcon = 'gmdi-call-to-action-o';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('action_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('reason')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('document')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('Pending'),
                Forms\Components\Select::make('member_id')
                    ->relationship('member', 'id')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query)=>$query->with(['member' => function($q){
                $q->withTrashed();
            }, 'user']))
            ->columns([
                Tables\Columns\TextColumn::make('action_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn($record)=>RequestActionStatus::from($record->status)->getColor()),
                Tables\Columns\TextColumn::make('meta')
                    ->formatStateUsing(fn($record)=> $record->meta['first_name'] . ' '
                                                    . substr($record->meta['middle_name'],0,1) . '. '
                                                    . $record->meta['surname'] . ' '
                                                    . $record?->meta['suffix'])
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    CancelDeleteMemberAction::make(),
                    ApprovedDeleteAction::make(),
                    RejectDeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->hidden(fn()=>!in_array(\Filament\Facades\Filament::getCurrentPanel()->getId(), ['root','admin'])),
                ]),
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
            'index' => Pages\ListRequestActions::route('/'),
        ];
    }
}
