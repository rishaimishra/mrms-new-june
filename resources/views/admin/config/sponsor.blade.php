@extends('admin.layout.edit')

@section('title')
    <div class="block-header">
        <h2>SPONSOR TEXT</h2>
    </div>
@endsection

@section('form')

    {!! Form::open() !!}

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="body">

                    <div class="form-group">
                        {!! Form::materialText(
                            'PLACE SPONSOR',
                            \App\Logic\SystemConfig::PLACE_SPONSOR,
                            old(\App\Logic\SystemConfig::PLACE_SPONSOR, $optionGroup->{\App\Logic\SystemConfig::PLACE_SPONSOR}),
                            $errors->first(\App\Logic\SystemConfig::PLACE_SPONSOR)
                            )
                            !!}
                    </div>

                    <div class="form-group">
                        {!! Form::materialText(
                            'AUTO SPONSOR',
                            \App\Logic\SystemConfig::AUTO_SPONSOR,
                            old(\App\Logic\SystemConfig::AUTO_SPONSOR, $optionGroup->{\App\Logic\SystemConfig::AUTO_SPONSOR}),
                            $errors->first(\App\Logic\SystemConfig::AUTO_SPONSOR)
                            )
                            !!}
                    </div>

                    <div class="form-group">
                        {!! Form::materialText(
                            'REAL ESTATE SPONSOR',
                            \App\Logic\SystemConfig::REAL_STATE_SPONSOR,
                            old(\App\Logic\SystemConfig::REAL_STATE_SPONSOR, $optionGroup->{\App\Logic\SystemConfig::REAL_STATE_SPONSOR}),
                            $errors->first(\App\Logic\SystemConfig::REAL_STATE_SPONSOR)
                            )
                            !!}
                    </div>

                    <div class="form-group">
                        {!! Form::materialText(
                            'QUIZ SPONSOR',
                            \App\Logic\SystemConfig::QUIZ_SPONSOR,
                            old(\App\Logic\SystemConfig::QUIZ_SPONSOR, $optionGroup->{\App\Logic\SystemConfig::QUIZ_SPONSOR}),
                            $errors->first(\App\Logic\SystemConfig::QUIZ_SPONSOR)
                            )
                            !!}
                    </div>

                    <div class="form-group">
                        {!! Form::materialText(
                            'PRODUCT SPONSOR',
                            \App\Logic\SystemConfig::PRODUCT_SPONSOR,
                            old(\App\Logic\SystemConfig::PRODUCT_SPONSOR, $optionGroup->{\App\Logic\SystemConfig::PRODUCT_SPONSOR}),
                            $errors->first(\App\Logic\SystemConfig::PRODUCT_SPONSOR)
                            )
                            !!}
                    </div>
                    
                    <div class="form-group">
                        {!! Form::materialText(
                            'EDSA SPONSOR',
                            \App\Logic\SystemConfig::EDSA_SPONSOR,
                            old(\App\Logic\SystemConfig::EDSA_SPONSOR, $optionGroup->{\App\Logic\SystemConfig::EDSA_SPONSOR}),
                            $errors->first(\App\Logic\SystemConfig::EDSA_SPONSOR)
                            )
                            !!}
                    </div>

                    <div class="form-group">
                        {!! Form::materialText(
                            'DSTV SPONSOR',
                            \App\Logic\SystemConfig::DSTV_SPONSOR,
                            old(\App\Logic\SystemConfig::DSTV_SPONSOR, $optionGroup->{\App\Logic\SystemConfig::DSTV_SPONSOR}),
                            $errors->first(\App\Logic\SystemConfig::DSTV_SPONSOR)
                            )
                            !!}
                    </div>

                    <div class="form-group">
                        {!! Form::materialText(
                            'STAR TIMES SPONSOR',
                            \App\Logic\SystemConfig::STAR_SPONSOR,
                            old(\App\Logic\SystemConfig::STAR_SPONSOR, $optionGroup->{\App\Logic\SystemConfig::STAR_SPONSOR}),
                            $errors->first(\App\Logic\SystemConfig::STAR_SPONSOR)
                            )
                            !!}
                    </div>


                </div>



            </div>
            {!! Form::submit('Save', ['class' => 'btn btn-primary waves-effect btn-lg']) !!}
        </div>
    </div>

@endsection
