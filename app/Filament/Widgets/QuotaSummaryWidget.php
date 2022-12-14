<?php

namespace App\Filament\Widgets;

use Carbon\CarbonInterface;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class QuotaSummaryWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected function getCards(): array
    {
        $cards = collect();

        foreach (auth()->user()->devices as $device) {
            $quotaSummary = $device->latestQuotaSummary;

            $cards->push(
                Card::make(
                    'In in '
                    .optional(optional($device->created_at)->startOfDay())
                        ->diffForHumans(
                            now()->startOfDay()->addDay(),
                            CarbonInterface::DIFF_ABSOLUTE
                        ),
                    number_format(
                        optional($quotaSummary)->watt_hours_in_cumsum / 1000,
                        3,
                        '.',
                        '\'',
                    ).' kWh'
                )
                    ->description($device->label)
                    ->descriptionIcon('heroicon-s-lightning-bolt')
            );

            $cards->push(
                Card::make(
                    'Out in '
                    .optional(optional($device->created_at)->startOfDay())
                        ->diffForHumans(
                            now()->startOfDay()->addDay(),
                            CarbonInterface::DIFF_ABSOLUTE
                        ),
                    number_format(
                        optional($quotaSummary)->watt_hours_out_cumsum / 1000,
                        3,
                        '.',
                        '\'',
                    ).' kWh'
                )
                    ->description($device->label)
                    ->descriptionIcon('heroicon-s-lightning-bolt')
            );

            // TODO: in and out records
            // TODO: energy captured today
            // TODO: energy provided today

            if ($device->investment && $device->price_per_kilowatt_hour) {
                $cards->push(
                    Card::make(
                        'Return on Investment',
                        number_format(
                            $device->returnOnInvestment() * 100,
                            1,
                            '.',
                            '\'',
                        ).' %'
                    )
                        ->description($device->label)
                        ->descriptionIcon('heroicon-s-lightning-bolt')
                );
            }

            if ($device->investment) {
                $cards->push(
                    Card::make(
                        $device->currency.'/kWh In',
                        number_format(
                            $device->kilowattHoursInCost(),
                            4,
                            '.',
                            '\'',
                        )
                    )
                        ->description($device->label)
                        ->descriptionIcon('heroicon-s-lightning-bolt')
                );
            }
        }

        return $cards->all();
    }
}
