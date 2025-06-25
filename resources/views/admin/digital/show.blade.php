@extends('admin.layout.main')

@section('content')
    {{--{{dd($digitalAddresses->type)}}--}}
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="header clearfix">
                    <h2 class="pull-left">
                        Digital Addresses
                    </h2>
                </div>
                <div class="body">
                    <div class="row">
                        {{--<div class="col-sm-6 col-md-4">
                            @if($user->avatar)
                                <img width="150" height="150" src="{{ asset('storage/' . $user->avatar) }}" alt="" class="img-responsive float-right"/>
                            @else
                                <img width="150" height="150" src="{{ asset('storage/avatar.png') }}" alt="" class="img-responsive float-right"/>
                            @endif
                        </div>--}}
                        <div class="col-sm-6 col-md-8">
                            <table class="table">
                                <tr>
                                    <td>Tag</td>
                                    <td>{{ $digitalAddresses->type }}</td>
                                </tr>
                                <tr>
                                    <td>Area Name</td>
                                    <td>{{ $digitalAddresses->addressArea->name }}</td>
                                </tr>
                                <tr>
                                    <td>Ward Number</td>
                                    <td>{{ $digitalAddresses->address->ward_number }}</td>
                                </tr>
                                <tr>
                                    <td>Constituency</td>
                                    <td>{{ $digitalAddresses->address->constituency }}</td>
                                </tr>
                                <tr>
                                    <td>Section</td>
                                    <td>{{ $digitalAddresses->addressSection->name }}</td>
                                </tr>
                                <tr>
                                    <td>Chiefdom</td>
                                    <td>{{ $digitalAddresses->addressChiefdom->name }}</td>
                                </tr>
                                <tr>
                                    <td>District</td>
                                    <td>{{ $digitalAddresses->address->district }}</td>
                                </tr>
                                <tr>
                                    <td>Province</td>
                                    <td>{{ $digitalAddresses->address->province }}</td>
                                </tr>
                                <tr>
                                    <td>Digital Addresses</td>
                                    <td>{{ $digitalAddresses->digital_addresses }}</td>
                                </tr>
                                <tr>
                                    <td>Open Location Code</td>
                                    <td>{{ $digitalAddresses->open_location_code }}</td>
                                </tr>




                            </table>
                        </div>

                        <div class="col-sm-6 col-md-12">
                            <div id="map" style="width: 100%; height: 500px;"></div>

                        </div>
                    </div>
                </div>
            </div>



        </div>

    </div>
    <script>

        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                center: new google.maps.LatLng({!! $digitalAddresses->latitude !!},{!! $digitalAddresses->longitude !!}),
                zoom: 16
            });
            var infoWindow = new google.maps.InfoWindow;
            var uluru = {lat: {!! $digitalAddresses->latitude !!}, lng: {{$digitalAddresses->longitude}} };
            var marker = new google.maps.Marker({position: uluru, map: map});


        }




    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA3sNZ6dV-Dw26AhYdEVJWVvIvwT8Mcozg&callback=initMap">
    </script>
@endsection
