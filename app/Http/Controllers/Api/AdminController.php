<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponder;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    use ApiResponder;

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'phone' => 'string|max:255|nullable',
        'date_of_birth' => 'date_format:d.m.Y|nullable',
    ];

    protected $messages = [
        'first_name.required' => 'Please enter a first name.',
        'first_name.max' => 'A first name must be shorter than 255 characters.',
        'last_name.required' => 'Please enter a last name.',
        'last_name.max' => 'A last name must be shorter than 255 characters.',
        'email.required' => 'Please enter an email address.',
        'email.email' => 'Please enter a valid email address.',
        'email.max' => 'An email must be shorter than 255 characters.',
        'email.unique' => 'This email is already taken.',
        'password.required' => 'Please enter a password',
        'password.min' => 'A password must be longer than 8 characters.',
        'password.confirmed' => 'Please confirm your password.',
        'phone.required' => 'Please enter a phone number.',
        'phone.max' => 'A phone number must be shorter than 255 characters.',
        'date_of_birth.required' => 'Please enter a date of birth.',
        'date_of_birth.date_format' => 'The date of birth must be in the format d.m.Y.',
    ];

    /**
     * Returns a list of admins.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $admins = User::where('type', User::ADMIN_USER);

        return $this->apiRespond($admins);
    }

    /**
     * Shows the chosen admin.
     *
     * @param User $admin
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function show(User $admin) {
        if (!$admin->isAdmin()) {
            return abort(404);
        }

        return $this->apiRespondSingle($admin);
    }

    /**
     * Stores the provided data into a new admin entry.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->rules['email'] = 'required|string|email|max:255|unique:users';
        $this->rules['password'] = 'required|string|min:8|confirmed';
        $this->validate($request, $this->rules, $this->messages);

        if ($request->input('date_of_birth')) {
            $dob = Carbon::create($request->input('date_of_birth'))->format('Y-m-d');
        } else {
            $dob = null;
        }

        $admin = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone'),
            'date_of_birth' => $dob,
            'type' => User::ADMIN_USER,
            'api_token' => User::generateApiToken(),
        ]);

        return $this->apiRespondSingle($admin);
    }

    /**
     * Updates the chosen admin entry with the provided data.
     *
     * @param Request $request
     * @param User $admin
     * @return \Illuminate\Http\JsonResponse|void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, User $admin)
    {
        if (!$admin->isAdmin()) {
            return abort(404);
        }

        $this->rules['email'] = 'required|string|email|max:255|unique:users,email,' . $admin->id;
        $this->rules['password'] = 'string|min:8|confirmed|nullable';

        $this->validate($request, $this->rules, $this->messages);

        if ($request->input('date_of_birth')) {
            $dob = Carbon::create($request->input('date_of_birth'))->format('Y-m-d');
        } else {
            $dob = null;
        }

        $admin->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => ($request->input('password')) ? Hash::make($request->input('password')) : $admin->password,
            'phone' => $request->input('phone'),
            'date_of_birth' => $dob,
        ]);

        return $this->apiRespondSingle($admin);
    }

    /**
     * Destroys the chosen admin entry.
     *
     * @param User $admin
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(User $admin)
    {
        $admin->delete();

        return $this->apiRespondSingle($admin);
    }
}
