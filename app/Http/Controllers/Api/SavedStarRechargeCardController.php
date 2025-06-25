<?php

namespace App\Http\Controllers\Api;


use App\Models\SavedStarRechargeCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;



class SavedStarRechargeCardController extends ApiController
{

    protected function getUserSavedRechargeCards()
    {
        return request()->user()->savedstarrechargecards();
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $saveddstvrechargecards = SavedStarRechargeCard::where('user_id',$user->id)->get();
        return $this->success('', [
            'savedstarrechargecards' => $saveddstvrechargecards
        ]);
    }

    public function create(Request $request)
    {
        $saveddstvrechargecard = new SavedStarRechargeCard();

        $user = $request->user();
        $saveddstvrechargecard->user_id = $user->id;
        $saveddstvrechargecard->recharge_card_number = $request->recharge_card_number;
        $saveddstvrechargecard->recharge_card_name = $request->recharge_card_name;
        $saveddstvrechargecard->save();

        return $this->success("Success", [
        ]);


    }

    public function delete(Request $request) {
        $id = $request->id;
        $saveddstvrechargecard = SavedStarRechargeCard::find($id);
        $saveddstvrechargecard->delete();
        
        return $this->success("Success", [
        ]);
    }
}
