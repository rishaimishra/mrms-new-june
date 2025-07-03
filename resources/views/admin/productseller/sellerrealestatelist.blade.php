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
            <h2>My Real Estate list</h2>
          
        </div>

        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
        @endif
        <div class="body">
            <div class="row">
                <table class="table ">
                <thead>
            <tr>
                <!-- <th>ID</th> -->
                <th>Name</th>
                <th>Title</th>
                <th>About</th>
               
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>

                    <tbody>
                    @foreach ($MyAutos as $category)
                    <tr>
                   
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->title }}</td>

                    <td>{{ $category->about }}</td>
                    
                    <td>{{ $category->created_at }}</td>
                  

                    <td>
                        <div class="table-data-feature">
                        <button type="button" onclick="window.location.href='{{ route('admin.edit-property-seller', ['id' => $category->id]) }}'" class="item" data-toggle="tooltip" data-placement="top" title="Edit" data-original-title="Edit">
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