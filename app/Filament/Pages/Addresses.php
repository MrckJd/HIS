<?php

namespace App\Filament\Pages;

use App\Jobs\SyncAddress;
use App\Models\Municipality;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Addresses extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $model = Municipality::class;

    protected static ?string $navigationIcon = 'gmdi-map-o';

    protected static string $view = 'filament.pages.addresses';

    protected static ?int $navigationSort = 13;

    public bool $showProgress = false;

    public int $progress = 0;

    public static function canAccess(): bool
    {
        return in_array(Filament::getCurrentPanel()->getId(), ['root']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                TextColumn::make('name')->label('Municipality Name')->searchable(),
                ]),
                Panel::make([
                    TextColumn::make('barangays.name')
                ])->collapsible()
            ])
            ->poll(2)
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->query(Municipality::query());
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncAddress')
                ->label('Sync Address')
                ->action(function(){
                    $this->showProgress = true;

                    SyncAddress::dispatch();

                    $this->progress = 0;

                })
                ->color('warning')
                ->icon('heroicon-o-arrow-path'),
        ];
    }

    public function updateProgress()
    {
        $monitor = \Croustibat\FilamentJobsMonitor\Models\QueueMonitor::query()
            ->where('name', 'SyncAddress')
            ->latest('started_at')
            ->first();

        $this->progress = $monitor?->progress ?? 0;

        if ($monitor && ($monitor->progress >= 100 || $monitor->failed)) {
            Notification::make()
                ->title($monitor->failed ? 'Failed to sync address data.' : 'Address data synced successfully!')
                ->success(!$monitor->failed)
                ->send();

            $this->showProgress = false;
        }
    }
}
