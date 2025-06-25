@extends('admin.layout.edit')

@section('title')
    <div class="block-header">
        <h2>Meta Tags</h2>
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
                                'Meta Title',
                                \App\Logic\SystemConfig::OPTION_META_TITLE,
                                old(\App\Logic\SystemConfig::OPTION_META_TITLE, $optionGroup->{\App\Logic\SystemConfig::OPTION_META_TITLE}),
                                $errors->first(\App\Logic\SystemConfig::OPTION_META_TITLE)
                             )
                          !!}
                    </div>
                    <div class="form-group">
                        {!! Form::materialText(
                                'Meta Keywords',
                                \App\Logic\SystemConfig::OPTION_META_KEYWORDS,
                                old(\App\Logic\SystemConfig::OPTION_META_KEYWORDS, $optionGroup->{\App\Logic\SystemConfig::OPTION_META_KEYWORDS}),
                                $errors->first(\App\Logic\SystemConfig::OPTION_META_KEYWORDS)
                             )
                          !!}
                    </div>
                    <div class="form-group">
                        {!! Form::materialText(
                                'Meta Description',
                                \App\Logic\SystemConfig::OPTION_META_DESCRIPTION,
                                old(\App\Logic\SystemConfig::OPTION_META_DESCRIPTION, $optionGroup->{\App\Logic\SystemConfig::OPTION_META_DESCRIPTION}),
                                $errors->first(\App\Logic\SystemConfig::OPTION_META_DESCRIPTION)
                             )
                          !!}
                    </div>

                </div>
            </div>
            {!! Form::submit('Save', ['class' => 'btn btn-primary waves-effect btn-lg']) !!}
        </div>
    </div>

@endsection