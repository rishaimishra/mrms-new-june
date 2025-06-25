@extends('admin.layout.grid')

@section('grid-title')
    <h2> Product Category </h2>
@endsection

@section('grid-actions')
    @role('admin')
    <a href="{{ route('admin.product-category.create') }}" class="btn btn-sm btn-primary">Create New</a>
    @endrole
    @if(Auth::user()->user_type=="shop")
    <a href="{{ route('admin.seller-product-category.create') }}" class="btn btn-sm btn-primary">Create New</a>
    @endif
@endsection

@section('grid-content')
    <div class="body">
        {!! $grid !!}
    </div>
@endsection
