<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;

class QuotaSummaryChart extends LineChartWidget
{
    protected static ?string $heading = 'Last 24 hours';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return (bool) count(auth()->user()->devices);
    }

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],
        'scales' => [
            'x' => [
                'title' => [
                    'display' => true,
                    'text' => 'Time of Day',
                ],
            ],
            'y1' => [
                'title' => [
                    'display' => true,
                    'text' => 'Out [Wh]',
                ],
                'stack' => 'y',
                'reverse' => true,
                'beginAtZero' => true,
            ],
            'y2' => [
                'title' => [
                    'display' => true,
                    'text' => 'In [Wh]',
                ],
                'stack' => 'y',
                'beginAtZero' => true,
            ],
            'y3' => [
                'title' => [
                    'display' => true,
                    'text' => 'State of Charge [%]',
                ],
                'position' => 'right',
                'beginAtZero' => true,
                'max' => 100,
                'grid' => [
                    'display' => false,
                ],
            ],
        ],
        'animation' => false,
    ];

    // TODO: filter devices
    // TODO: filter time frame
    // TODO: show sunrise and sunset
    protected function getData(): array
    {
        $datasets = collect();
        $labels = collect();

        foreach (auth()->user()->devices as $device) {
            $data = $device->quotaSummaries()
                           ->latest('timestamp')
                           ->take(25)
                           ->get()
                           ->reverse();

            $datasets->push(
                [
                    'showLine' => false,
                    'pointRadius' => 0,
                    'pointHitRadius' => 0,
                    'data' => $data->pluck('watt_hours_in_sum'),
                    'stepped' => 'after',
                    'yAxisID' => 'y1',
                ],
            );

            $datasets->push(
                [
                    'label' => $device->label,
                    'data' => $data->pluck('watt_hours_in_sum'),
                    'stepped' => 'after',
                    'yAxisID' => 'y2',
                ],
            );

            $datasets->push(
                [
                    'label' => $device->label,
                    'data' => $data->pluck('watt_hours_out_sum'),
                    'stepped' => 'after',
                    'yAxisID' => 'y1',
                ],
            );

            $datasets->push(
                [
                    'showLine' => false,
                    'pointRadius' => 0,
                    'pointHitRadius' => 0,
                    'data' => $data->pluck('watt_hours_out_sum'),
                    'stepped' => 'after',
                    'yAxisID' => 'y2',
                ],
            );

            $datasets->push(
                [
                    'label' => $device->label,
                    'data' => $data->pluck('state_of_charge'),
                    'yAxisID' => 'y3',
                    'borderDash' => [5, 5],
                    'spanGaps' => true,
                ],
            );

            if ($labels->isEmpty()) {
                $labels = $data->pluck('timestamp')
                               ->map(fn ($item) => $item->timezone(
                                   auth()->user()->timezone
                               )->addHour()->format('H'));
            }
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }
}
