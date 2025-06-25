<?php

namespace App\Http\Controllers\Api;


use App\Models\EdsaTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;



class EdsaTransactionController extends ApiController
{

    public function index(Request $request)
    {
        $user = $request->user();
        $edsatransactions = EdsaTransaction::where('user_id',$user->id)->get();
        return $this->success('', [
            'savedmeters' => $edsatransactions
        ]);
    }

    public function create(Request $request)
    {
        $edsa = new EdsaTransaction();
        $user = $request->user();
        $edsa->user_id = $user->id;
        $edsa->transaction_id = $request->transaction_id;
        $edsa->transaction_status = "Success";
        $edsa->meter_number = $request->meter_number;
        $edsa->meter_reading = $request->meter_reading;
        $edsa->amount = $request->amount;

        $edsa->save();

        return $this->success("Success",[]);
        
    }
}
