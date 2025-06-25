@extends('admin.layout.grid')

@section('grid-title')
<h2> Areas </h2>

@endsection
@section('grid-actions')
    @role('admin')
        <a href="{{ route('admin.address.create') }}" class="btn btn-sm btn-primary">Create New</a>
    @endrole
@endsection
@section('grid-content')
    @include('admin.layout.partial.alert')
    <div class="body">
        <div class="body">
            <div class="row">
                @if ($areas->count())
                <table class="table">
                    <tbody>

                        <th>Ward Number</th>
                        <th>Constituency</th>
                        <th>District</th>
                        <th>Province</th>
                        <th>Created at</th>

                        <th>Action</th>
                    </tbody>
                    <tbody>

                        @foreach($areas as $area)
                        <tr>

                            <td><a href="{{ route('admin.address.show', $area->id) }}" style="color: red;">{{ $area->ward_number }}</a></td>
                            <td>{{ $area->constituency }}</td>
                            <td>{{ $area->district }}</td>
                            <td>{{ $area->province }}</td>
                            <td>{{ \Carbon\Carbon::parse($area->created_at)->format('Y M, d') }}</td>

                            <td>

                                <a href="{{route('admin.address.edit', [$area->id]) }}" ><i class="material-icons">edit</i></a>

                                {!! Form::open(['route' => ['admin.address.destroy', $area->id], 'method' => 'DELETE','class'=>'delete']) !!}
                                {{ Form::button('<i class="material-icons">delete</i>', ['type' => 'submit', 'class' => 'btn btn-warning btn-sm'] )  }}

                                {!! Form::close() !!}

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {!! $areas->links() !!}
                @else
                <div class="alert alert-info">No result found.</div>
                @endif
            </div>


            <script>
                $(".delete").on("submit", function(){
                    return confirm("Are you sure?");
                });
            </script>
    </div>


@endsection


