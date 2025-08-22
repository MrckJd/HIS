<?php

namespace App\Filament\Resources;

use App\Enum\UserRole;
use App\Filament\Forms\AddMember;
use App\Filament\Resources\HouseholdResource\Pages;
use App\Filament\Services\PSGCService;
use App\Models\Household;
use App\Models\Member;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteAction;
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
                                                    ...AddMember::form(),
                                                    TextInput::make('is_leader')
                                                        ->default(true)
                                                        ->hidden()
                                                        ->dehydratedWhenHidden(),
                                                    ...AddMember::memberServicesForm(),
                                                ]),
                                        ])
                            ]),

                        Tab::make('Member')
                            ->schema([
                                Repeater::make('members')
                                    ->relationship('members')
                                    ->columnspanFull()
                                    ->defaultItems(0)
                                    ->collapsible()
                                    ->itemLabel(fn($state) => $state['first_name'] . ' ' . $state['surname'])
                                    ->columns(3)
                                    ->schema([
                                        ...AddMember::form(),
                                        ...AddMember::memberServicesForm(),
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
                TextColumn::make('members_count')
                    ->label('Members')
                    ->counts('members')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->bulkActions([
                BulkAction::make('mark_selected')
                    ->label('Generate ID')
                    ->icon('heroicon-o-identification')
                    ->action(function($records){
                        $members = Member::whereIn('household_id', $records->pluck('id')->toArray())->get();
                        $pdfContent = Pdf::view('filament.MemberID', ['members' => $members])
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
                        }, 'member_ids_.pdf');
                    })
            ]
            )
            ->actions([
                Tables\Actions\EditAction::make(),
                ActionGroup::make([
                    DeleteAction::make()
                        ->visible(fn ($record) => auth()->check() && (auth()->user()->role === UserRole::ADMIN->getLabel() || auth()->user()->role === UserRole::ROOT->getLabel()))
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
