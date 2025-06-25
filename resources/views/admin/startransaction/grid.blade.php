@extends('admin.layout.grid')

@section('grid-title')
<h2> START TIMES TRANSACTIONS </h2>
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



                    
    <div class="card" style="box-shadow: none;">
        <div class="header">
            <h2>
                STAR TIMES Transactions Listing
            </h2>
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
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Mobile</th>
                                <th>Transaction ID</th>
                                <th>Transaction Status</th>
                                <th>Bouquet Name</th>
                                <th>Service Action</th>
                                <th>Amount</th>
                                <!-- <th>GST</th> -->
                                <!-- <th>Created At</th> -->
                                <th>Created At</th>
                                <th>Reciept</th>
                            </tbody>
                            <tbody>
                            @foreach($transactions as $user)
                                
                            <tr>
                                <td>{{ $user->user_id }}</td>
                                <td><a href="{{ route('admin.user.show', $user->user_id) }}" >{{ $user->user->username }}</a> @if($user->user->is_edsa_agent) <span class="badge badge-pill badge-primary">EDSA AGENT</span>@endif</td>
                                <td>{{ $user->user->username}}</td>
                                <td>{{ $user->phone}}</td>
                                <td>{{ $user->transaction_id }}</td>
                                @if ($user->transaction_status)
                                    <td>Success</td>
                                @endif
                                <td>{{ $user->bouquet_name }}</td>
                                <td>{{ $user->subscription_type }}</td>
                                <td>{{ $user->amount }}</td>
                                <!-- <td>GST Fee</td> -->
                                <!-- <td>{{ \Carbon\Carbon::parse($user->created_at)->format('Y M, d') }}</td> -->
                                <td>{{ \Carbon\Carbon::parse($user->created_at) }}</td>
                                <td>
                                    <a class="btn btn-primary" href="{{ route('admin.startransaction.download-pdf', $user->id) }}"><i class="material-icons">download</i></a>
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


