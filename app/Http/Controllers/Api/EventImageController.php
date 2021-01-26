<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\EventImage;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventImageController extends Controller
{
    use ApiResponder;

    /**
     * Deletes the chosen news image.
     *
     * @param Event $event
     * @param EventImage $event_image
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Event $event, EventImage $event_image)
    {
        Storage::delete($event_image->src);
        Storage::delete($event_image->thumb_src);
        $event_image->delete();

        return $this->apiRespondSingle($event_image);
    }
}
