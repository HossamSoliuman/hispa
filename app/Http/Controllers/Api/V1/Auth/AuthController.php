<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\UserResource;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Support\Facades\Auth;

class AuthController
{
    use RespondsWithHttpStatus;

    public function login(LoginRequest $request)
    {
        if (! Auth::attempt($request->only(['phone', 'password']))) {
            return $this->failure(trans('auth.failed'), [], 404);
        }

        $user = $request->user();

        $allowedRoles = ['owner', 'dalal', 'counter', 'captain'];
        if (! in_array($user->role, $allowedRoles) || $user->status != 1) {
            Auth::logout();

            return $this->failure(trans('auth.failed'), [], 403);
        }

        if (isset($request->fcm_token)) {
            $user->update(['fcm_token' => $request->fcm_token]);
        }

        $token = $user->createToken('API TOKEN')->plainTextToken;

        $userResource = new UserResource($user);
        $userResource->token = $token;

        return $this->success(trans('site.login_user_success'), $userResource, 200);
    }

    public function logout()
    {

        $user = request()->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        return $this->success(trans('site.logout'), [], 200);

    }
}
