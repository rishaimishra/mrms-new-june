@extends('admin.layout.grid')

@section('grid-title')
    <h2> Places  Listing </h2>
@endsection

@section('grid-actions')
    @role('admin')
    <a href="{{ route('admin.place.create') }}" class="btn btn-sm btn-primary">Create New</a>
    @endrole
@endsection

@section('grid-content')
    <div class="body">
        {!! $grid !!}
    </div>
@endsection