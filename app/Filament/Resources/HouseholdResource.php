<?php

namespace App\Filament\Resources;

use App\Filament\Forms\AddMember;
use App\Filament\Resources\HouseholdResource\Pages;
use App\Filament\Services\PSGCService;
use App\Jobs\GeneratePDF;
use App\Models\Household;
use App\Models\Member;
use App\Models\Municipality;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;

class HouseholdResource extends Resource
{
    protected static ?string $model = Household::class;

    protected static ?string $navigationIcon = 'fluentui-people-community-16';

    public static function canAccess(): bool
    {
        return in_array(Filament::getCurrentPanel()->getId(), ['root', 'admin', 'encoder']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Household Information')
                    ->contained(false)
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Basic Information')
                            ->schema([
                                Section::make('Address')
                                    ->columns(['lg'=>3])
                                    ->description('Please input Household Address')
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
                                            ->loadingMessage('Loading branggays...')
                                            ->reactive()
                                            ->noSearchResultsMessage('No Baranggays found.')
                                            ->searchable('name')
                                            ->native(false)
                                            ->required()
                                            ->options(function($get){
                                                $municipality = $get('municipality');
                                                if(!$municipality){
                                                    return [];
                                                }
                                                return Municipality::where('code', $municipality)->first()?->barangays()->pluck('name', 'code') ?? [];
                                            }),
                                        Forms\Components\TextInput::make('purok')
                                            ->label('Purok / Sitio')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                                Section::make('Leader Information')
                                        ->description('Please input Household Leader Personal Information')
                                        ->schema([
                                            Repeater::make('leader')
                                                ->relationship('leader')
                                                ->defaultItems(1)
                                                ->columns(['lg'=>3])
                                                ->addable(false)
                                                ->reorderable(false)
                                                ->deletable(false)
                                                ->label('')
                                                ->schema([
                                                    TextInput::make('is_leader')
                                                    ->default(true)
                                                    ->hidden()
                                                    ->dehydratedWhenHidden(),
                                                    ...AddMember::form(),
                                                ]),
                                        ])
                            ]),

                        Tab::make('Member')
                            ->schema([
                                Repeater::make('members')
                                    ->relationship('members')
                                    ->columnspanFull()
                                    ->defaultItems(3)
                                    ->collapsible()
                                    ->itemLabel(fn($state) => $state['first_name'] . ' ' . $state['surname'])
                                    ->columns(3)
                                    ->schema([
                                        ...AddMember::form(),
                                    ]),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('leader_name')
                    ->label('Leader')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('leader', function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('middle_name', 'like', "%{$search}%")
                              ->orWhere('surname', 'like', "%{$search}%")
                              ->orWhere('suffix', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('address')
                    ->label('Complete Address'),
                TextColumn::make('user.name')
                    ->visible(fn()=>Filament::getCurrentPanel()->getId() == 'admin')
                    ->label('Encoder'),
                TextColumn::make('members_count')
                    ->label('Members')
                    ->counts('members')
                    ->badge(),
            ])
            ->deferLoading()
            ->modifyQueryUsing(function($query){
                 if(Filament::getCurrentPanel()->getId() == 'encoder'){
                    return $query->where('user_id', request()->user()->id);
                 }
                return $query;
            })
            ->bulkActions([
                BulkAction::make('mark_selected')
                    ->hidden(fn()=>Filament::getCurrentPanel()->getId() == 'encoder')
                    ->label('Generate ID')
                    ->icon('heroicon-o-identification')
                    ->action(function($records){
                        $filename = 'member_ids_' . now()->timestamp . '.pdf';
                        $householdIds = $records->pluck('id')->toArray();

                        GeneratePDF::dispatch($householdIds, $filename);

                        Notification::make()
                            ->title('PDF Generation')
                            ->body('Your ' . $filename . ' is being generated and will be available for download shortly.')
                            ->send();
                    })
            ]
            )
            ->actions([
                Tables\Actions\EditAction::make(),
                ActionGroup::make([
                    DeleteAction::make()
                        ->hidden(fn()=>Filament::getCurrentPanel()->getId() == 'encoder')
                ])
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
