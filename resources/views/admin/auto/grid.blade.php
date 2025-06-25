@extends('admin.layout.grid')

@section('grid-title')
    <h2> Autos  Listing </h2>
@endsection

@section('grid-actions')
    @role('admin')
    <a href="{{ route('admin.auto.create') }}" class="btn btn-sm btn-primary">Create New</a>
    @endrole
@endsection

@section('grid-content')
    <div class="body">
        {!! $grid !!}
    </div>
@endsection
