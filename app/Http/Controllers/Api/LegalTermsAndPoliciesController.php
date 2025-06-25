<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LegalTermsAndPolicies;

class LegalTermsAndPoliciesController extends Controller
{
    //
    public function index()
    {
        // Fetch all data from the AboutApp table
        $legalTermsAndPolicies = LegalTermsAndPolicies::all();
        
        // Return the data as JSON response
        return response()->json($legalTermsAndPolicies);
    }
}
