@extends('admin.layout.grid')

@section('grid-title')
<div style="display: flex; width: 100%; justify-content: space-between; align-items: center;">
    <h2> EDSA TRANSACTIONS </h2>
   
   

</div>
@endsection

@section('grid-content')


                    <!-- <div class="card">
                        <div class="header">
                            <h2>
                                Users Filters
                            </h2>
                        </div>
                        <div class="body">
                            {!! Form::open(['method' => 'get']) !!}
                            <div class="row">

                                <div class="col-sm-3">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Name</label>
                                            <input type="text" class="form-control" value="{{ request('name') }}" name="name">
                                        </div>
                                    </div>
                                </div>


                                <div class="col-sm-3">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Mobile Number</label>
                                            <input type="text" class="form-control" value="{{ request('mobile_number') }}" name="mobile_number">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Email</label>
                                            <input type="text" class="form-control" value="{{ request('email') }}" name="email"> -->
                                        <!-- </div>
                                    </div>
                                </div>




                                <div class="col-sm-3">
                                    <div class="form-group">
                                        {!! Form::materialSelect('Filter By', 'dwm', dwmFilter(),  request('dwm'), $errors->first('dwm')) !!}
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        {!! Form::checkbox('download', 1, request('download'), ['class' => 'filled-in chk-col-blue', 'id' => 'download']) !!}
                                        <label for="download">Download Excel</label>
                                    </div>
                                </div>



                            </div>

                            <button class="btn btn-primary waves-effect btn-lg" type="submit">Filter</button>
                            <a href="{{ route('admin.user.index') }}" class="btn-lg btn btn-default">Clear Filter</a>

                            {!! Form::close() !!}
                        </div>
                    </div> -->



                    
    <div class="card">
        <div class="header">
            <p>
                 Minimum Balance
                 
            </p>
            <form action="{{route('admin.edsatransaction.updateminbal')}}" method="post">@csrf
                <input type="text" name="minimum_balance" value="{{$transactions->first()->user->edsa_min_balance}}">
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>

            <!-- @php
                echo "test";
                foreach($transactions as $d) {
                    echo $d;
                }
            @endphp -->
            <div class="body">
                <div class="row">

                    @if ($transactions->count())
                        <table class="table">
                            <tbody>
                            <!-- <th>EDSA Token</th>
                            <th>EDSA Tariff</th>
                            <th>GST</th>
                            <th>Service Charge</th> -->
                            <th>User ID</th>
                            <th>Name</th>
                            <!-- <th>Email</th> -->
                            <th>Mobile</th>
                            <th>Transaction ID</th>
                            <th>Transaction Status</th>
                            <th>Meter Number</th>
                            <th>Tariff</th>
                            <th>Amount</th>
                            <!-- <th>Meter Reading</th> -->
                            <th>Units Cost</th>
                            <th>Service Charge</th>
                            <th>GST</th>
                            <!-- <th>Token</th> -->
                            <!-- <th>Created At</th> -->
                            <th>Created At</th>
                            <th>Reciept</th>
                            </tbody>
                            <tbody>
                            @foreach($transactions as $user)
                                
                                <tr>
				     <!-- <td><span class="badge badge-pill badge-primary">{{ $user->edsa_token }}</span></td>
                                    <td>{{ $user->edsa_tariff_category }}</td>
                                    <td>{{ $user->gst }}</td>
                                    <td>{{ $user->service_charge }}</td> -->
                                    <td>{{ $user->user_id }}</td>
                                    <td><a href="{{ route('admin.user.show', $user->user_id) }}" >{{ $user->user->username }}</a> @if($user->user->is_edsa_agent) <span class="badge badge-pill badge-primary">EDSA AGENT</span>@endif</td>
                                    <!-- <td>{{ $user->user->email}}</td> -->
                                    <td>{{ $user->user->mobile_number}}</td>
                                    <td>{{ $user->transaction_id }}</td>
                                    <td>{{ $user->transaction_status}}</td>
                                    <td>{{ $user->meter_number}}</td>
                                    <td>{{ $user->Tariff}}</td>
                                    <td>{{ $user->TransactionAmount}}</td>
                                    @if($user->CostOfUnits)
                                    <td>{{ $user->CostOfUnits }}</td>
                                    @else
                                    <td>null</td>
                                    @endif
                                    <td>{{ $user->ServiceCharge}}</td>
                                    
                                    <!-- <td>{{ $user->meter_reading}}</td> -->
                                    <td>{{ $user->TaxCharge}}</td>
                                    <!-- <td>{{ implode('-', str_split($user->token, 4)) }}</td> -->
                                    
                                    <!-- <td>{{ \Carbon\Carbon::parse($user->created_at)->format('Y M, d') }}</td> -->
                                    <td>{{ \Carbon\Carbon::parse($user->created_at)}}</td>
                                    <td>
                                        <a class="btn btn-primary" href="{{ route('admin.edsatransaction.download-pdf', $user->id) }}"><i class="material-icons">download</i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                       
                    @else
                        <div class="alert alert-info">No result found.</div>
                    @endif
                </div>

            </div>

    </div>


    </div>

@endsection


