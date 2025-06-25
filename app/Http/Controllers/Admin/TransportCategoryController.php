<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\TransportVehicleDetail;

class TransportCategoryController extends Controller
{
    public function VehicleIndex()
    {
        $categories = User::with('seller_detail')->where('transport_service_type','=','vehicle')->get();
        // $user = SellerDet::where('transport_service_type','=','vehicle')->get();
        return view('admin.Transport.vehicles', compact('categories'));
    }

    public function VehicleDetail($id){
        $categories = TransportVehicleDetail::where('user_id','=',$id)->first();
        $user = User::with('seller_detail')->where('id','=',$id)->first();
       
        return view('admin.Transport.vehicleDetails', compact('user','categories'));
        // dd($categories);
    }
    
    public function DeliveryIndex()
    {
        $categories = User::with('seller_detail')->where('transport_service_type','=','delivery')->get();
        // $user = SellerDet::where('transport_service_type','=','vehicle')->get();
        return view('admin.Transport.delivery', compact('categories'));
    }

    public function DeliveryDetail($id){
        $categories = TransportVehicleDetail::where('user_id','=',$id)->first();
        $user = User::with('seller_detail')->where('id','=',$id)->first();
       
        return view('admin.Transport.deliveryDetails', compact('user','categories'));
        // dd($categories);
    }
}
