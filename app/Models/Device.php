<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Http;

class Device extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'serial_number',
        'name',
        'latitude',
        'longitude',
        'currency',
        'investment',
        'price_per_kilowatt_hour',
    ];

    /**
     * Get the user that owns the device.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the quotas of the device.
     */
    public function quotas()
    {
        return $this->hasMany(Quota::class);
    }

    /**
     * Store the current quota of the device.
     */
    public function storeQuota()
    {
        $response = Http::ecoflow()
            ->withHeaders([
                'appKey' => $this->user->ecoflow_key,
                'secretKey' => $this->user->ecoflow_secret,
            ])
            ->retry(3, 100)
            ->get('/device/queryDeviceQuota', [
                'sn' => $this->serial_number,
            ]);

        $data = optional($response->object())->data;

        return $this->quotas()->updateOrCreate([
            'timestamp' => now()->startOfMinute(),
        ], [
            'state_of_charge' => optional($data)->soc,
            'watts_out_sum' => optional($data)->wattsOutSum,
            'watts_in_sum' => optional($data)->wattsInSum,
        ]);
    }

    /**
     * Get the quota summaries of the device.
     */
    public function quotaSummaries()
    {
        return $this->hasMany(QuotaSummary::class);
    }

    /**
     * Get the most recent quota summary of the device.
     */
    public function latestQuotaSummary(): HasOne
    {
        return $this->hasOne(QuotaSummary::class)->latestOfMany();
    }

    /**
     * Get sunrise date and time of the device.
     */
    public function sunrise($timestamp)
    {
        return CarbonImmutable::createFromTimestamp(
            date_sun_info(
                $timestamp->timestamp,
                $this->latitude,
                $this->longitude
            )['sunrise']
        );
    }

    /**
     * Get sunset date and time of the device.
     */
    public function sunset($timestamp)
    {
        return CarbonImmutable::createFromTimestamp(
            date_sun_info(
                $timestamp->timestamp,
                $this->latitude,
                $this->longitude
            )['sunset']
        );
    }

    /**
     * Get the return on investment of the device.
     */
    public function returnOnInvestment()
    {
        return $this->investment
               ? optional($this->latestQuotaSummary)->watt_hours_in_cumsum
                 / 1000 * $this->price_per_kilowatt_hour / $this->investment
               : null;
    }

    /**
     * Get the cost per kilowatt hour in of the device.
     */
    public function kilowattHoursInCost()
    {
        return $this->latestQuotaSummary
                   && $this->latestQuotaSummary->watt_hours_in_cumsum > 0
               ? $this->investment
                 / ($this->latestQuotaSummary->watt_hours_in_cumsum / 1000)
               : null;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function (Device $device) {
            $device->user()->associate(auth()->user());
        });

        static::created(function (Device $device) {
            $device->storeQuota();
        });
    }
}
