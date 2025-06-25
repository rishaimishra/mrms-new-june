@extends('admin.layout.grid')

@section('grid-title')
<h2> Delivery Listing </h2>
@endsection

@section('grid-content')
<div class="card">
        <!-- <div class="header">
            <h2>
                Sellers Listing
            </h2>
        </div> -->
           
            <div class="body">
                <div class="row">
                    
                    @if ($categories->count())
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
                            @foreach($categories as $user)
                                <tr>
                                    <td>{{ isset($user) ? $user->seller_detail->business_name : 'N/A' }}</td>
                                    <td>{{ isset($user) ? $user->name : 'N/A' }}</td>
                                    <td><a href="{{ route('admin.vehicle.show', $user->id) }}">{{ isset($user) ? $user->username : 'N/A' }}</a></td>

                                    <td>{{ isset($user->user) ? $user->user->user_type : 'N/A' }}</td>
                                   
                                    <td>
                                       {{isset($user->plan_title) ? $user->plan_title : 'N/A'}}
                                    </td>
                                    <td>
                                       {{isset($user->plan_title) ? $user->plan_title : 'N/A'}}
                                    </td>
                                   
                                    <td>{{ \Carbon\Carbon::parse($user->created_at)->format('Y M, d') }}</td>
                                    <td>
                                        @if($user->seller_detail->is_verified == "1")
                                            <!-- <td class="btn-warning text-center">{{ $user->transaction_status}}</td> -->
                                            <button type="button" class="btn btn-success">Verified</button>
                                        @endif
                                        @if($user->seller_detail->is_verified == "0")
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
@endsection