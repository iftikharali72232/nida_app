<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

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
        // Ensure only admin users can log in through the web
        return $this->guard()->attempt(
            array_merge($this->credentials($request), ['user_type' => 0, 'status' => 1]),
            $request->filled('remember')
        );
    }

    /**
     * Send the failed login response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user) {
            if ($user->user_type != 0) {
                return redirect()->back()->withErrors([
                    'email' => 'You are not allowed to log in via the web.',
                ]);
            }

            if (!$user->status) {
                return redirect()->back()->withErrors([
                    'email' => 'Your account is inactive. Please contact admin support.',
                ]);
            }
        }

        throw \Illuminate\Validation\ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }
}
