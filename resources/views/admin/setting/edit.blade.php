@extends('admin.layout.main')

@section('content')
    @isset($setting->id)
        {!! Form::model($setting, ['files' => true, 'route' => ['admin.setting.update', $setting->id],'method' => 'PATCH']) !!}
    @else
        {!!Form::open(['route' => 'admin.setting.store','files' => true]) !!}
    @endisset

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">

                    <h2>
                        Add / Edit Setting
                    </h2>


                </div>
                <div class="body">
                     <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>{{ $setting->option_name }}</label>
                                    <input type="text" class="form-control"
                                           value="{{ $setting->option_value }}"
                                           name="option_value" required>

                                    @if ($errors->has('option_value'))
                                        <label class="error">{{ $errors->first('option_value') }}</label>
                                    @endif
                                </div>
                            </div>
                            <button class="btn btn-primary waves-effect" type="submit" id="submitButton">Save</button>
                        </div>
                    </div>

                    </div>



                </div>
            </div>
        </div>

    </div>
    {!! Form::close() !!}
@stop
