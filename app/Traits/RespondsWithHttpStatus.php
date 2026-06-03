<?php

namespace App\Traits;

trait RespondsWithHttpStatus
{
    protected function success($message, $data = [], $status = 200, $external = [])
    {
        return response([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'external' => $external,

        ], $status);
    }

    protected function failure($message, $data = [], $status = 422, $external = [])
    {

        return response([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}
