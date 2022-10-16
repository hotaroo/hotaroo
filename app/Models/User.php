<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone',
        'date_format',
        'time_format',
        'ecoflow_key',
        'ecoflow_secret',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'ecoflow_key' => 'encrypted',
        'ecoflow_secret' => 'encrypted',
    ];

    /**
     * Interact with the user's timezone.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function timezone(): Attribute
    {
        return Attribute::make(
            get: fn ($timezone) => $timezone ?? config('app.timezone'),
            set: fn ($timezone) => $timezone == config('app.timezone')
                                   ? null
                                   : $timezone,
        );
    }

    /**
     * Get the user's date format.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function dateFormat(): Attribute
    {
        return Attribute::make(
            get: fn ($format) => $format ?? config('app.date_format'),
            set: fn ($format) => $format == config('app.date_format')
                                   ? null
                                   : $format,
        );
    }

    /**
     * Get the user's time format.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function timeFormat(): Attribute
    {
        return Attribute::make(
            get: fn ($format) => $format ?? config('app.time_format'),
            set: fn ($format) => $format == config('app.time_format')
                                   ? null
                                   : $format,
        );
    }

    /**
     * Get the devices for the user.
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    /**
     * Check if the user can access Filament.
     */
    public function canAccessFilament(): bool
    {
        return true;
    }
}
