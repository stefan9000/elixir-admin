<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class News extends Model
{
    /**
     * Stores the first queried translation result for further use.
     *
     * @var
     */
    protected $fetched_translation;

    /**
     * Custom attributes of the model.
     *
     * @var array
     */
    protected $appends = [
        'title',
        'body',
        'thumbnail',
        'video_src'
    ];

    /**
     * Fillable properties of the model.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'video_src', 'published_on', 'thumbnail',
    ];

    /**
     * Returns all the images belonging to these news.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(NewsImage::class);
    }

    /**
     * Returns all the translations belonging to these news.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(NewsTranslation::class);
    }

    /**
     * Fetches the title attribute for the chosen language.
     */
    public function getTitleAttribute()
    {
        (request()->lang) ? $lang = request()->lang : $lang = config('localization.default');

        if (!$this->fetched_translation) {
            $this->fetched_translation = $this->translations()
                ->where('lang', $lang)
                ->first();
        }

        if ($this->fetched_translation) {
            return $this->fetched_translation->title;
        }

        return '';
    }

    /**
     * Fetches the body attribute for the chosen language.
     *
     * @return mixed|string
     */
    public function getBodyAttribute()
    {
        (!request()->lang) ? $lang = request()->lang : $lang = config('localization.default');

        if (!$this->fetched_translation) {
            $this->fetched_translation = $this->translations()
                ->where('lang', $lang)
                ->first();
        }

        if ($this->fetched_translation) {
            return $this->fetched_translation->body;
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
        if ($this->attributes['thumbnail']) {
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
        if ($this->attributes['video_src']) {
            return asset(Storage::url($this->attributes['video_src']));
        } else {
            return null;
        }
    }
}
