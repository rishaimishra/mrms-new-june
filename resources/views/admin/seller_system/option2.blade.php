@extends('admin.layout.main')

@section('content')

   
    <div class="card">
        <div class="header">
            <h2>
                Users
            </h2>
        </div>
        <div class="body">
            <div class="row">
            <table class="table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($names as $entry)
                        <tr>
                            <td>{{ $entry['name'] }}</td>
                            <td>{{ $entry['role'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>
    </div>
@endsection
