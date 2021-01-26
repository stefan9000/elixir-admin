<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponder;
use App\Mail\PasswordReset;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    use ApiResponder;

    /**
     * Creates a new password request entry.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email'
        ]);

        $user = User::where('type', User::REGULAR_USER)
            ->where('email', $request->input('email'))
            ->whereNull('provider')
            ->first();

        if (!$user) {
            $this->apiRespondError(409, 'Specified email is not linked to any account.');
        }

        $token = Str::random(50);
        $reset_request = DB::table('password_resets')->insert([
            'email' => $request->input('email'),
            'token' => $token
        ]);

        Mail::to($request->input('email'))
            ->send(new PasswordReset($token));

        return $this->apiRespondMessage(200, 'A password reset request has been created successfully.');
    }

    /**
     * Shows the password reset form.
     *
     * @param $token
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($token)
    {
        $reset_request = DB::table('password_resets')->where('token', $token)->first();

        if (!$reset_request) {
            abort(404);
        }

        $reset_success = false;
        return view('guest.password_reset.edit', compact(['token', 'reset_success']));
    }

    /**
     * Updates the user's password.
     *
     * @param Request $request
     * @param $token
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $token)
    {
        $reset_request = DB::table('password_resets')->where('token', $token)->first();

        if (!$reset_request) {
            abort(404);
        }

        $this->validate($request, [
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = User::where('email', $reset_request->email)->first();

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        $reset_success = true;

        DB::table('password_resets')->where('token', $token)->delete();

        return view('guest.password_reset.edit', compact(['token', 'reset_success']));
    }
}
