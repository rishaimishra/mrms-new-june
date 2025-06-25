@extends('admin.layout.main')

@section('content')

    {!! Form::open(['files'=>true]) !!}

    @yield('title')

    @include('admin.layout.partial.alert')

    @yield('form')

    {!! Form::close() !!}
@endsection
