<?php

namespace App\Filament\Pages;

use App\Enum\UserRole;
use App\Models\Household;
use App\Filament\Services\PSGCService;
use App\Models\Member;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;
use Filament\Pages\Page;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use App\Models\Service;
use Filament\Facades\Filament;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DataFilter extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.data-filter';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Reports';


    public static function canAccess(): bool
    {
        return in_array(Filament::getCurrentPanel()->getId(), ['root', 'admin']);
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
                    ->searchable(['first_name', 'surname']),
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
            ])
            ->filters([
               SelectFilter::make('services')
                    ->label('Filter Services Availed')
                    ->relationship('services', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['values'])) {
                            $query->whereHas('services', function ($q) use ($data) {
                                $q->whereIn('services.id', $data['values']);
                            });
                        }
                    }),
                \Filament\Tables\Filters\Filter::make('age_range')
                    ->label('Age Range')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('min')->numeric()->minValue(0)->label('Min Age'),
                        \Filament\Forms\Components\TextInput::make('max')->numeric()->minValue(0)->label('Max Age'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $min = $data['min'] ?? null;
                        $max = $data['max'] ?? null;

                        if ($min !== null && $min !== '') {
                            $query->whereDate('birth_date', '<=', now()->subYears((int) $min)->toDateString());
                        }

                        if ($max !== null && $max !== '') {
                            $query->whereDate('birth_date', '>=', now()->subYears((int) $max + 1)->addDay()->toDateString());
                        }
                    })
                    ->indicateUsing(function (array $data) {
                        $indicators = [];
                        if (filled($data['min'] ?? null)) {
                            $indicators[] = 'Min Age: ' . $data['min'];
                        }
                        if (filled($data['max'] ?? null)) {
                            $indicators[] = 'Max Age: ' . $data['max'];
                        }
                        return $indicators;
                    }),
                \Filament\Tables\Filters\Filter::make('municipality')
                    ->label('Municipality')
                    ->form([
                        \Filament\Forms\Components\Select::make('municipality')
                            ->label('Municipality')
                            ->options(PSGCService::getMunicipalities())
                            ->searchable()
                            ->preload(),
                        \Filament\Forms\Components\Select::make('barangay')
                            ->label('Barangay')
                            ->options(function (callable $get) {
                                $municipality = $get('municipality') ?? '';
                                return PSGCService::getBarangays($municipality);
                            })
                            ->searchable()
                            ->preload(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['municipality'])) {
                            $query->whereHas('household', fn ($q) => $q->where('municipality', $data['municipality']));
                        }

                        if (!empty($data['barangay'])) {
                            $query->whereHas('household', fn ($q) => $q->where('barangay', $data['barangay']));
                        }
                    })
                    ->indicateUsing(function (array $data) {
                        $indicators = [];
                        if (filled($data['municipality'] ?? null)) {
                            $indicators[] = 'Municipality: ' . PSGCService::getMunicipalityName($data['municipality']);
                        }
                        if ((filled($data['barangay'] ?? null)) && (filled($data['municipality'] ?? null))) {
                            $indicators[] = 'Barangay: ' . PSGCService::getBarangayName($data['barangay'], $data['municipality']);
                        }
                        return $indicators;
                    }),
            ])
            ->query(Member::query())
            ->modifyQueryUsing(fn (Builder $query) => $query->with('household'))
            ->groups([
                \Filament\Tables\Grouping\Group::make('household_id')
                    ->label('Household')
                    ->getTitleFromRecordUsing(fn (Member $record) => $record->household?->address ?? 'No Household')
                    ->collapsible(),
                \Filament\Tables\Grouping\Group::make('gender')
                    ->label('Gender')
                    ->collapsible(),
            ])
            ->recordAction('view');
    }



}
