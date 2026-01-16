<?php

if (!function_exists('api_response')) {
    function api_response($data = null, $code = 200, $message = '', $success = true)
    {
        $intCode = is_numeric($code) ? (int)$code : 500;
        return response()->json([
            'success' => $success,
            'code' => $intCode,
            'message' => $message,
            'data' => $data,
        ], $intCode);
    }
}
