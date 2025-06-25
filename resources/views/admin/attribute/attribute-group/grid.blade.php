@extends('admin.layout.grid')

@section('grid-title')
    <h2>{{ __("Attribute Group") }}</h2>
@endsection

@section('grid-actions')
    @if(Auth::user()->user_type == 'shop')
    <a href="{{ route('admin.seller-attribute-group.create') }}" class="btn btn-sm btn-primary">Create New</a>
    @endif
    @role('admin')
    <a href="{{ route('admin.attribute-group.create') }}" class="btn btn-sm btn-primary">Create New</a>
    @endrole
@endsection

@section('grid-content')
    <div class="body">
        {!! $grid !!}
    </div>
@endsection
