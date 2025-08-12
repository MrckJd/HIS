<?php

namespace App\Filament\Resources\DashBoardWidgetResource\Widgets;

use App\Models\MemberServices;
use App\Models\Service;
use Filament\Widgets\ChartWidget;

class ServicesPieChart extends ChartWidget
{
    protected static ?string $heading = 'Services Distribution';

    protected function getData(): array
    {


        $member_service = MemberServices::with('service')->selectRaw('service_id, count(*) as count')->groupBy('service_id')->get();

        $service_count = [];
        $service_labes = [];

        if($member_service->count() > 0) {
            foreach ($member_service as $service) {
                $service_count[] = $service->count;
                $service_labes[] = Service::find($service->service_id)->name ?? 'Unknown Service';
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Services Distribution',
                    'data' => count($service_count) > 0 ? $service_count : [0],

                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                    'borderColor' => '#FFFFFF',
                ],
            ],
            'labels' => count($service_labes) == 0 ? ['No Services'] : $service_labes,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
