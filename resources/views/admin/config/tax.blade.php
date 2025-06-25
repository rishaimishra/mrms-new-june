@extends('admin.layout.edit')

@section('title')
    <div class="block-header">
        <h2>TAX</h2>
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
                            'DIGITAL ADMINISTRATION',
                            \App\Logic\SystemConfig::DIGITAL_ADMINISTRATION,
                            old(\App\Logic\SystemConfig::DIGITAL_ADMINISTRATION, $optionGroup->{\App\Logic\SystemConfig::DIGITAL_ADMINISTRATION}),
                            $errors->first(\App\Logic\SystemConfig::DIGITAL_ADMINISTRATION,'<p class="error">:message</p>')
                            )
                            !!}

                    </div>

                    <div class="form-group">
                        {!! Form::materialText(
                            'TRANSPORT WEAR & TEAR',
                            \App\Logic\SystemConfig::TRANSPORT,
                            old(\App\Logic\SystemConfig::TRANSPORT, $optionGroup->{\App\Logic\SystemConfig::TRANSPORT}),
                            $errors->first(\App\Logic\SystemConfig::TRANSPORT)
                            )
                            !!}

                    </div>
                    <div class="form-group">
                        {!! Form::materialText(
                            'FUEL',
                            \App\Logic\SystemConfig::FUEL,
                            old(\App\Logic\SystemConfig::FUEL, $optionGroup->{\App\Logic\SystemConfig::FUEL}),
                            $errors->first(\App\Logic\SystemConfig::FUEL)
                            )
                            !!}

                    </div>
                    <div class="form-group">
                        {!! Form::materialText(
                            'GST',
                            \App\Logic\SystemConfig::GST,
                            old(\App\Logic\SystemConfig::GST, $optionGroup->{\App\Logic\SystemConfig::GST}),
                            $errors->first(\App\Logic\SystemConfig::GST)
                            )
                            !!}

                    </div>
                    <div class="form-group">
                        {!! Form::materialText(
                            'TIP',
                            \App\Logic\SystemConfig::TIP,
                            old(\App\Logic\SystemConfig::TIP, $optionGroup->{\App\Logic\SystemConfig::TIP}),
                            $errors->first(\App\Logic\SystemConfig::TIP)
                            )
                            !!}

                    </div>


                </div>



            </div>
            {!! Form::submit('Save', ['class' => 'btn btn-primary waves-effect btn-lg']) !!}
        </div>
    </div>

@endsection
