<?php

namespace App\Http\Controllers\Api;

use App\Mail\OrderShipped;
use App\Models\DigitalAddress;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


use Twilio\Rest\Client; 


class OrderController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected function getUserOrder()
    {

        return request()->user()->orders()->with('address', 'addressArea', 'addressChiefdom', 'addressSection', 'orderProduct')->paginate();
    }

    public function index()
    {
        return $this->success('', [
            'order' => $this->getUserOrder()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    protected function validator($request, $isUpdate = false)
    {
        return validator($request->all(), [

            'digital_address_id' => 'required|exists:digital_addresses,id',

        ]);
    }

    public function placeOrder(Request $request)
    {

        //$this->validator($request)->validate();

        $order = new Order();
        $user = request()->user();

        $cartProducts = $user->cart->products()->get();

        if (count($cartProducts) < 1) {

            return $this->genericError('User Cart Is Empty');
        }
        /*if($user->cart->digital_address_id){

            return $this->genericError('User Cart Is Empty');

        }*/
        $digitalAddreaa = DigitalAddress::find($user->cart->digital_address_id);
        $order->order_type = "Pay on Delivery";
        $order->transaction_id = "";
        $order->order_status = "pending";
        $order->digital_addresses = $digitalAddreaa->digital_addresses;
        $order->address_type = $digitalAddreaa->type;
        $order->latitude = $digitalAddreaa->latitude;
        $order->longitude = $digitalAddreaa->longitude;

        $order->digital_administration = $user->cart->digital_administration;
        $order->transport = $user->cart->transport;
        $order->fuel = $user->cart->fuel;
        $order->gst = $user->cart->gst;
        $order->tip = $user->cart->tip;
        $order->sub_total = $user->cart->sub_total;
        $order->grand_total = $user->cart->total;

        $order->address()->associate($digitalAddreaa->address_id);
        $order->addressArea()->associate($digitalAddreaa->address_area_id);
        $order->addressChiefdom()->associate($digitalAddreaa->address_chiefdom_id);
        $order->addressSection()->associate($digitalAddreaa->address_section_id);

        $request->user()->orders()->save($order);


        foreach ($cartProducts as $cartProduct) {


            $basket = Product::find($cartProduct->id);

            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->id;
            $orderProduct->name = $basket->name;
            $orderProduct->quantity = $cartProduct->pivot->quantity;
            $orderProduct->price = $basket->price;
            $orderProduct->save();

            // $basket->quantity = $basket->quantity - $cartProduct->pivot->quantity;
            $basket->save();
            $user->cart->products()->where(['product_id' => $cartProduct->id])->detach();
        }
        /* Order Send to User */
        // $user->notify(new ProductOrder($order));

        /* Order Send to Admin */
        //$admin = AdminUser::where('username', 'admin')->first();
        //$admin->notify(new ProductOrderAdmin($order));


        // Mail::to($request->user())->send(new OrderShipped($order));


            $accountSid = getenv("TWILIO_SID");
            $authToken = getenv("TWILIO_TOKEN");
            $twilioNumber = getenv("TWILIO_FROM");
            $twilioAL = getenv("TWILIO_FROM_AL");

    
            $client = new Client($accountSid, $authToken);
            
            $user_mobile = $user->mobile_number;
            // $message = "Order successfully placed, order ID: ". $order->id;

            $message = "Dear ".$user->username.", Your order has been successfully placed Order Id ". $order->id;
            
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
        Mail::to($request->user())->send(new OrderShipped($order));
            
        return $this->success('Order successfully placed.', [
            'order' => $this->getUserOrder()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
