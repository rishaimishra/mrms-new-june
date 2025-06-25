<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Models\SavedDstvRechargeCard;
use App\Models\DstvSubscription;
use App\Models\User;

class DstvUtilitiesController extends AdminController
{
    //
    public function index()
    {
        // $edsaUtilities = EdsaUtilities::first();
        return view('admin.dstvutilities.view');
    }

    public function rechargeCard(Request $request)
    {
        // Validate the request data
        $request->validate([
            'recharge_card_name' => 'required',
            'recharge_card_number' => 'required',
            'user_name' => 'required',
        ]);

        // Lookup the user ID based on the provided username
        $user = User::where('username', $request->user_name)->first();

        // If the user exists, save the meter number along with the user ID
        if ($user) {
            SavedDstvRechargeCard::create([
                'recharge_card_name' => $request->recharge_card_name,
                'recharge_card_number' => $request->recharge_card_number,
                'user_id' => $user->id,
            ]);
            
            // You can return a success message or redirect back with a success message
            return redirect()->back()->with('success', 'Recharge saved successfully.');
        } else {
            // If the user doesn't exist, return with an error message
            return redirect()->back()->with('error', 'User not found.');
        }
    }
    public function dstv_subscription(){
        $users_seller = [];
         $dstv = DstvSubscription::all();
         $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
         foreach ($users as $user) {
             $users_seller[$user->id] = $user->name;
         }
        return view('admin.dstvutilities.dstv_subscription',compact('dstv','users_seller'));
    }
    public function dstv_subscription_store(Request $request){
        // return $request;
        $subscription = new DstvSubscription();
        $subscription->user_id = $request->users;
        $subscription->role = $request->role;
        $subscription->go_plan_monthly = $request->go_plan_monthly;
        $subscription->go_plan_referal_fee = $request->go_plan_referal_fee;
        $subscription->individual_plan_monthly = $request->individual_plan_monthly;
        $subscription->individual_plan_referal_fee = $request->individual_plan_referal_fee;
        $subscription->business_plan_monthly = $request->business_plan_monthly;
        $subscription->business_plan_referal_fee = $request->business_plan_referal_fee;
        $subscription->save();
        return redirect()->back()->with('success', 'Subscription plan save sucessfully.');
    }

    public function dstv_subscription_edit($id){
        // return $id;
        $users_seller = [];
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        $edsa_subscription = DstvSubscription::where('id',$id)->first();
        return view('admin.dstvutilities.edit_dstv_subscription',compact('edsa_subscription','users_seller'));
    }
    public function dstv_subscription_update(Request $request){
        // return $request;
        $subscription = DstvSubscription::where('id',$request->subscription_id)->first();
        $subscription->user_id = $request->users;
        $subscription->role = $request->role;
        $subscription->go_plan_monthly = $request->go_plan_monthly;
        $subscription->go_plan_referal_fee = $request->go_plan_referal_fee;
        $subscription->individual_plan_monthly = $request->individual_plan_monthly;
        $subscription->individual_plan_referal_fee = $request->individual_plan_referal_fee;
        $subscription->business_plan_monthly = $request->business_plan_monthly;
        $subscription->business_plan_referal_fee = $request->business_plan_referal_fee;
        $subscription->save();
        return redirect()->back();
    }
    public function dstv_subscription_delete($id){
        // return $id;
        $subscription = DstvSubscription::where('id',$id)->first();
        $subscription->delete();
        return redirect()->back();
    }
}
