<?php

namespace App\Http\Controllers\Api;


use App\Models\SavedMeter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;



class SavedMeterController extends ApiController
{

    protected function getUserSavedMeters()
    {
        return request()->user()->savedmeters();
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $savedMeters = SavedMeter::where('user_id',$user->id)->get();
        return $this->success('', [
            'savedmeters' => $savedMeters
        ]);
    }

    public function create(Request $request)
    {
        $savedMeter = new SavedMeter();

        $user = $request->user();
        $savedMeter->user_id = $user->id;
        $savedMeter->meter_number = $request->meter_number;
        $savedMeter->meter_name = $request->meter_name;
        $savedMeter->save();

        return $this->success("Success", [
        ]);


    }
    
    
    public function delete(Request $request) {
        $id = $request->id;
        $savedMeter = SavedMeter::find($id);
        $savedMeter->delete();
        
        return $this->success("Success", [
        ]);
    }
}
