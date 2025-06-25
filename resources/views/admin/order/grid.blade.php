@extends('admin.layout.grid')

@section('grid-title')
    <h2> Orders  Listing </h2>
@endsection

@section('grid-actions')
    @role('admin')

    @endrole
@endsection

@section('grid-content')
    <style>
        .pending{
            color:orange;
            text-transform: capitalize;
        }
        .processing{
            color:yellow;
            text-transform: capitalize;
        }
        .delivered{
            color:green;
            text-transform: capitalize;
        }
        .decline{
            color:red;
            text-transform: capitalize;
        }
    </style>
    <div class="body">
        {!! $grid !!}
    </div>
@endsection
