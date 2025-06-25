@extends('admin.layout.grid')

@section('grid-title')
<h2> SAVED METERS </h2>
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
                Saved Meter Listing
            </h2>
        </div>

            <!-- @php
                echo "test";
                foreach($savedmeters as $d) {
                    echo $d;
                }
            @endphp -->
            <div class="body">
                <div class="row">

                    @if ($savedmeters->count())
                        <table class="table">
                            <tbody>
                            <th>Meter Number</th>
                            <th>Username</th>
                            <th>Meter Name</th>
                            <th>Email</th>
                            <th>Mobile Number</th>
                            <th>Created At</th>
                            </tbody>
                            <tbody>
                            @foreach($savedmeters as $user)
                                <tr>
                                    <td>{{ $user->meter_number }}</td>
                                    <td><a href="{{ route('admin.user.show', $user->user_id) }}" >{{ $user->user->username }}</a></td>
                                    <td>{{ $user->meter_name }}</td>
                                    <td>{{ $user->user->email}}</td>
                                    <td>{{ $user->user->mobile_number}}</td>
                                    <td>{{ \Carbon\Carbon::parse($user->created_at)->format('Y M, d') }}</td>
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


