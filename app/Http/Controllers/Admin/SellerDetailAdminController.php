<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Library\Grid\Grid;
use App\Models\User;
use App\Models\ProductCategory;
use App\Models\SellerDetail;
use App\Models\Notification;
use App\Models\SeaFreightShipment;
use App\Imports\FreightImport;
use App\Imports\PaymentImport;
use App\Imports\MoneyTransImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FreightExport;
use App\Exports\PaymentCollectionExport;
use App\Exports\MoneyTransferExport;
use App\Models\CollectionPayment;
use App\Models\MoneyTransfer;
use Illuminate\Support\Facades\Auth;

class SellerDetailAdminController extends Controller
{
    protected $savedmeters;

    public function index()
    {
        $sellers = SellerDetail::with('user')->get();
        
        return view('admin.seller.grid', compact('sellers'));
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
        return view('admin.seller.seller_sea_frights',compact('sea_frieghts'));
    }
    public function export_frieght_excel(Request $request){
        $sea_frieghts = SeaFreightShipment::with('deliveryBook')->get();
       return Excel::download(new FreightExport($sea_frieghts), 'sea_air_freight.xlsx');
    }

    public function notification(){
        $notification = Notification::all();
        return view('admin.seller.notifications',compact('notification'));
    }
    public function notificationDetail($id){
        $notification = Notification::where('id','=',$id)->first();

        if ($notification) {
            return response()->json([
                'success' => true,
                'message' => $notification->message, // or any data you want to return
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }
    }
    public function collectionPayments(){
        $payments = CollectionPayment::with('user')
        ->groupBy('seller_id')
        ->get();
        return view('admin.seller.collectionPayments',compact('payments'));
    }
   
    public function moneyTransfer(){
        $payments = MoneyTransfer::with('user')
        ->groupBy('seller_id')
        ->get();
        $seller_list = User::where('is_seller','=',1)->get();
        return view('admin.seller.moneyTransfer',compact('payments','seller_list'));
    }

    public function upload_payment_collection(Request $request){
        // return $request;
        $user = Auth::user();
        $request->validate([
            'payment_collection_file' => 'required|mimes:xlsx,csv',
        ]);
        $seller_id = $user->id;
        Excel::import(new PaymentImport($seller_id), $request->file('payment_collection_file'));
        return redirect()->back()->with('success', 'Data imported successfully!');
    }   
    public function upload_money_transfer(Request $request){
       
        $request->validate([
            'money_transfer_file' => 'required|mimes:xlsx,csv',
        ]);
        $seller_id = $request->seller_id;
        Excel::import(new MoneyTransImport($seller_id), $request->file('money_transfer_file'));
        return redirect()->back()->with('success', 'Data imported successfully!');
    }   

    public function export_payment_excel(Request $request){
        $payments = CollectionPayment::all();
        return Excel::download(new PaymentCollectionExport($payments), 'collection_payments.xlsx');
    }
    public function export_payment_excel_moneytransfer(Request $request){
        $payments = MoneyTransfer::all();
        return Excel::download(new MoneyTransferExport($payments), 'money_transfer.xlsx');
    }
    public function single_export_payment_excel(Request $request){
        // return $request;
        $sea_frieghts = SeaFreightShipment::where('container_batch_no',$request->container_batch_no)->get();
        return Excel::download(new FreightExport($sea_frieghts), 'single_sea_air_freight.xlsx');
    }
    public function single_export_collection_excel(Request $request){
        // return $request;
        $payments = CollectionPayment::where('seller_id',$request->seller_id)->get();
        return Excel::download(new PaymentCollectionExport($payments), 'single_collection_payments.xlsx');
    }
    public function single_export_moneytransfer_excel(Request $request){
        // return $request;
        $payments = MoneyTransfer::where('seller_id',$request->seller_id)->get();
        return Excel::download(new MoneyTransferExport($payments), 'money_transfer.xlsx');
    }
}
