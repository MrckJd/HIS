<?php

namespace App\Filament\Resources\DashBoardWidgetResource\Widgets;

use App\Filament\Services\PSGCService;
use App\Models\Household;
use App\Models\Member;
use Filament\Widgets\ChartWidget;

class MunicipalityChart extends ChartWidget
{
    protected static ?string $heading = 'Municipality';

    protected function getData(): array
    {
        // Fetching the count of members in each municipality

        return [
            'datasets' => [
                [
                    'label' => 'Population by Municipality',
                    'data' => [
                        Member::whereIn('household_id', Household::where('municipality', PSGCService::getMunicipalityCode('Bansalan'))->pluck('id'))->count(),
                        Member::whereIn('household_id', Household::where('municipality', PSGCService::getMunicipalityCode('Hagonoy'))->pluck('id'))->count(),
                        Member::whereIn('household_id', Household::where('municipality', PSGCService::getMunicipalityCode('Kiblawan'))->pluck('id'))->count(),
                        Member::whereIn('household_id', Household::where('municipality', PSGCService::getMunicipalityCode('Magsaysay'))->pluck('id'))->count(),
                        Member::whereIn('household_id', Household::where('municipality', PSGCService::getMunicipalityCode('Malalag'))->pluck('id'))->count(),
                        Member::whereIn('household_id', Household::where('municipality', PSGCService::getMunicipalityCode('Matanao'))->pluck('id'))->count(),
                        Member::whereIn('household_id', Household::where('municipality', PSGCService::getMunicipalityCode('Padada'))->pluck('id'))->count(),
                        Member::whereIn('household_id', Household::where('municipality', PSGCService::getMunicipalityCode('Santa Cruz'))->pluck('id'))->count(),
                        Member::whereIn('household_id', Household::where('municipality', PSGCService::getMunicipalityCode('Sulop'))->pluck('id'))->count(),
                    ],
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => ['Bansalan', 'Hagonoy', 'Kiblawan', 'Magsaysay', 'Malalag', 'Matanao', 'Padada', 'Santa Cruz', 'Sulop'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
