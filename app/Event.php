<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    /**
     * Stores the first queried translation result for further use.
     *
     * @var
     */
    protected $fetched_translation;

    /**
     * Custom attributes appended to the model.
     *
     * @var array
     */
    protected $appends = [
        'name',
        'description',
        'thumbnail',
        'video_src',
        'sold_out',
        'tickets_left',
        'price',
        'user_ticket_count',
        'user_ticket_count_used',
        'user_ticket_count_not_used',
    ];

    /**
     * Fillable properties of the model.
     *
     * @var array
     */
    protected $fillable = [
        'video_src', 'starts_on', 'finishes_on', 'location', 'latitude', 'longitude', 'zoom', 'thumbnail',
        'artist', 'starting_tickets', 'starting_price', 'mid_tickets', 'mid_price', 'end_tickets', 'end_price',
    ];

    /**
     * Casts provided attributes to dates.
     *
     * @var array
     */
    protected $dates = [
        'starts_on',
        'finishes_on',
    ];

    protected $hidden = [
        'tickets', 'total_tickets', 'created_at', 'updated_at', 'starting_tickets', 'starting_price', 'mid_tickets', 'mid_price', 'end_tickets', 'end_price'
    ];

    /**
     * Returns all the images belonging to this event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(EventImage::class);
    }

    /**
     * Returns all the translations belonging to this event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(EventTranslation::class);
    }

    /**
     * Returns all tickets belonging to the event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Fetches the name attribute for the chosen language.
     */
    public function getNameAttribute()
    {
        (request()->lang) ? $lang = request()->lang : $lang = config('localization.default');

        if (!$this->fetched_translation) {
            $this->fetched_translation = $this->translations()
                ->where('lang', $lang)
                ->first();
        }

        if ($this->fetched_translation) {
            return $this->fetched_translation->name;
        }

        return '';
    }

    /**
     * Fetches the description attribute for the chosen language.
     *
     * @return mixed|string
     */
    public function getDescriptionAttribute()
    {
        (!request()->lang) ? $lang = request()->lang : $lang = config('localization.default');

        if (!$this->fetched_translation) {
            $this->fetched_translation = $this->translations()
                ->where('lang', $lang)
                ->first();
        }

        if ($this->fetched_translation) {
            return $this->fetched_translation->description;
        }

        return '';
    }

    /**
     * Formats the thumbnail attribute to include the host name.
     *
     * @return string
     */
    protected function getThumbnailAttribute()
    {
        if (isset($this->attributes['thumbnail'])) {
            return asset(Storage::url($this->attributes['thumbnail']));
        } else {
            return null;
        }
    }

    /**
     * Formats the video attribute to include the host name.
     *
     * @return string
     */
    protected function getVideoSrcAttribute()
    {
        if (isset($this->attributes['video_src'])) {
            return asset(Storage::url($this->attributes['video_src']));
        } else {
            return null;
        }
    }

    /**
     * Returns the remaining number of tickets.
     *
     * @return int|mixed
     */
    protected function getTicketsLeftAttribute()
    {
        return (($this->starting_tickets + $this->mid_tickets + $this->end_tickets) - $this->tickets()->count());
    }

    /**
     * Returns the amount of tickets which the user has bought.
     *
     * @return int
     */
    protected function getUserTicketCountAttribute()
    {
        return $this->tickets()->where('user_id', auth()->user()->id)->count();
    }

    /**
     * Returns the amount of unused tickets which the user has bought.
     *
     * @return mixed
     */
    protected function getUserTicketCountNotUsedAttribute()
    {
        return $this->tickets()
            ->where('user_id', auth()->user()->id)
            ->where('used', Ticket::NOT_USED)
            ->count();
    }

    /**
     * Returns the amount of used tickets of the user.
     *
     * @return mixed
     */
    protected function getUserTicketCountUsedAttribute()
    {
        return $this->tickets()
            ->where('user_id', auth()->user()->id)
            ->where('used', Ticket::USED)
            ->count();
    }

    /**
     * Checks whether the event is sold out.
     *
     * @return int
     */
    protected function getSoldOutAttribute()
    {
        return (($this->starting_tickets + $this->mid_tickets + $this->end_tickets) <= $this->tickets->count()) ? 1 : 0;
    }

    /**
     * Fetches the current price of the event.
     *
     * @return mixed
     */
    protected function getPriceAttribute()
    {
        $tickets_sold = $this->tickets()->count();
        if ($tickets_sold >= $this->starting_tickets) {
            if ($tickets_sold >= ($this->starting_tickets + $this->mid_tickets)) {
                $current_price = $this->end_price;
            } else {
                $current_price = $this->mid_price;
            }
        } else {
            $current_price = $this->starting_price;
        }

        return $current_price;
    }
}
