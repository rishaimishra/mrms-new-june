@extends('admin.layout.main')

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="header clearfix">
                    <h2 class="pull-left">
                        {{ $user->name }}
                         @if($user->is_edsa_agent)
                            <a href="" class="badge badge-pill badge-primary">EDSA AGENT</a>
                        @endif
                    </h2>
                    
                     <h2 style="margin-left:250px;">
                        @if($user->is_dstv_agent)
                            <a href="" class="badge badge-pill badge-primary">DSTV AGENT</a>
                        @endif
                    </h2>
                    
                    
                    @role('admin')
                    <a id="edit" class="btn btn-success btn-lg waves-effect pull-right"
                       href="{{ route('admin.user.edit', $user->id) }}">Edit</a>
                    @endrole
                </div>
                @include('admin.layout.partial.alert')
                <div class="body">
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            @if($user->avatar)
                            <img width="150" height="150" src="{{ asset('storage/' . $user->avatar) }}" alt="" class="img-responsive" style=" margin: 0 auto; "/>
                            @else
                                <img width="150" height="150" src="{{ asset('storage/avatar.png') }}" alt="" class="img-responsive" style=" margin: 0 auto; "/>
                            @endif
                        </div>
                        <div class="col-sm-6 col-md-8">
                            <table class="table">
                                <tr>
                                    <td>Name</td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td>Username</td>
                                    <td>{{ $user->username }}</td>
                                </tr>
                                <tr>
                                    <td>Mobile Number</td>
                                    <td>{{ $user->mobile_number }}</td>
                                </tr>
                                <tr>
                                    <td>Active</td>
                                    <td>{{ ($user->is_active)?"Yes":"No" }}</td>
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

            <div class="card">
                <div class="header clearfix">
                    <h2 class="pull-left">
                        Digital Addresses
                    </h2>
                </div>
                {{--<div class="body">
                @grid([
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
                ])
                </div>--}}
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
                                    <label>Section</label>
                                    <input type="text" class="form-control" value="{{ request('section') }}" name="section">
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


                        <div class="col-sm-3" style=" margin: 20px 0 10px 0; ">
                            <button class="btn btn-primary waves-effect btn-lg" type="submit">Filter</button>
                            <a href="{{ route('admin.user.show', $user->id) }}" class="btn-lg btn btn-default">Clear Filter</a>
                        </div>

                    </div>



                    {!! Form::close() !!}
                    @if ($digitalAddresses->count())
                        <table class="table">
                            <tbody>
                            <th>Area</th>
                            <th>Ward Number</th>
                            <th>Constituency</th>
                            <th>Section</th>
                            <th>Chiefdom</th>
                            <th>District</th>
                            <th>Province</th>
                            <th>Digital address</th>
                            <th>Tag</th>
                            <th>Created At</th>
                            </tbody>
                            <tbody>

                            @foreach($digitalAddresses as $address)

                                <tr>
                                    <td><a href="{{ route('admin.digitl-address.show', $address->id) }}" >{{ $address->addressArea->name }}</a></td>
                                    <td>{{ $address->address->ward_number }}</td>
                                    <td>{{ $address->address->constituency }}</td>
                                    <td>{{ $address->addressSection->name }}</td>
                                    <td>{{ $address->addressChiefdom->name}}</td>
                                    <td>{{ $address->address->district}}</td>
                                    <td>{{ $address->address->province}}</td>
                                    <td>{{ $address->digital_addresses}}</td>
                                    <td>{{ $address->type}}</td>

                                    <td>{{ \Carbon\Carbon::parse($address->created_at)->format('Y M, d') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        {!! $digitalAddresses->links() !!}
                    @else
                        <div class="alert alert-info">No result found.</div>
                    @endif
                </div>
            </div>

        </div>

    </div>
@endsection
