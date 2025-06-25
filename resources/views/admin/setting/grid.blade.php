@extends('admin.layout.main')

@section('content')
    <style>
        .bt{ margin-right: 4px; float: left; }
    </style>
    <div class="card">
        <div class="header">
            <h2>
                Setting

            </h2>
        </div>


        <div class="body">
            <div class="row">

                @if ($results->count())
                    <table class="table">
                        <tbody>
                        <th>Option Name</th>
                        <th>Option Value</th>
                        <th>Created At</th>
                        <th>Action</th>
                        </tbody>
                        <tbody>
                        @foreach($results as $result)

                            <tr>
                                <td>{{ $result->option_name?? null }}</td>
                                <td>{{  $result->option_value?? null  }}</td>

                                <td>{{ \Carbon\Carbon::parse($result->created_at)->format('Y M, d') }}</td>
                                <td>
                                    <a href="{{ route('admin.setting.edit',[optional($result)->id]) }}" title="view record" class="btn btn-outline-primary btn-sm grid-row-button show_modal_form bt">
                                        <i class="material-icons">remove_red_eye</i>
                                    </a>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {!! $results->links() !!}
                @else
                    <div class="alert alert-info">No result found.</div>
                @endif
            </div>

        </div>
        <script>
            $(".delete").on("submit", function(){
                return confirm("Are you sure?");
            });
        </script>

    </div>
@endsection