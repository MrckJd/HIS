<?php

namespace App\Filament\Pages;

use App\Enum\UserRole;
use App\Filament\Forms\AddMember;
use App\Filament\Services\PSGCService;
use App\Models\Member;
use App\Models\Service;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;

class AddMemberServices extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static string $view = 'filament.pages.add-member-services';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Add member services';

    public static function canAccess(): bool
    {
        return in_array(Filament::getCurrentPanel()->getId(), ['root', 'admin', 'serviceProvider']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatarUrl')
                    ->label('Avatar')
                    ->circular(),
                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->getStateUsing(fn ($record) => trim(
                        $record->surname . ', ' . $record->first_name . ' ' . $record->middle_name
                    ))
                    ->description(fn ($record) => $record->code)
                    ->searchable(['first_name', 'surname', 'code']),
                TextColumn::make('gender')
                    ->badge(),
                TextColumn::make('household.address')
                    ->label('Complete Address')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('Precinct No/Cluster No')
                    ->label('Precinct/Cluster')
                    ->getStateUsing(fn ($record) => trim(
                        $record->precinct_no . ' - ' . $record->cluster_no
                    )),
                TextColumn::make('services_count')
                    ->label('Services Availed')
                    ->counts('services')
                    ->badge()
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading('Services Availed')
                    ->infolist([
                        Section::make()
                            ->schema([
                                ImageEntry::make('avatarUrl')
                                    ->label('')
                                    ->circular()
                                    ->size(60)
                                    ->defaultImageUrl(url('/images/default-avatar.png'))
                                    ->extraAttributes(['class' => 'mx-auto w-8 h-8']), // Center and size avatar
                                TextEntry::make('member.name')
                                    ->label('')
                                    ->getStateUsing(fn ($record) => trim(
                                        $record->surname . ', ' . $record->first_name . ' ' . $record->middle_name
                                    ))
                                    ->alignCenter()
                                    ->weight('bold')
                                    ->size('lg')
                                    ->extraAttributes(['class' => 'text-center mt-2']), // Center and style name
                            ])
                            ->columns(1)
                            ->extraAttributes(['class' => 'text-center']), // Center section content
                       Section::make('Services Availed')
                            ->schema([
                                RepeatableEntry::make('memberServices')
                                    ->hiddenLabel()
                                    ->contained(false)
                                    ->columns(2)
                                    ->schema([
                                        TextEntry::make('service.name')->hiddenLabel(),
                                        TextEntry::make('date_received')->date('Y-m-d')->hiddenLabel(),
                            ])
                        ])
                        ->collapsible(),
                    ]),
                Action::make('manageServices')
                    ->label('Add / Edit Services')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->modalHeading(fn (Member $record) => 'Services for ' . $record->first_name)
                    ->modalWidth('lg')
                    ->form([
                        TableRepeater::make('memberServices')
                            ->relationship('memberServices')
                            ->schema([
                                Select::make('service_id')
                                    ->label('Service')
                                    ->options(Service::pluck('name', 'id')->toArray())
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                DatePicker::make('date_received')
                                    ->label('Date Received')
                                    ->required()
                                    ->default(now())
                                    ->displayFormat('Y-m-d'),
                                ])
                                ->columns(2)
                                ->addActionLabel('Add Service')
                                ->deleteAction(
                                    fn ($action) => $action->requiresConfirmation()
                                )
                                ->default(function (Member $record) {
                                    return $record->services->map(function ($service) {
                                        return [
                                            'service_id' => $service->id,
                                            'date_received' => Carbon::parse($service->pivot->date_received)->format('Y-m-d'),
                                        ];
                                    })->toArray();
                                })
                                // ->rules([
                                //     'unique_dates' => function () {
                                //         return function ($attribute, $value, $fail) {
                                //             $dates = collect($value)->pluck('date_received')->filter()->toArray();
                                //             if (count(array_unique($dates)) !== count($dates)) {
                                //                 $fail('Duplicate dates are not allowed. Each service must have a unique date received.');
                                //             }
                                //         };
                                //     },
                                // ])
                    ])
                    ->modalSubmitActionLabel('Save')
                    ->closeModalByClickingAway(false),
            ])
            ->filters([

            ])
            ->query(Member::query())
            ->recordAction('view');
    }



}
