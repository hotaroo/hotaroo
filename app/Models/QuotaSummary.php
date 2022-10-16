<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotaSummary extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'timestamp' => 'immutable_datetime',
    ];

    /**
     * Get the device that owns the quota summary.
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
