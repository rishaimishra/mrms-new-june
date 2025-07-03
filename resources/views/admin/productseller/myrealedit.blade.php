@extends('admin.layout.main')

@section('content')

    @include('admin.layout.partial.alert')


    @isset($auto->id)
        {!! Form::model($auto, ['files' => true, 'route' => ['admin.update-property-seller', $auto->id],'method' => 'POST']) !!}
    @else
        {!!Form::open(['route' => 'admin.real-estate.store','files' => true]) !!}
    @endisset

    <div class="row">
        <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <input type="hidden" name="auto_id" value="{{$auto->id}}">
                    <h2>
                        {{ __("Add / Edit Real Estate") }}
                        @isset($auto->id)
                            <div class="pull-right">
                                {{ Form::button('Delete', ['type' => 'button', 'class' => 'btn btn-warning btn-sm delete'] )  }}
                            </div>
                        @endisset
                    </h2>
                </div>
                <div class="body">

                    <div class="col-md-6 multi-img-upload">

                        <label for="">Photos</label>


                        <div class="padding-img">
                            <a href="javascript: return false;" class="add" id="hidden-files">
                                <i class="material-icons">camera_enhance</i>
                            </a>
                        </div>


                        <input type="file" name="images[]" style="display: none;" id="hidden-images" multiple>
                        <span>[Select multiple images using 'Ctrl']</span>
                        {!! $errors->first('images', '<p class="error">:message</p>') !!}
                        {!! $errors->first('images.*', '<p class="error">:message</p>') !!}
                    </div>


                    @if($auto->images)
                        <div class="row">

                            @foreach($auto->images as $image)
                                <div class="col-lg-3">
                                    <div class="padding-img" style="width: 50%" id="img{{$image->id}}">
                                        <img class="img-thumbnail" src="{{ asset('storage/' . $image->image) }}" alt="">
                                        <span class="imgs text-danger">
                                        <a href="javascript:imgdelete({{$image->id}})" class="text-danger">
                                            Delete
                                        </a>
                                    </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    @endif
                    <div class="col-md-6">

                        <label for="">Background Image</label>


                        <div class="padding-img">
                            <a href="javascript: return false;" class="add" id="hidden-background-files">
                                <i class="material-icons">camera_enhance</i>
                            </a>
                        </div>


                        <input type="file" name="background_image" style="display: none;" id="hidden-background-images">
                        {!! $errors->first('images', '<p class="error">:message</p>') !!}
                        {!! $errors->first('images.*', '<p class="error">:message</p>') !!}
                    </div>

                    @if($auto->background_image)
                        <div class="row">
                                <div class="col-lg-3">
                                    <div class="padding-img" style="width: 50%" id="img{{$auto->id}}">
                                        <img class="img-thumbnail" src="{{ asset('product_background_images/' . $auto->background_image) }}" alt="">
                                        <span class="imgs text-danger">
                                        <a href="javascript:imgbgdelete({{$auto->id}})" class="text-danger">
                                            Delete
                                        </a>
                                    </span>
                                    </div>
                                </div>
                        </div>
                        @endif
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Auto Name</label>
                                    <input type="text" class="form-control"
                                           value="{{ old('name',optional($auto)->name) }}"
                                           name="name" required>

                                    @if ($errors->has('name'))
                                        <label class="error">{{ $errors->first('name') }}</label>
                                    @endif
                                </div>
                            </div>

                            @if(! isset($auto->id) || $attributeGroups === false)
                                {!! Form::materialSelect('Attribute Set', 'attribute_set_id', $attributeSets) !!}
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-line">
                            <label for="">Title</label>
                            {!! Form::text('title', old('title', optional($auto)->title), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    {!! $errors->first('title', '<p class="error">:message</p>'); !!}

                    <div class="form-group">
                        <div class="form-line">
                            <label for="">About</label>
                            {!! Form::textarea('about', old('about', $auto->about), ['class' => 'form-control', 'rows' => 5,'id'=>'about']) !!}
                        </div>
                    </div>

                    {!! $errors->first('about', '<p class="error">:message</p>'); !!}


                    <div class="form-group form-float" style="display:none">
                        <div class="form-line">
                            <label>Area</label>

                            <input type="text" class="form-control"
                                   value="{{old('area',$auto->addressArea->name)}}" id="area" name="area" required
                                   autocomplete="false">

                            <input type="hidden" name="address_area_id" id="address_area_id"
                                   value="{{old('address_area_id',$auto->area_id)}}">

                            <input type="hidden" name="address_id" id="address_id"
                                   value="{{old('address_id',$auto->address_id)}}">

                        </div>
                        @if ($errors->has('name'))
                            <label class="error">{{ $errors->first('name') }}</label>
                        @endif

                    </div>

                    <div class="row" style="display:none">
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Ward</label>
                                    <input type="text" class="form-control" name="ward" id="ward"
                                           value="{{old('ward',$auto->address->ward_number)}}" required readonly="true">


                                </div>
                                @if ($errors->has('name'))
                                    <label class="error">{{ $errors->first('name') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Constituency</label>
                                    <input type="text" class="form-control" name="constituency" id="constituency"
                                           value="{{old('constituency',$auto->address->constituency)}}" required>


                                </div>
                                @if ($errors->has('name'))
                                    <label class="error">{{ $errors->first('name') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row" style="display:none">
                        <div class="col-sm-6">


                            <label>Section</label>
                            {{--<input type="text" class="form-control" name="section" id="section" required>--}}

                            <select name="address_section_id" class="form-control selectpicker" id="section">
                                <option value="">Select</option>
                                @if($auto->address)
                                    @foreach($auto->address->addressSection as $key=>$value)
                                        <option
                                            value="{{$value->id}}" {{($value->id==old('address_section_id',$auto->section_id))?"selected":""}}>{{$value->name}}</option>
                                    @endforeach
                                @endif
                            </select>


                            @if ($errors->has('chiefdom'))
                                <label class="error">{{ $errors->first('chiefdom') }}</label>
                            @endif

                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Chiefdom</label>

                                    {{-- <input type="text" class="form-control" name="chiefdom" id="chiefdom"  required>--}}
                                    <select name="address_chiefdom_id" class="form-control selectpicker" id="chiefdom">
                                        <option value="">Select</option>
                                        @if($auto->address)
                                            @foreach($auto->address->addressChiefdom as $key=>$value)
                                                <option
                                                    value="{{$value->id}}" {{($value->id==old('address_chiefdom_id',$auto->chiefdom_id))?"selected":""}}>{{$value->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if ($errors->has('chiefdom'))
                                        <label class="error">{{ $errors->first('chiefdom') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="display:none">
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>District</label>
                                    <input type="text" class="form-control" name="district" id="district" required
                                           readonly="true" value="{{old('district',$auto->address->district)}}">


                                </div>
                                @if ($errors->has('district'))
                                    <label class="error">{{ $errors->first('district') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Province</label>
                                    <input type="text" class="form-control" name="province" id="province" required
                                           readonly="true" value="{{old('province',$auto->address->province)}}">

                                    @if ($errors->has('province'))
                                        <label class="error">{{ $errors->first('province') }}</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="display:none">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="">Address</label>
                                    {!! Form::text('address', old('address', ($auto->map_addresses)), ['class' => 'form-control', 'id' => 'address']) !!}
                                </div>
                            </div>
                            {!! $errors->first('address', '<p class="error">:message</p>'); !!}
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="">Latitude</label>
                                    <input type="text" class="form-control" name="latitude" id="latitude" required
                                           value="{{old('latitude',$auto->latitude)}}">
                                </div>
                            </div>
                            {!! $errors->first('event_address', '<p class="error">:message</p>'); !!}
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="">Longitude</label>
                                    <input type="text" class="form-control" name="longitude" id="longitude" required
                                           value="{{old('longitude',$auto->longitude)}}">
                                </div>
                            </div>
                            {!! $errors->first('event_address', '<p class="error">:message</p>'); !!}
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-12">
                        <div id="map" style="width: 100%; height: 500px;"></div>

                    </div>
                    <div class="col-sm-6 col-md-12">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="Business Availability">Availability</label> <br>
                                <div id="businessHoursContainer3"></div>
                            </div>
                        </div>
                        <textarea id="businessHoursOutput" name="availability_times"
                                  style="display: none">{{ old('availability_times',json_encode($auto->availability_times))  }}</textarea>

                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="">Meta Tag 1</label>
                                    {!! Form::text('meta_tag1', old('meta_tag1', ($auto->meta_tag1)), ['class' => 'form-control', 'id' => 'meta_tag1']) !!}
                                </div>
                            </div>
                            {!! $errors->first('meta_tag1', '<p class="error">:message</p>'); !!}
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="">Meta Tag 2</label>
                                    <input type="text" class="form-control" name="meta_tag2" id="meta_tag2"
                                           value="{{old('meta_tag2',$auto->meta_tag2)}}">
                                </div>
                            </div>
                            {!! $errors->first('meta_tag2', '<p class="error">:message</p>'); !!}
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="">Meta Tag 3</label>
                                    <input type="text" class="form-control" name="meta_tag3" id="meta_tag3"
                                           value="{{old('meta_tag3',$auto->meta_tag3)}}">
                                </div>
                            </div>
                            {!! $errors->first('meta_tag3', '<p class="error">:message</p>'); !!}
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="">Sequence</label>
                                    <input type="number" min="0" class="form-control" name="sequence" id="sequence"
                                           value="{{old('sequence',$auto->sequence)}}">
                                </div>
                            </div>
                            {!! $errors->first('sequence', '<p class="error">:message</p>'); !!}
                        </div>
                    </div>


                    <button class="btn btn-primary waves-effect" type="submit" id="submitButton">Save</button>


                </div>
            </div>

            @if(isset($auto->id) && $attributeGroups !== false)

                <div class="card">
                    <div class="header">
                        <h2>{{ __("Additional Attributes") }}</h2>

                    </div>
                    <div class="body">
                        @foreach($attributeGroups as $group)
                            <h2 class="card-inside-title">{{ $group->attribute_group_name }}</h2>

                            @foreach ($group->attributes as $attribute)

                                @if($attribute->frontend_type == 'input')
                                    {!!Form::materialText($attribute->frontend_label, $attribute->attribute_code) !!}
                                @else
                                    <div class="form-group form-float">
                                        <div class="form-line{{ $errors->has($attribute->attribute_code) ? ' error' : '' }}">
                                            <label>{{ $attribute->frontend_label }}</label>

                                            <select class="form-control" name="{{ $attribute->attribute_code }}">
                                                <option value="">Choose a option</option>
                                                @foreach($attribute->optionValues as $option)
                                                    <option {{ old($attribute->attribute_code, $auto->{$attribute->attribute_code}) == $option->label ? "selected" : "" }} {!! $option->is_featured == 1 ? 'style="font-weight: bold; color: #000"' : '' !!} value="{{ $option->label }}">{{ $option->label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {!! $errors->first($attribute->attribute_code, '<p class="error">:message</p>') !!}
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="header">
                    <h2>Available</h2>
                </div>
                <div class="body">
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="is_available" value="0">
                        <input type="checkbox" name="is_available" value="1" class="custom-control-input filled-in"
                               id="customCheck2" {{old('is_available',optional($auto)->is_available)==1?'checked':''}} >
                        <label class="custom-control-label" for="customCheck2">Is Available </label>

                    </div>
                </div>
            </div>
            <div class="card" style="display:none">
                <div class="header">
                    <h2>Categories</h2>
                </div>
                <div class="body list-tree">
                    @if ($errors->has('categories'))
                        <label class="error">{{ $errors->first('categories') }}</label>
                    @endif
                    @include('admin.auto.categories', ['categories' => $categories, 'parent' => null])

                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}

    @isset($auto->id)
        {!! Form::open(['route' => ['admin.real-estate.destroy', $auto->id], 'method' => 'DELETE','class'=>'delete','id'=>'deleteForm']) !!}

        {!! Form::close() !!}
    @endisset
@stop


@push("before_head_close")
    <style type="text/css" media="screen">
        .map_canvas {
            width: 100%;
            height: 215px;
            margin: 10px 20px 10px 0;
        }

        .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {
            width: 100%;
        }

        .show-tick {
            width: 100% !important;
        }

        .typeahead {
            border: 2px solid #FFF;
            border-radius: 4px;
            padding: 8px 12px;
            max-width: 300px;
            min-width: 290px;
            background: #1f3f41f2;
            color: #FFF;
        }

        .tt-menu {
            width: 300px;
        }

        ul.typeahead {
            margin: 0px;
            padding: 10px 0px;
            top: 100px !important;
        }

        ul.typeahead.dropdown-menu li a {
            padding: 10px !important;
            border-bottom: #CCC 1px solid;
            color: #FFF;
        }

        ul.typeahead.dropdown-menu li:last-child a {
            border-bottom: 0px !important;
        }

        .bgcolor {
            max-width: 550px;
            min-width: 290px;
            max-height: 340px;
            background: url("world-contries.jpg") no-repeat center center;
            padding: 100px 10px 130px;
            border-radius: 4px;
            text-align: center;
            margin: 10px;
        }

        .demo-label {
            font-size: 1.5em;
            color: #686868;
            font-weight: 500;
            color: #FFF;
        }

        .dropdown-menu > .active > a, .dropdown-menu > .active > a:focus, .dropdown-menu > .active > a:hover {
            text-decoration: none;
            background-color: #1f3f41;
            outline: 0;
        }
    </style>
    <link href="{{ url('admin/css/jquery.businessHours.css') }}" rel="stylesheet"/>
    <link href="{{ url('admin/css/jquery.timepicker.min.css') }}" rel="stylesheet"/>

@endpush

@push('before_body_close')

    <script type="text/javascript" src="{{ url('admin/js/jquery.timepicker.min.js') }}"></script>

    <script type="text/javascript" src="{{ url('admin/js/businessHours.js') }}"></script>
    <script type="text/javascript" src="{{ url('admin/plugins/tinymce/tinymce.js') }}"></script>
    <script type="text/javascript" src="{{ url('admin/js/typeahead.js') }}"></script>
    <script>
        $(document).ready(function () {

            tinymce.init({selector: '#about'});
            @if(old("area",$auto->addressArea->name))

            let query = '{{old("area",$auto->addressArea->name)}}';
            $.ajax({
                url: "/api/v1/digital-address/area_search",
                data: 'searchTerm=' + query,
                dataType: "json",
                type: "POST",
                success: function (data) {
                    $.map(data, function (item) {
                        $('#section').empty();
                        $('#chiefdom').empty();
                        $("#ward").val(item.address.wardNumber);
                        $("#constituency").val(item.address.constituency);
                        $("#address_area_id").val(item.id);
                        $("#address_id").val(item.addressId);
                        //for (let j = 0; j < opts[i].addressSection.length; j++) {
                        $('#section').append(new Option(item.addressSection.name, item.addressSection.id));

                        //}
                        for (let k = 0; k < item.address.addressChiefdom.length; k++) {
                            $('#chiefdom').append(new Option(item.address.addressChiefdom[k].name, item.address.addressChiefdom[k].id));
                        }

                        $('.selectpicker').selectpicker('refresh');
                        $("#district").val(item.address.district);
                        $("#province").val(item.address.province);
                    });
                }
            });
            @endif
            $('#area').typeahead({
                source: function (query, result) {
                    $.ajax({
                        url: "/api/v1/digital-address/area_search",
                        data: 'searchTerm=' + query,
                        dataType: "json",
                        type: "POST",
                        success: function (data) {
                            result($.map(data, function (item) {
                                return item;
                            }));
                        }
                    });
                },
                updater: function (item) {
                    console.log(item);
                    $('#section').empty();
                    $('#chiefdom').empty();
                    $("#ward").val(item.address.wardNumber);
                    $("#constituency").val(item.address.constituency);
                    $("#address_area_id").val(item.id);
                    $("#address_id").val(item.addressId);
                    //for (let j = 0; j < opts[i].addressSection.length; j++) {
                    $('#section').append(new Option(item.addressSection.name, item.addressSection.id));

                    //}
                    for (let k = 0; k < item.address.addressChiefdom.length; k++) {
                        $('#chiefdom').append(new Option(item.address.addressChiefdom[k].name, item.address.addressChiefdom[k].id));
                    }

                    $('.selectpicker').selectpicker('refresh');
                    $("#district").val(item.address.district);
                    $("#province").val(item.address.province);

                    return item;
                }
            });
        });
    </script>
    <script>

        $(document).ready(function () {

            $(".delete").click(function () {
                if (confirm("Are you sure?")) {
                    $("#deleteForm").submit(); // Submit the form
                }

            });
        });

        function imgdelete(id) {
            if (confirm("Are you sure?")) {
                $.ajax({
                    headers: {
                        'X-CSRF-Token': "{{ csrf_token() }}"
                    },
                    type: 'GET',
                    url: "{{url('/sl-admin/imageDelete/real-estate-seller')}}/" + id,
                    success: function (data) {

                        $("#img" + id).css('display', 'none');
                    }
                });
            }
        }
        function imgbgdelete(id) {
            if (confirm("Are you sure?")) {
                $.ajax({
                    headers: {
                        'X-CSRF-Token': "{{ csrf_token() }}"
                    },
                    type: 'GET',
                    url: "{{url('/sl-admin/imagebgDelete/auto')}}/" + id,
                    success: function (data) {

                        $("#img" + id).css('display', 'none');
                    }
                });
            }
        }

        var businessHoursManager = $("#businessHoursContainer3").businessHours();

        jQuery("#submitButton").on('click', function () {

            //$("textarea#businessHoursOutput").val(JSON.stringify(businessHoursManager.serialize()));
            $("textarea#businessHoursOutput").val(JSON.stringify(businessHoursManager.serialize()));
        });


        $("#businessHoursContainer3").businessHours({
            postInit: function () {
                $('.operationTimeFrom, .operationTimeTill').timepicker({
                    'timeFormat': 'H:i',
                    'step': 15
                });
            },
            dayTmpl: '<div class="dayContainer" style="width: 12%;">' +
                '<div data-original-title="" class="colorBox"><div class="weekday"></div><input type="checkbox" class="invisible operationState"></div>' +
                '<div class="operationDayTimeContainer">' +
                '<div class="operationTime input-group"><span class="input-group-addon"><i class="far fa-sun"></i></span><input type="text" name="startTime" class="mini-time form-control operationTimeFrom" value=""></div>' +
                '<div class="operationTime input-group"><span class="input-group-addon"><i class="far fa-moon"></i></span><input type="text" name="endTime" class="mini-time form-control operationTimeTill" value=""></div>' +
                '</div></div>',
        });

        if ($("#businessHoursOutput").val() != 'null') {

            $("#businessHoursContainer3").businessHours({
                postInit: function () {
                    $('.operationTimeFrom, .operationTimeTill').timepicker({
                        'timeFormat': 'H:i',
                        'step': 15
                    });
                },
                operationTime: jQuery.parseJSON($("#businessHoursOutput").val()),
                dayTmpl: '<div class="dayContainer" style="width: 12%;">' +
                    '<div data-original-title="" class="colorBox"><input type="checkbox" class="invisible operationState"><div class="weekday"></div></div>' +
                    '<div class="operationDayTimeContainer">' +
                    '<div class="operationTime input-group"><span class="input-group-addon"><i class="far fa-sun"></i></span><input type="text" name="startTime" class="mini-time form-control operationTimeFrom" value=""></div>' +
                    '<div class="operationTime input-group"><span class="input-group-addon"><i class="far fa-moon"></i></span><input type="text" name="endTime" class="mini-time form-control operationTimeTill" value=""></div>' +
                    '</div></div>'
            });

        } else {
            $("#businessHoursContainer3").businessHours({
                postInit: function () {
                    $('.operationTimeFrom, .operationTimeTill').timepicker({
                        'timeFormat': 'H:i',
                        'step': 15
                    });
                },
                operationTime: [{"is_active": false, "timeFrom": null, "timeTill": null}, {
                    "is_active": false,
                    "timeFrom": null,
                    "timeTill": null
                }, {"is_active": false, "timeFrom": null, "timeTill": null}, {
                    "is_active": false,
                    "timeFrom": null,
                    "timeTill": null
                }, {"is_active": false, "timeFrom": null, "timeTill": null}, {
                    "is_active": false,
                    "timeFrom": null,
                    "timeTill": null
                }, {"is_active": false, "timeFrom": null, "timeTill": null}],
                dayTmpl: '<div class="dayContainer" style="width: 12%;">' +
                    '<div data-original-title="" class="colorBox"><input type="checkbox" class="invisible operationState"><div class="weekday"></div></div>' +
                    '<div class="operationDayTimeContainer">' +
                    '<div class="operationTime input-group"><span class="input-group-addon"><i class="far fa-sun"></i></span><input type="text" name="startTime" class="mini-time form-control operationTimeFrom" value=""></div>' +
                    '<div class="operationTime input-group"><span class="input-group-addon"><i class="far fa-moon"></i></span><input type="text" name="endTime" class="mini-time form-control operationTimeTill" value=""></div>' +
                    '</div></div>'
            });
        }

    </script>
    <script>
        var map, places, infoWindow;
        var autocomplete;
        var markers = [];
        var marker;
        var MARKER_PATH = 'https://developers.google.com/maps/documentation/javascript/images/marker_green';
        var countryRestrict = {'country': 'SL'};
        var latt = {{$auto->latitude ?? 8.4871072}};
        var lngg = {{$auto->longitude ?? -13.235809}};
        var add = "{{$auto->map_addresses ?? "32 Walpole St, Freetown, Sierra Leone"}}";

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: new google.maps.LatLng(latt, lngg),
                zoom: 16
            });
            var infoWindow = new google.maps.InfoWindow;
            var uluru = {lat: latt, lng: lngg};
            $("#latitude").val(latt);
            $("#longitude").val(lngg);
            $("#address").val(add);
            markers[0] = new google.maps.Marker({
                position: uluru,
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP,
            });

            google.maps.event.addListener(markers[0], 'dragend', function () {
                geocodePosition(markers[0].getPosition());
            });

            autocomplete = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */ (
                    document.getElementById('address')));
            places = new google.maps.places.PlacesService(map);

            autocomplete.addListener('place_changed', onPlaceChanged);

            /*// Add a DOM event listener to react when the user selects a country.
            document.getElementById('country').addEventListener(
                'change', setAutocompleteCountry);*/

        }


        function geocodePosition(pos) {
            geocoder = new google.maps.Geocoder();
            geocoder.geocode
            ({
                    latLng: pos
                },
                function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        console.log(results[0].geometry.location.lat());
                        $("#latitude").val(results[0].geometry.location.lat());
                        $("#longitude").val(results[0].geometry.location.lng());
                        $("#address").val(results[0].formatted_address);
                        $("#mapErrorMsg").hide(100);
                    } else {
                        $("#mapErrorMsg").html('Cannot determine address at this location.' + status).show(100);
                    }
                }
            );
        }

        function onPlaceChanged() {
            var place = autocomplete.getPlace();
            if (place.geometry) {
                map.panTo(place.geometry.location);
                map.setZoom(15);
                search();
            } else {
                document.getElementById('address').placeholder = 'Enter a city';
            }
        }

        function search() {
            var search = {
                bounds: map.getBounds(),
                types: ['lodging']
            };

            places.nearbySearch(search, function (results, status) {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    clearResults();
                    clearMarkers();
                    console.log(results);
                    // Create a marker for each hotel found, and
                    // assign a letter of the alphabetic to each marker icon.
                    //for (var i = 0; i < results.length; i++) {
                    for (var i = 0; i < 1; i++) {
                        var markerLetter = String.fromCharCode('A'.charCodeAt(0) + (i % 26));
                        var markerIcon = MARKER_PATH + markerLetter + '.png';
                        // Use marker animation to drop the icons incrementally on the map.
                        markers[i] = new google.maps.Marker({
                            position: results[i].geometry.location,
                            animation: google.maps.Animation.DROP,
                            draggable: true,
                        });

                        $("#latitude").val(results[i].geometry.location.lat());
                        $("#longitude").val(results[i].geometry.location.lng());
                        $("#address").val(results[i].vicinity);
                        // If the user clicks a hotel marker, show the details of that hotel
                        // in an info window.
                        markers[i].placeResult = results[i];
                        google.maps.event.addListener(markers[0], 'dragend', function () {
                            geocodePosition(markers[0].getPosition());
                        });
                        //google.maps.event.addListener(markers[i], 'click', showInfoWindow);
                        setTimeout(dropMarker(i), i * 100);
                        //addResult(results[i], i);
                    }
                }
            });
        }

        function clearMarkers() {
            for (var i = 0; i < markers.length; i++) {
                if (markers[i]) {
                    markers[i].setMap(null);
                }
            }
            markers = [];
        }

        function clearResults() {
            var results = document.getElementById('results');
            /*while (results.childNodes[0]) {
                results.removeChild(results.childNodes[0]);
            }*/
        }

        function showInfoWindow() {
            var marker = this;
            places.getDetails({placeId: marker.placeResult.place_id},
                function (place, status) {
                    if (status !== google.maps.places.PlacesServiceStatus.OK) {
                        return;
                    }
                    infoWindow.open(map, marker);
                    buildIWContent(place);
                });
        }

        function dropMarker(i) {
            return function () {
                markers[i].setMap(map);
            };
        }

        function addResult(result, i) {
            var results = document.getElementById('results');
            var markerLetter = String.fromCharCode('A'.charCodeAt(0) + (i % 26));
            var markerIcon = MARKER_PATH + markerLetter + '.png';

            var tr = document.createElement('tr');
            tr.style.backgroundColor = (i % 2 === 0 ? '#F0F0F0' : '#FFFFFF');
            tr.onclick = function () {
                google.maps.event.trigger(markers[i], 'click');
            };

            var iconTd = document.createElement('td');
            var nameTd = document.createElement('td');
            var icon = document.createElement('img');
            icon.src = markerIcon;
            icon.setAttribute('class', 'placeIcon');
            icon.setAttribute('className', 'placeIcon');
            var name = document.createTextNode(result.name);
            iconTd.appendChild(icon);
            nameTd.appendChild(name);
            tr.appendChild(iconTd);
            tr.appendChild(nameTd);
            results.appendChild(tr);
        }

        //hidden files
        $("#hidden-files").on('click', function () {
            $("#hidden-images").trigger('click');
        });
        $("#hidden-images").on('change', function () {
            $(".selected-images").remove();
            $(this).after('<div class="selected-images">' + $(this)[0].files.length + ' image selected.</div>');
        });

        $(document).ready(function () {
            $("#hidden-file").on('click', function () {
                $("#hidden-image").trigger('click');
            });
        });

        $("#hidden-background-files").on('click', function () {
            $("#hidden-background-images").trigger('click');
        });
        $("#hidden-background-images").on('change', function () {
            $(".selected-backgroun-images").remove();
            $(this).after('<div class="selected-background-images">' + $(this)[0].files.length + ' image selected.</div>');
        });

        $(document).ready(function () {
            $("#hidden-background-file").on('click', function () {
                $("#hidden-background-image").trigger('click');
            });
        });
    </script>

    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA3sNZ6dV-Dw26AhYdEVJWVvIvwT8Mcozg&callback=initMap&libraries=places">
    </script>
@endpush
