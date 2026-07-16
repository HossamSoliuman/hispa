<?php

namespace App\Http\Controllers\Gov\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/gov/dashboard';

    public function __construct()
    {
        $this->middleware('guest:gov', ['except' => ['logout']]);
    }

    public function showLoginForm(): View
    {
        return view('gov.auth.login');
    }

    public function username(): string
    {
        return 'email';
    }

    protected function guard()
    {
        return Auth::guard('gov');
    }

    /**
     * Only active government supervisors may authenticate through this panel.
     *
     * @return array<string, mixed>
     */
    protected function credentials(Request $request): array
    {
        return array_merge(
            $request->only($this->username(), 'password'),
            ['role' => 'gov', 'status' => 1],
        );
    }

    protected function authenticated(Request $request, $user): RedirectResponse
    {
        return redirect()->intended(route('gov.dashboard'));
    }

    protected function loggedOut(Request $request): RedirectResponse
    {
        return redirect()->route('gov.login');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('gov')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('gov.login');
    }
}
