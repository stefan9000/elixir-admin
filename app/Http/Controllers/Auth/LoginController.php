<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponder;
use App\Providers\RouteServiceProvider;
use App\User;
use Google_Client;
use GuzzleHttp\Exception\ClientException;
use http\Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, ApiResponder;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            array_merge($this->credentials($request), [
                'provider' => null
            ]), $request->filled('remember')
        );
    }

    /**
     * Attempt to log the user into the application via oauth provider.
     *
     * @param String $provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginSocial(String $provider)
    {
        switch($provider) {
            case 'google':
                return self::loginGoogle();
                break;
            case 'facebook':
                return self::loginFacebook();
                break;
            case 'apple':
                return self::loginApple();
                break;
            default:
                return $this->apiRespondError(404, 'Not found.');
        }
    }

    /**
     * Logs the user in via google's login api.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function loginGoogle()
    {
        try {
            $id_token = request()->id_token;
            $platform = request()->platform;

            if (!$id_token) {
                return $this->apiRespondError(406, 'Provided id token is empty.');
            }

            if (!$platform || !in_array($platform, ['ios', 'android'])) {
                return $this->apiRespondError(406, 'Platform not specified or invalid.');
            }

            $client_id = ($platform === 'ios') ? config('services.google.ios_client_id') : config('services.google.android_client_id');

            $client = new Google_Client(['client_id' => $client_id]);
            $payload = $client->verifyIdToken(request()->id_token);

            if (!$payload) {
                return $this->apiRespondError(401, "Token verification failed.\n Token: " . request()->id_token);
            }
        } catch (ClientException $c) {
            return $this->apiRespondError(401, $c->getMessage());
        }

        $app_user = User::where('provider', 'google')
            ->where('provider_id', $payload['sub'])
            ->first();

        if (!$app_user) {
            $app_user = User::create([
                'email' => $payload['email'],
                'provider' => 'google',
                'provider_id' => $payload['sub'],
//                'provider_token' => request()->id_token,
                'api_token' => User::generateApiToken()
            ]);
        }

        return $this->apiRespondSingle($app_user);
    }

    /**
     * Logs the user in via facebook's login api.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function loginFacebook()
    {
        $access_token = request()->access_token;

        if (!$access_token) {
            $this->apiRespondError(406, 'Access token not provided.');
        }

        try {
            $fb_user = Socialite::driver('facebook')->stateless()->userFromToken($access_token);

            $app_user = User::where('provider', 'facebook')->where('provider_id', $fb_user['id'])->first();

            if (!$app_user) {
                $name = explode(' ', $fb_user['name']);
                $first_name = (isset($name[0])) ? $name[0] : null;
                $last_name = (isset($name[1])) ? $name[1] : null;

                $app_user = User::create([
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => '',
                    'provider' => 'facebook',
                    'provider_id' => $fb_user['id'],
//                    'provider_token' => $access_token,
                    'api_token' => User::generateApiToken(),
                ]);
            }

            return $app_user;
        } catch (ClientException $e) {
            return $this->apiRespondError(401, $e->getMessage());
        }
    }

    protected function loginApple()
    {
        $ch = curl_init('https://appleid.apple.com/auth/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'authorization_code',
            'code' => request()->code,
            'redirect_uri' => config('services.apple.redirect_uri'),
            'client_id' => config('services.apple.client_id'),
            'client_secret' => config('services.apple.client_secret'),
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'User-agent: curl',
        ]);

        $response = json_decode(curl_exec($ch));
        curl_close($ch);

        if (!$response->access_token) {
            $this->apiRespondError(401, 'Error getting an access token.');
        }

        $claims = explode('.', $response->id_token)[1];
        $claims = json_decode(base64_decode($claims));

        try {
            $app_user = User::where('provider', 'apple')->where('provider_id', $claims->sub)->first();

            if (!$app_user) {
                $app_user = User::create([
                    'first_name' => '',
                    'last_name' => '',
                    'email' => $claims->email,
                    'provider' => 'apple',
                    'provider_id' => $claims->sub,
//                    'provider_token' => $access_token,
                    'api_token' => User::generateApiToken(),
                ]);
            }

            return $app_user;
        } catch (ClientException $e) {
            return $this->apiRespondError(401, $e->getMessage());
        }
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        return response()->json($this->guard()->user()->only([
            'first_name',
            'last_name',
            'email',
            'phone',
            'date_of_birth',
            'api_token',
            'type',
        ]));
    }
}
