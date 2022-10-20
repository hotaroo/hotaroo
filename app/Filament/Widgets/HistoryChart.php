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
                $start = now(auth()->user()->timezone)
                             ->startOfDay()
                             ->subMonth();
                $end = now(auth()->user()->timezone)->endOfDay();
                $interval = 'perDay';
                $format = 'd';

                break;
            case 'y':
                $start = now(auth()->user()->timezone)
                             ->startOfMonth()
                             ->subYear();
                $end = now(auth()->user()->timezone)->endOfMonth();
                $interval = 'perMonth';
                $format = 'M';

                break;
            default: // case 'w'
                $start = now(auth()->user()->timezone)->startOfDay()->subWeek();
                $end = now(auth()->user()->timezone)->endOfDay();
                $interval = 'perDay';
                $format = 'D';
        }

        $datasets = collect();
        $labels = collect();

        foreach (auth()->user()->devices as $device) {
            $trend = Trend::query(
                QuotaSummary::whereBelongsTo($device)
            )
                ->between(
                    start: $start,
                    end: $end,
                )
                ->$interval()
                ->dateColumn('timestamp')
                ->convertTimezone(
                    from: config('app.timezone'),
                    to: auth()->user()->timezone
                );

            $datasets->push(
                [
                    'label' => $device->label,
                    'data' => $trend->sum('watt_hours_in_sum')->map(
                        fn (TrendValue $value) => $value->aggregate / 1000
                    ),
                    'borderWidth' => 3,
                ],
            );

            if ($labels->isEmpty()) {
                $labels = $trend->placeholders($format)
                                ->map(fn (TrendValue $value) => $value->date);
            }
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }
}
