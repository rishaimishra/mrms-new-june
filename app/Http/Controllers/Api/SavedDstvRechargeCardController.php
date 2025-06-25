<?php

namespace App\Http\Controllers\Api;


use App\Models\SavedDstvRechargeCard;
use App\Models\DstvTransaction;
use App\Models\StarTransaction;
use App\Models\TrackshippingTransaction;
use App\Models\EdsaTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendPdfMail;
use PDF;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Auth;

use App\Models\SeaFreightShipment;
use App\Models\User;


class SavedDstvRechargeCardController extends ApiController
{
    public $snay_api_key  = "API_KEY: 6d5b96f0-1d90-46cd-885f-6603c023adcf";
    public $apiKey = 'fc3a26797ece28169fd189e889fadc55';
    public $publicKey = 'PK_401ccdd16900a23e548ad5daa2a7d0fe64b1f391e79';
    public $secretKey = 'SK_9378c0ade0b32509a9597369178813a26e57bf15e50';
    public $edsa_api_key = "nQerpyjf7gVAAiJS4XoVnlMi7lGSJktDZdVWGpzkMzo=";
    

    protected function getUserSavedRechargeCards()
    {
        return request()->user()->saveddstvrechargecards();
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $saveddstvrechargecards = SavedDstvRechargeCard::where('user_id',$user->id)->get();
        return $this->success('', [
            'saveddstvrechargecards' => $saveddstvrechargecards
        ]);
    }

    public function create(Request $request)
    {
        $saveddstvrechargecard = new SavedDstvRechargeCard();

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
        $saveddstvrechargecard = SavedDstvRechargeCard::find($id);
        $saveddstvrechargecard->delete();

        return $this->success("Success", [
        ]);
    }
    public function dstv_recharge(Request $request) {

        // return $request;
        // Initialize cURL for the first request
        $ch = curl_init();

        // Set the URL and other options for the first request
        curl_setopt($ch, CURLOPT_URL, 'https://sandbox.vtpass.com/api/service-variations?serviceID=dstv');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request and fetch the response
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
            curl_close($ch); // Close the cURL resource
            return; // Exit the function
        }

        // Decode JSON response
        $data = json_decode($response, true);

