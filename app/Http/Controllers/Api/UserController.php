<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponder;
use App\Mail\ContactMessage;
use App\Ticket;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    use ApiResponder;

    /**
     * Returns a list of events for which the user has bought tickets.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function eventsWithTickets()
    {
        $event_ids = Ticket::where('user_id', auth()->user()->id)
            ->get()
            ->pluck('event_id')
            ->unique();

        if ($event_ids->isNotEmpty()) {
            $events = Event::where(function($query) use ($event_ids) {
                foreach ($event_ids as $e) {
                    $query->orWhere('id', $e);
                }
            });
        } else {
            $events = new Collection();

            return $this->apiRespondAll($events);
        }

        return $this->apiRespond($events);
    }

    /**
     * Returns all of the user's tickets.
     *
     * @param Event $event
     * @return \Illuminate\Http\JsonResponse
     */
    public function eventTickets(Event $event)
    {
        $tickets = Ticket::where('event_id', $event->id)
            ->where('user_id', auth()->user()->id)
            ->get();

        return $this->apiRespondAll($tickets);
    }

    /**
     * Updates the user's info
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateInfo(Request $request)
    {
        if (auth()->user()->provider) {
            $rules = [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'string|email|max:255|unique:users,email,' . auth()->user()->id . '|nullable',
                'phone' => 'string|max:255|nullable',
            ];
        } else {
            $rules = [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . auth()->user()->id,
                'password' => 'string|min:8|confirmed|nullable',
                'phone' => 'required|string|max:255',
            ];
        }


        $this->validate($request, $rules);

        auth()->user()->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password ? Hash::make($request->password) : auth()->user()->password,
            'api_token' => $request->password ? User::generateApiToken() : auth()->user()->api_token,
        ]);

        return $this->apiRespondSingle(User::find(auth()->user()->id));
    }

    /**
     * Sends a contact message.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function contact(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:255',
            'message' => 'required|string|max:10000'
        ]);

        Mail::to('contact@elixirevents.com')
            ->send(new ContactMessage($request));

        return $this->apiRespondMessage(200, 'Message successfuly sent.');
    }
}
