@extends('admin.layout.grid')

@section('grid-title')
    <h2> Real Estate Interested User Listing </h2>
@endsection

@section('grid-content')
    <div class="body">
        {!! $grid !!}
    </div>
@endsection
