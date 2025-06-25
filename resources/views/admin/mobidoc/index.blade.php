@extends('admin.layout.edit')

@section('content')
<style>
    .bootstrap-select.btn-group .dropdown-menu{
           width:100% !important;
       }
       .bootstrap-select .bs-searchbox .form-control, .bootstrap-select .bs-actionsbox .form-control, .bootstrap-select .bs-donebutton .form-control{
           margin-left:0px !important;
       }
       .bootstrap-select .bs-searchbox:after{
           display: none;
       }
       .bootstrap-select.btn-group .dropdown-toggle .caret{
           left: 0px;
       }
       #images{
           width:100%;
       }
</style>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    
    <div class="card">
        <div class="header" style="display: flex;justify-content:space-between;">
            <h2>Mobi Doc</h2>
            <a href="{{ route('admin.movie_doc.create') }}" class="btn btn-primary">Create New Category</a>

        </div>
        <div class="body">
            <div class="row">
                <table class="table ">
                <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Parent</th>
                <th>Sequence</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>

                    <tbody>
                    @foreach ($categories as $category)
                    <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>Mobi-doc</td>
                    <td>{{$category->sequence}}</td>
                    <td>{{ $category->created_at }}</td>
                    <td>{{ $category->updated_at }}</td>
                    <td>
                        <div class="table-data-feature">
                            <button type="button" onclick="window.location.href='{{ route('admin.movie_doc.edit', ['id' => $category->id]) }}'" class="item" data-toggle="tooltip" data-placement="top" title="Edit" data-original-title="Edit">
                                <i class="material-icons">remove_red_eye</i>
                            </button>
                        </div>
                    </td>
                    </tr>
                    @endforeach
                            
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop