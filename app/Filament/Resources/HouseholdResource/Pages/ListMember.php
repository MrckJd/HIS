<?php

namespace App\Filament\Resources\HouseholdResource\Pages;

use App\Filament\Actions\BackAction;
use App\Filament\Actions\Table\IsLeaderAction;
use App\Filament\Actions\Table\ViewIdAction;
use App\Filament\Forms\AddMember;
use App\Filament\Resources\HouseholdResource;
use App\Models\Service;
use BladeUI\Icons\Components\Icon;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;

class ListMember extends ManageRelatedRecords
{
    protected static string $resource = HouseholdResource::class;

    protected static string $relationship = 'listmembers';

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

        $leaderName = $leader->first()->first_name . ' ' . ($leader->first()->middle_name ? substr($leader->first()->middle_name, 0, 1) . '. ' : '') . $leader->first()->surname . ' ' . ($leader->first()->suffix ? $leader->first()->suffix : '');

        return $title . ' (' . $leaderName . ')';
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatarUrl')
                    ->label('Avatar')
                    ->disk('public')
                    ->circular(),
                TextColumn::make('full_name')
                    ->label('Name')
                    ->formatStateUsing(fn($state, $record) => $state . ' (' . ($record->role ? $record->role : 'No Role') . ')')
                    ->description(fn($record) => $record->is_leader ? 'Household Leader' : '')
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
            ->bulkActions([
                BulkAction::make('mark_selected')
                    ->label('Generate ID')
                    ->icon('heroicon-o-identification')
                    ->action(function($records){
                        $pdfContent = Pdf::view('filament.MemberID', ['members' => $records])
                            ->format(Format::A4)
                            ->withBrowsershot(fn (Browsershot $bs) => $bs
                                ->portrait()
                                ->noSandbox()
                                ->setDelay(2000)
                                ->timeout(60)
                                ->showBackground()
                            )
                            ->base64();

                        return response()->streamDownload(function() use ($pdfContent) {
                            echo base64_decode($pdfContent);
                        }, 'member_ids_' . $records->first()->household->title . '.pdf');
                    })
            ])
            ->actions([
                ViewIdAction::make(),
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


