@extends('admin.layout.edit')

<style>
    .cursor{
        cursor: pointer;
    }
    .show-tick{
        width: 100% !important;
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    @if($area->id)
        {!! Form::model($area, ['route' => ['admin.address.update', $area->id], 'method' => 'put', 'files' => true]) !!}
    @else
        {!! Form::open(['route' => 'admin.address.store', 'method' => 'post']) !!}
    @endif

    @if($area->id)
        <input type="hidden" name="id" value="{{ $area->id }}">
    @endif

    <h4>{{ $area->id ? 'Edit'  : 'Create'}} Area</h4>
    <div class="row">
        <div class="col-sm-8">
            <div class="card">
                <div class="header">
                    <h2>Area Details</h2>
                </div>
                <div class="body">
                    <div class="row">



                        {{--<div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::materialText('Area Name', 'area_name', old('area_name', $area->name), $errors->first('area_name')) !!}
                            </div>
                        </div>--}}

                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::materialText('Ward Number', 'ward_number', old('ward_number', $area->ward_number), $errors->first('ward_number')) !!}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::materialText('constituency', 'constituency', old('constituency', $area->constituency), $errors->first('constituency')) !!}
                            </div>
                        </div>
                        {{--<div class="col-sm-12">
                            <div class="form-group">
                                {!! Form::label('area_names', 'Area Names') !!}
                                <select name="area_names[]" class="selectpicker" id="join_person" multiple style="width: 100% !important">
                                    @foreach($addressArea as $ckey => $chiefdom)
                                        <option class="A" id="{{$ckey}}" value="{{$chiefdom}}"
                                                    >{{$chiefdom}}</option>

                                    @endforeach
                                </select>
                            </div>
                        </div>--}}

                        {{--<div class="col-sm-12">
                            <div class="form-group">
                                {!! Form::label('chiefdoms', 'Chiefdoms') !!}
                                <select name="chiefdoms[]" class="selectpicker" id="join_person" multiple>
                                    @foreach($addressChiefdom as $ckey => $chiefdom)
                                        <option class="A" id="{{$ckey}}" value="{{$chiefdom}}"
                                        >{{$chiefdom}}</option>

                                    @endforeach
                                </select>
                            </div>
                        </div>--}}

                        {{--<div class="col-sm-12">
                            <div class="form-group">
                                {!! Form::label('sections', 'Sections') !!}
                                <select name="sections[]" class="selectpicker" id="join_person" multiple>
                                    @foreach($addressSection as $ckey => $chiefdom)
                                        <option class="A" id="{{$ckey}}" value="{{$chiefdom}}"
                                        >{{$chiefdom}}</option>

                                    @endforeach
                                </select>
                            </div>
                        </div>--}}

                        {{--@if(old('chiefdoms')|| $area->addressChiefdom->count())
                            @foreach(old('chiefdoms', $area->addressChiefdom->pluck('name')) as $ckey => $chiefdom)
                                <div class="col-sm-12">
                                    @include('admin.area.input.chiefdom', ['chiefdom' => $chiefdom, 'key' => $ckey])
                                    {!! $errors->first('chiefdoms.*', '<p class="error">:message</p>') !!}
                                </div>
                            @endforeach
                        @else
                            <div class="col-sm-12">
                                @include('admin.area.input.chiefdom')
                                {!! $errors->first('chiefdoms.*', '<p class="error">:message</p>') !!}
                            </div>
                        @endif

                        @if(old('sections')|| $area->addressSection->count())

                            @foreach(old('sections', $area->addressSection->pluck('name')) as $skey => $section)

                                <div class="col-sm-12">
                                    @include('admin.area.input.section', ['section' => $section, 'key' => $skey])
                                    {!! $errors->first('sections.*', '<p class="error">:message</p>') !!}
                                </div>
                            @endforeach
                        @else
                            <div class="col-sm-12">
                                @include('admin.area.input.section')
                                {!! $errors->first('sections.*', '<p class="error">:message</p>') !!}
                            </div>
                        @endif

                        @if(old('area_names')|| $area->addressArea->count())

                            @foreach(old('area_names', $area->addressArea->pluck('name')) as $akey => $area_names)
                                <div class="col-sm-12">
                                    @include('admin.area.input.areaname', ['area_name' => $area_names, 'key' => $akey])
                                    {!! $errors->first('sections.*', '<p class="error">:message</p>') !!}
                                </div>
                            @endforeach
                        @else
                            <div class="col-sm-12">
                                @include('admin.area.input.areaname')
                                {!! $errors->first('sections.*', '<p class="error">:message</p>') !!}
                            </div>
                        @endif--}}

                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::materialText('District', 'district', old('district', $area->district), $errors->first('district')) !!}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::materialText('Province', 'province', old('province', $area->province), $errors->first('province')) !!}
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <button id="save" type="submit" class="btn btn-primary btn-lg waves-effect">{{($area->id)?"Update":"Save"}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>

@endsection


@push('scripts')

    <script src="{{ asset('admin/js/jquery.geocomplete.js') }}"></script>

    <script>



            jQuery(document).ready(function () {

                jQuery(".add-more").on('click', function () {
                    var objClone = jQuery(this).closest('.col-sm-12').clone(true, true);
                    //console.log(objClone.find("input:text").attr("name"));
                    //console.log(jQuery.type(objClone.find("input:text").attr("name")));
                    objClone.find("input:text").val("").prop('readonly',false);
                    objClone.find('.add-more').remove();
                    objClone.find('.remove-more').show();

                    jQuery(this).closest('.col-sm-12').after(objClone);
                });

                jQuery(".remove-more").on('click', function () {
                    jQuery(this).closest('.col-sm-12').remove();
                });


            });
    </script>


@endpush

