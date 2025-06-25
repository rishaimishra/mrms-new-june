@extends('admin.layout.edit')

@section('title')
    <div class="block-header">
        <h2>Credit Setting</h2>
    </div>
@endsection

@section('form')

    {!! Form::open() !!}

    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="body">
                    <div class="form-group">
                        {!! Form::materialText(
                                'Credit Price',
                                \App\Logic\SystemConfig::OPTION_CREDIT_PRICE,
                                old(\App\Logic\SystemConfig::OPTION_CREDIT_PRICE, $optionGroup->{\App\Logic\SystemConfig::OPTION_CREDIT_PRICE}),
                                $errors->first(\App\Logic\SystemConfig::OPTION_CREDIT_PRICE)
                             )
                          !!}
                    </div>
                    <div class="form-group">
                        {!! Form::materialText(
                                'Credit',
                                \App\Logic\SystemConfig::OPTION_CREDIT,
                                old(\App\Logic\SystemConfig::OPTION_CREDIT, $optionGroup->{\App\Logic\SystemConfig::OPTION_CREDIT}),
                                $errors->first(\App\Logic\SystemConfig::OPTION_CREDIT)
                             )
                          !!}
                    </div>
                    <div class="form-group">
                        {!! Form::materialText(
                                'Minimum Credit',
                                \App\Logic\SystemConfig::OPTION_MIN_CREDIT,
                                old(\App\Logic\SystemConfig::OPTION_MIN_CREDIT, $optionGroup->{\App\Logic\SystemConfig::OPTION_MIN_CREDIT}),
                                $errors->first(\App\Logic\SystemConfig::OPTION_MIN_CREDIT)
                             )
                          !!}
                        <small>Minimum credit need to buy user</small>
                    </div>

                </div>
            </div>
            {!! Form::submit('Save', ['class' => 'btn btn-primary waves-effect btn-lg']) !!}
        </div>
    </div>

@endsection