@extends('admin.layout.main')

@section('content')

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="header">
                    <h2>
                        ADD CREDIT
                        <small>Reward and Add Credit in <strong>{{ $user->getName() }}'s</strong> Account</small>
                    </h2>
                </div>
                <div class="body">
                    {!! Form::open() !!}

                    <div class="form-group">
                        {!! Form::materialText('Credits', 'credits', old('credits'), $errors->first('credits'), ['autocomplete' => 'off']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::materialTextArea('Description', 'description', old('description'), $errors->first('description')) !!}
                    </div>

                    {!! Form::submit('SUBMIT', ['class' => 'btn btn-primary m-t-15 btn-lg waves-effect']) !!}

                    {!! Form::close() !!}
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <img src="{{ $user->profile->getAvatarUrl() }}"/>
            <br><br>
            <p><strong>User Name: </strong> {{ $user->getName() }}</p>
            <p><strong>Available Credit: </strong>{{ $user->available_credits }}</p>
        </div>
    </div>
@endsection