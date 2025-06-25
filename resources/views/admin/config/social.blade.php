@extends('admin.layout.edit')

@section('title')
<div class="block-header">
    <h2>SOCIAL LINKS</h2>
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
                        'Facebook',
                        \App\Logic\SystemConfig::OPTION_FACEBOOK,
                        old(\App\Logic\SystemConfig::OPTION_FACEBOOK, $optionGroup->{\App\Logic\SystemConfig::OPTION_FACEBOOK}),
                        $errors->first(\App\Logic\SystemConfig::OPTION_FACEBOOK)
                        )
                        !!}
                    </div>

                    <div class="form-group">
                        {!! Form::materialText(
                            'Twitter',
                            \App\Logic\SystemConfig::OPTION_TWITTER,
                            old(\App\Logic\SystemConfig::OPTION_TWITTER, $optionGroup->{\App\Logic\SystemConfig::OPTION_TWITTER}),
                            $errors->first(\App\Logic\SystemConfig::OPTION_TWITTER)
                            )
                            !!}
                        </div>

                        <div class="form-group">
                            {!! Form::materialText(
                                'Linkedin',
                                \App\Logic\SystemConfig::OPTION_LINKEDIN,
                                old(\App\Logic\SystemConfig::OPTION_LINKEDIN, $optionGroup->{\App\Logic\SystemConfig::OPTION_LINKEDIN}),
                                $errors->first(\App\Logic\SystemConfig::OPTION_LINKEDIN)
                                )
                                !!}
                            </div>

                            <div class="form-group">
                                {!! Form::materialText(
                                    'Youtube',
                                    \App\Logic\SystemConfig::OPTION_YOUTUBE,
                                    old(\App\Logic\SystemConfig::OPTION_YOUTUBE, $optionGroup->{\App\Logic\SystemConfig::OPTION_YOUTUBE}),
                                    $errors->first(\App\Logic\SystemConfig::OPTION_YOUTUBE)
                                    )
                                    !!}
                                </div>

                                <div class="form-group">
                                    {!! Form::materialText(
                                        'Instagram',
                                        \App\Logic\SystemConfig::OPTION_INSTAGRAM,
                                        old(\App\Logic\SystemConfig::OPTION_INSTAGRAM, $optionGroup->{\App\Logic\SystemConfig::OPTION_INSTAGRAM}),
                                        $errors->first(\App\Logic\SystemConfig::OPTION_INSTAGRAM)
                                        )
                                        !!}
                                    </div>

                                    <div class="form-group">
                                            {!! Form::materialText(
                                                'Pinterest',
                                                \App\Logic\SystemConfig::OPTION_PINTEREST,
                                                old(\App\Logic\SystemConfig::OPTION_PINTEREST, $optionGroup->{\App\Logic\SystemConfig::OPTION_PINTEREST}),
                                                $errors->first(\App\Logic\SystemConfig::OPTION_PINTEREST)
                                                )
                                                !!}
                                            </div>

                                </div>
                                {!! Form::submit('Save', ['class' => 'btn btn-primary waves-effect btn-lg']) !!}
                            </div>
                        </div>

                        @endsection
