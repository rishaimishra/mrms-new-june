<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IntellectualProperty;

class IntellectualPropertyController extends Controller
{
    //
    public function index()
    {
        // Fetch all data from the AboutApp table
        $intellectualProperty = IntellectualProperty::all();
        
        // Return the data as JSON response
        return response()->json($intellectualProperty);
    }
}