        // Check if 'content' and 'serviceID' exist in the response
        if (isset($data['content']['serviceID']) && $data['content']['serviceID'] == "dstv") {
            // Initialize cURL for the merchant verification request
            curl_setopt($ch, CURLOPT_URL, 'https://sandbox.vtpass.com/api/merchant-verify');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'billersCode' => $request->billersCode,
                'serviceID' => $request->serviceID
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "api-key: $this->apiKey",
                "public-key: $this->publicKey",
                "secret-key: $this->secretKey"
            ]);
            // Execute the request and fetch the response
            $response = curl_exec($ch);

            // Check for errors
            if (curl_errno($ch)) {
                echo 'cURL error: ' . curl_error($ch);
                curl_close($ch); // Close the cURL resource
                return; // Exit the function
            }

            // Decode JSON response
            $data = json_decode($response, true);
            $bouqet_name = $data['content']['Current_Bouquet'];
            $due_date = $data['content']['Due_Date'];
            // Check if the verification was successful
            if (($data['content']['Status']) && $data['content']['Status'] == "OPEN") {
                // Initialize cURL for the payment request
                  // Get current date and time
                 // Create a DateTime object for the current time
                    $dateTime = new \DateTime();

                    // Subtract 4 hours from the current time
                    $dateTime->modify('+1 hours');

                    // Format the date and time to YYYYMMDDHHII
                    $formattedDateTime = $dateTime->format('YmdHi');

                    // Generate a random string of 4 alphabetic characters
                    $randomString = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 4);

                    // Combine formatted date-time with random string
                    $id = $formattedDateTime . $randomString;
                curl_setopt($ch, CURLOPT_URL, 'https://sandbox.vtpass.com/api/pay');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                    'request_id' => $id,
                    'serviceID' => $request->serviceID,
                    'billersCode' => $request->billersCode,
                    'amount' => $request->amount,
                    'phone' => $request->phone,
                    'variation_code' => $request->variation_code,
                    'subscription_type' => $request->subscription_type,
                    'quantity' => $request->quantity
                ]));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "api-key: $this->apiKey",
                    "public-key: $this->publicKey",
                    "secret-key: $this->secretKey"
                ]);

                // Execute the request and fetch the response
                $response2 = curl_exec($ch);

                // Check for errors
                if (curl_errno($ch)) {
                    echo 'cURL error: ' . curl_error($ch);
                } else {
                    // Decode JSON response
                   $response2 = json_decode($response2, true);
                   $trans_id = $response2['content']['transactions']['transactionId'];
                   $convinience_fee = $response2['content']['transactions']['convinience_fee'];
                    $dstv_transaction = new DstvTransaction();
                    $dstv_transaction->user_id = Auth::user()->id;
                    $dstv_transaction->transaction_id = $trans_id;
                    $dstv_transaction->transaction_status = $response2['response_description'];
                    $dstv_transaction->amount = $request->amount;
                    $dstv_transaction->subscription_type = $request->subscription_type;
                    $dstv_transaction->response = json_encode($response2);
                    $dstv_transaction->phone = $request->phone;
                    $dstv_transaction->variation_code = $request->variation_code;
                    $dstv_transaction->quantity = $request->quantity;
                    $dstv_transaction->email = $request->email;
                    $dstv_transaction->bouquet_name = $bouqet_name;
                    $dstv_transaction->due_date = $due_date;
                    $dstv_transaction->smartcard_number = $request->billersCode;
                    $dstv_transaction->gateway_charge = 0;
                    $dstv_transaction->convinience_fee = $convinience_fee;
                    
                    // return $dstv_transaction;
                    $dstv_transaction->save();

                    // try {
                    //     $accountSid = getenv("TWILIO_SID");
                    //         $authToken = getenv("TWILIO_TOKEN");
                    //         $twilioNumber = getenv("TWILIO_FROM");
                    //         $twilioAL = getenv("TWILIO_FROM_AL");


                    //         $client = new Client($accountSid, $authToken);

                    //         $user_mobile = $request->phone;
                          
                    //         $message = "Your transaction has been successfully Transaction Id ". $trans_id;

                    //         if(substr($user_mobile, 0, 3) === "+91"){
                    //             $client->messages->create($user_mobile, [
                    //                 'from' => $twilioNumber,
                    //                 'body' => $message
                    //             ]);
                    //         } else {
                    //             $client->messages->create($user_mobile, [
                    //                 'from' => $twilioAL,
                    //                 'body' => $message
                    //             ]);
                    //         }
                    // } catch (\Throwable $e) {
                    //     return $e->getMessage();
                    // }

                    $transaction = $dstv_transaction;

                    $pdf = PDF::loadView('admin.dstvtransaction.pdf', compact('transaction'));
                    $pdf->save(public_path('dstvtransaction.pdf'));


                    $pdfPath = public_path('dstvtransaction.pdf');
                    // return $request->email;
                    
                  $emailResponse = sendCustomEmail($request->email, 'PDF Email Subject', 'Here is your PDF', $pdfPath);

                    return $this->success('success', [
                        'data' => $dstv_transaction
                    ]);
                    // return $this->success("Success", $response2);
                }
            }

            // Close the cURL resource for the first request
            curl_close($ch);
        } else {
            // Handle the case where 'serviceID' is not 'dstv'
            echo 'Service ID is not dstv';
            curl_close($ch); // Close the cURL resource
        }
    }

     public function star_recharge(Request $request) {
        // return $request;
        // Initialize cURL for the first request
        $ch = curl_init();

        // Set the URL and other options for the first request
        curl_setopt($ch, CURLOPT_URL, 'https://sandbox.vtpass.com/api/service-variations?serviceID=startimes');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request and fetch the response
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
            curl_close($ch); // Close the cURL resource
            return; // Exit the function
        }

        // Decode JSON response
        $data = json_decode($response, true);

        // Check if 'content' and 'serviceID' exist in the response
        if (isset($data['content']['serviceID']) && $data['content']['serviceID'] == "startimes") {
            // Initialize cURL for the merchant verification request
            curl_setopt($ch, CURLOPT_URL, 'https://sandbox.vtpass.com/api/merchant-verify');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'billersCode' => $request->billersCode,
                'serviceID' => $request->serviceID
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "api-key: $this->apiKey",
                "public-key: $this->publicKey",
                "secret-key: $this->secretKey"
            ]);

            // Execute the request and fetch the response
            $response = curl_exec($ch);

            // Check for errors
            if (curl_errno($ch)) {
                echo 'cURL error: ' . curl_error($ch);
                curl_close($ch); // Close the cURL resource
                return; // Exit the function
            }

            // Decode JSON response
            $data = json_decode($response, true);
            $smartcard_number = $data['content']['Smartcard_Number'];
            // $due_date = $data['content']['Due_Date'];
            // Check if the verification was successful
            // if (($data['content']['Status']) && $data['content']['Status'] == "OPEN") {
                // Initialize cURL for the payment request
                  // Get current date and time
                 // Create a DateTime object for the current time
                    $dateTime = new \DateTime();

                    // Subtract 4 hours from the current time
                    $dateTime->modify('+1 hours');

                    // Format the date and time to YYYYMMDDHHII
                    $formattedDateTime = $dateTime->format('YmdHi');

                    // Generate a random string of 4 alphabetic characters
                    $randomString = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 4);

                    // Combine formatted date-time with random string
                    $id = $formattedDateTime . $randomString;
                curl_setopt($ch, CURLOPT_URL, 'https://sandbox.vtpass.com/api/pay');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                    'request_id' => $id,
                    'serviceID' => $request->serviceID,
                    'billersCode' => $request->billersCode,
                    'amount' => $request->amount,
                    'phone' => $request->phone,
                    'variation_code' => $request->variation_code
                ]));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "api-key: $this->apiKey",
                "public-key: $this->publicKey",
                "secret-key: $this->secretKey"
                ]);

                // Execute the request and fetch the response
                $response2 = curl_exec($ch);

                // Check for errors
                if (curl_errno($ch)) {
                    echo 'cURL error: ' . curl_error($ch);
                } else {
                    // Decode JSON response
                  return $response2 = json_decode($response2, true);
                   return $response2['content']['transactions']->transactionId;
                   $trans_id = $response2['content']['transactions']['transactionId'];
                   $convinience_fee = $response2['content']['transactions']['convinience_fee'];
                //    
                    // Process the second response here
                    $dstv_transaction = new StarTransaction();
                    $dstv_transaction->user_id = Auth::user()->id;
                    $dstv_transaction->transaction_id = $trans_id;
                    $dstv_transaction->transaction_status = $response2['response_description'];
                    $dstv_transaction->amount = $request->amount;
                    $dstv_transaction->subscription_type = $request->subscription_type;
                    $dstv_transaction->response = json_encode($response2);
                    $dstv_transaction->phone = $request->phone;
                    $dstv_transaction->variation_code = $request->variation_code;
                    // $dstv_transaction->bouquet_name = $bouqet_name;
                    // $dstv_transaction->due_date = $due_date;
                    $dstv_transaction->smartcard_number = $smartcard_number;
                    $dstv_transaction->gateway_charge = 0;
                    $dstv_transaction->convinience_fee = $convinience_fee;
                    // return $dstv_transaction;
                    $dstv_transaction->save();

                    // try {
                    //     $accountSid = getenv("TWILIO_SID");
                    //         $authToken = getenv("TWILIO_TOKEN");
                    //         $twilioNumber = getenv("TWILIO_FROM");
                    //         $twilioAL = getenv("TWILIO_FROM_AL");


                    //         $client = new Client($accountSid, $authToken);

                    //         $user_mobile = $request->phone;
                          
                    //         $message = "Your transaction has been successfully Transaction Id ". $trans_id;

                    //         if(substr($user_mobile, 0, 3) === "+91"){
                    //             $client->messages->create($user_mobile, [
                    //                 'from' => $twilioNumber,
                    //                 'body' => $message
                    //             ]);
                    //         } else {
                    //             $client->messages->create($user_mobile, [
                    //                 'from' => $twilioAL,
                    //                 'body' => $message
                    //             ]);
                    //         }
                    // } catch (\Throwable $e) {
                    //     return $e->getMessage();
                    // }

                    $transaction = $dstv_transaction;

                    $pdf = PDF::loadView('admin.dstvtransaction.pdf', compact('transaction'));
                    $pdf->save(public_path('dstvtransaction.pdf'));


                    $pdfPath = public_path('dstvtransaction.pdf');
                    // return $request->email;
                    // try{
                    //     Mail::to($request->email)->send(new SendPdfMail($transaction, $pdfPath));
                    //     Mail::to($response2['content']['transactions']['email'])->send(new SendPdfMail($transaction, $pdfPath));
                    // }catch(\Exception $e){
                    //     return $e->getMessage();
                    // }

                    // $recipients = [
                    //     $request->email,
                    //     $response2['content']['transactions']['email']
                    // ];

                  $emailResponse = sendCustomEmail($request->email, 'PDF Email Subject', 'Here is your PDF', $pdfPath);
                    return $this->success('success', [
                        'data' => $dstv_transaction
                    ]);
                    // return $this->success("Success", $response2);
                // }
            }

            // Close the cURL resource for the first request
            curl_close($ch);
        } else {
            // Handle the case where 'serviceID' is not 'dstv'
            echo 'Service ID is not dstv';
            curl_close($ch); // Close the cURL resource
        }
    }

    public function track_shipping(Request $request){
        $TrackingId = urlencode($request->shipmentNumber);

        $seller_id = $request->seller_id;
        $shipmentNumber = SeaFreightShipment::where('seller_id','=',$seller_id)->where('booking_reference_id','=',$TrackingId)->first();
        $shipmentNumber->consignee_name = $shipmentNumber->consignee_name;
        $shipmentNumber->consignee_mobile_no = $shipmentNumber->consignee_mobile_no;
        $shipmentNumber->consignor_name = $shipmentNumber->consignor_name;
        $shipmentNumber->consignor_mobile_no = $shipmentNumber->consignor_mobile_no;
        $shipmentNumber->freight_number = $shipmentNumber->freight_number;
        $shipmentNumber->freight_charges = $shipmentNumber->freight_charges;
        $shipmentNumber->region = $shipmentNumber->region;
        $shipmentNumber->seller_name = $shipmentNumber->seller_name;

        $user = User::where('id',$seller_id)->with('seller_detail')->first();
        $businessCoordinates = $user->seller_detail ? explode(",",$user->seller_detail->business_coordinates) : [];
        $shipmentNumber->business_coordinates =  $businessCoordinates;
        $shipmentNumber->referral_fee =  $user->referral_fee;
        $shipmentNumber->business_plan =  $user->business_plan;
        // Initialize a cURL session
        $curl = curl_init();
        
        // Set the cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.sinay.ai/container-tracking/api/v2/shipment?shipmentNumber={$shipmentNumber->container_batch_no}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                $this->snay_api_key,
                "Accept: application/json",
            ],
        ]);
        
        // Execute the cURL request and store the response
        $response = curl_exec($curl);

    // Check for errors
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => $error_msg], 500);
        }
    $track_shipping = new TrackshippingTransaction();
    $track_shipping->user_id = Auth::user()->id;
    $track_shipping->tracking_number = $request->shipmentNumber;
    $track_shipping->sinay_shipping_response = $response;
    $track_shipping->shipper = $request->shipper;
    // return $track_shipping;
    $track_shipping->save();
    // Close the cURL session
    curl_close($curl);

    
    return $this->success('success', [
        'data' => $track_shipping,
        'shipping_data'=>$shipmentNumber
    ]);

    }


    public function get_dstv_transaction(){
        $user_id = Auth::user()->id;
        $dstv_transaction = DstvTransaction::where('user_id',$user_id)->orderByDesc('created_at')->get();
        return $this->success('success', [
            'data' => $dstv_transaction
        ]);
    }
    public function get_star_transaction(){
        $user_id = Auth::user()->id;
        $star_transaction = StarTransaction::where('user_id',$user_id)->orderByDesc('created_at')->get();
        return $this->success('success', [
            'data' => $star_transaction
        ]);
    }
    public function get_track_shipping_transaction(){
        $user_id = Auth::user()->id;
        $track_shipping_transaction = TrackshippingTransaction::where('user_id',$user_id)->get();
        return $this->success('success', [
            'data' => $track_shipping_transaction
        ]);
    }

    public function add_air_sea_freight(Request $request){
        if ($request->delivery_bit == 0) {
            $track_shipping = new TrackshippingTransaction();
            $track_shipping->user_id = Auth::user()->id;
            $track_shipping->tracking_number = $request->shipmentNumber;
            $track_shipping->country = $request->country;
            $track_shipping->delivery_bit = $request->delivery_bit;
            // return $track_shipping;
            $track_shipping->save();
            return $this->success('success', [
                'data' => $track_shipping
            ]);
        } else {
            $shipmentNumber = urlencode($request->input('shipmentNumber'));

            // Initialize a cURL session
            $curl = curl_init();
            
            // Set the cURL options
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.sinay.ai/container-tracking/api/v2/shipment?shipmentNumber={$shipmentNumber}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    $this->snay_api_key,
                    "Accept: application/json",
                ],
            ]);
            
            // Execute the cURL request and store the response
            $response = curl_exec($curl);
    
        // Check for errors
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => $error_msg], 500);
        }
        $track_shipping = new TrackshippingTransaction();
        $track_shipping->user_id = Auth::user()->id;
        $track_shipping->tracking_number = $request->shipmentNumber;
        $track_shipping->shipper = $request->shipper;
        $track_shipping->sinay_shipping_response = $response;
        $track_shipping->country = $request->country;
        $track_shipping->delivery_bit = $request->delivery_bit;
        $track_shipping->transaction_type = $request->transaction_type;
        $track_shipping->pickup_location = $request->pickup_location;
        $track_shipping->delivery_address = $request->delivery_address;
        $track_shipping->digital_addministration_fee = $request->digital_addministration_fee;
        $track_shipping->transport_wear_fear = $request->transport_wear_fear;
        $track_shipping->fuel_fee = $request->fuel_fee;
        $track_shipping->gst_fee = $request->gst_fee;
        $track_shipping->sub_total = $request->sub_total;
        $track_shipping->total_to_pay = $request->total_to_pay;
        // return $track_shipping;
        $track_shipping->save();
        // Close the cURL session
        curl_close($curl);
    
        
        return $this->success('success', [
            'data' => $track_shipping
        ]);
        }
        
    }

    public function edsa_api(Request $request)
    {
       
        // Fetch the last transaction_id from the 'sesl.edsa_transactions' table
        $lastTransaction = \DB::table('sesl.edsa_transactions')->orderByDesc('id')->first();

        // Check if a transaction_id exists
        if (!$lastTransaction) {
            return response()->json(['error' => 'No previous transaction ID found.'], 500);
        }

        // Increment the transaction_id by 1
        $random11DigitNumber = $lastTransaction->transaction_id + 1;
    
        $payload = [
                "meterNumber" => $request->input('meterNumber'),  // Replace with actual username
                "amount" => $request->input('amount'),
                "transactionId" =>  (string)$random11DigitNumber  // Replace with actual password
        ];
        
        // Initialize a cURL session
        try {
            // Initialize cURL session
            $curl = curl_init();
        
            // Set cURL options
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://www.vendtechsl.com:459/sales/v1/buy",  // API endpoint
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,  // Make a POST request
                CURLOPT_POSTFIELDS => json_encode($payload),  // Send the payload as JSON
                CURLOPT_HTTPHEADER => [
                    "X-Api-Key: $this->edsa_api_key",
                    "Content-Type: application/json",  // Sending JSON
                    "Accept: application/json"
                ],
            ]);
        
            // Execute the cURL request and get the response
            $response = curl_exec($curl);
            // Check if cURL executed successfully
            if (curl_errno($curl)) {
               
                throw new \Exception(curl_error($curl));  // Throw an exception if there's a cURL error
            }
            // Close the cURL session
            curl_close($curl);

            // Return the response if successful
            $res_email = Auth::user()->email;
            $responseData = json_decode($response, true);

            if(isset($responseData['result'])){
           
                // Return the response data
                $edsa_transaction = new EdsaTransaction();
                $edsa_transaction->user_id = Auth::user()->id;
                $edsa_transaction->transaction_id = $responseData['result']['successResponse']['transactionId'];
                if($responseData['result']['status'] == 'success'){
                    $edsa_transaction->transaction_status = 'Successful';
                }
                $edsa_transaction->meter_number =  $responseData['result']['successResponse']['meterNumber'];
                $edsa_transaction->amount =  $responseData['result']['successResponse']['amount'];
                $edsa_transaction->balance =  $responseData['result']['successResponse']['walleBalance'];
                $edsa_transaction->edsa_tariff_category =  $responseData['result']['successResponse']['voucher']['tariff'];
                $edsa_transaction->service_charge =  $responseData['result']['successResponse']['voucher']['serviceCharge'];
                $edsa_transaction->units =  $responseData['result']['successResponse']['voucher']['units'];
                $edsa_transaction->mobile = $request->mobile_number;
                $edsa_transaction->response = json_encode($responseData);
            
                // return $dstv_transaction;
                $edsa_transaction->save();
        
                // $transaction = $edsa_transaction;
        
                // $pdf = PDF::loadView('admin.edsatransaction.pdf', compact('transaction'));
                // $pdf->save(public_path('edsatransaction.pdf'));
        
        
                // $pdfPath = public_path('edsatransaction.pdf');
             
                // $email = Auth::user()->email;
                // $emailResponse = sendCustomEmail($email, 'PDF Email Subject', 'Here is your PDF', $pdfPath);

                    //  try {
                    //             $accountSid = getenv("TWILIO_SID");
                    //                 $authToken = getenv("TWILIO_TOKEN");
                    //                 $twilioNumber = getenv("TWILIO_FROM");
                    //                 $twilioAL = getenv("TWILIO_FROM_AL");
        
        
                    //                 $client = new Client($accountSid, $authToken);
        
                    //                 $user_mobile = Auth::user()->mobile_number;
                                
                    //                 $message = "Your transaction has been successfully Transaction Id ". $trans_id;
        
                    //                 if(substr($user_mobile, 0, 3) === "+91"){
                    //                     $client->messages->create($user_mobile, [
                    //                         'from' => $twilioNumber,
                    //                         'body' => $message
                    //                     ]);
                    //                 } else {
                    //                     $client->messages->create($user_mobile, [
                    //                         'from' => $twilioAL,
                    //                         'body' => $message
                    //                     ]);
                    //                 }
                    //         } catch (\Throwable $e) {
                    //              return $e->getMessage();
                    //         }
                return $this->success('success', [
                    'data' => $edsa_transaction
                ]);

            }else {

                  // Return the response data
                  $edsa_transaction = new EdsaTransaction();
                  $edsa_transaction->user_id = Auth::user()->id;
                  $edsa_transaction->transaction_id = $random11DigitNumber;
                    $edsa_transaction->transaction_status = $responseData['Status'];
                  $edsa_transaction->meter_number =  $request->input('meterNumber');
                  $edsa_transaction->amount =  $request->input('amount');
                  $edsa_transaction->balance =  'NA';
                  $edsa_transaction->edsa_tariff_category =  'NA';
                  $edsa_transaction->service_charge =  'NA';
                  $edsa_transaction->units =  'NA';
                  $edsa_transaction->mobile = $request->mobile_number;
                  $edsa_transaction->response = json_encode($responseData);
              
                  // return $dstv_transaction;
                  $edsa_transaction->save();
        
                  
                return $this->error([
                    'data' => $responseData
                ]);
               
                return response()->json($responseData);
            }
      
        } catch (\Exception $e) {
            // Handle errors
            return response()->json(['error' => 'cURL Error: ' . $e->getMessage()], 500);
        }
        
        // Decode the JSON response from the API
   
      
             
            
              
                return response()->json($edsa_transaction);
       

    }
    
    
    
    

    public function get_edsa_transaction(){
        $user_id = Auth::user()->id;
        $edsa_transaction = EdsaTransaction::where('user_id',$user_id)->orderByDesc('created_at')->get();
        return $this->success('success', [
            'data' => $edsa_transaction
        ]);
    }
    


}
