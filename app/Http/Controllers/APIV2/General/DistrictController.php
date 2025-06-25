<?php

namespace App\Http\Controllers\APIV2\General;

use App\Http\Controllers\API\ApiController;
use App\Logic\SystemConfig;
use App\Models\District;

class DistrictController extends ApiController
{


    public function getDistrict()
    {
        $result = District::all();

        return $this->success([
            'result' => $result,
        ]);
    }
}
