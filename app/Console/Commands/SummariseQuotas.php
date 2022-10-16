<?php

namespace App\Console\Commands;

use App\Models\QuotaSummary;
use App\Models\User;
use Illuminate\Console\Command;

class SummariseQuotas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quota:summarise';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Summarise quotas for all devices';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (User::all() as $user) {
            foreach ($user->devices as $device) {
                $summaries = $device->quotaSummaries()
                                    ->latest('timestamp')
                                    ->take(24)
                                    ->get()
                                    ->sortBy('last_quota_id');

                $lastQuotaId = optional($summaries->last())->last_quota_id;
                $quotas = $device->quotas()
                                 ->where('id', '>', (int) $lastQuotaId)
                                 ->get();

                foreach ($quotas as $quota) {
                    $timestamp = $quota->timestamp->startOfHour()->addHour();

                    $currentSummary = $summaries->firstWhere(
                        'timestamp', $timestamp
                    );
                    $previousSummary = $summaries->firstWhere(
                        'timestamp', $timestamp->subHour()
                    );

                    if (! $currentSummary) {
                        if (! $previousSummary) {
                            $previousSummary = $device->latestQuotaSummary;
                        }

                        $currentSummary = new QuotaSummary;

                        $currentSummary->timestamp = $timestamp;
                        $currentSummary->state_of_charge
                            = $quota->state_of_charge;
                        $currentSummary->watt_hours_out_sum
                            = $quota->watts_out_sum / 60;
                        $currentSummary->watt_hours_out_cumsum
                            = optional($previousSummary)->watt_hours_out_cumsum
                            + $currentSummary->watt_hours_out_sum;
                        $currentSummary->watt_hours_in_sum
                            = $quota->watts_in_sum / 60;
                        $currentSummary->watt_hours_in_cumsum
                            = optional($previousSummary)->watt_hours_in_cumsum
                            + $currentSummary->watt_hours_in_sum;
                        $currentSummary->last_quota_id = $quota->id;
                        $currentSummary->quota_count = 1;

                        $summaries->push($currentSummary);
                    } else {
                        $currentSummary->state_of_charge
                            = $quota->state_of_charge;
                        $currentSummary->watt_hours_out_sum
                            += $quota->watts_out_sum / 60;
                        $currentSummary->watt_hours_out_cumsum
                            += $quota->watts_out_sum / 60;
                        $currentSummary->watt_hours_in_sum
                            += $quota->watts_in_sum / 60;
                        $currentSummary->watt_hours_in_cumsum
                            += $quota->watts_in_sum / 60;
                        $currentSummary->last_quota_id = $quota->id;
                        $currentSummary->quota_count++;

                        $summaries->merge([$currentSummary]);
                    }
                }

                foreach ($summaries as $summary) {
                    $device->quotaSummaries()->save($summary);
                }
            }
        }

        return Command::SUCCESS;
    }
}
