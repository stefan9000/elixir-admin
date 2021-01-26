<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponder;
use App\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    use ApiResponder;

    /**
     * Contains all the validation rules.
     *
     * @var array
     */
    protected $rules = [
        'translations.*.title' => 'string|max:1000|nullable',
        'translations.*.body' => 'string|nullable',
        'video' => 'mimes:avi,mp4,webm|nullable',
        'images.*' => 'image|nullable',
        'thumbnail' => 'image|nullable',
        'published_on' => 'required|date_format:Y-m-d',
    ];

    /**
     * Contains all the validation messages.
     *
     * @var array
     */
    protected $messages = [
        'translations.*.title.max' => 'News titles can not be longer than 1000 characters.',
        'video.mimes' => 'Please provide a video in the AVI, MP4 or WEBM format.',
        'images.*.mimes' => 'Please provide images in the JPG, JPEG or PNG format.',
        'thumbnail.mimes' => 'Please provide a thumbnail in the JPG, JPEG or PNG format.'
    ];

    /**
     * Returns a list of news.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $news = News::with('images');

        return $this->apiRespond($news);
    }

    /**
     * Shows the chosen news.
     *
     * @param News $news
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(News $news)
    {
        $news = $news->with('images')->find($news->id);

        return $this->apiRespondSingle($news);
    }

    /**
     * Stores the provided data into a new news entry.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->rules, $this->messages);

        $news = News::create([
            'video_src' => ($request->file('video')) ? Storage::disk('public')->put('news/video', $request->file('video')) : null,
            'thumbnail' => ($request->file('thumbnail')) ? Storage::disk('public')->put('news/thumbnail', $request->file('thumbnail')) : null,
            'user_id' => auth()->user()->id,
            'published_on' => $request->published_on,
        ]);

        $languages = ['rs', 'en', 'de', 'fr'];

        foreach ($languages as $l) {
            if (isset($request->input('translations')[$l])) {
                $news->translations()->create([
                    'lang' => $l,
                    'title' => $request->input('translations')[$l]['title'],
                    'body' => $request->input('translations')[$l]['body'],
                ]);
            } else {
                $news->translations()->create([
                    'lang' => $l,
                ]);
            }
        }

        if ($request->file('images')) {
            foreach ($request->file('images') as $i) {
                $news->images()->create([
                    'src' => Storage::disk('public')->put('news/images', $i),
                    'thumb_src' => null //TO DO
                ]);
            }
        }

        $news->images = $news->images;
        return $this->apiRespondSingle($news);
    }

    /**
     * Updates the chosen news entry with the provided data.
     *
     * @param Request $request
     * @param News $news
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, News $news)
    {
        $this->validate($request, $this->rules, $this->messages);

        $news->update([
            'video_src' => ($request->file('video')) ? Storage::disk('public')->put('news/video', $request->file('video')) : $news->getOriginal('video_src'),
            'thumbnail' => ($request->file('thumbnail')) ? Storage::disk('public')->put('news/thumbnail', $request->file('thumbnail')) : $news->getOriginal('thumbnail'),
            'user_id' => auth()->user()->id,
            'published_on' => $request->published_on,
        ]);

        $languages = ['rs', 'en', 'de', 'fr'];

        foreach ($languages as $l) {
            $translation = $news->translations()->where('lang', $l)->first();

            if ($translation) {
                if (isset($request->input('translations')[$l])) {
                    $translation->update([
                        'title' => $request->input('translations')[$l]['title'],
                        'body' => $request->input('translations')[$l]['body'],
                    ]);
                }
            } else {
                if (isset($request->input('translations')[$l])) {
                    $news->translations()->create([
                        'lang' => $l,
                        'title' => $request->input('translations')[$l]['title'],
                        'body' => $request->input('translations')[$l]['body'],
                    ]);
                } else {
                    $news->translations()->create([
                        'lang' => $l,
                    ]);
                }
            }
        }

        if ($request->file('images')) {
            foreach ($request->file('images') as $i) {
                $news->images()->create([
                    'src' => Storage::disk('public')->put('news/images', $i),
                    'thumb_src' => null //TO DO
                ]);
            }
        }

        $news->images = $news->images;
        return $this->apiRespondSingle($news);
    }

    /**
     * Destroys the chosen news entry and all of its relations.
     *
     * @param News $news
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(News $news)
    {
        $news->translations()->delete();

        if ($news->images->isNotEmpty()) {
            foreach ($news->images as $i) {
                Storage::disk('public')->delete($i->src);
                Storage::disk('public')->delete($i->thumb_src);
            }
        }

        Storage::delete($news->video_src);
        $news->delete();

        return $this->apiRespondSingle($news);
    }

    /**
     * Stores images sent from the CK Editor.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeCkEditorImage(Request $request) {
        $this->validate($request, [
            'upload' => 'required|mimes:jpg,jpeg,png'
        ]);

        return response()->json([
            'url' => Storage::url(Storage::disk('public')->put('news/content_images', $request->file('upload'))),
        ]);
    }
}
