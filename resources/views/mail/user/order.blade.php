@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

    <div class="body">

        <div class="row">
            <div class="col-lg-6">

                <p>User :<strong>{{$order->user->name}}</strong> </p>

            </div>

            <div class="col-lg-6">

                <p>Order ID : <label>{{$order->id}}</label></p>

            </div>

            <div class="col-lg-6">

                <p>Order Type :<label>{{$order->order_type}}</label></p>

            </div>

            <div class="col-lg-6">

                <p>Order Status :  <label>{{$order->order_status}}</label></p>

            </div>

            <div class="col-lg-6">

                <p>Order ID : <label>{{$order->id}}</label></p>

            </div>

            <div class="col-lg-6">

                <p>	Address Area : <label>{{$order->addressArea->name}}</label></p>

            </div>

            <div class="col-lg-6">

                <p>	Address Section :<label>{{$order->addressSection->name}}</label> </p>

            </div>

            <div class="col-lg-6">

                <p>	Address Constituency :<label>{{$order->address->constituency}}</label></p>

            </div>

            <div class="col-lg-6">

                <p>	Address Chiefdom : <label>{{$order->addressChiefdom->name}}</label></p>

            </div>

            <div class="col-lg-6">

                <p>	Address district : <label>{{$order->address->district}}</label></p>

            </div>

            <div class="col-lg-6">

                <p>	Address province : <label>{{$order->address->province}}</label></p>

            </div>

            <div class="col-lg-12">
                <table class="table">
                    <tr>
                        <th>Product</th>
                        <th>Qut.</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                    @foreach($order->orderProduct as $orderProduct)
                        <tr>
                            <th>{{$orderProduct->name}}</th>
                            <th>{{$orderProduct->quantity}}</th>
                            <th>{{$orderProduct->price}}</th>
                            <th>{{$orderProduct->quantity * $orderProduct->price}}</th>
                        </tr>
                    @endforeach
                </table>
            </div>

            <div class="col-lg-8">
                <div class="form-group form-float">
                    &nbsp;
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group form-float">

                    <p>	Sub Total : <label>{{$order->sub_total}}</label> </p>

                </div>
                <div class="form-group form-float">

                    <p>	Digital Administration :  <label>{{$order->digital_administration}}</label></p>

                </div>
                <div class="form-group form-float">

                    <p>	Transport : <label>{{$order->transport}}</label></p>

                </div>
                <div class="form-group form-float">

                    <p>	Fuel : <label>{{$order->fuel}}</label></p>

                </div>
                <div class="form-group form-float">

                    <p>	GST :<label>{{$order->gst}}</label> </p>

                </div>
                <div class="form-group form-float">

                    <p>	Tip : <label>{{$order->tip}}</label></p>

                </div>
                <div class="form-group form-float">

                    <p>	Grand Total :
                        <label>{{$order->grand_total}}</label></p>

                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent