<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponder;
use App\Mail\TicketsPurchased;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class TicketController extends Controller
{
    use ApiResponder;

    /**
     * Returns all the tickets belonging to the user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userTickets()
    {
        $tickets = auth()->user()->tickets;

        return $this->apiRespond($tickets);
    }

    /**
     * Returns all the tickets belonging to an event.
     *
     * @param Event $event
     * @return \Illuminate\Http\JsonResponse
     */
    public function eventTickets(Event $event)
    {
        $tickets = $event->tickets;

        return $this->apiRespond($tickets);
    }

    /**
     * Shows the chosen ticket.
     *
     * @param Event $event
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Event $event, string $code)
    {
        $ticket = $event->tickets()->where('code', $code)->with('user')->first();

        if (!$ticket) {
            $this->apiRespondError(404, 'Not found');
        }

        return $this->apiRespondSingle($ticket);
    }

    /**
     * Creates a new ticket entry.
     *
     * @param Request $request
     * @param Event $event
     * @return \Illuminate\Http\JsonResponse|void
     * @throws \Stripe\Exception\ApiErrorException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, Event $event)
    {
        $this->validate($request, [
            'tickets' => 'required|numeric|min:1',
            'token_id' => 'required|string',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        $prices = [
            'starting' => $event->starting_price,
            'mid' => $event->mid_price,
            'end' => $event->end_price,
        ];

        $total_price = 0;

        $tickets_bought = $request->input('tickets');
        $tickets_sold = $event->tickets->count();
        $tickets_total = ($event->starting_tickets + $event->mid_tickets + $event->end_tickets);

        if (($tickets_sold + $tickets_bought) > $tickets_total) {
            $this->apiRespondError(403, 'Not enough available tickets.');
        }

        if ($event->finishes_on <= date('Y-m-d H:i:s')) {
            $this->apiRespondError(409, 'The event has already finished.');
        }

        $params = [];
        for ($i = 1; $i <= $tickets_bought; $i++) {
            if ($tickets_sold >= $event->starting_tickets) {
                if ($tickets_sold >= ($event->starting_tickets + $event->mid_tickets)) {
                    $current_price = $prices['end'];
                } else {
                    $current_price = $prices['mid'];
                }
            } else {
                $current_price = $prices['starting'];
            }

            $total_price += ($current_price * 100);
            $tickets_sold++;

            $params[] = [
                'user_id' => auth()->user()->id,
                'code' => Ticket::generateUniqueCode(),
                'price' => $current_price
            ];
        }

        if (!auth()->user()->stripe_id) {
            try {
                $customer = Customer::create([
                    'source' => $request->input('token_id'),
                    'email' => auth()->user()->email,
                    'name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                ]);

                auth()->user()->update([
                    'stripe_id' => $customer->id,
                ]);
            } catch (\Exception $e) {
                $this->apiRespondError(400, $e->getMessage());
            }
        } else {
            Customer::update(auth()->user()->stripe_id, [
                'source' => $request->input('token_id'),
            ]);
        }

        try {
            Charge::create([
                'amount' => $total_price,
                'customer' => auth()->user()->stripe_id,
                'currency' => 'CHF',
                'description' => $request->input('tickets') . ' tickets for "'. $event->name .'"',
            ]);
        } catch (\Exception $e) {
            $this->apiRespondError(400, $e->getMessage());
        }

        foreach ($params as $p) {
            $event->tickets()->create($p);
        }

        $request->per_page = $request->input('tickets');
        $created_tickets = Ticket::where('user_id', auth()->user()->id)
            ->where('event_id', $event->id)
            ->latest();

        if (auth()->user()->email) {
            Mail::to(auth()->user()->email)
                ->send(new TicketsPurchased($created_tickets->get()));
        }

        return $this->apiRespond($created_tickets);
    }

    /**
     * Updates the used parameter of the chosen ticket if one exists.
     *
     * @param Event $event
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Event $event, string $code)
    {
        $ticket = $event->tickets()->where('code', $code)->first();

        if (!$ticket || $ticket->used) {
            $this->apiRespondError(404, 'Not found');
        }

        $ticket->update([
            'used' => Ticket::USED,
        ]);

        return $this->apiRespondSingle($ticket);
    }
}
