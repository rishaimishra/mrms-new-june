<?php


namespace App\Http\Controllers\APIV2;

use App\Http\Controllers\Controller;
use App\Types\ApiStatusCode;

class ApiController extends Controller
{
    public function success($attrs)
    {
        return response()->json(array_merge(
            ['success' => true, 'code' => ApiStatusCode::SUCCESS],
            $attrs
        ));
    }

    public function error($code, $attrs)
    {
        return response()->json(array_merge(
            ['success' => false, 'code' => $code],
            $attrs
        ));
    }
}