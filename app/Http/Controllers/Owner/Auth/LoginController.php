<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard-user';

    public function __construct()
    {
        $this->middleware('guest:web', ['except' => ['logout']]);
    }

    public function showLoginForm()
    {
        return view('frontend.auth.login');
    }

    public function username()
    {
        return 'email';
    }

    protected function guard()
    {
        return Auth::guard('web');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();

            $allowedRoles = ['owner', 'dalal', 'counter'];

            if (! in_array($user->role, $allowedRoles)) {
                Auth::guard('web')->logout();

                return back()->withErrors(['email' => __('auth.failed')])->withInput();
            }

            return redirect()->intended(route('frontend.dashboard.user'));
        }

        return back()->withErrors(['email' => __('auth.failed')])->withInput();
    }

    protected function loggedOut(Request $request)
    {
        return redirect()->route('frontend.show_login_form');
    }

    public function logout()
    {
        Auth::guard('web')->logout();

        return redirect()->route('frontend.show_login_form');
    }
    /**
     * Show admin dashboard.
     */
}
