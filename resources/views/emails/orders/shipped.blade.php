@component('mail::message')
Hi {{ $order->user->name }}
<div>Your order number is {{ $order->id }}</div>
<div>We would like to thank you for your purchase today.</div>

@component('mail::table')
| Product       | Qut.          |  Price   |   Total  |
| ------------- |:-------------:| --------:| --------:|
@foreach($order->orderProduct as $orderProduct)
| {{$orderProduct->name}}      | {{$orderProduct->quantity}}      | {{$orderProduct->price}}      | {{$orderProduct->quantity * $orderProduct->price}} |
@endforeach
|               |               | Sub Total | {{$order->sub_total}}  |
|               |               |  Digital Administration  |  {{$order->digital_administration}}  |
|               |               | Transport | {{$order->transport}} |
|               |               | Fuel | {{$order->fuel}} |
|               |               | GST  | {{$order->gst}} |
|               |               |  Tip  | {{$order->tip}} |
|               |               |  Grand Total  | {{$order->grand_total}} |
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
