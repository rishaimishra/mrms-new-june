<?php

namespace App\Http\Controllers\Admin;


use App\Models\User;
use App\Models\EdsaTransaction;

class DashboardController extends AdminController
{
    public function __invoke()
    {

        $data['User'] = User::count();
        $data['interestedAutos'] = User::whereHas('interestedAutos')->count();
        $data['interestedRealEstate'] = User::whereHas('interestedRealEstate')->count();
        $data['transactions']= EdsaTransaction::where('delete_bit','0')->with('user')->get();
        
        //$data['interestedRealEstate'] = User::whereHas('interestedRealEstate');
        //$data['interestedRealEstate'] = User::whereHas('interestedRealEstate');
        return view('admin.dashboard', $data);
    }
}
