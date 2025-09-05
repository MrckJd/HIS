<?php

namespace App\Filament\Resources\DashBoardWidgetResource\Widgets;

use App\Filament\Services\PSGCService;
use App\Models\Household;
use App\Models\Member;
use App\Models\Municipality;
use Filament\Widgets\ChartWidget;

class MunicipalityChart extends ChartWidget
{
    protected static ?string $heading = 'Municipality';

    protected function getData(): array
    {
        $municipalities = Municipality::query()->orderBy('name')->get();

        $labels = [];
        $data = [];

        foreach ($municipalities as $municipality) {
            $labels[] = $municipality->name;
            $householdIds = Household::where('municipality', $municipality->code)->pluck('id');
            $data[] = Member::whereIn('household_id', $householdIds)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Population by Municipality',
                    'data' => $data,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
