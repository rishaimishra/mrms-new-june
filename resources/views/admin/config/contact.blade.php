@extends('admin.layout.edit')

@section('title')
<div class="block-header">
    <h2>Contact Details</h2>
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
                        'Opening Hours',
                        \App\Logic\SystemConfig::OPTION_CONTACT_OPENING_HOUR,
                        old(\App\Logic\SystemConfig::OPTION_CONTACT_OPENING_HOUR, $optionGroup->{\App\Logic\SystemConfig::OPTION_CONTACT_OPENING_HOUR}),
                        $errors->first(\App\Logic\SystemConfig::OPTION_CONTACT_OPENING_HOUR)
                        )
                        !!}
                    </div>

                    <div class="form-group">
                        {!! Form::materialTextArea(
                            'Address',
                            \App\Logic\SystemConfig::OPTION_ADDRESS,
                            old(\App\Logic\SystemConfig::OPTION_ADDRESS, $optionGroup->{\App\Logic\SystemConfig::OPTION_ADDRESS}),
                            $errors->first(\App\Logic\SystemConfig::OPTION_ADDRESS),
                        ['rows' => 2]
                            )
                            !!}
                        </div>

                        <div class="form-group">
                            {!! Form::materialTextArea(
                                'Postal Address',
                                \App\Logic\SystemConfig::OPTION_POSTAL_ADDRESS,
                                old(\App\Logic\SystemConfig::OPTION_POSTAL_ADDRESS, $optionGroup->{\App\Logic\SystemConfig::OPTION_POSTAL_ADDRESS}),
                                $errors->first(\App\Logic\SystemConfig::OPTION_POSTAL_ADDRESS),
                        ['rows' => 2]
                                )
                                !!}
                            </div>

                            <div class="form-group">
                                {!! Form::materialText(
                                    'TollFree Number',
                                    \App\Logic\SystemConfig::OPTION_TOLLFREE_NUMBER,
                                    old(\App\Logic\SystemConfig::OPTION_TOLLFREE_NUMBER, $optionGroup->{\App\Logic\SystemConfig::OPTION_TOLLFREE_NUMBER}),
                                    $errors->first(\App\Logic\SystemConfig::OPTION_TOLLFREE_NUMBER)
                                    )
                                    !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::materialText(
                                        'Contact Email',
                                        \App\Logic\SystemConfig::OPTION_CONTACT_EMAIL,
                                        old(\App\Logic\SystemConfig::OPTION_CONTACT_EMAIL, $optionGroup->{\App\Logic\SystemConfig::OPTION_CONTACT_EMAIL}),
                                        $errors->first(\App\Logic\SystemConfig::OPTION_CONTACT_EMAIL)
                                        )
                                        !!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::materialText(
                                            'Enquiry Email',
                                            \App\Logic\SystemConfig::OPTION_ENQUIRY_EMAIL,
                                            old(\App\Logic\SystemConfig::OPTION_ENQUIRY_EMAIL, $optionGroup->{\App\Logic\SystemConfig::OPTION_ENQUIRY_EMAIL}),
                                            $errors->first(\App\Logic\SystemConfig::OPTION_ENQUIRY_EMAIL)
                                            )
                                            !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::materialText(
                                                'Contact number one',
                                                \App\Logic\SystemConfig::OPTION_CONTACT_NUMBER_ONE,
                                                old(\App\Logic\SystemConfig::OPTION_CONTACT_NUMBER_ONE, $optionGroup->{\App\Logic\SystemConfig::OPTION_CONTACT_NUMBER_ONE}),
                                                $errors->first(\App\Logic\SystemConfig::OPTION_CONTACT_NUMBER_ONE)
                                                )
                                                !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::materialText(
                                                    'Contact number two',
                                                    \App\Logic\SystemConfig::OPTION_CONTACT_NUMBER_TWO,
                                                    old(\App\Logic\SystemConfig::OPTION_CONTACT_NUMBER_TWO, $optionGroup->{\App\Logic\SystemConfig::OPTION_CONTACT_NUMBER_TWO}),
                                                    $errors->first(\App\Logic\SystemConfig::OPTION_CONTACT_NUMBER_TWO)
                                                    )
                                                    !!}
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::materialText(
                                                        'Contact number three',
                                                        \App\Logic\SystemConfig::OPTION_CONTACT_NUMBER_THREE,
                                                        old(\App\Logic\SystemConfig::OPTION_CONTACT_NUMBER_THREE, $optionGroup->{\App\Logic\SystemConfig::OPTION_CONTACT_NUMBER_THREE}),
                                                        $errors->first(\App\Logic\SystemConfig::OPTION_CONTACT_NUMBER_THREE)
                                                        )
                                                        !!}
                                                    </div>

                                                </div>
                                            </div>
                                            {!! Form::submit('Save', ['class' => 'btn btn-primary waves-effect btn-lg']) !!}
                                        </div>
                                    </div>

                                    @endsection
