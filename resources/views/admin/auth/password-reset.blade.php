@extends('admin.layout.auth')

@section('card-body')
    {!! Form::open(['novalidate' => 'novalidate', 'id' => 'sign_in']) !!}
    <div class="msg">Reset Password</div>
    <div class="input-group">
        <span class="input-group-addon">
            <i class="material-icons">person</i>
        </span>
        <div class="form-line{{ $errors->has('email') ? ' error' : '' }}">
            {!! Form::text('email', old('username'), ['class' => 'form-control', 'placeholder' => 'Email', 'required', 'autofocus']) !!}
        </div>
        {!! $errors->first('email', '<label for="email" class="error">:message</label>') !!}
    </div>
    <div class="input-group">
        <span class="input-group-addon">
            <i class="material-icons">lock</i>
        </span>
        <div class="form-line">
            {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password', 'required']) !!}
        </div>
        {!! $errors->first('password', '<label for="password" class="error">:message</label>') !!}
    </div>
    {!! Form::hidden('token', request('token')) !!}
    <div class="input-group">
        <span class="input-group-addon">
            <i class="material-icons">lock</i>
        </span>
        <div class="form-line">
            {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Password Confirmation', 'required']) !!}
        </div>
        {!! $errors->first('password_confirmation', '<label for="password_confirmation" class="error">:message</label>') !!}
    </div>

    <div class="row">

        <div class="col-xs-4">
            {!! Form::submit('RESET', ['class' => 'btn btn-block btn-primary waves-effect']) !!}
        </div>
    </div>

    {!! Form::close() !!}
@endsection