<?php

namespace App\Http\Controllers\API\General;

use App\Http\Controllers\API\ApiController;
use App\Models\BoundaryDelimitation;
use App\Types\ApiStatusCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PopulateOnWardController extends ApiController
{
    public function populate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ward' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => $validator->errors()
            ]);
        }

        $delimitation = BoundaryDelimitation::where('ward',$request->ward)->get();

        foreach($delimitation as $del)
        {
            $result['constituency'][] =$del['constituency'];
            $result['section'][] =$del['section'];
            $result['chiefdom'][] =$del['chiefdom'];
            $result['district'][] =$del['district'];
            $result['province'][] =$del['province'];

        }

        $result['constituency'] = array_unique($result['constituency']);
        $result['section'] = array_unique($result['section']);
        $result['chiefdom'] = array_unique($result['chiefdom']);
        $result['district'] = array_unique($result['district']);
        $result['province'] = array_unique($result['province']);

        if($result['province'][0]=='Western Area')
        {
            if($result['district'][0]=='Western Rural')
            {
                $result['postcode'] = 'WR'.sprintf("%003d", $request->ward);
            }else
            {
                $result['postcode'] = 'WU'.sprintf("%003d", $request->ward);
            }

        }else
        {
            $result['postcode'] = substr($result['province'][0],0,1).'P'.sprintf("%003d", $request->ward);

        }

        return $this->success([
            'result' => $result,
        ]);
    }

}
