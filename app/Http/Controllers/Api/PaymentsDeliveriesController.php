<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentsDeliveries;

class PaymentsDeliveriesController extends Controller
{
    //
    public function index()
    {
        // Fetch all data from the AboutApp table
        $paymentsDeliveries = PaymentsDeliveries::all();
        
        // Return the data as JSON response
        return response()->json($paymentsDeliveries);
    }
}
