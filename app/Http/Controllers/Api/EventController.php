<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponder;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    use ApiResponder;

    protected $rules = [
        'location' => 'string|max:255|nullable',
        'artist' => 'string|max:255|nullable',
        'latitude' => 'numeric|nullable',
        'longitude' => 'numeric|nullable',
        'zoom' => 'numeric|nullable',
        'start_date' => 'required|date_format:Y-m-d',
        'start_time' => 'required|date_format:H:i',
        'end_date' => 'required|date_format:Y-m-d',
        'end_time' => 'required|date_format:H:i',
        'starting_tickets' => 'required|numeric|min:1',
        'starting_price' => 'required|numeric|min:1',
        'mid_tickets' => 'numeric|min:1|nullable',
        'mid_price' => 'numeric|min:1|nullable',
        'end_tickets' => 'numeric|min:1|nullable',
        'end_price' => 'numeric|min:1|nullable',
        'translations.*.name' => 'string|max:1000|nullable',
        'translations.*.description' => 'string|max:75000|nullable',
        'video' => 'mimes:avi,mp4,webm|nullable',
        'images.*' => 'image|nullable',
        'thumbnail' => 'image|nullable',
    ];

    protected $messages = [];

    /**
     * Returns the event list.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $events = Event::with('images');

        return $this->apiRespond($events);
    }

    /**
     * Shows the chosen event.
     *
     * @param Event $event
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Event $event)
    {
        $event = $event->with('images')->find($event->id);

        return $this->apiRespondSingle($event);
    }

    /**
     * Stores the provided data into a new event entry.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->rules, $this->messages);

        $event = Event::create([
            'location' => $request->input('location'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'zoom' => $request->input('zoom'),
            'artist' => $request->input('artist'),
            'starts_on' => $request->input('start_date') . ' ' . $request->input('start_time') . ':00',
            'finishes_on' => $request->input('end_date') . ' ' . $request->input('end_time') . ':00',
            'starting_tickets' => $request->input('starting_tickets'),
            'starting_price' => $request->input('starting_price'),
            'mid_tickets' => $request->input('mid_tickets'),
            'mid_price' => $request->input('mid_price'),
            'end_tickets' => $request->input('end_tickets'),
            'end_price' => $request->input('end_price'),
            'video_src' => ($request->hasFile('video')) ? Storage::disk('public')->put('event/video', $request->file('video')) : null,
            'thumbnail' => ($request->hasFile('thumbnail')) ? Storage::disk('public')->put('event/thumbnail', $request->file('thumbnail')) : null,
        ]);

        $languages = ['en', 'rs', 'de', 'fr'];
        foreach ($languages as $l) {
            if (isset($request->input('translations')[$l])) {
                $event->translations()->create([
                    'lang' => $l,
                    'name' => $request->input('translations')[$l]['name'],
                    'description' => $request->input('translations')[$l]['description'],
                ]);
            } else {
                $event->translations()->create([
                    'lang' => $l,
                ]);
            }
        }

        if ($request->file('images')) {
            foreach ($request->file('images') as $i) {
                $event->images()->create([
                    'src' => Storage::disk('public')->put('event/images', $i),
                    'thumb_src' => null //TO DO
                ]);
            }
        }

        $event->images = $event->images;
        return $this->apiRespondSingle($event);
    }

    /**
     * Updates the chosen event entry with the provided data.
     *
     * @param Request $request
     * @param Event $event
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Event $event)
    {
        $this->validate($request, $this->rules, $this->messages);

        $event->update([
            'location' => $request->input('location'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'zoom' => $request->input('zoom'),
            'artist' => $request->input('artist'),
            'starts_on' => $request->input('start_date') . ' ' . $request->input('start_time') . ':00',
            'finishes_on' => $request->input('end_date') . ' ' . $request->input('end_time') . ':00',
            'starting_tickets' => $request->input('starting_tickets'),
            'starting_price' => $request->input('starting_price'),
            'mid_tickets' => $request->input('mid_tickets'),
            'mid_price' => $request->input('mid_price'),
            'end_tickets' => $request->input('end_tickets'),
            'end_price' => $request->input('end_price'),
            'video_src' => ($request->hasFile('video')) ? Storage::disk('public')->put('event/video', $request->file('video')) : $event->getoriginal('video_src'),
            'thumbnail' => ($request->hasFile('thumbnail')) ? Storage::disk('public')->put('event/thumbnail', $request->file('thumbnail')) : $event->getoriginal('thumbnail'),
        ]);

        $languages = ['rs', 'en', 'de', 'fr'];

        foreach ($languages as $l) {
            $translation = $event->translations()->where('lang', $l)->first();
            if ($translation) {
                if (isset($request->input('translations')[$l])) {
                    $translation->update([
                        'name' => $request->input('translations')[$l]['name'],
                        'description' => $request->input('translations')[$l]['description'],
                    ]);
                }
            } else {
                if (isset($request->input('translations')[$l])) {
                    $event->translations()->create([
                        'lang' => $l,
                        'name' => $request->input('translations')[$l]['name'],
                        'description' => $request->input('translations')[$l]['description'],
                    ]);
                } else {
                    $event->translations()->create([
                        'lang' => $l,
                    ]);
                }
            }
        }

        if ($request->file('images')) {
            foreach ($request->file('images') as $i) {
                $event->images()->create([
                    'src' => Storage::disk('public')->put('event/images', $i),
                    'thumb_src' => null //TO DO
                ]);
            }
        }

        $event->images = $event->images;
        return $this->apiRespondSingle($event);
    }

    /**
     * Destroys the chosen event along with its video and all its images.
     *
     * @param Event $event
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Event $event)
    {
        if ($event->images->isNotEmpty()) {
            foreach ($event->images as $i) {
                Storage::disk('public')->delete($i->src);
                Storage::disk('public')->delete($i->thumb_src);
            }
            $event->images()->delete();
        }
        $event->translations()->delete();

        Storage::disk('public')->delete($event->video_src);
        Storage::disk('public')->delete($event->thumbnail);

        $event->delete();
        return $this->apiRespondSingle($event);
    }

    /**
     * Returns all the tickets of the event.
     *
     * @param Event $event
     * @return \Illuminate\Http\JsonResponse
     */
    public function tickets(Event $event)
    {
        $tickets = Ticket::where('event_id', $event->id)
                    ->with('user');

        return $this->apiRespond($tickets);
    }

    /**
     * Returns all the locations of past and future events.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function locations()
    {
        $locations = Event::all();

        return $this->apiRespondAll($locations);
    }
}
