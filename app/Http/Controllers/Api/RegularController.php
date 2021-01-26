<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\ApiResponder;
use App\Http\Controllers\Controller;
use App\Ticket;
use App\User;
use Illuminate\Http\Request;

class RegularController extends Controller
{
    use ApiResponder;

    /**
     * Returns a list of regular users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $regulars = User::select([
            'id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'date_of_birth',
            'provider',
        ])
        ->where('type', User::REGULAR_USER);

        return $this->apiRespond($regulars);
    }

    /**
     * Shows the chosen regular user.
     *
     * @param User $regular
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function show(User $regular)
    {
        if ($regular->isAdmin() || $regular->isDoorman()) {
            return abort(404);
        }

        return $this->apiRespondSingle($regular);
    }

    /**
     * Returns all the tickets of the event.
     *
     * @param User $regular
     * @return \Illuminate\Http\JsonResponse
     */
    public function tickets(User $regular)
    {
        $tickets = Ticket::where('user_id', $regular->id)
            ->with('event');

        return $this->apiRespond($tickets);
    }
}
