<?php


namespace App\Traits;


use App\Types\ApiStatusCode;

trait ApiResponse
{

    protected function genericSuccess($data)
    {
        return response()->json($data);
    }

    protected function success($message = null, $attrs = [])
    {
        $data = ['status' => true] + $attrs;

        if (! is_null($message)) {
            $data['message'] = $message;
        }

        return response()->json($data);
    }

    protected function genericError($message)
    {
        return $this->error([
            'message' => $message
        ]);
    }

    protected function error($attrs, $code = ApiStatusCode::DISALLOWED_ERROR)
    {

        return response()->json(array_merge(
            ['status' => false],
            $attrs
        ), $code);
    }
}