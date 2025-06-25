@extends('admin.layout.main')

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="header">
                    <h2>
                        Real Estate Filters
                    </h2>
                </div>
                <div class="body">
                    {!! Form::open(['method' => 'get']) !!}
                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>User Name</label>
                                    <input type="text" class="form-control" value="{{ request('user') }}" name="user">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Real Estate Name</label>
                                    <input type="text" class="form-control" value="{{ request('realEstate') }}" name="realEstate">
                                </div>
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
                    <a href="{{ route('admin.real-estate-report.index') }}" class="btn-lg btn btn-default">Clear Filter</a>

                    {!! Form::close() !!}
                </div>
            </div>



            <div class="card">
                <div class="header">
                    <h2>
                        Real Estate Listing
                    </h2>
                </div>


                <div class="body">
                    <div class="row">

                        @if ($realestateInterestedUser->count())
                            <table class="table">
                                <tbody>
                                <th>User</th>
                                <th>Real Estate</th>
                                <th>Created At</th>
                                </tbody>
                                <tbody>

                                @foreach($realestateInterestedUser as $user)


                                        <tr>
                                            <td><a href="{{ route('admin.user.show', $user->user_id) }}" target="_blank">{{ $user->user_name }}</a></td>
                                            <td><a href="{{ route('admin.real-estate.edit', $user->real_estates_id) }}" >{{ $user->real_estates_name }}</a></td>
                                            <td>{{ \Carbon\Carbon::parse($user->created_at)->format('Y M, d H:i:s') }}</td>
                                        </tr>

                                @endforeach
                                </tbody>
                            </table>

                            {!! $realestateInterestedUser->links() !!}
                        @else
                            <div class="alert alert-info">No result found.</div>
                        @endif
                    </div>

                </div>


            </div>


        </div>

    </div>
@endsection

@push('scripts')

@endpush
