@extends('admin.layout.auth')

@section('card-body')

    @include('admin.layout.partial.alert')

    {!! Form::open(['novalidate' => 'novalidate', 'id' => 'forgot_password']) !!}

        <div class="msg">
            Enter your email address that you used to register.
        </div>
        <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
            <div class="form-line">
                <input type="email" class="form-control" name="email" placeholder="Email" required autofocus>

            </div>
            @if ($errors->has('email'))
                <label class="error">{{ $errors->first('email') }}</label>
            @endif

            @if ($errors->has('status'))
                <label class="error">{{ $errors->first('status') }}</label>
            @endif
        </div>

        <button class="btn btn-block btn-lg btn-primary waves-effect" type="submit">RESET MY PASSWORD</button>

        <div class="row m-t-20 m-b--5 align-center">
            <a href="{{route('admin.auth.login')}}">Sign In!</a>
        </div>
    {!! Form::close() !!}

@stop

@push('scripts')

    <script src="{{ url('admin/js/pages/examples/forgot-password.js') }}"></script>
@endpush