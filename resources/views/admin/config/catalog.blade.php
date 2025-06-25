@extends('admin.layout.edit')

@section('title')
    <div class="block-header">
        <h2>Business Catalog</h2>
    </div>
@endsection

@section('form')

    {!! Form::open(['files' => true]) !!}

    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="body">

                    <div class="form-group">
                        {!! Form::materialFile(
                                'Business Catalog PDF',
                                'catalog',
                                $errors->first(\App\Logic\SystemConfig::OPTION_BUSINESS_CATALOG)
                             )
                          !!}
                        <br>
                        @if(getSystemConfig(\App\Logic\SystemConfig::OPTION_BUSINESS_CATALOG))
                        <a class="various" data-fancybox-type="iframe"
                           href="{{ asset('catalog/'. getSystemConfig(\App\Logic\SystemConfig::OPTION_BUSINESS_CATALOG)) }}">Download</a>
                        @endif
                    </div>


                </div>
            </div>
            {!! Form::submit('Save', ['class' => 'btn btn-primary waves-effect btn-lg']) !!}
        </div>
    </div>

@endsection

@push('scripts')

    <script>
        jQuery(".various").fancybox({
            maxWidth: 800,
            maxHeight: 600,
            fitToView: false,
            width: '70%',
            height: '70%',
            autoSize: false,
            closeClick: false,
            openEffect: 'none',
            closeEffect: 'none'
        });
    </script>

@endpush