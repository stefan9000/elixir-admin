<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use Notifiable, Billable;

    /**
     * Possible user types.
     */
    const REGULAR_USER = 1;
    const DOORMAN_USER = 8;
    const ADMIN_USER = 9;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone', 'date_of_birth', 'api_token', 'type',
        'provider_first_login', 'original_provider', 'provider', 'provider_id', 'provider_token',
        'stripe_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'provider_id', 'provider_token', 'email_verified_at', 'stripe_id',
        'card_brand', 'card_last_four', 'trial_ends_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date'
    ];

    /**
     * Appends all the attributes from their getters.
     *
     * @var array
     */
    protected $appends = [
        'date_of_birth',
    ];

    /**
     * Returns all the tickets which the user has bought.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Returns true if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return ($this->type === User::ADMIN_USER);
    }

    /**
     * Returns true if the user is a doorman.
     *
     * @return bool
     */
    public function isDoorman()
    {
        return ($this->type === User::DOORMAN_USER);
    }

    /**
     * Generates a unique API token.
     *
     * @return string
     */
    public static function generateApiToken()
    {
        do {
            $token = Str::random(60);
        } while (User::where('api_token', $token)->first());

        return $token;
    }

    /**
     * Formats and returns the date of birth attribute.
     *
     * @return false|string|null
     */
    protected function getDateOfBirthAttribute()
    {
        if (isset($this->attributes['date_of_birth'])) {
            $value = $this->attributes['date_of_birth'] ?? null;
        } else {
            $value = null;
        }

        if ($value !== null) {
            return date('d.m.Y', strtotime($value));
        } else {
            return null;
        }
    }
}
