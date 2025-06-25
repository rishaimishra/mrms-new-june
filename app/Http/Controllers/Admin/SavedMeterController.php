<?php

namespace App\Http\Controllers\Admin;

use App\Library\Grid\Grid;
use App\Models\SavedMeter;
use Illuminate\Http\Request;


class SavedMeterController extends AdminController
{


    protected $savedmeters;

    public function index()
    {
        $savedmeters = SavedMeter::with('user')->get();
        
        return view('admin.savedmeter.grid', compact('savedmeters'));
    }
}