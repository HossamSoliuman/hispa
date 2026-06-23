<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Owner\OwnerMasterDataService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected array $allowedGuards = ['owner', 'dalal', 'gov'];

    protected string $defaultGuard = 'owner';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm(?string $guard = null)
    {
        $guard = $this->normalizeGuard($guard ?? request('guard'));

        return view('site.signup', compact('guard'));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:255', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'guard' => ['nullable', 'in:'.implode(',', $this->allowedGuards)],
        ]);
    }

    protected function guard()
    {
        return Auth::guard($this->currentGuard());
    }

    protected function create(array $data)
    {
        $guard = $this->normalizeGuard($data['guard'] ?? null);
        $role = $guard; // نفس الاسم

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'role' => $role,
            'status' => 1,
            'password' => Hash::make($data['password']),
        ]);

        // لو بتستخدم spatie/laravel-permission
        if (method_exists($user, 'assignRole')) {
            $user->assignRole($role);
        }

        // Seed the new owner's own copy of the default master data (ports, fish,
        // units, payment methods, ...) so each owner controls isolated data.
        if ($role === 'owner') {
            app(OwnerMasterDataService::class)->seedFor($user);
        }

        return $user;
    }

    protected function redirectTo()
    {
        $guard = $this->currentGuard();

        $name = match ($guard) {
            'owner' => 'owner.dashboard',
            'dalal' => 'dalal.dashboard',
            //            'counter' => 'counter.dashboard',
            'gov' => 'gov.dashboard',
            default => 'landing-page',
        };

        return Route::has($name) ? route($name) : '/';
    }

    protected function currentGuard(): string
    {
        $routeGuard = request()->route('guard');
        $inputGuard = request()->input('guard');

        return $this->normalizeGuard($routeGuard ?? $inputGuard);
    }

    protected function normalizeGuard(?string $guard): string
    {
        $guard = $guard ? strtolower($guard) : $this->defaultGuard;

        return in_array($guard, $this->allowedGuards, true) ? $guard : $this->defaultGuard;
    }
    //    protected function registered(Request $request, $user)
    //    {
    //        return redirect()->route('customer.index_route')->with(['message'=>' Your account registered successfully, please check your email to active your account.',
    //            'alert-type'=>'success']);
    //    }

}
