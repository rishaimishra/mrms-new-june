@extends('admin.layout.edit')


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


    @if($user->id)
        {!! Form::model($user, ['route' => ['admin.user.update', $user->id], 'method' => 'put', 'files' => true]) !!}
    @else
        {!! Form::open(['route' => 'admin.user.store', 'method' => 'post']) !!}
    @endif

    @if($user->id)
        <input type="hidden" name="id" value="{{ $user->id }}">
    @endif
    
    @if($user->is_edsa_agent)
        <a href="" class="badge badge-pill badge-primary">EDSA AGENT</a>
    @endif
    
    @if($user->is_dstv_agent)
        <a href="" class="badge badge-pill badge-primary">DSTV AGENT</a>
    @endif

    <h4>{{ $user->id ? 'Edit'  : 'Create'}} User</h4>
    <div class="row">
        <div class="col-sm-8">
            <div class="card">
                <div class="header">
                    <h2>User Details</h2>
                </div>
                <div class="body">
                    <div class="row">



                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::materialText('Name', 'name', old('name', $user->name), $errors->first('name')) !!}
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::materialText('Email', 'email', old('email', $user->email), $errors->first('email'), ['disabled']) !!}
                            </div>
                        </div>



                        <div class="col-sm-12">
                            <div class="form-group">
                                {!! Form::materialSelect('Is_active', 'is_active', [true=>"True",false=>"False"], old('gender', $user->is_active), $errors->first('is_active')) !!}
                            </div>
                        </div>
                        
                         <div class="col-sm-12">
                            <div class="form-group">
                                {!! Form::materialSelect('Is EDSA Agent', 'is_edsa_agent', [true=>"True",false=>"False"], old('gender', $user->is_edsa_agent), $errors->first('is_edsa_agent')) !!}
                            </div>
                        </div>
                        
                        
                        <div class="col-sm-12">
                            <div class="form-group">
                                {!! Form::materialSelect('Is DSTV Agent', 'is_dstv_agent', [true=>"True",false=>"False"], old('gender', $user->is_dstv_agent), $errors->first('is_dstv_agent')) !!}
                            </div>
                        </div>


                        <div class="col-sm-12">
                            <div class="form-group">
                            {!! Form::materialText('EDSA STOCKS', 'edsa_stocks', old('edsa_stocks', $user->edsa_stocks), $errors->first('edsa_stocks')) !!}
                            </div>
                        </div>
                        
                        

                        <div class="col-sm-12">
                            <div class="form-group">
                                <button id="save" type="submit" class="btn btn-primary btn-lg waves-effect">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





        </div>

    </div>


@endsection


@push('scripts')

    <script src="{{ asset('admin/js/jquery.geocomplete.js') }}"></script>

    <script>

        //document.getElementsByName("email").disabled = true;;
        jQuery(function () {
            $("input[name*='email']").prop('disabled', true);
            jQuery("#geocomplete").geocomplete({
                map: ".map_canvas",
                details: "form ",
                markerOptions: {
                    draggable: true
                }
            });

            jQuery("#geocomplete").bind("geocode:dragged", function (event, latLng) {
                jQuery("input[name=lat]").val(latLng.lat());
                jQuery("input[name=lng]").val(latLng.lng());
                jQuery("#reset").show();
            });


            jQuery("#reset").click(function () {
                jQuery("#geocomplete").geocomplete("resetMarker");
                jQuery("#reset").hide();
                return false;
            });


            jQuery("#geocomplete").trigger("geocode");

        });
    </script>


@endpush

