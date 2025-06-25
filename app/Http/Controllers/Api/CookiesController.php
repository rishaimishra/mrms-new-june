<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cookies;

class CookiesController extends Controller
{
    //
    public function index()
    {
        // Fetch all data from the AboutApp table
        $cookies = Cookies::all();
        
        // Return the data as JSON response
        return response()->json($cookies);
    }
}
