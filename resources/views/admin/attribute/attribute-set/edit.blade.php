@extends('admin.layout.main')

@section('content')
    @include('admin.layout.partial.alert')
    @if(Auth::user()->user_type == 'shop')
    {!!Form::model($attributeSet, ['method' => 'put', 'route' => ['admin.seller-attribute-set.update', [$attributeSet->attribute_set_id]]]) !!}
    <input type="hidden" name="attribute_set_id" value="{{ $attributeSet->attribute_set_id }}">
    @else
    {!!Form::model($attributeSet, ['method' => 'put', 'route' => ['admin.attribute-set.update', [$attributeSet->attribute_set_id]]]) !!}
    @endif
  

    @include('admin.attribute.attribute-set.form')

    {!! Form::close() !!}
    @if(Auth::user()->user_type == 'shop')
    {!! Form::open(['method' => 'delete', 'route' => ['admin.seller-attribute-set.destroy', [$attributeSet->attribute_set_id]], 'id' => 'delete-form','style' => 'display: none']) !!}
    <input type="hidden" name="attribute_set_id" value="{{ $attributeSet->attribute_set_id }}">
    <button type="submit">Delete</button>
    {!! Form::close() !!}
    @else
    {!! Form::open(['method' => 'delete', 'route' => ['admin.attribute-set.destroy', ['attribute_set' => $attributeSet->attribute_set_id]], 'id' => 'delete-form','style' => 'display: none']) !!}
    <button type="submit">Delete</button>
    {!! Form::close() !!}
    @endif


    @push('scripts')

        <script type="text/javascript">
            $(function () {
                $('button#delete').on('click', function () {
                    if (confirm('Are you sure want to delete this attribute set?')) {
                        $("#delete-form").submit();
                    }
                });
            });
        </script>
    @endpush

@endsection
