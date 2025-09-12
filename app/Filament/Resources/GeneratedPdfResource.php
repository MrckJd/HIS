<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GeneratedPdfResource\Pages;
use App\Models\QueueMonitor;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GeneratedPdfResource extends Resource
{
    protected static ?string $model = QueueMonitor::class;

    protected static ?string $navigationIcon = 'fas-id-card';

    protected static ?string $navigationLabel = 'Generated PDF';

    protected static ?string $label = 'Generated PDFs';

    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return in_array(Filament::getCurrentPanel()->getId(), ['root', 'admin']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->label(__('filament-jobs-monitor::translations.status'))
                    ->formatStateUsing(fn (string $state): string => __("filament-jobs-monitor::translations.{$state}"))
                    ->color(fn (string $state): string => match ($state) {
                        'running' => 'primary',
                        'succeeded' => 'success',
                        'failed' => 'danger',
                    }),
                TextColumn::make('name')
                    ->label(__('filament-jobs-monitor::translations.name'))
                    ->sortable(),
                ViewColumn::make('progress')
                    ->label(__('filament-jobs-monitor::translations.progress'))
                    ->view('filament.table.progress-column')
                    ->sortable(),
                TextColumn::make('started_at')
                    ->label(__('filament-jobs-monitor::translations.started_at'))
                    ->since()
                    ->sortable(),
            ])
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('name', '!=', 'SyncAddress'))
            ->defaultSort  ('started_at', 'desc')
            ->filters([
                //
            ])
            ->poll('2')
            ->actions([
                Action::make('download')
                    ->label('')
                    ->icon('fas-download')
                    ->visible(fn (QueueMonitor $record): bool => $record->status === 'succeeded')
                    ->action(function ($record) {
                        $filename = $record->name ?? 'file.pdf';
                        $path = storage_path('app/public/PDF/' . $filename);

                        if (file_exists($path)) {
                            return response()->download($path, $filename);
                        }

                        return redirect()->back()->with('error', 'File not found.');
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGeneratedPdfs::route('/'),
        ];
    }
}
