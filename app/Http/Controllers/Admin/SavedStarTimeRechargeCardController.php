<?php

namespace App\Http\Controllers\Admin;

use App\Library\Grid\Grid;
use App\Models\SavedStarRechargeCard;
use Illuminate\Http\Request;


class SavedStarTimeRechargeCardController extends AdminController
{


    protected $saveddstvrechargecard;

    public function index()
    {
        $saveddstvrechargecard = SavedStarRechargeCard::with('user')->get();
        
        return view('admin.savedstartimerechargecard.grid', compact('saveddstvrechargecard'));
    }
}