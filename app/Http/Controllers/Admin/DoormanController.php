<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class DoormanController extends Controller
{
    /**
     * Shows a list of doormen.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.doorman.index');
    }

    /**
     * Shows the doorman creation page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.doorman.create');
    }

    /**
     * Shows the doorman editing page.
     *
     * @param User $doorman
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $doorman)
    {
        return view('admin.doorman.edit', compact(['doorman']));
    }
}
