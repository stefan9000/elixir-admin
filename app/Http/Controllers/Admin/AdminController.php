<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Shows a list of admins.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.admin.index');
    }

    /**
     * Shows the admin creation page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.admin.create');
    }

    /**
     * Shows the admin editing page.
     *
     * @param User $admin
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $admin)
    {
        return view('admin.admin.edit', compact(['admin']));
    }
}
