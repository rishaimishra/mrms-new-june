<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\StreetNameApplicationForm;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\StreetNameApplicationPaymentHistory;

class PaymentStreetNameApplication extends Controller
{
    public function index()
    {
        $street_name_application_form = null;
        $payment_histories = [];
        if(!empty(request()->street_application_id))
        {
            $street_application_id = request()->street_application_id;
            $street_name_application_form = StreetNameApplicationForm::find($street_application_id);

            $payment_histories = StreetNameApplicationPaymentHistory::where('street_application_id',$street_application_id)->OrderBy('id','DESC')->get();
        }
        return view("admin.street_name_application.payment.index")->with(compact('street_name_application_form','payment_histories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'price' =>  'required|numeric',
            'pay_type' => 'required|string',
            'payer_name' => 'required|string',
            'pay_taken_by' => 'required|string',
        ]);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->errors());
        }

        $street_name_application_payment_history = new StreetNameApplicationPaymentHistory();
        $street_name_application_payment_history->street_application_id = $request->street_application_id;
        $street_name_application_payment_history->reference_no = date("YmdHis");
        $street_name_application_payment_history->date = $request->date;
        $street_name_application_payment_history->pay_type = $request->pay_type;
        $street_name_application_payment_history->price = $request->price;
        $street_name_application_payment_history->payer_name = $request->payer_name;
        $street_name_application_payment_history->pay_taken_by = $request->pay_taken_by;
        $street_name_application_payment_history->save();

        return redirect()->back()->with('success', 'Your payment has been saved.');
    }
}
