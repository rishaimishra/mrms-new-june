<?php

namespace App\Http\Controllers\Admin;

use App\Library\Grid\Grid;
use App\Models\SavedDstvRechargeCard;
use Illuminate\Http\Request;


class SavedDstvRechargeCardController extends AdminController
{


    protected $saveddstvrechargecard;

    public function index()
    {
        $saveddstvrechargecard = SavedDstvRechargeCard::with('user')->get();
        
        return view('admin.saveddstvrechargecard.grid', compact('saveddstvrechargecard'));
    }
}