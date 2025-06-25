<?php

namespace App\Http\Controllers\Admin;

use App\Library\Grid\Grid;
use App\Models\User;
use App\Models\ProductCategory;
use App\Models\SellerDetail;
use App\Models\SeaFreightShipment;
use App\Imports\FreightImport;
use App\Imports\MoneyTransImport;
use App\Exports\FreightExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CollectionPayment;
use App\Models\MoneyTransfer;
use DB;
use App\Models\SellerCategory;
use App\SellerTheme;
use App\SellerThemeRelation;
use App\Models\Plan;
use App\Exports\MoneyTransferExport;
use App\Models\NewsSubscription;
use App\Models\NationalNews;

class SellerDetailController extends AdminController
{


    protected $savedmeters;

    public function index()
    {
        // $sellers = SellerDetail::with('user')->get();
      
        
        $sellers = SellerDetail::with('user') 
        ->leftjoin('seller_categories', 'seller_details.user_id', '=', 'seller_categories.seller_id')
        ->leftJoin('plans', 'seller_categories.plan_id', '=', 'plans.id')
        ->select('seller_details.*', 'plans.plan_title')
        ->get();
        
        return view('admin.seller.grid', compact('sellers'));
    }

    public function sellerAccount(){
         // $user = User::findOrFail($id);
         $id = Auth::user()->id;
         $user = User::with('seller_detail')->where('id',$id)->first();
        //  dd($user->seller_detail);
         $user_id = $user->seller_detail->user_id;
         $category = SellerCategory::where('category_id',$user->seller_detail->store_category)->first();
        //  dd($category);
        if (isset($category->category_id)) {
            # code...
            
          $product_category=ProductCategory::where('id',$category->category_id)->first();
        }else{
        $product_category = 'N/A';

        }
       
        if (isset($category->plan_id)) {
            # code...
            
         
            $planName = Plan::where('id',$category->plan_id)->first();
        }else{
            $planName = 'N/A';

        }

     
        //dd(DigitalAddress::query()->where('user_id',$id));
        /* return view('admin.user.show',[
                'dataProvider' => new EloquentDataProvider(DigitalAddress::query()->with('address','address.addressArea','address.addressChiefdom','address.addressSection','addressArea','addressChiefdom','addressSection')->where('user_id',$id)),
                'user' =>$user
            ] );*/

            $sellerThemes = SellerTheme::all();
            $specific_seller_theme = SellerThemeRelation::with('theme')->where('seller_id','=',$user_id)->first();
            if (isset($specific_seller_theme->theme)) {
                $theme_name = $specific_seller_theme->theme;
            }else{
                $theme_name = 'N/A';
            }

        return view('admin.seller_system.account', [
            'user' => $user,
            'plan' => $product_category,
            'plan_name' =>$planName,
            'sellerThemes'=>$sellerThemes,
            'specific_theme'=>$theme_name
        ]);
    
    }

    public function verify(Request $request)
    {

        $sellerdetail = SellerDetail::where('user_id',$request->id)->first();
        $productcategory = ProductCategory::where('seller_detail_id', $sellerdetail->id)->first();
        $sellerdetail->is_verified = 1;
        $productcategory->is_active = 1;
        $sellerdetail->save();
        $productcategory->save();

        $sellers = SellerDetail::with('user')->get();
        dd($sellers);
        
        return view('admin.seller.grid', compact('sellers'));
        //return view('admin.seller.index');
    }




    public function sea_air_frieghts(){
        // return "sdaf";
        $sea_frieghts = SeaFreightShipment::groupBy('container_batch_no')->get();
        return view('admin.seller_system.option1',compact('sea_frieghts'));
    }
    public function users() {
        // Fetch consignor names and add a 'role' field
        $consignorNames = SeaFreightShipment::select('consignor_name')->get()->map(function($item) {
            return ['name' => $item->consignor_name, 'role' => 'consignor'];
        });
    
        // Fetch consignee names and add a 'role' field
        $consigneeNames = SeaFreightShipment::select('consignee_name')->get()->map(function($item) {
            return ['name' => $item->consignee_name, 'role' => 'consignee'];
        });
    
        // Merge the two collections
        $names = $consignorNames->merge($consigneeNames);
    
        return view('admin.seller_system.option2', compact('names'));
    }
    
    public function upload_sea_air_frieghts(Request $request){
        // return $request;
         $user = Auth::user();
        $request->validate([
            'sea_air_freight_file' => 'required|mimes:xlsx,csv',
        ]);
        $seller_id = $user->id;
        $seller_name = $user->first_name;
        Excel::import(new FreightImport($seller_id, $seller_name), $request->file('sea_air_freight_file'));
        return redirect()->back()->with('success', 'Data imported successfully!');
    }   

