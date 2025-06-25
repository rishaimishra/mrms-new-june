@extends('admin.layout.edit')

@section('title')
    <div class="block-header">

    </div>
@endsection

@section('form')

    {!! Form::open(['route' => 'admin.account.update-password']) !!}

    <div class="row">
        <div class="col-sm-8">
            <div class="card">
                <div class="header">
                    <h2>Profile Update</h2>
                </div>
                <div class="body">
                    <div class="row">
                        @role('admin')
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="firstName">First Name</label>
                                    <span class="required-field"></span>
                                    <input class="form-control" name="first_name" type="text" id="firstName" value="{{ Auth::user('admin')->first_name }}" required>
                                </div>
                                @if ($errors->has('first_name'))
                                    <label class="error">{{ $errors->first('first_name') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="lastName">Last Name</label>
                                    <span class="required-field"></span>
                                    <input class="form-control" name="last_name" type="text" id="lastName" value="{{ Auth::user('admin')->last_name }}" required>
                                </div>
                                @if ($errors->has('last_name'))
                                    <label class="error">{{ $errors->first('last_name') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="email">Email</label>
                                    <span class="required-field"></span>
                                    <input class="form-control" name="email" type="email" id="email" value="{{ Auth::user('admin')->email }}" required>
                                </div>
                                @if ($errors->has('email'))
                                    <label class="error">{{ $errors->first('email') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="username">User Name</label>
                                    <span class="required-field"></span>
                                    <input class="form-control" name="username" type="text" id="username" value="{{ Auth::user('admin')->username }}" required>
                                </div>
                                @if ($errors->has('username'))
                                    <label class="error">{{ $errors->first('username') }}</label>
                                @endif
                            </div>
                        </div>
                        @endrole
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="label">Current Password</label>
                                    <span class="required-field"></span>
                                    <input class="form-control" name="current_password" type="password" id="label" required>
                                </div>
                                @if ($errors->has('current_password'))
                                    <label class="error">{{ $errors->first('current_password') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="label">New Password </label>
                                    <span class="required-field"></span>
                                    <input class="form-control" name="new_password" type="password" id="label">

                                </div>
                                <small>Enter new password if you wish to change.</small>
                                @if ($errors->has('new_password'))
                                    <label class="error">{{ $errors->first('new_password') }}</label>
                                @endif
                            </div>
                        </div>


                        <div class="col-sm-12">
                            <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                        </div>


                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

