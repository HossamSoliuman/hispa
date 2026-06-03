<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Traits\RespondsWithHttpStatus;

class PaymentMethods extends Controller
{
    use RespondsWithHttpStatus;

    public function index()
    {
        $data = PaymentMethod::Active()->get();

        return $this->success(trans('site.getData'), $data, 200);

    }
}
