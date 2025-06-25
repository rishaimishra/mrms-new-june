@extends('admin.layout.main')


<style>
    p>img{
        width: 100%;
        height: auto:
    }
</style>
@section('content')
@php
use Carbon\Carbon;
@endphp
<div class="card">
    <div class="header bg-orange">
        <div class="row">
            <div class="col-md-3">
                Newslatter details
            </div>
        </div>
    </div>
    <div class="body">
        <div class="row">
            <div class="col-sm-12">
                <label for="" style="font-size: 30px;">Headline</label>
                <p style="font-size: 40px;">{{ $hd->headline }}</p>
            </div>
           
            <div class="col-sm-12">
                <label for="" style="font-size: 30px;">Headline Image</label>
                <br>
                @if ($hd->headline_image)
                    
               
                <img style="width:100%;height:100%;" src="{{ asset('storage/'.$hd->headline_image) }}" alt="">
                @endif
            </div>
            <div class="col-sm-12">
                {{--  <label for="" style="font-size: 30px;">Headline Description</label>  --}}
                <p style="font-size: 20px;">{{ $hd->headline_description }}</p>
            </div>
            <div class="col-sm-12">
                {{--  <label for="" style="font-size: 30px;">Headline Description</label>  --}}
                <label for="" style="font-weight:bold;color:gray;">Editor</label> : <p style="font-size: 20px;margin-bottom:0px;display:inline-block;">{{ $hd->editor }}</p>
                
                <p style="font-size: 20px;margin-bottom:0px;">{{ Carbon::parse($hd->created_at)->format('d F Y') }}</p>
                <p>{{ Carbon::parse($hd->created_at)->diffForHumans() }}</p>
            </div>
            <div class="col-sm-12">
                <label for="" style="font-size: 30px;">Story</label>
                <p class="story_class">{!! $hd->story_board !!}</p>
            </div>
            {{--  <div class="col-sm-4">
                <label for="">User</label>
                <p>{{ $hd->user_id ? $hd->user_id->first_name : 'No user found' }}</p>
            </div>  --}}
          
        </div>
        {{--  <div class="row">
            <p style="margin-left: 15px;"><label for="">Story Images</label></p>
            
            @foreach ($hd['HeadingImages'] as $s_images)
            <div class="col-md-4">
                <img style="width:100px;height:100px;" src="{{ asset('storage/'.$s_images->images) }}" alt="">
            </div>
            @endforeach
            
            
        </div>  --}}
    
    </div>
</div>
@stop
@push('scripts')

    {{--<script src="{{ url('admin/plugins/morrisjs/morris.js') }}"></script>--}}
    {{--<script src="{{ url('admin/js/pages/charts/morris.js') }}"></script>--}}
    <script src="{{ url('admin/js/pages/app.js') }}"></script>
@endpush

