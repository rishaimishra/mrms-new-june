@extends('admin.layout.edit')

@section('title')
    <div class="block-header">
        <h2>PUBLIC NOTICE</h2>
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
                            'Public Notice',
                            \App\Logic\SystemConfig::OPTION_PUBLIC_NOTICE,
                            old(\App\Logic\SystemConfig::OPTION_PUBLIC_NOTICE, $optionGroup->{\App\Logic\SystemConfig::OPTION_PUBLIC_NOTICE}),
                            $errors->first(\App\Logic\SystemConfig::OPTION_PUBLIC_NOTICE)
                            )
                            !!}
                    </div>
                </div>



            </div>
            {!! Form::submit('Save', ['class' => 'btn btn-primary waves-effect btn-lg']) !!}
        </div>
    </div>

@endsection
