@extends('admin.layout.main')

@section('content')
<style>
    .pending{
        color:orange;
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
        <div class="col-sm-12">
            <div class="card">
                <div class="header">
                    <h2>
                        Order Filters
                    </h2>
                </div>
                <div class="body">
                    {!! Form::open(['method' => 'get']) !!}
                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>User Name</label>
                                    <input type="text" class="form-control" value="{{ request('user') }}" name="user">
                                </div>
                            </div>
                        </div>


                       {{-- <div class="col-sm-3">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Order Id</label>
                                    <input type="text" class="form-control" value="{{ request('order_id') }}" name="order_id">
                                </div>
                            </div>
                        </div>--}}

                        <div class="col-sm-3">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Status</label>
                                    <input type="text" class="form-control" value="{{ request('status') }}" name="status">
                                </div>
                            </div>
                        </div>




                        {{--<div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::materialSelect('Year', 'year', yearFilter(),  request('year'), $errors->first('year')) !!}
                            </div>
                        </div>--}}
                        {{--<div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::materialSelect('Filter By', 'dwm', dwmFilter(),  request('dwm'), $errors->first('dwm')) !!}
                            </div>
                        </div>--}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::checkbox('download', 1, request('download'), ['class' => 'filled-in chk-col-blue', 'id' => 'download']) !!}
                                <label for="download">Download Excel</label>
                            </div>
                        </div>



                    </div>

                    <button class="btn btn-primary waves-effect btn-lg" type="submit">Filter</button>
                    <a href="{{ route('admin.order-report.index') }}" class="btn-lg btn btn-default">Clear Filter</a>

                    {!! Form::close() !!}
                </div>
            </div>



            <div class="card">
                <div class="header">
                    <h2>
                        Order Listing
                    </h2>
                </div>


                <div class="body">
                    <div class="row">

                        @if ($orders->count())
                            <table class="table">
                                <tbody>
                                <th>User</th>
                                <th>Order ID</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Created At</th>
                                </tbody>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td><a href="{{ route('admin.user.show', $order->user_id) }}" target="_blank">{{ $order->user->name }}</a></td>
                                        <td><a href="{{ route('admin.digitl-address.show', $order->id) }}" >{{ $order->id }}</a></td>

                                        <td style="text-transform: capitalize" class="{{ $order->order_status }}">{{ $order->order_status }}</td>
                                        <td>{{ number_format($order->grand_total, 2) }}</td>

                                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('Y M, d') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            {!! $orders->links() !!}
                        @else
                            <div class="alert alert-info">No result found.</div>
                        @endif
                    </div>

                </div>


            </div>


        </div>

    </div>
@endsection

@push('scripts')

@endpush
