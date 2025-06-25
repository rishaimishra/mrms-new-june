<?php

namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentsDeliveries;

class PaymentsDeliveriesController extends AdminController
{
    //
    public function index()
    {
        $paymentsDeliveries = PaymentsDeliveries::first();
        return view('admin.paymentdeliveries.view' ,  compact('paymentsDeliveries')); //need to change according to privacy policy
    }

    public function update(Request $request)
    {
        $paymentsDeliveries = PaymentsDeliveries::first();

        if (!$paymentsDeliveries) {
            PaymentsDeliveries::create([
                'title' => $request->title
            ]);
        } else {
            $paymentsDeliveries->update([
                'title' => $request->title
            ]);
        }

        return redirect()->route('admin.payment.index')->with('success', 'Legal Terms updated successfully!');//need to change according to privacy policy
    }
}
