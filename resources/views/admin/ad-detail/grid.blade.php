@extends('admin.layout.grid')
<link href="https://vjs.zencdn.net/8.16.1/video-js.css" rel="stylesheet" />
@section('grid-title')
    <h2> Advertisement Management </h2>
@endsection

@section('grid-actions')
    @role('admin')
    <a href="{{ route('admin.ad-detail.create') }}" class="btn btn-sm btn-primary">Create New</a>
    @endrole
@endsection
<!-- 
@section('grid-content')



    <div class="body">
        {!! $grid !!}
    </div>

    <script src="https://vjs.zencdn.net/8.16.1/video.min.js"></script>
    <script src="https://unpkg.com/videojs-http-streaming@3.15.0/dist/videojs-http-streaming.js"></script>
@endsection -->
