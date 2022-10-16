<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quota extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'timestamp',
        'state_of_charge',
        'watts_out_sum',
        'watts_in_sum',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'timestamp' => 'immutable_datetime',
    ];

    /**
     * Get the device that owns the quota.
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
