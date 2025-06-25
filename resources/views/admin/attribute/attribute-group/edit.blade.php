@extends('admin.layout.main')

@section('content')
    @include('admin.layout.partial.alert')
    @if(Auth::user()->user_type == 'shop')
    {!!Form::model($attributeGroup, ['method' => 'put', 'route' => ['admin.seller-attribute-group.update', [$attributeGroup->attribute_group_id]]]) !!}
    <input type="hidden" name="attribute_group_id" value="{{ $attributeGroup->attribute_group_id }}" />
    @else
    {!!Form::model($attributeGroup, ['method' => 'put', 'route' => ['admin.attribute-group.update', [$attributeGroup->attribute_group_id]]]) !!}
    @endif
   

    @include('admin.attribute.attribute-group.form')

    {!! Form::close() !!}
    @if(Auth::user()->user_type == 'shop')
    {!! Form::open(['method' => 'delete', 'route' => ['admin.seller-attribute-group.destroy', [$attributeGroup->attribute_group_id]], 'id' => 'delete-form','style' => 'display: none']) !!}
    <input type="hidden" name="attribute_group_id" value="{{ $attributeGroup->attribute_group_id }}">
    <button type="submit">Delete</button>
    {!! Form::close() !!}

    @else
    {!! Form::open(['method' => 'delete', 'route' => ['admin.attribute-group.destroy', ['attribute_group' => $attributeGroup->attribute_group_id]], 'id' => 'delete-form','style' => 'display: none']) !!}
    <button type="submit">Delete</button>
    {!! Form::close() !!}
    @endif

   
    @push('scripts')

        <script type="text/javascript">
            $(function () {
                $('button#delete').on('click', function () {
                    if (confirm('Are you sure want to delete this attribute group?')) {
                        $("#delete-form").submit();
                    }
                });
            });
        </script>
    @endpush

@endsection
