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
    @if(Auth::user()->user_type == 'shop')
    @isset($productCategory->id)
        {!! Form::model($productCategory, ['files' => true, 'route' => ['admin.seller-product-category.update', $productCategory->id],'method' => 'PATCH']) !!}
    @else
        {!!Form::open(['files' => true, 'route' => 'admin.seller-product-category.store']) !!}
    @endisset
    @endif
    @isset($productCategory->id)
        {!! Form::model($productCategory, ['files' => true, 'route' => ['admin.product-category.update', $productCategory->id],'method' => 'PATCH']) !!}
    @else
        {!!Form::open(['files' => true, 'route' => 'admin.product-category.store']) !!}
    @endisset

    
    <div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                    <h2>Add / Edit Product Category</h2>
            </div>
            <div class="body">
                    <div class="row">
                        <div class="col-sm-12">
                            {!! Form::materialText('Name', 'name') !!}
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label">Parent Category</label>
                            {!! Form::select('parent_id', $mainCategories, old('parent_id',optional($productCategory)->parent_id), ['class' => 'form-control', 'id' => 'parent','data-live-search'=>'true']) !!}
                            {!! $errors->first('parent', '<p class="error">:message</p>') !!}
                        </div>
                        <div class="col-sm-12">
                            {!! Form::materialText('Sponsor Text', 'sponsor_text') !!}
                        </div>
                        <div class="col-sm-12">
                            {!! Form::materialText('Sequence', 'sequence') !!}
                        </div>
                       
                    </div>
            </div>
        </div>
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
$selectedValue = (float) old('go_plan_monthly', $productCategory->go_plan_monthly ?? '0.00');
$selectedValue_ind = (float) old('individual_plan_monthly', $productCategory->individual_plan_monthly ?? '0.00');
$selectedValue_bus = (float) old('business_plan_monthly', $productCategory->business_plan_monthly ?? '0.00');
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
$selectedper = (float) old('go_plan_referal_fee', $productCategory->go_plan_referal_fee ?? '0.00');
$selectedper_ind = (float) old('individual_plan_referal_fee', $productCategory->individual_plan_referal_fee ?? '0.00');
$selectedper_bus = (float) old('business_plan_referal_fee', $productCategory->business_plan_referal_fee ?? '0.00');
?>
        @if (!$productCategory)
            <div class="card">
                <div class="header">
                        <h2>Subscription and Referal fees</h2>
                </div>
                <div class="body">
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="form-label">Pay As You Go plan subscription fee (NLe)</label>
                                {!! Form::select('go_plan_monthly', $subscription, old('go_plan_monthly', $productCategory->go_plan_monthly ?? ''), ['class' => 'form-control', 'id' => 'go_plan_monthly', 'data-live-search' => 'true']) !!}
                                {{--  {!! Form::materialText('Pay As You Go plan subscription fee (NLe)', 'go_plan_monthly') !!}  --}}
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Pay As You Go plan refferal fee (%)</label>
                                {!! Form::select('go_plan_referal_fee', $ref_fee, null, ['class' => 'form-control', 'id' => 'go_plan_referal_fee', 'data-live-search' => 'true']) !!}
                                {{--  {!! Form::materialText('Pay As You Go plan refferal fee (%)', 'go_plan_referal_fee') !!}  --}}
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">MSME plan subscription fee (NLe)</label>
                                {!! Form::select('individual_plan_monthly', $subscription, null, ['class' => 'form-control', 'id' => 'individual_plan_monthly', 'data-live-search' => 'true']) !!}
                                {{--  {!! Form::materialText('MSME plan subscription fee (NLe)', 'individual_plan_monthly') !!}  --}}
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">MSME plan refferal fee (%)</label>
                                {!! Form::select('individual_plan_referal_fee', $ref_fee, null, ['class' => 'form-control', 'id' => 'individual_plan_referal_fee', 'data-live-search' => 'true']) !!}
                                {{--  {!! Form::materialText('MSME plan refferal fee (%)', 'individual_plan_referal_fee') !!}  --}}
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Business plan subscription fee (NLe)</label>
                                {!! Form::select('business_plan_monthly', $subscription, null, ['class' => 'form-control', 'id' => 'business_plan_monthly', 'data-live-search' => 'true']) !!}
                                {{--  {!! Form::materialText('Business plan subscription fee (NLe)', 'business_plan_monthly') !!}  --}}
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Business Plan refferal fee (%)</label>
                                {!! Form::select('business_plan_referal_fee', $ref_fee, null, ['class' => 'form-control', 'id' => 'business_plan_referal_fee', 'data-live-search' => 'true']) !!}
                                {{--  {!! Form::materialText('Business Plan refferal fee (%)', 'business_plan_referal_fee') !!}  --}}
                            </div>
                        </div>
                </div>
            </div>
        @endif
        @if ($productCategory->parent_id == null)
            <div class="card">
                <div class="header">
                        <h2>Subscription and Referal fees</h2>
                </div>
                <div class="body">
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="form-label">Pay As You Go plan subscription fee (NLe)</label>
                                {!! Form::select('go_plan_monthly', $subscription, $selectedValue, ['class' => 'form-control', 'id' => 'go_plan_monthly', 'data-live-search' => 'true']) !!}
                                 </div>
                            <div class="col-sm-6">
                                <label class="form-label">Pay As You Go plan refferal fee (%)</label>
                                {!! Form::select('go_plan_referal_fee', $ref_fee, $selectedper, ['class' => 'form-control', 'id' => 'go_plan_referal_fee', 'data-live-search' => 'true']) !!}
                         </div>
                            <div class="col-sm-6">
                                <label class="form-label">MSME plan subscription fee (NLe)</label>
                                {!! Form::select('individual_plan_monthly', $subscription, $selectedValue_ind, ['class' => 'form-control', 'id' => 'individual_plan_monthly', 'data-live-search' => 'true']) !!}
                               
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">MSME plan refferal fee (%)</label>
                                {!! Form::select('individual_plan_referal_fee', $ref_fee, $selectedper_ind, ['class' => 'form-control', 'id' => 'individual_plan_referal_fee', 'data-live-search' => 'true']) !!}
                                         </div>
                            <div class="col-sm-6">
                                <label class="form-label">Business plan subscription fee (NLe)</label>
                                {!! Form::select('business_plan_monthly', $subscription, $selectedValue_bus, ['class' => 'form-control', 'id' => 'business_plan_monthly', 'data-live-search' => 'true']) !!}
                                                 </div>
                            <div class="col-sm-6">
                                <label class="form-label">Business Plan refferal fee (%)</label>
                                {!! Form::select('business_plan_referal_fee', $ref_fee, $selectedper_bus, ['class' => 'form-control', 'id' => 'business_plan_referal_fee', 'data-live-search' => 'true']) !!}
                                     </div>
                        </div>
                </div>
            </div>
        @endif
    </div>
    <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="body">
                <div class="custom-control custom-checkbox">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="custom-control-input filled-in"
                           id="customCheck1" {{old('is_active',optional($productCategory)->is_active)==1?'checked':''}} >
                    <label class="custom-control-label" for="customCheck1">Active </label>

                </div>
                <button class="btn btn-primary waves-effect" type="submit">Save</button>
                @isset($productCategory->id)
                    {{ Form::button('Delete', ['type' => 'button', 'class' => 'btn btn-warning btn-sm delete'] )  }}
                @endisset
            </div>
        </div>
        <div class="card">
            <div class="body">
                {!! Form::materialFile('Image', 'images', $errors->first('images')) !!}
                @if(isset($productCategory->image) && ($productCategory->image))
                    <img  src="{{ asset('storage/' . $productCategory->image) }}" alt=""
                          class="img-responsive" style=" margin: 10px auto; "/>

                @endif
            </div>
        </div>

        <div class="card">
            <div class="body">
                {!! Form::materialFile('Background image', 'background_images', $errors->first('images')) !!}
                @if(isset($productCategory->image) && ($productCategory->image))
                    <img  src="{{ asset('storage/' . $productCategory->background_image) }}" alt=""
                          class="img-responsive" style=" margin: 10px auto; "/>

                @endif
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    @isset($productCategory->id)
        {!! Form::open(['route' => ['admin.product-category.destroy', $productCategory->id], 'method' => 'DELETE','class'=>'delete','id'=>'deleteForm']) !!}

        {!! Form::close() !!}
    @endisset
    <script>
        $(document).ready(function(){

            $(".delete").click(function(){
                if(confirm("Are you sure?")){
                    $("#deleteForm").submit(); // Submit the form
                }

            });
        });

    </script>
@stop