    public function FreightExport(Request $request){
        $sea_frieghts = SeaFreightShipment::with('deliveryBook')->get();
       return Excel::download(new FreightExport($sea_frieghts), 'sea_air_freight.xlsx');
    }
    public function notification(){
        return view('admin.seller_system.notifications');
    }
    public function collectionPayments(){
        $payments = CollectionPayment::all();
        return view('admin.seller_system.collectionPayments',compact('payments'));
    }
    public function MoneyTransfer(){
        $user = Auth::user()->id;
        $payments = MoneyTransfer::where('seller_id',$user)->get();
        return view('admin.seller_system.moneyTransfer',compact('payments'));
    }

    public function upload_money_transfer(Request $request){
       
        $request->validate([
            'money_transfer_file' => 'required|mimes:xlsx,csv',
        ]);
        $seller_id =  Auth::user()->id;
        Excel::import(new MoneyTransImport($seller_id), $request->file('money_transfer_file'));
        return redirect()->back()->with('success', 'Data imported successfully!');
    } 

    public function export_payment_excel_moneytransfer(Request $request){
        $seller_id =  Auth::user()->id;
        $payments = MoneyTransfer::where('seller_id',$seller_id)->get();
        return Excel::download(new MoneyTransferExport($payments), 'money_transfer.xlsx');
    }



    public function assignThemeSeller(Request $request)
    {
        // Validate the form inputs
        $request->validate([
            'seller_id' => 'required',
            'theme_id' => 'required',
        ]);

        SellerThemeRelation::updateOrCreate(
            [
                'seller_id' => $request->seller_id, // Search for a record with this seller_id
            ],
            [
                'theme_id' => $request->theme_id,   // If found, update the theme_id
            ]
        );

        return redirect()->back()->with('success', 'Theme has been assigned successfully.');
    }

    public function UpdateLogo(Request $request)
    {
       
        $request->validate([
            'business_logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image type
        ]);

        $user = Auth::user();
        $sellerDetail = $user->seller_detail;
        // dd($sellerDetail);

        // Handle the file upload
        if ($request->hasFile('business_logo')) {
            $file = $request->file('business_logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('busniess_images'), $filename);

            // Update the seller_detail with the new logo path
            $sellerDetail->business_logo = $filename;
            $sellerDetail->save();
        }

        return redirect()->back()->with('success', 'Business logo updated successfully!');
    }

    public function showAssignRoleForm()
    {
        // Fetch users with mobile numbers
        $users = User::whereNotNull('mobile_number')
        ->where('is_seller', '!=', 1)
        ->orWhereNull('is_seller')
        ->get();

        $user_seller_id = Auth::user()->id;
        // dd($user_seller_id);

        // Fetch users with mobile numbers
        $seller_users = User::whereNotNull('mobile_number')
        ->where('is_seller', '!=', 1)
        ->where('seller_id', '=', $user_seller_id)
        ->get();


        // Pass users to the view
        return view('admin.seller_system.assign-role', compact('users','seller_users'));
    }

    /**
     * Handle the role assignment request.
     */
    public function assignRole(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'seller_role' => 'required|string',
        ]);

        // Find the user by id
        $user = User::findOrFail($request->user_id);

        // Assign the role
        $user->seller_id = Auth::user()->id;
        $user->seller_role = $request->seller_role;

        $user->save();

