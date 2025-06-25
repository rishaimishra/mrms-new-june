<!-- resources/views/admin/aboutapp/index.blade.php -->
@extends('admin.layout.grid')

@section('grid-title')
<h2> ABOUT APP </h2>
@endsection

@section('grid-content')
                    <div class="card">
                        <div class="body">
                        {!! Form::open(['route' => ['admin.aboutapp.update', 1], 'method' => 'PUT']) !!}
                        @method('PUT')
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group form-float">
                    <div >
                        <label>About App Text</label>
                        <textarea  name="aboutAppInfo" style="width: 500px; height: 100px;">{{ $aboutApp ? $aboutApp->aboutAppInfo : '' }}</textarea>

                        <!-- <input type="text" class="form-control" value="{{ $aboutApp ? $aboutApp->aboutAppInfo : '' }}" name="aboutAppInfo"> -->
                    </div>
                </div>
            </div>
        </div>
        <button class="btn btn-primary waves-effect btn-lg" type="submit">Update</button>
        {!! Form::close() !!}
                        </div>
                    </div>
    </div>

@endsection

