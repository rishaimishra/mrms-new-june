<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AboutApp;

class AboutAppController extends Controller
{
    //
    public function index()
    {
        // Fetch all data from the AboutApp table
        $aboutApp = AboutApp::all();
        
        // Return the data as JSON response
        return response()->json($aboutApp);
    }
}
