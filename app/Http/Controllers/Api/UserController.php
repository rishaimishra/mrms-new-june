<?php


namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\MobiDocCategory;
use App\Models\ChatARideCategory;
use App\Models\ServiceCategory;
use App\Models\OnlineBookingCategory;
use App\Models\SellerCategory;
use App\Models\SellerDetail;
use App\Models\EdsaTransaction;
use App\Models\TransportVehicleDetails;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

use Twilio\Rest\Client; 

class UserController extends ApiController
{
    use AuthenticatesUsers;

    public function getUser()
    {
        $user = Auth::user();

        $user->avatar = asset("storage/{$user->avatar}");

        if (is_null($user->cart)) {
            $cart = new Cart();
            $cart->user_id = $user->id;
            $user->cart()->save($cart);
        }
        return $this->success(null, ['User' => $user]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|string|max:191',
            'avatar' => 'nullable|mimes:jpg,jpeg,png|',
        ]);

        $user = $request->user();
        $value = $request->all();

        if ($request->hasFile('avatar')) {
            if (file_exists(storage_path('app/' . $user->avatar))) {
                @unlink(storage_path('app/' . $user->avatar));
            }
            $path = \Storage::disk('public')->putFile('avatars', $request->file('avatar'));

            $value['avatar'] = $path;
        }
        $user->update($value);
        //$user->avatar =  \Storage::url($user->avatar);
        $user->avatar = asset("storage/{$user->avatar}");
        return $this->success('You are successfully Updated.', ['User' => $user]);
    }
    
    
    
        //edsa user agent verification

    public function setEdsaPasswordOTP(Request $request)
    {
        $user = $request->user();
        $digits = 4;
        $otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
        $user->current_otp = $otp;
        $user->save();
        $this->sendOtp($user->mobile_number,$otp);
        return $this->success("Success", ["otp"=>$otp]);
    }


    public function sendOtp($mobile_numbder,$otp) {

            $sid    = getenv("TWILIO_SID"); 
             
            $token  = getenv("TWILIO_TOKEN"); 
            $twilio_number = "+16185981277";

            try{
                $twilio = new Client($sid, $token); 
            
            $message = $twilio->messages->create($mobile_numbder, 
                                    [
                                        "messagingServiceSid" => "MGf47f8fe0c13f84488d726cdea4625dd7",      
                                        "body" => "Seven Eleven EDSA set Password OTP: ".$otp 
                                    ]); 
                
                dd("SMS Sent");
                return;
            }catch(Exception $e){
                dd("Error ".$e);
            }
            
            
            return $message->sid;

    }


    public function checkUserOtp(Request $request)
    {
        $user =  $request->user();
        $mob_otp = $request->otp;
        $current_otp = $user->current_otp;
        if($mob_otp == $current_otp)
        {
            return $this->success("Success", ['verification' => "Success" ]);
        }else{
            return $this->success("Success", ['verification' => "Failed" ]);
        }
    }


    public function setEdsaPassword(Request $request)
    {
        $user = $request->user();
        $edsa_four_digit_password = $request->password;
        $user->is_edsa_password_set = 1;
        $user->edsa_password = $edsa_four_digit_password;
        $user->save();

        return $this->success("Success", ['message' => "Password set Successfully"]);

    }


    public function verifyEdsaPassword(Request $request)
    {
        $user = $request->user();
        $edsa_set_password = $user->edsa_password;
        $password = $request->password;

        if($password == $edsa_set_password)
        {
            return $this->success("Success", ['verification' => "Success" ]);
        }else {
            return $this->success("Success", ['verification' => "Falied" ]);
        }
    }

    public function testOTP(Request $request){

        $code = generateOtp();
        $message = "Dear SevenEleven User Your OTP is: ".$code;
        try {
            $accountSid = getenv("TWILIO_SID");
            $authToken = getenv("TWILIO_TOKEN");
            $twilioNumber = getenv("TWILIO_FROM");
            $twilioAL = getenv("TWILIO_FROM_AL");

    
            $client = new Client($accountSid, $authToken);
            
            $user_mobile = '+917439550411';

            if(substr($user_mobile, 0, 3) === "+91"){
                $client->messages->create($user_mobile, [
                    'from' => $twilioNumber,
                    'body' => $message
                ]);
            } else {
                $client->messages->create($user_mobile, [
                    'from' => $twilioAL,
                    'body' => $message
                ]);
            }
            

            // $client->messages->create('+917439550411', [
            //     'from' => 'SEVENELEVEN',
            //     'body' => $message
            // ]);
            
    
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

            return $this->success("Verification code send for password reset.");
    }


    // public function testMail(Request $request){
    //     $message
    // }




    public function get_movie_doc(){
        $mobi_doc = MobiDocCategory::orderBy('sequence')->get();
        if ($mobi_doc) {
            return $this->success("Movie Doc List", ['Mobi Doc' => $mobi_doc]);
        }else{
            return $this->success("No Movie Doc List Found");
        }
       
       
    }
    
    public function get_chat_a_ride(){
        $chat_ride = ChatARideCategory::orderBy('sequence')->get();
        if ($chat_ride) {
            return $this->success("Chat A Ride List", ['Chat A Ride' => $chat_ride]);
        }else{
            return $this->success("No Chat A Ride List Found");
        }
        
       
    }
    public function get_service_category(Request $request){
        // Check if a parent_id is provided in the request
        $parentId = $request->input('parent_id', null);
    
        // If parent_id is provided, filter by parent_id
        if ($parentId) {
            $mobi_doc = ServiceCategory::where('parent_id', $parentId)
                ->orderBy('sequence')
                ->get();
        } else {
            // If no parent_id is provided, get all categories
            $mobi_doc = ServiceCategory::where('parent_id', NULL)
                ->orderBy('sequence')
                ->get();
        }
    
        // Check if any categories are found and return appropriate response
        if ($mobi_doc->isEmpty()) {
            return $this->success("No Service Category List Found");
        } else {
            return $this->success("Service Category List", ['Service Category' => $mobi_doc]);
        }
    }
    

    public function get_online_booking(){
        $chat_ride = OnlineBookingCategory::all();
        if ($chat_ride) {
            return $this->success("Online Movie List", ['Online Movie' => $chat_ride]);
        }else{
            return $this->success("No Online Movie List Found");
        }
        
       
    }

    public function get_movie_doc_seller(Request $request){
        // Fetch seller IDs for the given real estate category
        $sellerIds = SellerCategory::where('mobi_doc_category_id', 1)->pluck('seller_id');
        
        // If no seller IDs are found, return an appropriate response
        if ($sellerIds->isEmpty()) {
            return $this->success("No Seller Found");
        }
        
        // Query the users table where the ID matches any of the seller IDs
        $users = User::with('seller_detail.themeRelations.theme')->whereIn('id', $sellerIds)->get();
        
        // Check if there are users and then process the images and sellertheme
        foreach ($users as $user) {
            // Access seller_detail for each user
            $sellerDetail = $user->seller_detail;
            
            // Fetch and process the sellertheme for the user
            if ($sellerDetail) {
                $sellertheme = SellerDetail::with(['themeRelations.theme'])
                                            ->where('user_id', $sellerDetail->user_id)
                                            ->get();
    
                // Process sellertheme to only include the themeName with the full URL
                $themeNames = $sellertheme->flatMap(function ($seller) {
                    return $seller->themeRelations->map(function ($relation) {
                        // Modify the themeName to include the full path (URL)
                        if ($relation->theme) {
                            $relation->theme->themeName = asset('storage/' . $relation->theme->theme_name);
                        }
                        return $relation->theme->themeName;  // Return the modified themeName
                    });
                });
    
                // Add the processed themeNames to the seller_detail array
                $sellerDetail->sellertheme = $themeNames;
                
                // Check if seller_detail exists and then set business registration image
                if ($sellerDetail->business_registration_image) {
                    $sellerDetail->business_registration_image = asset('busniess_images/' . $sellerDetail->business_registration_image);
                } else {
                    $sellerDetail->business_registration_image = null;  // Or a default image
                }
        
                // Check if seller_detail exists and then set business logo
                if ($sellerDetail->business_logo) {
                    $sellerDetail->business_logo = asset('busniess_images/' . $sellerDetail->business_logo);
                } else {
                    $sellerDetail->business_logo = null;  // Or a default image
                }
            }
        }
        
        // Return the data if there are users
        if ($users->isNotEmpty()) {
            return $this->success("Sellers List Of Real estate Category", ['Data' => $users]);
        } else {
            return $this->success("No Seller Found");
        }
    }

    public function get_chat_a_ride_seller(Request $request)
    {
        $catId = ChatARideCategory::where('id', $request->id)->first();
    
        // Check if the category exists
        if (!$catId) {
            return $this->success("Category not found");
        }
    
        $catname = $catId->name;
    
        // Retrieve sellers
        $sellers = DB::table('transport_vehicle_details')
                ->join('users', 'transport_vehicle_details.user_id', '=', 'users.id')
                ->where('transport_vehicle_details.transport_type', $catname)
                ->get();

        // Loop through each seller and add business_coordinates as an array
        foreach ($sellers as $seller) {
            $sellers_details = DB::table('seller_details')->where('user_id',$seller->user_id)->first();
            if ($sellers_details) {
                # code...
                $seller->business_name = $sellers_details->business_name;
                if (!empty($sellers_details->business_coordinates)) {
                    $coordinates = explode(',', $sellers_details->business_coordinates);
        
                    // Ensure we have exactly 2 parts (latitude, longitude)
                    if (count($coordinates) === 2) {
                        $seller->business_coordinates = [
                            'latitude' => $coordinates[0],
                            'longitude' => $coordinates[1],
                        ];
                    } else {
                        // Handle invalid format (optional)
                        $seller->business_coordinates = null;
                    }
                } else {
                    $seller->business_coordinates = null; // If coordinates are empty
                }
            }
            
            if ($seller->transport_type == 'Cars') {
                # code...
                $seller->vehicle_multiplier = 2.80;
                
            
            }elseif ($seller->transport_type == 'Bus/Minibus') {
                # code...
                $seller->vehicle_multiplier = 2.10;
                
            }elseif ($seller->transport_type == 'Motorbikes (Okada)') {
                # code...
                $seller->vehicle_multiplier = 2.80;
                
            }elseif ($seller->transport_type == 'VIP Rides') {
                # code...
                $seller->vehicle_multiplier = 5;
                
            }elseif ($seller->transport_type =='Taxi') {
                # code...
                $seller->vehicle_multiplier = 2.80;
                
            }else {
                # code...
                $seller->vehicle_multiplier = 0.70;
                
            }
            $seller->rateperkm = '10';
            $seller->availabity = 1;
            // $seller->motorbikes = 0.70;
            // $seller->tricycles = 1.40;
            // $seller->minibus = 2.10;
            // $seller->car = 2.80;
            // $seller->taxi = 2.80;
            // $seller->vip = 5;

            $seller->image1 = 'http://3.23.33.189/busniess_images/1729886278_Picture7.png';
        }
    
        // Check if sellers exist and return the appropriate response
        if ($sellers->isNotEmpty()) {
            return $this->success("Vehicle List Of Chat A Ride", ['Data' => $sellers]);
        } else {
            return $this->success("No Seller Found", ['Data' => $sellers]);
        }
    }
    
    public function get_service_category_seller(Request $request){
        // return $request;
        $sellerIds = SellerCategory::where('mobi_doc_category_id', $request->id)->pluck('seller_id');
        $sellerIds = [150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169];
        // Query the users table where the ID matches any of the seller IDs
          // If no seller IDs are found, return an appropriate response
         // If no seller IDs are found, return an appropriate response
            if (empty($sellerIds)) {
                return $this->success("No Seller Found");
            }
        
        // Query the users table where the ID matches any of the seller IDs
        $users = User::with('seller_detail.themeRelations.theme')->whereIn('id', $sellerIds)->get();
        
        // Check if there are users and then process the images and sellertheme
        foreach ($users as $user) {
            // Access seller_detail for each user
            $sellerDetail = $user->seller_detail;
            
            // Fetch and process the sellertheme for the user
            if ($sellerDetail) {
                $sellertheme = SellerDetail::with(['themeRelations.theme'])
                                            ->where('user_id', $sellerDetail->user_id)
                                            ->get();
    
                // Process sellertheme to only include the themeName with the full URL
                $themeNames = $sellertheme->flatMap(function ($seller) {
                    return $seller->themeRelations->map(function ($relation) {
                        // Modify the themeName to include the full path (URL)
                        if ($relation->theme) {
                            $relation->theme->themeName = asset('storage/' . $relation->theme->theme_name);
                        }
                        return $relation->theme->themeName;  // Return the modified themeName
                    });
                });
    
                // Add the processed themeNames to the seller_detail array
                $sellerDetail->sellertheme = $themeNames;
                
                // Check if seller_detail exists and then set business registration image
                if ($sellerDetail->business_registration_image) {
                    $sellerDetail->business_registration_image = asset('busniess_images/' . $sellerDetail->business_registration_image);
                } else {
                    $sellerDetail->business_registration_image = null;  // Or a default image
                }
        
                // Check if seller_detail exists and then set business logo
                if ($sellerDetail->business_logo) {
                    $sellerDetail->business_logo = asset('busniess_images/' . $sellerDetail->business_logo);
                } else {
                    $sellerDetail->business_logo = null;  // Or a default image
                }
            }
        }
        
        // Return the data if there are users
        if ($users->isNotEmpty()) {
            return $this->success("Sellers List Of Real estate Category", ['Data' => $users]);
        } else {
            return $this->success("No Seller Found");
        }
    }
    
    public function get_autos_category_seller(Request $request){
        // return $request;
        $sellerIds = SellerCategory::where('auto_category_id', 4)->pluck('seller_id');

        // Query the users table where the ID matches any of the seller IDs
        $users = User::with('seller_detail')->whereIn('id', $sellerIds)->get();
        // Check if there are users and then process the images and sellertheme
        foreach ($users as $user) {
            // Access seller_detail for each user
            $sellerDetail = $user->seller_detail;
            
            // Fetch and process the sellertheme for the user
            if ($sellerDetail) {
                $sellertheme = SellerDetail::with(['themeRelations.theme'])
                                            ->where('user_id', $sellerDetail->user_id)
                                            ->get();
    
                // Process sellertheme to only include the themeName with the full URL
                $themeNames = $sellertheme->flatMap(function ($seller) {
                    return $seller->themeRelations->map(function ($relation) {
                        // Modify the themeName to include the full path (URL)
                        if ($relation->theme) {
                            $relation->theme->themeName = asset('storage/' . $relation->theme->theme_name);
                        }
                        return $relation->theme->themeName;  // Return the modified themeName
                    });
                });
    
                // Add the processed themeNames to the seller_detail array
                $sellerDetail->sellertheme = $themeNames;
                
                // Check if seller_detail exists and then set business registration image
                if ($sellerDetail->business_registration_image) {
                    $sellerDetail->business_registration_image = asset('busniess_images/' . $sellerDetail->business_registration_image);
                } else {
                    $sellerDetail->business_registration_image = null;  // Or a default image
                }
        
                // Check if seller_detail exists and then set business logo
                if ($sellerDetail->business_logo) {
                    $sellerDetail->business_logo = asset('busniess_images/' . $sellerDetail->business_logo);
                } else {
                    $sellerDetail->business_logo = null;  // Or a default image
                }
            }
        }
        
        // Return the data if there are users
        if ($users->isNotEmpty()) {
            return $this->success("Sellers List Of Real estate Category", ['Data' => $users]);
        } else {
            return $this->success("No Seller Found");
        }
    }
    
    public function get_realestate_category_seller(Request $request) {
        // Fetch seller IDs for the given real estate category
        $sellerIds = SellerCategory::where('realstate_category_id', 1)->pluck('seller_id');
        
        // If no seller IDs are found, return an appropriate response
        if ($sellerIds->isEmpty()) {
            return $this->success("No Seller Found");
        }
        
        // Query the users table where the ID matches any of the seller IDs
        $users = User::with('seller_detail.themeRelations.theme')->whereIn('id', $sellerIds)->get();
        
        // Check if there are users and then process the images and sellertheme
        foreach ($users as $user) {
            // Access seller_detail for each user
            $sellerDetail = $user->seller_detail;
            
            // Fetch and process the sellertheme for the user
            if ($sellerDetail) {
                $sellertheme = SellerDetail::with(['themeRelations.theme'])
                                            ->where('user_id', $sellerDetail->user_id)
                                            ->get();
    
                // Process sellertheme to only include the themeName with the full URL
                $themeNames = $sellertheme->flatMap(function ($seller) {
                    return $seller->themeRelations->map(function ($relation) {
                        // Modify the themeName to include the full path (URL)
                        if ($relation->theme) {
                            $relation->theme->themeName = asset('storage/' . $relation->theme->theme_name);
                        }
                        return $relation->theme->themeName;  // Return the modified themeName
                    });
                });
    
                // Add the processed themeNames to the seller_detail array
                $sellerDetail->sellertheme = $themeNames;
                
                // Check if seller_detail exists and then set business registration image
                if ($sellerDetail->business_registration_image) {
                    $sellerDetail->business_registration_image = asset('busniess_images/' . $sellerDetail->business_registration_image);
                } else {
                    $sellerDetail->business_registration_image = null;  // Or a default image
                }
        
                // Check if seller_detail exists and then set business logo
                if ($sellerDetail->business_logo) {
                    $sellerDetail->business_logo = asset('busniess_images/' . $sellerDetail->business_logo);
                } else {
                    $sellerDetail->business_logo = null;  // Or a default image
                }
            }
        }
        
        // Return the data if there are users
        if ($users->isNotEmpty()) {
            return $this->success("Sellers List Of Real estate Category", ['Data' => $users]);
        } else {
            return $this->success("No Seller Found");
        }
    }
    
    

    public function get_online_booking_seller(Request $request){
        $sellerIds = SellerCategory::where('chat_a_ride_category_id', $request->id)->pluck('seller_id');

        // Query the users table where the ID matches any of the seller IDs
        $users = User::whereIn('id', $sellerIds)->get();
        if (count($users) > 0) {
            return $this->success("Sellers List Of Online Booking", ['Data' => $users]);
            }else{
                return $this->success("No Seller Found");
            }
    }

    public function get_wallet_balance(){
        $data['transactions']= EdsaTransaction::where('delete_bit','0')->with('user')->get();
        $lastTransaction = $data['transactions']->last();

        if ($lastTransaction) {
            return $this->success("Wallet_Balance", ['Balance' => $lastTransaction->balance]);
            }else{
                return $this->success("No Seller Found");
            }
    }
}
