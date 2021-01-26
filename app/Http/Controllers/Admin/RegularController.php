<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class RegularController extends Controller
{
    /**
     * Shows a list of regular users.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.regular.index');
    }

    /**
     * Shows the chosen regular user.
     *
     * @param User $regular
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $regular)
    {
        return view('admin.regular.show', compact(['regular']));
    }

    /**
     * Returns the ticket list of the event.
     *
     * @param User $regular
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tickets(User $regular)
    {
        return view('admin.regular.tickets', compact(['regular']));
    }
}
