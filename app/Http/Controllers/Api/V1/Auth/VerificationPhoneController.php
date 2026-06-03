<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VerificationCodeRequest;
use App\Http\Requests\Api\VerificationPhoneRequest;
use App\Traits\VerificationCodeTrait;

class VerificationPhoneController extends Controller
{
    use VerificationCodeTrait;

    public function createCode(VerificationPhoneRequest $request, $type = 'verfiy_account')
    {

        return $this->createCodeMobile($request->phone, $type);

    }

    public function verify(VerificationCodeRequest $request, $type = 'verfiy_account')
    {

        return $this->verifyMobile($request->code, '', $type);

    }
}
