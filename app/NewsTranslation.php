<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsTranslation extends Model
{
    /**
     * Fillable properties of the model.
     *
     * @var array
     */
    protected $fillable = [
        'news_id', 'title', 'body', 'lang',
    ];

    /**
     * Returns the news to which this translation belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
