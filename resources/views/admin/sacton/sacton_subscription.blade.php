@extends('admin.layout.edit')

@section('content')
<style>
    .bootstrap-select.btn-group .dropdown-menu{
           width:100% !important;
       }
       .bootstrap-select .bs-searchbox .form-control, .bootstrap-select .bs-actionsbox .form-control, .bootstrap-select .bs-donebutton .form-control{
           margin-left:0px !important;
       }
       .bootstrap-select .bs-searchbox:after{
           display: none;
       }
       .bootstrap-select.btn-group .dropdown-toggle .caret{
           left: 0px;
       }
       #images{
           width:100%;
       }
</style>
<?php
$subscription = [
    ''  => 'Select subscription',
    0.00 => '0.00',
    10.00 => '10.00',
    15.00 => '15.00',
    25.00 => '25.00',
    50.00 => '50.00',
    100.00 => '100.00',
    250.00 => '250.00',
    500.00 => '500.00',
    750.00 => '750.00',
    1000.00 => '1000.00',
    1500.00 => '1500.00',
    2500.00 => '2500.00',
    5000.00 => '5000.00',
];
$ref_fee = [
    ''  => 'Select percentage',
    0.00 => '0.00',
    5.00 => '5.00',
    7.50 => '7.50',
    10.00 => '10.00',
    12.50 => '12.50',
    15.00 => '15.00',
    17.50 => '17.50',
    20.00 => '20.00',
    25.00 => '25.00',
];
$users = [
    ''  => 'Select Users',
    1 => 'Bilal',
];
$role = [
    ''  => 'Select Role',
    'business_owner' => ' DSTV - Business Owner',
    'business_partner' => ' Business Partner (For Businesses)',
    'dealer' => ' Dealer',
    'agent' => ' Agent',
];
?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
        <div class="header">
            <h2>Add Subscription fee</h2>
        </div>
        <form action="{{ route('admin.sactonsubscription-store') }}" method="POST">
            @csrf
            <div class="body">
                <div class="row">
                    <div class="col-sm-6">
                        <label class="form-label">Users</label>
                        <br>
                        {!! Form::select('users',$users_seller, ['class' => 'form-control', 'id' => 'users', 'data-live-search' => 'true']) !!}
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Role</label>
                        <br>
                        {!! Form::select('role', $role, ['class' => 'form-control', 'id' => 'role', 'data-live-search' => 'true']) !!}
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Pay As You Go plan subscription fee (NLe)</label><br>
                        {!! Form::select('go_plan_monthly', $subscription, ['class' => 'form-control', 'id' => 'go_plan_monthly', 'data-live-search' => 'true']) !!}
                    
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Pay As You Go plan refferal fee (%)</label>
                        {!! Form::select('go_plan_referal_fee', $ref_fee, null, ['class' => 'form-control', 'id' => 'go_plan_referal_fee', 'data-live-search' => 'true']) !!}
                        
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">MSME plan subscription fee (NLe)</label>
                        {!! Form::select('individual_plan_monthly', $subscription, null, ['class' => 'form-control', 'id' => 'individual_plan_monthly', 'data-live-search' => 'true']) !!}
                        
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">MSME plan refferal fee (%)</label>
                        {!! Form::select('individual_plan_referal_fee', $ref_fee, null, ['class' => 'form-control', 'id' => 'individual_plan_referal_fee', 'data-live-search' => 'true']) !!}
                        
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Business plan subscription fee (NLe)</label>
                        {!! Form::select('business_plan_monthly', $subscription, null, ['class' => 'form-control', 'id' => 'business_plan_monthly', 'data-live-search' => 'true']) !!}
                    
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Business Plan refferal fee (%)</label>
                        {!! Form::select('business_plan_referal_fee', $ref_fee, null, ['class' => 'form-control', 'id' => 'business_plan_referal_fee', 'data-live-search' => 'true']) !!}
                    
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="header">
            <h2>Sacton Subscription detail</h2>
        </div>
        <div class="body">
            <div class="row">
                <table class="table ">
                    <tbody>
                        <th>User ID</th>
                        <th>Role Name</th>
                        <th>Go Plan fee</th>
                        <th>Go Plan refferal percentage</th>
                        <th>Go Plan fee</th>
                        <th>Go Plan refferal percentage</th>
                        <th>Go Plan fee</th>
                        <th>Go Plan refferal percentage</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tbody>
                    <tbody>
                        @foreach ($dstv as $item)
                            <tr>
                                <td>{{ $item->user_id }}</td>
                                <td>{{ $item->role }}</td>
                                <td>{{ $item->go_plan_monthly }}</td>
                                <td>{{ $item->go_plan_referal_fee }}</td>
                                <td>{{ $item->individual_plan_monthly }}</td>
                                <td>{{ $item->individual_plan_monthly }}</td>
                                <td>{{ $item->business_plan_monthly }}</td>
                                <td>{{ $item->business_plan_referal_fee }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td>
                                    <a href="{{ route('admin.sactonsubscription-edit', ['id' => $item->id]) }}"><i class="material-icons">edit</i></a>
                                    <a href="{{ route('admin.sactonsubscription-delete', ['id' => $item->id]) }}"><i class="material-icons">delete</i></a>
                                   </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop