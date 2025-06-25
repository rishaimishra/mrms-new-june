@extends('admin.layout.main')

@section('content')
    @include('admin.layout.partial.alert')
    @if(Auth::user()->user_type == "shop")
    {!!Form::open(['route' => 'admin.seller-attribute-set.store']) !!}
    @else
    {!!Form::open(['route' => 'admin.attribute-set.store']) !!}
    @endif
    


    @include('admin.attribute.attribute-set.form')
    {!! Form::close() !!}

@endsection
