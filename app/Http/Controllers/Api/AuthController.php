<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\User;
use App\Models\UserVerification;
use App\Models\SellerDetail;
use App\Models\SellerCategory;
use App\Models\SeaFreightShipment;
use App\Models\CollectionPayment;
use App\Models\MoneyTransfer;
use App\Notifications\ForgotPasswordSms;
use App\Notifications\MobileNumberVerification;
use Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Validator;
use function Illuminate\Support\Facades\Hash;

use Twilio\Rest\Client;

class AuthController extends ApiController
{
    public $successStatus = 200;

    use AuthenticatesUsers;

    public function __construct()
    {

        $this->middleware('throttle:5,1')->only('resendOTP');
        $this->middleware('throttle:5,5')->only('changeMobileNumber');
        $this->middleware('throttle:5,5')->only('mobileVerification');
    }

    public function register(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'mobile_number' => 'required|phone:AUTO|unique:users,mobile_number',
            'username' => 'required|string|alpha_num|unique:users,username'
        ]);
        

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['code'] = 0;
        /* Gererate otp from helper*/
        $code = generateOtp();
        /* User Register */
        $user = User::create($input);

        /* OTP code save */
        $user->userVerifications()->insert([
            'identity' => $user->mobile_number,
            'code' => $code,
            'user_id' => $user->id,
            'expired_at' => now()->addMinutes(15),
            'created_at' => now()
        ]);

        /* Otp Send On User's  Mobile */
        // $user->notify(new MobileNumberVerification($code));


        // Triggering Twilio SMS OTP
        // try {
        //     $accountSid = getenv("TWILIO_SID");
        //     $authToken = getenv("TWILIO_TOKEN");
        //     $twilioNumber = getenv("TWILIO_FROM");

        //     // dd("account ssid ".$accountSid);
        //     // dd("auth token".$authToken);

 
        //     $client = new Client($accountSid, $authToken);
 
        //     $client->messages->create($user->mobile_number, [
        //         'from' => $twilioNumber,
        //         'body' => $message
        //     ]);
        //     // dd("Sms sent");
            
 
        // } catch (\Exception $e) {
        //     dd($e->getMessage());
        // }

        // END of TWILIO SMS OTP

        $token = $user->createToken('AppName')->accessToken;
        if (is_null($user->cart)) {
            $cart = new Cart();
            $cart->user_id = $user->id;
            $user->cart()->save($cart);
        }
        return $this->success(
            'You are successfully registered with us. We have sent you a OPT to verify your mobile number.',
            [
                'is_mobile_verified' => (!is_null($user->mobile_verified_at)) ? true : false,
                'token' => $token,
                'code' => $code
            ]
        );
    }


    public function registerSeller(Request $request)
    {
        // return $request;
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'mobile_number' => 'required|phone:AUTO|unique:users,mobile_number',
                'username' => 'required|string|alpha_num|unique:users,username',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422); // 422 Unprocessable Entity
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['code'] = 1;
        $input['is_seller'] = 1;
        // $input['role_id'] = $input['role'];
        $input['nin'] = $request->nin;
        $input['street_number'] = $request->street_number;
        $input['street_name'] = $request->street_name;
        $input['area'] = $request->area;
        $input['ward'] = $request->ward;
        $input['section'] = $request->section;
        $input['chiefdon'] = $request->chiefdon;
        $input['province'] = $request->province;

        $input['mobile_verified_at'] = new \DateTime();

        /* User Register */
        $user = User::create($input);

        $sellerdetail = new SellerDetail();
        $sellerdetail->user_id = $user->id;
        $sellerdetail->store_name = $input['name'];
        if ($request->hasFile('business_registration_image')) {
            $file = $request->file('business_registration_image');
            $bus_reg_filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('busniess_images'), $bus_reg_filename);
        }
        else{
            $bus_reg_filename = '';
        }
        if ($request->hasFile('business_logo')) {
            $file = $request->file('business_logo');
            $logo_filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('busniess_images'), $logo_filename);
        }
        else{
            $logo_filename = '';
        }
        $sellerdetail->street_number=$request->street_number;
        $sellerdetail->business_registration_image=$bus_reg_filename;
        $sellerdetail->business_logo=$logo_filename;
        $sellerdetail->street_name=$request->street_name;
        $sellerdetail->area=$request->area;
        $sellerdetail->ward=$request->ward;
        $sellerdetail->section=$request->section;
        $sellerdetail->chiefdon=$request->chiefdon;
        $sellerdetail->province=$request->province;
        $sellerdetail->business_name=$request->business_name;
        $sellerdetail->tin=$request->tin;
        $sellerdetail->business_coordinates=$request->business_coordinates;
        $sellerdetail->mobile1=$request->mobile1;
        $sellerdetail->mobile2=$request->mobile2;
        $sellerdetail->mobile3=$request->mobile3;
        $sellerdetail->business_email=$request->business_email;
        $sellerdetail->opening_time=$request->opening_time;
        $sellerdetail->closing_time=$request->closing_time;
        $sellerdetail->bank_account=$request->bank_account;
        $sellerdetail->account_name=$request->account_name;
        $sellerdetail->account_number=$request->account_number;
        $sellerdetail->swift_code=$request->swift_code;
        $sellerdetail->esfc=$request->esfc;
        $sellerdetail->originating_country=$request->originating_country;
        $sellerdetail->shipment_type=$request->shipment_type;
        $sellerdetail->save();
        $seller_catgory = new SellerCategory();
        $seller_catgory->seller_id = $user->id;
        $seller_catgory->category_id = $request->category_id;
        $seller_catgory->service_id = $request->service_id;
        $seller_catgory->save();
        // // Triggering Twilio SMS OTP
        // try {
        //     $accountSid = getenv("TWILIO_SID");
        //     $authToken = getenv("TWILIO_TOKEN");
        //     $twilioNumber = getenv("TWILIO_FROM");

        //     // dd("account ssid ".$accountSid);
        //     // dd("auth token".$authToken);

 
        //     $client = new Client($accountSid, $authToken);
 
        //     $client->messages->create($user->mobile_number, [
        //         'from' => $twilioNumber,
        //         'body' => $message
        //     ]);
        //     // dd("Sms sent");
            
 
        // } catch (\Exception $e) {
        //     dd($e->getMessage());
        // }

        // // END of TWILIO SMS OTP

        
        // $token = $user->createToken('AppName')->accessToken;

        return $this->success(
            'You are successfully registered with us. ',
            // [
            //     'is_mobile_verified' => (!is_null($user->mobile_verified_at)) ? true : false,
            //     'token' => $token
            // ]
        );
    }


    public function login(Request $request)
    {
        /* Validate Request */
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
    
        /* Validate max attempts */
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
    
            $seconds = $this->limiter()->availableIn(
                $this->throttleKey($request)
            );
            return $this->error([
                'message' => Lang::get('auth.throttle', ['seconds' => $seconds])
            ]);
        }
    
        /* Add Attempts */
        $this->incrementLoginAttempts($request);
    
        $credentials = [];
        if ($this->isEmail($request->input('username'))) {
            $credentials = ['email' => request('username'), 'password' => request('password')];
        } elseif ($this->isMobile($request)) {
            $credentials = ['mobile_number' => request('username'), 'password' => request('password')];
        } else {
            $credentials = ['username' => request('username'), 'password' => request('password')];
        }
    
        /* Auth Credentials */
        if (!empty($credentials) && Auth::attempt($credentials)) {
    
            /* clear Attempts */
            $this->clearLoginAttempts($request);
    
            /* @var $user \App\Models\User */
            $user = Auth::user();
    
            // Check if user is active
            if ($user->is_active) {
    
                // Generate token
                $token = $user->createToken('AppName')->accessToken;
    
                // Check if user has a cart, if not create a new cart
                if (is_null($user->cart)) {
                    $cart = new Cart();
                    $cart->user_id = $user->id;
                    $user->cart()->save($cart);
                }
    
                // Get the complete URL for avatar, if it exists
                $avatarUrl = null;
                if ($user->avatar) {
                    // If avatar is stored in public folder
                    $avatarUrl = asset('storage/' . $user->avatar);
    
                    // Alternatively, if avatar is stored in the storage folder and you use Laravel's storage link
                    // $avatarUrl = Storage::url($user->avatar);
                }
    
                // Prepare user data to include in the response
                $userData = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'mobile_number' => $user->mobile_number,
                    'name' => $user->name,
                    'avatar' => $avatarUrl,  // Full URL to the avatar
                    'is_active' => $user->is_active,
                    'is_mobile_verified' => $user->hasVerifiedMobile(),
                    'created_at' => $user->created_at->toDateTimeString(),
                    'updated_at' => $user->updated_at->toDateTimeString(),
                ];
    
                // Return success response with user data and token
                return $this->success("Success", [
                    'user' => $userData,  // Include user data in the response
                    'token' => $token
                ]);
            } else {
                return $this->genericError('This user account is not active.');
            }
        } else {
            return $this->genericError('Invalid credentials.');
        }
    }
    



    public function isEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function isMobile($request)
    {

        $validator = Validator::make(['username' => $request->username], [
            'username' => 'phone:AUTO'
        ]);
        if ($validator->fails()) {

            return false;
        } else {

            return true;
        }
    }

    public function forgotPassword(Request $request)
    {

        /* Validate Request */
        $request->validate([
            'username' => 'required'
        ]);
        $credentials = [];
        if ($this->isEmail($request->input('username'))) {
            $credentials = ['email' => request('username')];
        } elseif ($this->isMobile($request)) {
            $credentials = ['mobile_number' => request('username')];
        } else {
            $credentials = ['username' => request('username')];
        }


        /* Generate otp from helper*/
        $code = generateOtp();

        /* User find */
        $user = User::where($credentials)->first();

        if ($user) {
            /* OTP code save */
            $user->userVerifications()->insert([
                'identity' => $user->mobile_number,
                'code' => $code,
                'user_id' => $user->id,
                'expired_at' => now()->addMinutes(15),
                'created_at' => now()
            ]);

            $message = "Dear SevenEleven User Your OTP is: ".$code;
            /* Otp Send to User's  Mobile */
            // $user->notify(new ForgotPasswordSms($code));
            try {
                $accountSid = getenv("TWILIO_SID");
                $authToken = getenv("TWILIO_TOKEN");
                $twilioNumber = getenv("TWILIO_FROM");
                $twilioAL = getenv("TWILIO_FROM_AL");

                // dd("account ssid ".$accountSid);
                // dd("auth token".$authToken);

     
                $client = new Client($accountSid, $authToken);
                
                $user_mobile = $user->mobile_number;
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


                // $client->messages->create($user->mobile_number, [
                //     'from' => $twilioNumber,
                //     'body' => $message
                // ]);
                // dd("Sms sent");
                
     
            } catch (\Exception $e) {
                dd($e->getMessage());
            }

            return $this->success("Verification code send for password reset.");
        } else {
            return $this->genericError('Invalid username');
        }
    }

    public function checkOTP(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:4',
            'username' => 'required',
        ]);

        $credentials = [];
        if ($this->isEmail($request->input('username'))) {
            $credentials = ['email' => request('username')];
        } elseif ($this->isMobile($request)) {
            $credentials = ['mobile_number' => request('username')];
        } else {
            $credentials = ['username' => request('username')];
        }


        $code = $request->input('code');

        /* User find */
        $user = User::where($credentials)->first();

        if ($user) {

            $isValidVerification = UserVerification::where([
                'identity' => $user->mobile_number,
                'code' => $request->input('code'),
                'user_id' => $user->id
            ])
                ->where('expired_at', '>', now()->subMinutes(15)->format('Y-m-d H:i:s'))
                ->exists();


            if ($isValidVerification) {

                return $this->success('valid otp.');
            } else {
                return $this->error(['code' => 4003, "message" => 'OTP may expired or invalidated.']);
            }
        } else {
            return $this->genericError('Username not found.');
        }
    }

    public function resetPassword(Request $request)
    {

        /* Validate Request */
        $request->validate([
            'username' => 'required',
            'code' => 'required|digits:4',
            'password' => 'required|min:8',
        ]);
        $credentials = [];
        if ($this->isEmail($request->input('username'))) {
            $credentials = ['email' => request('username')];
        } elseif ($this->isMobile($request)) {
            $credentials = ['mobile_number' => request('username')];
        } else {
            $credentials = ['username' => request('username')];
        }


        /* User find */
        $user = User::where($credentials)->first();

        if ($user) {
            /* OTP code find */
            $isValidVerification = UserVerification::where([
                'identity' => $user->mobile_number,
                'code' => $request->input('code'),
                'user_id' => $user->id
            ])
                ->where('expired_at', '>', now()->subMinutes(15)->format('Y-m-d H:i:s'))
                ->exists();


            if ($isValidVerification) {

                $user->update([
                    'password' => Hash::make($request->input('password'))
                ]);

                UserVerification::where(['user_id' => $user->id])->delete();

                return $this->success('Password reset successfully.');
            } else {
                return $this->error(['code' => 4003, "message" => 'OTP may expired or invalidated.']);
            }
        } else {
            return $this->genericError('Username not found.');
        }
    }

    public function updatePassword(Request $request)
    {

        /* Validate Request */
        $request->validate([
            'current' => ['required', 'string'],
            'password' => ['required', 'min:8', 'different:current'],
        ], [
            'current' => 'Current Password',
            'password' => 'New Password'
        ]);

        $user = $request->user();

        if (!Hash::check($request->current, $user->password)) {
            return $this->genericError('Current Password does not match.');
        }

        $user->password = Hash::make($request->password);
        $user->save();
        return $this->success('Password reset successfully.');
    }


    public function resendOTP(Request $request)
    {

        $user = $request->user();

        /* Gererate otp from helper*/
        $code = generateOtp();

        $user->userVerifications()->insert([
            'identity' => $user->mobile_number,
            'code' => $code,
            'user_id' => $user->id,
            'expired_at' => now()->addMinutes(15),
            'created_at' => now()
        ]);


        /* Otp Send On User's  Mobile */
        $user->notify(new MobileNumberVerification($code));

        return $this->success('Mobile verification code sent.');
    }

    public function changeMobileNumber(Request $request)
    {
        /* Validate Request */
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = $request->user();
        $mobile_number = $request->input('mobile_number');
        /* Gererate otp from helper*/
        $code = generateOtp();

        $user->update([
            'mobile_number' => $mobile_number,
        ]);

        $user->userVerifications()->where(['user_id' => $user->id])->delete();

        $user->userVerifications()->insert([
            'identity' => $user->mobile_number,
            'code' => $code,
            'user_id' => $user->id,
            'expired_at' => now()->addMinutes(15),
            'created_at' => now()
        ]);


        /* Otp Send On User's  Mobile */
        $user->notify(new MobileNumberVerification($code));

        return $this->success('Mobile verification code sent.');
    }

    public function mobileVerification(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:4',
        ]);

        $user = $request->user();
        $code = $request->code;


        $isValidVerification = UserVerification::where([
            'identity' => $user->mobile_number,
            'code' => $code,
            'user_id' => $user->id
        ])
            ->where('expired_at', '>', now()->subMinutes(15)->format('Y-m-d H:i:s'))
            ->exists();

        if ($isValidVerification) {

            $user->update([
                'mobile_verified_at' => now()
            ]);

            UserVerification::where(['user_id' => $user->id])->delete();

            return $this->success('Mobile number verification success.');
        } else {
            return $this->error(['code' => 4003, "message" => 'OTP may expired or invalidated.']);
        }
    }


    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->success('You are successfully logged out.');
    }

    public function username()
    {
        return 'email';
    }
    public function get_seller($id,Request $request){
        // return $id;
        // return $air_sea = SeaFreightShipment::where('consignor_mobile_no', $request->mobile_no)
        // ->orWhere('consignee_mobile_no', $request->mobile_no)
        // ->with(['get_user_detail','get_user'])
        // ->get();
        // if ($air_sea) {
            $user = User::where('id',$id)->with('seller_detail')->first();
            $businessCoordinates = $user->seller_detail ? explode(",",$user->seller_detail->business_coordinates) : [];
            $user->seller_detail->business_coordinates =  $businessCoordinates;

            $air_sea = SeaFreightShipment::where('consignor_mobile_no', $request->mobile_no)
            ->orWhere('consignee_mobile_no', $request->mobile_no)
            ->where('seller_id',$id)
            ->get();
            $air_sea = SeaFreightShipment::where('seller_id',$id)
            ->where('consignor_mobile_no', '23230711711')
            ->get();
            // dd($air_sea);

            if($user){
            return $this->success("User get successfully", [
                'User' => $user,
                'air_sea' => $air_sea
            ]);
           }
           else{
            return $this->success(["message" => 'User not found or deleted']);
           }
        // }
        // else{
        //     return $this->success(["message" => 'Record not found or deleted']);
        // }
      
    }
    public function get_payment_collection($id){
        $collection_payemnt = CollectionPayment::where('customer_id',$id)->get();
        if ($collection_payemnt) {
            return $this->success("Payment get successfully", [
                'Data' => $collection_payemnt
            ]);
        }
        else{
            return $this->success(["message" => 'User has no payments']);
        }
    }
    
    
    public function get_money_tranfer_collection($id){
        $collection_payemnt = MoneyTransfer::where('customer_id',$id)->get();
        if ($collection_payemnt) {
            return $this->success("Payment get successfully", [
                'Data' => $collection_payemnt
            ]);
        }
        else{
            return $this->success(["message" => 'User has no payments']);
        }
    }

    public function get_money_tranfer_collectionSeller(){
         // Fetch seller IDs for the given real estate category
         $sellerIds = User::where('user_type', 'moneytransfer')->pluck('id');
        
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
    public function getcollectionSeller(){
      

          // Fetch seller IDs for the given real estate category
          $sellerIds = User::where('user_type', 'collection and payments')->pluck('id');
        
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

    public function shipper_by_country() {
        // Get unique countries along with corresponding sellers
        return $countriesWithSellers = SellerDetail::select('originating_country', 'user_id')
        ->with(['user:id,name']) // Eager load the user's id and name only
        ->where('store_category_name','=','SHIP')
        ->groupBy('originating_country', 'user_id') // Group by country and user ID
        ->get()
        ->map(function($seller) {
            return [
                'originating_country' => $seller->originating_country,
                'seller_id' => $seller->user_id,
                'name' => $seller->user ? $seller->user->name : 'N/A', // Check if user exists
            ];
        })
        ->groupBy('originating_country');
    
        return response()->json([
            'status' => true,
            'message' => 'Sellers by Country',
            'data' => $countriesWithSellers
        ], 200);
    }
}
