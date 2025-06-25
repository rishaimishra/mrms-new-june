<?php

namespace App\Http\Controllers\APIV2;

use Illuminate\Http\Request;
use App\Types\ApiStatusCode;
use App\StreetNameApplicationForm;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class StreetNamingAppController extends ApiController
{
    public function update_coordinates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tracking_no' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => $validator->errors()
            ]);
        }

        $street_name_application_form = StreetNameApplicationForm::where('tracking_no',$request->tracking_no)->first();
        if(!$street_name_application_form)
        {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => 'Street Name Application Form not found'
            ]);
        }

        if(!empty($request->near_by_plots_latitude))
        {
            $street_name_application_form->near_by_plots_latitude = $request->near_by_plots_latitude;
        }
        if(!empty($request->near_by_plots_longitutde))
        {
            $street_name_application_form->near_by_plots_longitutde = $request->near_by_plots_longitutde;
        }
        if(!empty($request->near_by_houses_latitude))
        {
            $street_name_application_form->near_by_houses_latitude = $request->near_by_houses_latitude;
        }
        if(!empty($request->near_by_houses_longitutde))
        {
            $street_name_application_form->near_by_houses_longitutde = $request->near_by_houses_longitutde;
        }
        $street_name_application_form->save();

        return $this->success([
            'success'   => true,
            'message' => 'Street Name Application Form updated successfully'
        ]);
    }
}
