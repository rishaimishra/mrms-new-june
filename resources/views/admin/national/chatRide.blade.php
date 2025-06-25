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
            <h2>Chat a ride</h2>
            <button class="btn btn-primary">Add new</button>
        </div>
        <div class="body">
            <div class="row">
                <table class="table ">
                    <tbody>
                        <th>User ID</th>
                        <th>Role Name</th>
                        <th>test fee</th>
                        <th>test refferal percentage</th>
                        <th>test fee</th>
                        <th>test refferal percentage</th>
                        <th>test fee</th>
                        <th>test refferal percentage</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tbody>
                    <tbody>
                            <tr>
                                <td>1</td>
                                <td>test</td>
                                <td>test</td>
                                <td>test</td>
                                <td>test</td>
                                <td>test</td>
                                <td>test</td>
                                <td>test</td>
                                <td>test</td>
                                <td>
                                    <a href="#"><i class="material-icons">edit</i></a>
                                    <a href="#"><i class="material-icons">delete</i></a>
                                   </td>
                            </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop