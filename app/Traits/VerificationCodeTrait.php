<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Verification;
use Illuminate\Support\Facades\App;

trait VerificationCodeTrait
{
    use RespondsWithHttpStatus;

    public function createCodeMobile($phone, $type)
    {
        $user = User::where('phone', $phone)->first();
        if (! $user) {
            return $this->failure(trans('site.the_phone_not_exits'), [], 404);
        }

        $user->update([
            'phone' => $phone,
        ]);
        if (App::environment('production')) {
            $message = createCode();
        } else {
            $message = '0000';

        }

        $verify = Verification::create([
            'user_id' => isset($user->id) ? $user->id : null,
            'code' => $message,
            'phone' => $phone,
            'type' => $type,
            'expired_at' => date('Y-m-d H:i:s', time() + 180),
        ]);
        if (App::environment('local')) {
            return $this->success(trans('site.sent_code'), $message, 200);

        } else {
            return $this->success(trans('site.sent_code'), [], 200);

        }

    }

    public function verfiyCode($code, $type)
    {

        $verify = Verification::where('code', $code)->where('type', $type)->orderByDesc('id')->first();

        if (! $verify) {
            return $this->failure(trans('site.code.is_not_exists'), [], 404);
        }

        if ($verify->expired_at < date('Y-m-d H:i:s')) {
            return $this->failure(trans('site.the_code_expiration'), [], 404);
        }

        $user = User::find($verify->user_id);
        if (! $user) {
            return $this->failure(trans('site.the_user_not_exists'), [], 404);
        }

        return $this->success(trans('site.success'), ['code' => $code, 'status' => true], 200);

    }

    public function resetNewPassword($code, $new_password, $type)
    {

        $verify = Verification::where('code', $code)->where('type', $type)->orderByDesc('id')->first();

        if (! $verify) {
            return $this->failure(trans('site.code.is_not_exists'), [], 404);
        }

        if ($verify->expired_at < date('Y-m-d H:i:s')) {
            return $this->failure(trans('site.the_code_expiration'), [], 404);
        }

        $user = User::find($verify->user_id);
        if (! $user) {
            return $this->failure(trans('site.the_user_not_exists'), [], 404);
        }

        if ($type == 'verfiy_account') {

            $user->update([
                'phone_verified_at' => now(),
            ]);

        } else {

            $user->update(['password' => bcrypt($new_password)]);

        }

        return $this->success(trans('site.success'), [], 200);

    }
}
