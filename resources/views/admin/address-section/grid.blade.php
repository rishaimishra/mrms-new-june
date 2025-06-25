@extends('admin.layout.grid')

@section('grid-title')
<h2> Section </h2>

@endsection
@section('grid-actions')
    @role('admin')
        <a href="{{ route('admin.address-area.create') }}" class="btn btn-sm btn-primary">Create New</a>
    @endrole
@endsection
@section('grid-content')
    <div class="body">
        <div class="body">
            <div class="row">
                @if ($addressSection->count())
                <table class="table">
                    <tbody>
                        <th>Area Name</th>
                        <th>Ward Number</th>
                        <th>Created at</th>

                        <th>Action</th>
                    </tbody>
                    <tbody>

                        @foreach($addressSection as $addressSec)
                        <tr>
                            <td>{{ $addressSec->name }}</td>
                            <td><a href="{{ route('admin.address.show', $addressSec->address_id) }}" style="color: red;">{{ $addressSec->address->ward_number }}</a></td>

                            <td>{{ \Carbon\Carbon::parse($addressSec->created_at)->format('Y M, d') }}</td>

                            <td>

                                <a href="{{route('admin.address-section.edit', [$addressSec->id]) }}" ><i class="material-icons">edit</i></a>

                                {!! Form::open(['route' => ['admin.address-section.destroy', $addressSec->id], 'method' => 'DELETE','class'=>'delete']) !!}
                                {{ Form::button('<i class="material-icons">delete</i>', ['type' => 'submit', 'class' => 'btn btn-warning btn-sm'] )  }}

                                {!! Form::close() !!}

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {!! $addressSection->links() !!}
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


