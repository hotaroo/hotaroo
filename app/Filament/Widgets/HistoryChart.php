<?php

namespace App\Filament\Widgets;

use App\Models\QuotaSummary;
use Filament\Widgets\BarChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class HistoryChart extends BarChartWidget
{
    protected static ?string $heading = 'Energy captured';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],
        'scales' => [
            'x' => [
                'stacked' => true,
            ],
            'y' => [
                'title' => [
                    'display' => true,
                    'text' => 'In [kWh]',
                ],
                'stacked' => true,
            ],
        ],
    ];

    public ?string $filter = 'w';

    protected function getFilters(): ?array
    {
        return [
            'w' => 'Week',
            'm' => 'Month',
            'y' => 'Year',
        ];
    }

    public static function canView(): bool
    {
        return (bool) count(auth()->user()->devices);
    }

    // TODO: support timezone
    protected function getData(): array
    {
        switch ($this->filter) {
            case 'm':
                $start = now()->startOfDay()->subMonth();
                $end = now()->endOfDay();
                $interval = 'perDay';

                break;
            case 'y':
                $start = now()->startOfMonth()->subYear();
                $end = now()->endOfMonth();
                $interval = 'perMonth';

                break;
            default: // case 'w'
                $start = now()->startOfDay()->subWeek();
                $end = now()->endOfDay();
                $interval = 'perDay';
        }

        $datasets = collect();
        $labels = collect();

        foreach (auth()->user()->devices as $device) {
            $data = Trend::query(
                QuotaSummary::whereBelongsTo($device)
            )
                ->between(
                    start: $start,
                    end: $end,
                )
                ->$interval()
                ->dateColumn('timestamp')
                ->sum('watt_hours_in_sum');

            $datasets->push(
                [
                    'label' => $device->label,
                    'data' => $data->map(
                        fn (TrendValue $value) => $value->aggregate / 1000
                    ),
                    'borderWidth' => 3,
                ],
            );

            // TODO: date format
            if ($labels->isEmpty()) {
                $labels = $data->map(fn (TrendValue $value) => $value->date);
            }
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }
}
