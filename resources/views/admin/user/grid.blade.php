@extends('admin.layout.grid')

@section('grid-title')
<h2> USERS </h2>
@endsection

@section('grid-content')


                    <div class="card">
                        <div class="header">
                            <h2>
                                Users Filters
                            </h2>
                        </div>
                        <div class="body">
                            {!! Form::open(['method' => 'get']) !!}
                            <div class="row">

                                <div class="col-sm-3">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Name</label>
                                            <input type="text" class="form-control" value="{{ request('name') }}" name="name">
                                        </div>
                                    </div>
                                </div>


                                <div class="col-sm-3">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Mobile Number</label>
                                            <input type="text" class="form-control" value="{{ request('mobile_number') }}" name="mobile_number">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Email</label>
                                            <input type="text" class="form-control" value="{{ request('email') }}" name="email">
                                        </div>
                                    </div>
                                </div>




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
                            <a href="{{ route('admin.user.index') }}" class="btn-lg btn btn-default">Clear Filter</a>

                            {!! Form::close() !!}
                        </div>
                    </div>



                    <div class="card">
                        <div class="header">
                            <h2>
                                Users Graph
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
                Users Listing
            </h2>
        </div>


            <div class="body">
                <div class="row">

                    @if ($users->count())
                        <table class="table">
                            <tbody>
                            <th>Name</th>

                            <th>Email</th>
                            <th>Mobile</th>

                            <th>Created At</th>
                            </tbody>
                            <tbody>
       
                            @foreach($users as $user)
                                <tr>
                                    <td><a href="{{ route('admin.usershow.show', $user->id) }}" >{{ $user->name }}</a></td>

                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->mobile_number }}</td>

                                    <td>{{ \Carbon\Carbon::parse($user->created_at)->format('Y M, d') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        {!! $users->links() !!}
                    @else
                        <div class="alert alert-info">No result found.</div>
                    @endif
                </div>

            </div>

        {{--<div class="body">
            <div class="row">
                @grid([
                'dataProvider' => $dataProvider,
                'rowsPerPage' => 15,
                'columns' => [
                'name',
                'username',
                'email',
                'mobile_number',
                [
                'class' => 'actions',
                'value' => [
                'view:/sl-admin/user/{id}',
                ]
                ]
                ]
                ])
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
                name: 'Users',
                marker: {
                    symbol: 'square'
                },
                data: {!! count($datas) ? json_encode(array_values($datas)) : json_encode([]) !!}

            }]
        });

    </script>
@endpush
