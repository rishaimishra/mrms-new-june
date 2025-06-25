@extends('admin.layout.edit')

@section('title')
<div class="block-header">
    <h2>ABOUT US</h2>
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
                        'Total Employees',
                        \App\Logic\SystemConfig::OPTION_TOTAL_EMPLOYEES,
                        old(\App\Logic\SystemConfig::OPTION_TOTAL_EMPLOYEES, $optionGroup->{\App\Logic\SystemConfig::OPTION_TOTAL_EMPLOYEES}),
                        $errors->first(\App\Logic\SystemConfig::OPTION_TOTAL_EMPLOYEES)
                        )
                        !!}
                    </div>

                    <div class="form-group">
                        {!! Form::materialText(
                            'Total Client',
                            \App\Logic\SystemConfig::OPTION_TOTAL_CLIENT,
                            old(\App\Logic\SystemConfig::OPTION_TOTAL_CLIENT, $optionGroup->{\App\Logic\SystemConfig::OPTION_TOTAL_CLIENT}),
                            $errors->first(\App\Logic\SystemConfig::OPTION_TOTAL_CLIENT)
                            )
                            !!}
                        </div>

                        <div class="form-group">
                            {!! Form::materialTextArea(
                                'About',
                                \App\Logic\SystemConfig::OPTION_ABOUT,
                                old(\App\Logic\SystemConfig::OPTION_ABOUT, $optionGroup->{\App\Logic\SystemConfig::OPTION_ABOUT}),
                                $errors->first(\App\Logic\SystemConfig::OPTION_ABOUT)
                                )
                                !!}
                            </div>

                            <div class="form-group">
                                {!! Form::materialText(
                                    'App Store',
                                    \App\Logic\SystemConfig::OPTION_APP_STORE,
                                    old(\App\Logic\SystemConfig::OPTION_APP_STORE, $optionGroup->{\App\Logic\SystemConfig::OPTION_APP_STORE}),
                                    $errors->first(\App\Logic\SystemConfig::OPTION_APP_STORE)
                                    )
                                    !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::materialText(
                                        'Google Play Store',
                                        \App\Logic\SystemConfig::OPTION_GOOGLE_PLAY,
                                        old(\App\Logic\SystemConfig::OPTION_GOOGLE_PLAY, $optionGroup->{\App\Logic\SystemConfig::OPTION_GOOGLE_PLAY}),
                                        $errors->first(\App\Logic\SystemConfig::OPTION_GOOGLE_PLAY)
                                        )
                                        !!}
                                    </div>

                                    {{-- <div class="form-group">
                                            <div class="form-line">
                                                    {!! Form::materialFile(
                                                        'Logo',
                                                        'logo',
                                                        $errors->first(\App\Logic\SystemConfig::OPTION_LOGO)
                                                     )
                                                  !!}
                                            </div>

                                        </div> --}}

                                    <div class="form-group">
                                        {!! Form::materialText(
                                            'Tag Line',
                                            \App\Logic\SystemConfig::OPTION_TAG_LINE,
                                            old(\App\Logic\SystemConfig::OPTION_TAG_LINE, $optionGroup->{\App\Logic\SystemConfig::OPTION_TAG_LINE}),
                                            $errors->first(\App\Logic\SystemConfig::OPTION_TAG_LINE)
                                            )
                                            !!}
                                        </div>
                                    </div>



                                </div>
                                {!! Form::submit('Save', ['class' => 'btn btn-primary waves-effect btn-lg']) !!}
                            </div>
                        </div>

                        @endsection
