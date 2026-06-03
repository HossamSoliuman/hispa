<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\VerificationCodeRequest;
use App\Http\Requests\Api\VerificationPhoneRequest;
use App\Traits\VerificationCodeTrait;

class ForgetPasswordController extends Controller
{
    use VerificationCodeTrait;

    public function forgetPassword(VerificationPhoneRequest $request)
    {
        $type = 'forgetPassword';

        return $this->createCodeMobile($request->phone, $type);

    }

    public function checkCode(VerificationCodeRequest $request)
    {
        $type = 'forgetPassword';

        return $this->verfiyCode($request->code, $type);

    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $type = 'forgetPassword';

        return $this->resetNewPassword($request->code, $request->password, $type);

    }
}