        return back()->with('success', 'Role assigned successfully to ' . $user->name);
    }

    public function stateNews(){
        $dstv = NewsSubscription::all();
        return view('admin.seller_system.state_newslist',compact('dstv'));
    }

    public function nationalNews(){
        $user_id = Auth::user()->id;
        $users_seller = [];
        $dstv = NationalNews::where('user_id',$user_id)->get();
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        return view('admin.seller_system.national.national_news',compact('dstv','users_seller','user_id'));
    }

    public function national_news_delete($id){
        // return $id;
        $subscription = NationalNews::where('id',$id)->first();
        $subscription->delete();
        return redirect()->back();
    }

    public function national_news_edit($id){
        // return $id;
        $users_seller = [];
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        $hd = NationalNews::where('id',$id)->first();
        return view('admin.seller_system.national.show',compact('hd','users_seller'));
    }
    public function national_news_edit_save($id){
    
        $users_seller = [];
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        $hd = NationalNews::where('id',$id)->first();

        return view('admin.seller_system.national.edit',compact('hd','users_seller'));
    }

    public function national_news_store(Request $request){

        $validatedData = $request->validate([
            'headline' => 'required|string|max:255',
            'story_board' => 'required',
            'headline_image' => 'required',
            // 'headlineimg' => 'required',
            'editor_name' => 'required'
        ]);
         $headline = new NationalNews();
        $headline->headline = $request->headline;
        $headline->editor_name = $request->editor_name;
        $headline->headline_description = $request->headline_description;
            if ($request->hasFile('front_image')) {
                $file = $request->file('front_image');
                $path = $file->store('form_images', 'public');
                $headline->front_image = $path;
             }
            if ($request->hasFile('headline_image')) {
                $file = $request->file('headline_image');
                $path = $file->store('form_images', 'public');
                $headline->headline_image = $path;
             }
             $headline->user_id = Auth::user()->id;
             $headline->status = 1;
             $headline->is_image = 1;
             $headline->story_board = $request->story_board;
             $headline->save();
            
             return back()->with('success', 'headline added successfully.');
    }
    public function national_news_edit_update(Request $request,$id){
      
        // Find the existing NationalNews record by ID
                $headline = NationalNews::findOrFail($id); // Using findOrFail to ensure the record exists
                
                // Update the fields with new data
                $headline->headline = $request->headline;
                $headline->editor_name = $request->editor_name;
                $headline->headline_description = $request->headline_description;
                $headline->story_board = $request->story_board;

                // Check if a new front image file was uploaded and store it
                if ($request->hasFile('front_image')) {
                    $file = $request->file('front_image');
                    $path = $file->store('form_images', 'public');
                    $headline->front_image = $path;
                }

                // Check if a new headline image file was uploaded and store it
                if ($request->hasFile('headline_image')) {
                    $file = $request->file('headline_image');
                    $path = $file->store('form_images', 'public');
                    $headline->headline_image = $path;
                }

                // Update other necessary fields
                $headline->user_id = Auth::user()->id;
                $headline->status = 1;
                $headline->is_image = 1;

                // Save the updated record
                $headline->save();
            
             return back()->with('success', 'headline added successfully.');
    }

    public function news_subscription_store(Request $request){
        // return $request;
        $validatedData = $request->validate([
            'headline' => 'required|string|max:255',
            'story_board' => 'required',
            'headline_image' => 'required',
            // 'headlineimg' => 'required',
            'editor_name' => 'required'
        ]);
         $headline = new NewsSubscription();
        $headline->headline = $request->headline;
        $headline->editor_name = $request->editor_name;
        $headline->headline_description = $request->headline_description;
        if ($request->hasFile('headline_image')) {
            $file = $request->file('headline_image');
            $path = $file->store('form_images', 'public');
            $headline->headline_image = $path;
             }
             $headline->user_id = 105;
             $headline->status = 1;
             $headline->story_board = $request->story_board;
             $headline->save();
             if ($request->hasFile('headlineimg')) {
                foreach ($request->File('headlineimg') as $key => $file) {
                    $path = $file->store('headlineimg', 'public');
                        $headline_image = new HeadlineImages();
                        $headline_image->images = $path;
                        $headline_image->headline_id = $headline->id;
                        $headline_image->save();
                    }
             }
             return back()->with('success', 'headline added successfully.');
    }

    public function news_subscription_edit($id){
        // return $id;
        $users_seller = [];
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        $hd = NewsSubscription::where('id',$id)->first();
        return view('admin.seller_system.statenewsShow',compact('hd','users_seller'));
    }
    public function news_subscription_edit_news($id){
        // return $id;
        $users_seller = [];
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        $hd = NewsSubscription::where('id',$id)->first();
        return view('admin.seller_system.statenewsEdit',compact('hd','users_seller'));
    }

    public function news_subscription_update(Request $request){
        $headline = NewsSubscription::findOrFail($request->news_id);

        // Update fields
        $headline->headline = $request->headline;
        $headline->editor_name = $request->editor_name;
        $headline->headline_description = $request->headline_description;
        $headline->story_board = $request->story_board;

        // Handle headline image update
        if ($request->hasFile('headline_image')) {
            $file = $request->file('headline_image');
            $path = $file->store('form_images', 'public');
            $headline->headline_image = $path;
             }

        $headline->save();
        return redirect()->back();
    }

    public function news_subscription_delete($id){
        // return $id;
        $subscription = NewsSubscription::where('id',$id)->first();
        $subscription->delete();
        return redirect()->back();
    }
   

}