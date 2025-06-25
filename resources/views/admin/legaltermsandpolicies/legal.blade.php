<!-- resources/views/admin/aboutapp/index.blade.php -->
@extends('admin.layout.grid')

@section('grid-title')
<h2> TERMS OF USE </h2>
@endsection

@section('grid-content')
                    <div class="card">
                        <div class="body">
                        {!! Form::open(['route' => ['admin.legal.update', 1], 'method' => 'PUT']) !!}
                        @method('PUT')
                            <div class="row">

                                <div class="col-sm-3">
                                    <div class="form-group form-float">
                                        <div>
                                            <label>Terms Of Use Text</label>
                                            <textarea  name="termsText" style="width: 500px; height: 100px;">{{ $legalTermsAndPolicies ? $legalTermsAndPolicies->termsText : '' }}</textarea>

                                            <!-- <input type="text" class="form-control"  value="{{ $legalTermsAndPolicies ? $legalTermsAndPolicies->termsText : '' }}" name="termsText" > -->
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

