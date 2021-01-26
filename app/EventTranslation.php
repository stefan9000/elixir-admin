<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventTranslation extends Model
{
    /**
     * Fillable properties of the model.
     *
     * @var array
     */
    protected $fillable = [
        'event_id', 'name', 'description', 'lang',
    ];

    /**
     * Returns the event to which this translation belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
