<?php

namespace App\Filament\Resources\HouseholdResource\Pages;

use App\Filament\Forms\AddMember;
use App\Filament\Resources\HouseholdResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Enums\MaxWidth;
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
                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('precinct_no')
                    ->label('Precinct No.')
                    ->searchable(),
                TextColumn::make('cluster_no')
                    ->label('Cluster No.')
                    ->searchable(),
                ToggleColumn::make('is_leader')
                    ->label('Leader')
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state) {
                            $record->household->members()
                                ->where('id', '!=', $record->id)
                                ->update(['is_leader' => false]);
                        }
                    }),
            ]);
    }
}


