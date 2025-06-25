@extends('admin.layout.main')

@section('content')
<style>
    .pending{
        color:orange;
        text-transform: capitalize;
    }
    .processing{
        color:yellow;
        text-transform: capitalize;
    }
    .delivered{
        color:green;
        text-transform: capitalize;
    }
    .decline{
        color:red;
        text-transform: capitalize;
    }
</style>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Order
                        @role('admin')
                        @if($order->order_status == "pending")
                        <a href="{{ route('admin.order.edit',[$order->id,"status"=>"processing"]) }}" title="Order Processing" class="btn btn btn-success btn-sm bt pull-right">
                            <i class="material-icons">keyboard_arrow_right</i>
                        </a>
                        <a href="{{ route('admin.order.edit',[$order->id,"status"=>"decline"]) }}" title="Order Decline" class="btn btn-warning btn-sm  bt pull-right">
                            <i class="material-icons">keyboard_arrow_right</i>
                        </a>
                        @elseif($order->order_status == "processing")
                            <a href="{{ route('admin.order.edit',[$order->id,"status"=>"delivered"]) }}" title="Order Delivered" class="btn btn-success btn-sm  bt pull-right">
                                <i class="material-icons">done</i>
                            </a>
                        @endif
                        @endrole
                    </h2>
                </div>
                <div class="body">

                    <div class="row">
                    <div class="col-lg-6">

                        <p>User : <strong>{{$order->user->name}}</strong> </p>

                    </div>

                    <div class="col-lg-6">

                        <p>Order ID : <label>{{$order->id}}</label></p>

                    </div>

                    <div class="col-lg-6">

                        <p>Order Type : <label>{{$order->order_type}}</label></p>

                    </div>

                    <div class="col-lg-6">

                        <p>Order Status : <label class="{{$order->order_status}}">{{$order->order_status}}</label></p>

                    </div>

                    {{-- <div class="col-lg-6">

                        <p>Order ID : <label>{{$order->id}}</label></p>

                    </div> --}}

                    <div class="col-lg-6">

                        <p>	Address Area : <label>{{$order->addressArea->name ?:"--"}}</label></p>

                    </div>

                    <div class="col-lg-6">

                        <p>	Address Section : <label>{{$order->addressSection->name ?:"--"}}</label> </p>

                    </div>

                    <div class="col-lg-6">

                        <p>	Address Constituency : <label>{{$order->address->constituency ?:"--"}}</label></p>

                    </div>

                    <div class="col-lg-6">

                            <p>	Address Chiefdom : <label>{{$order->addressChiefdom->name ?:"--"}}</label></p>

                    </div>

                    <div class="col-lg-6">

                                <p>	Address district : <label>{{$order->address->district ?:"--"}}</label></p>

                    </div>

                    <div class="col-lg-6">

                                <p>	Address province : <label>{{$order->address->province ?:"--"}}</label></p>

                    </div>

                    <div class="col-lg-6">

                        <p>	Digital Addresses : <label>{{$order->digital_addresses ?:"--"}}</label></p>

                    </div>

                    <div class="col-lg-6">

                        <p> Tag : <label>{{$order->address_type ?:"--"}}</label></p>

                    </div>

                    <div class="col-lg-12">
                        <table class="table">
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Sub-Total</th>
                            </tr>
                            @foreach($order->orderProduct as $orderProduct)
                                <tr>
                                    <th>{{$orderProduct->name}}</th>
                                    <th> {{$orderProduct->quantity}}</th>
                                    <th>Le {{number_format($orderProduct->price,2)}}</th>
                                    <th>Le {{number_format($orderProduct->quantity * $orderProduct->price,2)}}</th>
                                </tr>
                            @endforeach
                        </table>
                    </div>

                        <div class="col-lg-6">
                            <div class="form-group form-float">
                                &nbsp;
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group form-float">
                                <div class="col-lg-6">
                                    <strong>Total :</strong>
                                </div>
                                <div class="col-lg-6"> <label>Le {{number_format($order->sub_total,2)}}</label></div>

                            </div>
                            <div class="form-group form-float">
                                <div class="col-lg-6">
                                    <strong>Digital Administration :</strong>
                                </div>
                                <div class="col-lg-6"> <label>Le {{number_format($order->digital_administration,2)}}</label></div>

                            </div>
                            <div class="form-group form-float">
                                <div class="col-lg-6">
                                    <strong>Transport Wear & Tear :</strong>
                                </div>
                                    <div class="col-lg-6"> <label>Le {{number_format($order->transport,2)}}</label></div>

                            </div>
                            <div class="form-group form-float">
                                <div class="col-lg-6">
                                    <strong>Fuel :</strong>
                                </div>
                                    <div class="col-lg-6"> <label>Le {{number_format($order->fuel,2)}}</label></div>

                            </div>
                            <div class="form-group form-float">
                                <div class="col-lg-6">
                                    <strong>GST (15%) :</strong>
                                </div>
                                    <div class="col-lg-6"> <label>Le {{number_format($order->gst,2)}}</label> </div>

                            </div>
                            <div class="form-group form-float">
                                <div class="col-lg-6">
                                    <strong>Tip :</strong>
                                </div>
                                    <div class="col-lg-6"> <label>Le {{number_format($order->tip,2)}}</label></div>

                            </div>
                            <div class="form-group form-float">
                                <div class="col-lg-6">
                                    <strong>Grand Total :</strong>
                                </div>
                                <div class="col-lg-6"> <label>Le {{number_format($order->grand_total,2)}}</label></div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop


@push("before_head_close")
    <style type="text/css" media="screen">

        .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {
            width: 100%;
        }

        .show-tick {
            width: 100% !important;
        }
    </style>
    <link href="{{ url('admin/css/jquery.businessHours.css') }}" rel="stylesheet"/>
    <link href="{{ url('admin/css/jquery.timepicker.min.css') }}" rel="stylesheet"/>

@endpush
