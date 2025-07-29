<?php

namespace App\Filament\Resources\HouseholdResource\Pages;

use App\Filament\Forms\AddMember;
use App\Filament\Resources\HouseholdResource;
use Filament\Support\Colors\Color;
use Filament\Actions;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\IconColumn\IconColumnSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ListMember extends ManageRelatedRecords
{
    protected static string $resource = HouseholdResource::class;

    protected static string $relationship = 'members';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static bool $canCreateAnother = false;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(HouseholdResource::getUrl()),
            Actions\Action::make('members')
                ->label('Add Member')
                ->successNotificationTitle('Member added successfully')
                ->slideOver()
                ->form(AddMember::form())
                ->modalWidth(MaxWidth::Large)
                ->action(fn($record,$data)=> $record->members()->create($data)),
        ];
    }

    public function getTitle(): string
    {

        $title = $this->getRecord()->title;
        $leader = $this->getRecord()
                        ->members()
                        ->where('is_leader', true)
                        ->get();

        if ($leader->isEmpty()) {
            return $title . ' (No Leader)';
        }

        $leaderName = $leader->first()->first_name . ' ' . ($leader->first()->middle_name ? $leader->first()->middle_name : '') . ' ' . $leader->first()->surname . ' ' . ($leader->first()->suffix ? $leader->first()->suffix : '');

        return $title . ' (' . $leaderName . ')';
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_leader')
                    ->label('   ')
                    ->size(IconColumnSize::Medium)
                    ->trueIcon('fas-crown')
                    ->state(function($record) {
                        return $record->is_leader ? 'fas-crown' : '';
                    })
                    ->color(Color::Amber),
                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('precinct_no')
                    ->label('Precinct No.')
                    ->searchable(),
                TextColumn::make('cluster_no')
                    ->label('Cluster No.')
                    ->searchable(),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('is_leader')
                        ->label('Set as Leader')
                        ->hidden(fn($record) => $record->is_leader)
                        ->icon('fas-crown')
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $record->household->members()
                                ->where('id', '!=', $record->id)
                                ->update(['is_leader' => false]);
                            $record->update(['is_leader' => true]);
                        }),
                    EditAction::make('edit')
                        ->icon('heroicon-o-pencil')
                        ->form(AddMember::form())
                        ->modalWidth(MaxWidth::Large)
                        ->slideOver(),
                    Action::make('delete')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->action(fn($record) => $record->delete())
                        ->successNotificationTitle('Member deleted successfully'),
                ])
            ])
            // ->recordClasses(function ($record) {
            //     return $record->is_leader ? '!bg-amber-500 hover:!amber-400' : '';
            // })
            ;
    }
}


