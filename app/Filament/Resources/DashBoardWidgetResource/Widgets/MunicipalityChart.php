<?php

namespace App\Filament\Resources\DashBoardWidgetResource\Widgets;

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
                    Member::whereIn('household_id', Household::where('municipality', 'Bansalan')->pluck('id'))->count(),
                    Member::whereIn('household_id', Household::where('municipality', 'Hagonoy')->pluck('id'))->count(),
                    Member::whereIn('household_id', Household::where('municipality', 'Kiblawan')->pluck('id'))->count(),
                    Member::whereIn('household_id', Household::where('municipality', 'Magsaysay')->pluck('id'))->count(),
                    Member::whereIn('household_id', Household::where('municipality', 'Malalag')->pluck('id'))->count(),
                    Member::whereIn('household_id', Household::where('municipality', 'Matanao')->pluck('id'))->count(),
                    Member::whereIn('household_id', Household::where('municipality', 'Padada')->pluck('id'))->count(),
                    Member::whereIn('household_id', Household::where('municipality', 'Santa Cruz')->pluck('id'))->count(),
                    Member::whereIn('household_id', Household::where('municipality', 'Sulop')->pluck('id'))->count(),
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
