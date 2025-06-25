@extends('admin.layout.main')

@section('content')
    <div class="card">
        <div class="header clearfix">
            <h2 class="pull-left">
                System User
            </h2>

        </div>
        @include('admin.layout.partial.alert')
    <div class="body">
        <div class="row">
            @if ($users->count())
                <table class="table">
                    <tbody>
                    <th>Name</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Action</th>
                    </tbody>
                    <tbody>

                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td style="text-transform: capitalize">{{ $user->getRoleNames()[0] }}</td>

                            <td>{{ \Carbon\Carbon::parse($user->created_at)->format('Y M, d') }}</td>
                            <th><a href="{{ route('admin.system-user.show', $user->id) }}">Edit</a> | <a href="{{ route('admin.system-user.delete', $user->id) }}">Delete</a></th>

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
        </div>

@endsection
