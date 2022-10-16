<?php

namespace App\Faker;

use Carbon\CarbonPeriod;
use Faker\Provider\Base;

class TimeSeriesProvider extends Base
{
    protected static $period;

    public static function timeSeries($start, $interval)
    {
        if (! static::$period) {
            static::$period = CarbonPeriod::create($start, $interval, INF);
        }

        $current = static::$period->current();
        static::$period->next();

        return $current->startOfMinute();
    }
}
