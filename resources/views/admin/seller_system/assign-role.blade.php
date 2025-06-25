@extends('admin.layout.main')

@section('content')


<div class="container">
    <h2>Assign Role to User</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.assign.sellerrole') }}" method="POST" class="p-4 bg-light rounded shadow-sm">
    @csrf

    <style>
    /* Apply margin-left to the span inside the Bootstrap Select dropdown */
    .bootstrap-select .dropdown-menu li a span {
        margin-left: 20px;
    }
    </style>

    <!-- Mobile Number Selection -->
    <div class="form-group mb-3">
        <label for="user_id" class="form-label">Select Mobile Number</label>
        <select name="user_id" id="user_id" class="form-control selectpicker" data-live-search="true" required>
            <option value="">-- Select User --</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->mobile_number }} - {{ $user->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Role Selection -->
    <div class="form-group mb-4">
        <label for="seller_role" class="form-label">Assign Role</label>
        <select name="seller_role" id="seller_role" class="form-control" required>
            <option value="" disabled selected>-- Select Role --</option>
            <option value="Manager">Manager</option>
            <option value="Supervisor">Supervisor</option>
            <option value="Agent">Agent</option>
        </select>
    </div>

    <!-- Submit Button -->
    <div class="form-group text-center">
        <button type="submit" class="btn btn-primary w-50">Assign Role</button>
    </div>
</form>


    <div class="container">
        <h2>Assigned Roles to Users</h2>

        @if($users->isEmpty())
            <p>No users found with mobile numbers.</p>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile Number</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($seller_users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->mobile_number }}</td>
                            <td>{{ $user->seller_role }}</td>
                           
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>




@endsection