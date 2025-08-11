<?php

namespace App\Filament\Resources\HouseholdResource\Pages;

use App\Filament\Actions\BackAction;
use App\Filament\Actions\Table\IsLeaderAction;
use App\Filament\Forms\AddMember;
use App\Filament\Resources\HouseholdResource;
use App\Models\Household;
use App\Models\Service;
use Filament\Support\Colors\Color;
use Filament\Actions;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\IconColumn\IconColumnSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;

class ListMember extends ManageRelatedRecords
{
    protected static string $resource = HouseholdResource::class;

    protected static string $relationship = 'members';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static bool $canCreateAnother = false;

    public function mount($record):void
    {
        parent::mount($record);

        $this->previousUrl = HouseholdResource::getUrl();
    }
    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()->url($this->previousUrl),
            Actions\Action::make('members')
                ->label('Add Member')
                ->icon('heroicon-o-user-plus')
                ->successNotificationTitle('Member added successfully')
                ->form([
                    Tabs::make('Member Details')
                                ->contained(false)
                                ->tabs([
                                    Tab::make('Personal Info')
                                        ->columns(3)
                                        ->schema(AddMember::form()),
                                    Tab::make('Services')
                                        ->schema([TableRepeater::make('memberServices')
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
                                                ]),]),
                                                                ]),
                                ])
                                ->modalWidth(MaxWidth::FourExtraLarge)
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
                    ->label('')
                    ->size(IconColumnSize::Medium)
                    ->trueIcon('fas-crown')
                    ->state(function($record) {
                        return $record->is_leader ? 'fas-crown' : '';
                    })
                    ->color(Color::Amber),
                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(['surname', 'first_name', 'middle_name', 'suffix'])
                    ->sortable(fn($query) => $query)
                    ->sortable(query: function ($query, $direction) {
                        return $query
                            ->orderBy('surname', $direction)
                            ->orderBy('first_name', $direction)
                            ->orderBy('middle_name', $direction)
                            ->orderBy('suffix', $direction);
                    }),
                TextColumn::make('precinct_no')
                    ->label('Precinct No.')
                    ->sortable()
                    ->default('N/A')
                    ->badge(fn($record) => $record->precinct_no ? true : false)
                    ->color(fn($record) => $record->precinct_no ? 'danger' : ''),
                TextColumn::make('cluster_no')
                    ->label('Cluster No.')
                    ->sortable()
                    ->color(fn($record) => $record->cluster_no ? 'danger' : '')
                    ->default('N/A')
                    ->badge(fn($record) => $record->cluster_no ? true : false),
                TextColumn::make('member_services_count')
                    ->counts('memberServices')
                    ->label('Services Availed')
                    ->badge(fn($record) => $record->memberServices->count() > 0)
                    ->color(fn($record) => $record->memberServices->count() > 0 ? 'success' : '')
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state > 0 ? $state : 'N/A'),
            ])
            ->actions([
                ActionGroup::make([
                    IsLeaderAction::make(),
                    EditAction::make('edit')
                        ->icon('heroicon-o-pencil')
                        ->modalWidth(MaxWidth::FourExtraLarge)
                        ->form([
                            Tabs::make('Member Details')
                                ->contained(false)
                                ->tabs([
                                    Tab::make('Personal Info')
                                        ->columns(3)
                                        ->schema(AddMember::form()),
                                    Tab::make('Services')
                                        ->schema([
                                            TableRepeater::make('members.memberServices')
                                                ->relationship('memberServices')
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
                                                        ->searchable()
                                                        ->required(),
                                                    DatePicker::make('date_received')
                                                        ->label('Date Received')
                                                        ->required(),
                                            ]),
                                        ]),
                                ]),
                            ]),
                    Action::make('delete')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->action(fn($record) => $record->delete())
                        ->successNotificationTitle('Member deleted successfully'),
                ])
            ]);
    }
}


