@extends('admin.layout.main')

@section('content')

    <div class="block-header">
        <h2>DASHBOARD</h2>
    </div>

    <!-- Widgets -->
    <div class="row clearfix">

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a href="{{ route('admin.user.index') }}" class="info-box bg-pink hover-expand-effect" data-placement="{{ route('admin.user.index') }}">
                <div class="icon">
                    <i class="material-icons">accessibility</i>
                </div>
                <div class="content">
                    <div class="text">Total User</div>
                    <div class="number count-to" data-from="0" data-to="{{ 0 }}" data-speed="15" data-fresh-interval="20">{{ $User }}</div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a href="{{ route('admin.auto-report.index') }}" class="info-box bg-cyan hover-expand-effect" data-placement="{{ route('admin.auto-report.index') }}">
                <div class="icon">
                    <i class="material-icons">person_add</i>
                </div>
                <div class="content">
                    <div class="text">Autos Interested User</div>
                    <div class="number count-to" data-from="0" data-to="{{ 0 }}" data-speed="15" data-fresh-interval="20">{{ $interestedAutos }}</div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a href="{{ route('admin.real-estate-report.index') }}" class="info-box bg-teal hover-expand-effect" data-placement="{{ route('admin.real-estate-report.index') }}">
                <div class="icon">
                    <i class="material-icons">person_add</i>
                </div>
                <div class="content">
                    <div class="text">Real Estate Interested User</div>
                    <div class="number count-to" data-from="0" data-to="{{ 0 }}" data-speed="15" data-fresh-interval="20">{{ $interestedRealEstate }}</div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <a href="{{ route('admin.real-estate-report.index') }}" class="info-box bg-teal hover-expand-effect" data-placement="{{ route('admin.real-estate-report.index') }}">
                <div class="icon">
                    <i class="material-icons">person_add</i>
                </div>
                @php
                $lastTransaction = $transactions->last(); // Get last transaction
                @endphp
                <div class="content">
                    <div class="text">Wallet balance</div>
                    <div class="number count-to" data-from="0" data-to="{{ 0 }}" data-speed="15" data-fresh-interval="20">{{$lastTransaction->balance}}</div>
                </div>
            </a>
        </div>

      



    </div>

    <style>
        .info-box-4{
            cursor: pointer !important;
        }
        a{
            text-decoration: none !important;
        }
    </style>

@endsection

@push('scripts')

    {{--<script src="{{ url('admin/plugins/morrisjs/morris.js') }}"></script>--}}
    {{--<script src="{{ url('admin/js/pages/charts/morris.js') }}"></script>--}}
    <script src="{{ url('admin/js/pages/app.js') }}"></script>
@endpush
