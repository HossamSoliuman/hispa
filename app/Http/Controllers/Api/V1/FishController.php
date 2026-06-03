<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Fish;
use App\Traits\RespondsWithHttpStatus;

class FishController extends Controller
{
    use RespondsWithHttpStatus;

    public function index()
    {
        $fish = Fish::Active()->get();

        return $this->success(trans('site.getData'), $fish, 200);
    }

    public function show($id)
    {
        $fish = Fish::Active()->find($id);

        return $this->success(trans('site.getData'), $fish, 200);
    }
}
