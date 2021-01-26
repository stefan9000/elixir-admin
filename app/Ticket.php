<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    /**
     * Constants defining values for whether a ticket was user or not.
     */
    const USED = 1;
    const NOT_USED = 0;

    /**
     * Contains all the fillable properties of a ticket.
     *
     * @var array
     */
    protected  $fillable = [
        'user_id', 'code', 'used', 'price',
    ];

    /**
     * Returns the event of the ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Returns the user who bought the ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generates a unique code for the ticket.
     *
     * @return string
     */
    public static function generateUniqueCode()
    {
        do {
            $code = Str::random(60);
        } while(Ticket::where('code', $code)->first());

        return $code;
    }
}
