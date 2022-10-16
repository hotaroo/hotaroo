<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class QuotaWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getCards(): array
    {
        $cards = collect();

        foreach (auth()->user()->devices as $device) {
            $quotas = $device->quotas()
                             ->latest('timestamp')
                             ->take(60)
                             ->get()
                             ->reverse();

            $cards->push(
                Card::make(
                    'State of Charge',
                    is_null(optional($quotas->last())->state_of_charge)
                    ? 'Disconnected'
                    : $quotas->last()->state_of_charge.' %'
                )
                    ->description($device->label)
                    ->descriptionIcon('heroicon-s-lightning-bolt')
                    ->chart($quotas->pluck('state_of_charge')->all())
                    ->color(
                        is_null(optional($quotas->last())->state_of_charge)
                        ? 'warning'
                        : 'success'
                    )
            );

            $cards->push(
                Card::make(
                    'In',
                    is_null(optional($quotas->last())->watts_in_sum)
                    ? 'Disconnected'
                    : $quotas->last()->watts_in_sum.' W'
                )
                    ->description($device->label)
                    ->descriptionIcon('heroicon-s-lightning-bolt')
                    ->chart($quotas->pluck('watts_in_sum')->all())
                    ->color(
                        is_null(optional($quotas->last())->watts_in_sum)
                        ? 'warning'
                        : 'success'
                    )
            );

            $cards->push(
                Card::make(
                    'Out',
                    is_null(optional($quotas->last())->watts_out_sum)
                    ? 'Disconnected'
                    : $quotas->last()->watts_out_sum.' W'
                )
                    ->description($device->label)
                    ->descriptionIcon('heroicon-s-lightning-bolt')
                    ->chart($quotas->pluck('watts_out_sum')->all())
                    ->color(
                        is_null(optional($quotas->last())->watts_out_sum)
                        ? 'warning'
                        : 'success'
                    )
            );
        }

        return $cards->all();
    }
}
