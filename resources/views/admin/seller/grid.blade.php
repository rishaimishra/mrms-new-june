@extends('admin.layout.grid')

@section('grid-title')
<h2> Seller Details </h2>
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
            <h2>
                Sellers Listing
            </h2>
        </div>

           
            <div class="body">
                <div class="row">
                    
                    @if ($sellers->count())
                        <table class="table">
                            <tbody>
                            <th>Business Name</th>
                            <th>Registrant Name</th>
                            <th>Username</th>
                            <th>Subscription Type</th>
                            <th>Subscription Category</th>
                            <!-- <th>UserType<th> -->
                            <th>Subscription Plan</th>
                            
                           
                            <th>Registered On</th>
                            <th>Verify</th>
                            </tbody>
                            <tbody>
                            @foreach($sellers as $user)
                                <tr>
                                <td>{{ isset($user) ? $user->business_name : 'N/A' }}</td>
                                  
                                    <td>{{ isset($user->user) ? $user->user->name : 'N/A' }}</td>
                                    <td><a href="{{ route('admin.user.show', $user->user_id) }}" >{{ $user->user->username ?? "N/A" }}</a></td>
                                   
                                    <td>{{ isset($user->user) ? $user->user->user_type : 'N/A' }}</td>
                                    <td>{{ isset($user->store_category_name) ? $user->store_category_name : $user->user->user_type}}</td>
                                    <td>
                                       {{isset($user->plan_title) ? $user->plan_title : 'N/A'}}
                                    </td>
                                   
                                    <td>{{ \Carbon\Carbon::parse($user->created_at)->format('Y M, d') }}</td>
                                    <td>
                                        @if($user->is_verified == "1")
                                            <!-- <td class="btn-warning text-center">{{ $user->transaction_status}}</td> -->
                                            <button type="button" class="btn btn-success">Verified</button>
                                        @endif
                                        @if($user->is_verified == "0")
                                            <!-- <td class="btn-success text-center">{{ $user->transaction_status}}</td> -->
                                            <a href="{{ route('admin.seller.verify', $user->user_id) }}" ><button type="button" class="btn btn-warning">Verify</button></a>
                                        @endif
                                        
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


