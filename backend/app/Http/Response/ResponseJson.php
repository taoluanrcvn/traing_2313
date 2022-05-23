<?php

namespace App\Http\Response;

use Illuminate\Http\Response;

class ResponseJson
{
    public static function success($data = [],$code = Response::HTTP_OK) {
        return response()->json([
            'statusCode' => true,
            'data' => $data
        ], $code);
    }

    public static function error($messages = [] , $code = Response::HTTP_MISDIRECTED_REQUEST) {
        return response()->json([
            'statusCode' => false,
            'messages' => $messages
        ], $code);
    }

}
