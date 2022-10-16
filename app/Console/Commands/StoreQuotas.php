<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class StoreQuotas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quota:store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store current quotas for all devices';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (User::all() as $user) {
            if ($user->ecoflow_key && $user->ecoflow_secret) {
                foreach ($user->devices as $device) {
                    $device->storeQuota();
                }
            }
        }

        return Command::SUCCESS;
    }
}
