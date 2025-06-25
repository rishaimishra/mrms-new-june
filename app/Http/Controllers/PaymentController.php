<?php

namespace App\Http\Controllers;

use PayPal\Rest\ApiContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use Paypal\Exception\PayPalConnectionException;

/** All Paypal Details class **/

use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use App\Logic\SystemConfig;
use Redirect;
use Session;
use URL;

class PaymentController extends Controller
{
    private $_api_context;

    public function __construct()
    {
        /** PayPal api context **/
        $paypal_conf = \Config::get('paypal');

        $this->_api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_conf['client_id'],
            $paypal_conf['secret']
        ));
        $this->_api_context->setConfig($paypal_conf['settings']);
    }
    public function index(Request $request)
    {
        // $this->validate($request, [

        //     'amount' => 'required',

        //     'property_id' => 'required|digits_between:2,10',

        //     'payee_name' => 'required|max:250',
        //     'mobile_number' => 'required|digits_between:7,15',
        // ]);
        $optionGroup = SystemConfig::getOptionGroup(SystemConfig::COMMUNITY_GROUP);
        return view('paywithpaypal', compact('optionGroup'));
    }
    public function payWithpaypal(Request $request)
    {

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item_1 = new Item();
        $item_1->setName($request->get('property_id'))
            /** item name **/
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice($request->get('amount'));
        /** unit price **/

        $item_list = new ItemList();
        $item_list->setItems(array($item_1));

        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal($request->get('amount'));

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Your transaction description');

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::route('status'))
            /** Specify return URL **/
            ->setCancelUrl(URL::route('cancel'));
        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        /** dd($payment->create($this->_api_context));exit; **/
        try {
            $payment->create($this->_api_context);
        } catch (PayPalConnectionException $ex) {
            return response()->json(array_merge(
                ['success' => false, 'code' => $ex->getCode(), "message" => $ex->getData()]
            ));
        }
        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }

        /** add payment ID to session **/
        $payment_id = $payment->getId();

        $this->makePayment($request, $payment_id, $request->get('amount'), false);

        Session::put('paypal_payment_id', $payment_id);

        if (isset($redirect_url)) {
            /** redirect to paypal **/
            return Redirect::away($redirect_url);
        }
        \Session::put('error', 'Unknown error occurred');
        return Redirect::to('/');
    }

    protected function makePayment($request, $payment_id, $amount, $is_paid = false)
    {
        $payment = new \App\Models\Payment();

        $payment->payment_id = $payment_id;
        $payment->payment_mode = 'paypal';
        $payment->amount = $amount;
        $payment->amount_in_le = $request->amount_in_le;
        $payment->is_complete = $is_paid;
        $payment->property_id = $request->property_id;
        $payment->payee_name = $request->payee_name;
        $payment->mobile_number = $request->mobile_number;

        $payment->save();

        return $payment;
    }

    public function getPaymentStatus()
    {
        /** Get the payment ID before session clear **/
        $payment_id = Session::get('paypal_payment_id');

        /** clear the session payment ID **/
        Session::forget('paypal_payment_id');


        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId(Input::get('PayerID'));

        /**Execute the payment **/
        $result = $payment->execute($execution, $this->_api_context);
        if ($result->getState() == 'approved') {

            $pay = \App\Models\Payment::where('payment_id', $payment_id)->first();

            $pay->is_complete = true;

            $pay->save();

            $property = \App\Models\Property::with('landlord')->findOrFail($pay->property_id);

            $t_amount = intval(str_replace(',', '', $pay->amount_in_le));
            $t_penalty = 0;

            $data['payment_type'] = 'online';
            $data['transaction_id'] = $pay->id;
            $data['payee_name'] = $pay->payee_name;
            $data['assessment'] = number_format($property->assessment->getCurrentYearTotalDue(), 0, '.', '');
            $data['admin_user_id'] = 0;
            $data['total'] = $t_amount + $t_penalty;
            $data['amount'] = $t_amount;
            $propertyPayment = $property->payments()->create($data);

            $property2 = \App\Models\Property::with('landlord')->findOrFail($pay->property_id);
            $t_balance = number_format($property2->assessment->getCurrentYearTotalDue(), 0, '.', '');

            $propertyPayment->balance = $t_balance;
            $propertyPayment->transaction_id = $pay->id;
            $propertyPayment->save();
            //$this->addPayment($request, $payment->amount, $payment, $payment->credits);
            if (preg_match('^(\+)([1-9]{3})(\d{8})$^', $pay->mobile_number)) {
                $property->landlord->notify(new PaymentSMSNotification($property, $pay->mobile_number, $payment));
            }
            return view('payments.pos-receipt', compact('property', 'propertyPayment'));
        }
        return   $result;
    }

    public function cancel(Request $request)
    {
        Session::forget('paypal_payment_id');
        return response()->json(array_merge(
            ['success' => false, 'code' => "4001", "message" => 'You have either cancelled the request or your session has expired']
        ));
    }
}
