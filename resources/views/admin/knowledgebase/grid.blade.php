@extends('admin.layout.grid')

@section('grid-title')
    <h2> Fun & Games  Listing </h2>
@endsection

@section('grid-actions')
    @role('admin')
    <a href="{{ route('admin.question.create') }}" class="btn btn-sm btn-primary">Create New</a>
    <a href="{{ route('admin.question.upload') }}" class="btn btn-sm btn-primary">Upload Quiz</a>
    @endrole
@endsection

@section('grid-content')
    <div class="body">
        {!! $grid !!}
    </div>
@endsection
