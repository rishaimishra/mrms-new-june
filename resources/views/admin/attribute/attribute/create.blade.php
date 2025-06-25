@extends('admin.layout.main')

@section('content')
    @include('admin.layout.partial.alert')
    @if (Auth::user()->user_type == 'shop')
    {!!Form::open(['route' => 'admin.seller-attribute.store']) !!}
    @else
    {!!Form::open(['route' => 'admin.attribute.store']) !!}
    @endif
    


    @include('admin.attribute.attribute.form')
    {!! Form::close() !!}

@endsection
