<?php

namespace App\Traits;

trait JsonResponseTrait
{
    public static function response($status = 200, $success = true, $data = null, $message = null)
    {
        return response()->json([
            'success' => $success,
            'data'    => $data,
            'message' => $message,
        ], $status);
    }
    public static function error($status = 400, $message = 'خطایی رخ داده است')
    {
        return self::response($status, false,null, $message);
    }

    public static function success($data = null, $message = null)
    {
        return self::response(200, true, $data, $message);
    }


}
