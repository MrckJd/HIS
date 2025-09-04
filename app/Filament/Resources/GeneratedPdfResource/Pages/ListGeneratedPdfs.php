<?php

namespace App\Filament\Resources\GeneratedPdfResource\Pages;

use App\Filament\Resources\GeneratedPdfResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGeneratedPdfs extends ListRecords
{
    protected static string $resource = GeneratedPdfResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
