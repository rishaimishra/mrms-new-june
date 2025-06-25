@extends('admin.layout.main')

@section('content')
    @include('admin.layout.partial.alert')
    @if(Auth::user()->user_type == 'shop')
    {!!Form::model($attribute, ['method' => 'put', 'route' => ['admin.seller-attribute.update', [$attribute->attribute_id]]]) !!}
    <input type="hidden" name="attribute_id" value="{{ $attribute->attribute_id }}">
    @else
    {!!Form::model($attribute, ['method' => 'put', 'route' => ['admin.attribute.update', [$attribute->attribute_id]]]) !!}
    @endif
    

    @include('admin.attribute.attribute.form')

    {!! Form::close() !!}
    @if(Auth::user()->user_type == 'shop')
    @isset($attribute)
        {!! Form::open(['method' => 'delete', 'route' => ['admin.seller-attribute.destroy', [$attribute->attribute_id]], 'id' => 'delete-form','style' => 'display: none']) !!}
        <input type="hidden" name="attribute_id" value="{{ $attribute->attribute_id }}">
        <button type="submit">Delete</button>
        {!! Form::close() !!}
    @endisset
    @else
    @isset($attribute)
        {!! Form::open(['method' => 'delete', 'route' => ['admin.attribute.destroy', ['attribute' => $attribute->attribute_id]], 'id' => 'delete-form','style' => 'display: none']) !!}
        <button type="submit">Delete</button>
        {!! Form::close() !!}
    @endisset
    @endif
    

    @push('scripts')

        <script type="text/javascript">
            $(function () {
                $('button#delete').on('click', function () {
                    if (confirm('Are you sure want to delete this attribute?')) {
                        $("#delete-form").submit();
                    }
                });
            });
        </script>
    @endpush

@endsection
