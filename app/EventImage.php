<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EventImage extends Model
{
    /**
     * Custom attributes of the model.
     *
     * @var array
     */
    protected $appends = [
        'src',
        'thumb_src'
    ];

    /**
     * Fillable properties of the model.
     *
     * @var array
     */
    protected $fillable = [
        'event_id', 'src', 'thumb_src'
    ];

    /**
     * Returns the event to which this image belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Formats the src attribute to include the host name.
     *
     * @return string
     */
    protected function getSrcAttribute()
    {
        return asset(Storage::url($this->attributes['src']));
    }

    /**
     * Formats the thumb src attribute to include the host name.
     *
     * @return string
     */
    protected function getThumbSrcAttribute()
    {
        return asset(Storage::url($this->attributes['thumb_src']));
    }
}
