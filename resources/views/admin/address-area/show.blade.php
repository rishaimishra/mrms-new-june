@extends('admin.layout.main')

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="header clearfix">
                    <h2 class="pull-left">
                        Area View
                    </h2>
                    <a id="edit" class="btn btn-success btn-lg waves-effect pull-right"
                       href="{{ route('admin.address.edit', $area->id) }}">Edit</a>
                </div>
                <div class="body">
                    <div class="row">

                        <div class="col-sm-12 col-md-12">
                            <table class="table">
                                <tr>
                                    <th>Area Name</th>
                                    <td>{{ $area->addressArea()->pluck('name')->implode(', ') }}</td>
                                </tr>
                                <tr>
                                    <th>Ward Number</th>
                                    <td>{{ $area->ward_number }}</td>
                                </tr>
                                <tr>
                                <tr>

                                    <th>Constituency</th>
                                    <td>{{ $area->constituency }}</td>
                                </tr>
                                <tr>
                                    <th>Chiefdoms</th>
                                    <td>{{ $area->addressChiefdom()->pluck('name')->implode(', ') }}</td>
                                </tr>
                                <tr>
                                    <th>Sections</th>
                                    <td>{{ $area->addressSection()->pluck('name')->implode(', ') }}</td>
                                </tr>
                                <tr>
                                    <th>District</th>
                                    <td>{{ ($area->district) }}</td>
                                </tr>
                                <tr>
                                    <th>Province</th>
                                    <td>{{ ($area->province) }}</td>
                                </tr>


                            </table>
                        </div>

                        <div class="col-sm-6 col-md-12">
                            <table class="table">


                            </table>
                        </div>
                    </div>
                </div>
            </div>

           {{-- <div class="card">
                <div class="header clearfix">
                    <h2 class="pull-left">
                        Digital Addresses
                    </h2>
                </div>
                <div class="body">
                @grid([
                    'dataProvider' => $dataProvider,
                    'rowsPerPage' => 15,
                    'columns' => [
                        'area_name',
                        'ward_number',
                        'constituency',
                        'section',
                        'chiefdom',
                        'districe',
                        'province',
                        [
                            'class' => 'actions',
                            'value' => [
                                'view:/sl-admin/digitl-address/{id}',
                            ]
                        ]
                    ]
                ])
                </div>--}}
                {{--<div class="body">
                    @if ($digitalAddresses->count())
                        <table class="table">
                            <tbody>
                            <th>Address Name</th>
                            <th>Ward Number</th>
                            <th>Constituency</th>
                            <th>Section</th>
                            <th>Chiefdom</th>
                            <th>Districe</th>
                            <th>Province</th>

                            <th>Created At</th>
                            </tbody>
                            <tbody>

                            @foreach($digitalAddresses as $address)
                                <tr>
                                    <td>{{ $address->address_name }}</td>
                                    <td>{{ $address->ward_number }}</td>
                                    <td>{{ $address->constituency }}</td>
                                    <td>{{ $address->section }}</td>
                                    <td>{{ $address->chiefdom}}</td>
                                    <td>{{ $address->districe}}</td>
                                    <td>{{ $address->province}}</td>

                                    <td>{{ \Carbon\Carbon::parse($address->created_at)->format('Y M, d') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        {!! $digitalAddresses->links() !!}
                    @else
                        <div class="alert alert-info">No result found.</div>
                    @endif
                </div>--}}
            </div>

        </div>

    </div>
@endsection
