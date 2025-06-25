@extends('admin.layout.grid')

@section('grid-title')
    <h2> Auto Category Listing </h2>
@endsection

@section('grid-actions')
    @role('admin')
    <a href="{{ route('admin.auto-category.create') }}" class="btn btn-sm btn-primary">Create New</a>
    @endrole
@endsection

@section('grid-content')
    <div class="body">
        {!! $grid !!}
    </div>
@endsection
