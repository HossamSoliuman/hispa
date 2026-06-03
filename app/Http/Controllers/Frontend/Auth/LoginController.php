<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        return view('site.login');
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

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => __('auth.failed')])->withInput();
        }

        // Ensure the role is allowed
        $allowedRoles = ['owner', 'dalal', 'counter'];
        if (! in_array($user->role, $allowedRoles)) {
            return back()->withErrors(['email' => __('auth.failed')])->withInput();
        }

        // Use the role name as the guard name (assuming guard config matches role name)
        $guard = $user->role;

        if (Auth::guard($guard)->attempt($credentials)) {
            return redirect()->intended(route("{$guard}.dashboard"));
        }

        return back()->withErrors(['email' => __('auth.failed')])->withInput();
    }

    protected function loggedOut(Request $request)
    {
        return redirect()->route('frontend.show_login_form');
    }

    public function logout(Request $request)
    {
        $guards = ['owner', 'dalal', 'counter', 'web'];

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
                break;
            }
        }

        return redirect()->route('frontend.show_login_form');
    }

    /**
     * Show admin dashboard.
     */
}
