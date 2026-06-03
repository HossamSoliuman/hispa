<?php

namespace App\Http\Controllers\Admin\Auth;

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
    protected $redirectTo = '/admin/dashboard';

    public function __construct()
    {
        $this->middleware('guest:admin', ['except' => ['logout']]);
    }

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function username()
    {
        return 'email';
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    //    public function login(Request $request)
    //    {
    //        $credentials = $request->only('email', 'password');
    //
    //        if (Auth::guard('admin')->attempt($credentials)) {
    //            // Admin logged in successfully
    //            session(['admin_session_key' => true]);
    //
    //            return redirect()->intended(route('admins.index'));
    //        }
    //
    //        return back()->withErrors(['email' =>__('auth.failed')]);
    //
    //    }
    protected function loggedOut(Request $request)
    {
        return redirect()->route('admin.show_login_form');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.show_login_form');
    }

    protected function authenticated(Request $request, $user)
    {
        return redirect()->intended(route('admin.dashboard'));
    }
    /**
     * Show admin dashboard.
     */
}
