@extends('admin.layout.main')


@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Update Details</h2>

                </div>
                @include('admin.layout.partial.alert')
                <div class="body">
                    {!! Form::open(['novalidate' => 'novalidate', 'id' => 'update_user','route' => 'admin.system-user.update']) !!}
                    <input type="hidden" name="id" value="{{isset($admin_user->id)==true?$admin_user->id:""}}">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="first_name" value="{{old('first_name',$admin_user->first_name)}}" required>
                                    <label class="form-label">First Name</label>

                                </div>
                                @if ($errors->has('first_name'))
                                    <label class="error">{{ $errors->first('first_name') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="last_name" value="{{old('last_name',$admin_user->last_name)}}" required>
                                    <label class="form-label">Last Name</label>

                                </div>
                                @if ($errors->has('last_name'))
                                        <label class="error">{{ $errors->first('last_name') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="email" class="form-control"  value="{{old('email',$admin_user->email)}}" disabled required>
                                    <label class="form-label">Email</label>

                                </div>
                                @if ($errors->has('email'))
                                        <label class="error">{{ $errors->first('email') }}</label>
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control"  value="{{old('username',$admin_user->username)}}" disabled required>
                                    <label class="form-label">Username</label>

                                </div>
                                @if ($errors->has('username'))
                                        <label class="error">{{ $errors->first('username') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="password" class="form-control" minlength="6" name="password" required>
                                    <label class="form-label">Password</label>

                                </div>
                                @if ($errors->has('password'))
                                        <label class="error">{{ $errors->first('password') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-sm-3 form-group">
                            <label class="form-label">User Type </label>
                            <select name="user_role" id="user_role" class="form-control show-tick">
                                <option value="">-- Select User Type --</option>
                                @foreach(Spatie\Permission\Models\Role::select('name')->where('id','!=',1)->get() as $role)
                                    <option value="{{$role['name']}}" {{$admin_user->getRoleNames()[0]==$role['name'] ?'selected':''}}>{{ucfirst($role['name'])}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('user_role'))
                                <label class="error">{{ $errors->first('user_role') }}</label>
                            @endif

                        </div>
                    </div>
                    <button class="btn btn-primary waves-effect" type="submit">SUBMIT</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>



@stop

@push('scripts')
    <script src="{{ url('admin/plugins/jquery-validation/jquery.validate.js') }}"></script>
    <script src="{{ url('admin/js/pages/forms/form-validation.js') }}"></script>



@endpush
