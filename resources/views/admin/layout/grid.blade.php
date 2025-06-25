@extends('admin.layout.main')

@section('content')

    @include('admin.layout.partial.alert')

    <div class="card">
        <div class="header clearfix">
            <div class="pull-left">
                @yield('grid-title')
            </div>
            <div class="pull-right">
                @yield('grid-actions')
            </div>
        </div>
        <div class="table-responsive">
            @yield('grid-content')
        </div>
    </div>
@endsection


