<!-- resources/views/admin/aboutapp/index.blade.php -->
@extends('admin.layout.grid')

@section('grid-title')
<h2> INTELLECTUAL PROPERTY </h2>
@endsection

@section('grid-content')
                    <div class="card">
                        <div class="body">
                        {!! Form::open(['route' => ['admin.intellectual.update', 1], 'method' => 'PUT']) !!}
                        @method('PUT')
                            <div class="row">

                                <div class="col-sm-3">
                                    <div class="form-group form-float">
                                        <div>
                                            <label>Intellectual Property Text</label>
                                            <textarea  name="title" style="width: 500px; height: 100px;">{{ $intellectualProperty ? $intellectualProperty->title : '' }}</textarea>

                                        </div>
                                    </div>
                                </div>


                            </div>

                            <button class="btn btn-primary waves-effect btn-lg" type="submit">SUBMIT</button>
                            <!-- <a href="{{ route('admin.user.index') }}" class="btn-lg btn btn-default">Clear Filter</a> -->

                            {!! Form::close() !!}
                        </div>
                    </div>
    </div>

@endsection

