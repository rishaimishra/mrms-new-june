@extends('admin.layout.main')

@section('content')
    @include('admin.layout.partial.alert')

    @if(Auth::user()->user_type == 'shop')
    {!!Form::open(['route' => 'admin.seller-attribute-group.store']) !!}
    @else
    {!!Form::open(['route' => 'admin.attribute-group.store']) !!}
    @endif
   


    @include('admin.attribute.attribute-group.form')
    {!! Form::close() !!}

@endsection
