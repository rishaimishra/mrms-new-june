@extends('admin.layout.main')

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="header">
                    <h2>
                        Digital Addresses Filters
                    </h2>
                </div>
                <div class="body">
                    {!! Form::open(['method' => 'get']) !!}
                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Area</label>
                                    <input type="text" class="form-control" value="{{ request('area') }}" name="area">
                                </div>
                            </div>
                        </div>


                        <div class="col-sm-3">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Chiefdom</label>
                                    <input type="text" class="form-control" value="{{ request('chiefdom') }}" name="chiefdom">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Section</label>
                                    <input type="text" class="form-control" value="{{ request('section') }}" name="section">
                                </div>
                            </div>
                        </div>




                        {{--<div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::materialSelect('Year', 'year', yearFilter(),  request('year'), $errors->first('year')) !!}
                            </div>
                        </div>--}}
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::materialSelect('Filter By', 'dwm', dwmFilter(),  request('dwm'), $errors->first('dwm')) !!}
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::checkbox('download', 1, request('download'), ['class' => 'filled-in chk-col-blue', 'id' => 'download']) !!}
                                <label for="download">Download Excel</label>
                            </div>
                        </div>



                    </div>

                    <button class="btn btn-primary waves-effect btn-lg" type="submit">Filter</button>
                    <a href="{{ route('admin.digitl-address.index') }}" class="btn-lg btn btn-default">Clear Filter</a>

                    {!! Form::close() !!}
                </div>
            </div>

            <div class="card">
                <div class="header">
                    <h2>
                        Digital Addresses Graph
                    </h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div id="userGraph" style="height: 400px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="header">
                    <h2>
                        Digital Addresses Listing
                    </h2>
                </div>


                <div class="body">
                    <div class="row">

                        @if ($digitalAddress->count())
                            <table class="table">
                                <tbody>
                                <th>User</th>
                                <th>Area</th>
                                <th>Chiefdom</th>
                                <th>Section</th>
                                <th>Created At</th>
                                </tbody>
                                <tbody>
                                @foreach($digitalAddress as $digitalAdd)
                                    <tr>
                                        <td><a href="{{ route('admin.user.show', $digitalAdd->user_id) }}" target="_blank">{{ $digitalAdd->user->name }}</a></td>
                                        <td><a href="{{ route('admin.digitl-address.show', $digitalAdd->id) }}" >{{ $digitalAdd->addressArea->name }}</a></td>

                                        <td>{{ $digitalAdd->addressChiefdom->name }}</td>
                                        <td>{{ $digitalAdd->addressSection->name }}</td>

                                        <td>{{ \Carbon\Carbon::parse($digitalAdd->created_at)->format('Y M, d') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            {!! $digitalAddress->links() !!}
                        @else
                            <div class="alert alert-info">No result found.</div>
                        @endif
                    </div>

                </div>


            </div>
            {{--<div class="card">
                <div class="header clearfix">
                    <h2 class="pull-left">
                        Digital Addresses
                    </h2>
                </div>
                <div class="body">
                --}}{{--@grid([
                    'dataProvider' => $dataProvider,
                    'rowsPerPage' => 15,
                    'columns' => [
                        'addressArea.name',
                        'addressChiefdom.name',
                        'addressSection.name',
                        'address.ward_number',
                        'address.constituency',
                        'address.district',
                        'address.province',
                        [
                            'class' => 'actions',
                            'value' => [
                                'view:/sl-admin/digitl-address/{id}',
                            ]
                        ]
                    ]
                ])--}}{{--
                </div>

            </div>--}}

        </div>

    </div>
@endsection

@push('scripts')
    <script>

        Highcharts.chart('userGraph', {
            chart: {
                type: 'spline'
            },
            title: {
                text: '{!! $title !!}'
            },
            subtitle: {
                text: '{!! $subtitle !!}'
            },
            xAxis: {
                categories: {!! count($datas) ? json_encode(array_keys($datas)) : json_encode([]) !!}
            },
            yAxis: {
                title: {
                    text: 'Counting'
                },
                labels: {
                    formatter: function () {
                        return this.value;
                    }
                }
            },
            tooltip: {
                crosshairs: true,
                shared: true
            },
            plotOptions: {
                spline: {
                    marker: {
                        radius: 4,
                        lineColor: '#666666',
                        lineWidth: 1
                    }
                }
            },
            series: [{
                name: 'DigitalAddress',
                marker: {
                    symbol: 'square'
                },
                data: {!! count($datas) ? json_encode(array_values($datas)) : json_encode([]) !!}

            }]
        });

    </script>
@endpush
