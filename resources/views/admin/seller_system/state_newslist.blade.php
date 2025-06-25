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
       .slimScrollDiv{
            height: 484px !important;
       }
       .list{
            height: 484px !important;
       }
</style>
<style type="text/css">
      

      
    .p_2 {
        padding: 20px;
    }

    .img_pre {
        width: 100px;
        height: 100px;
        border: 1px dashed gray;
        border-radius: 10px;
        margin-top: 10px;
        text-align: center;
    }

    .border_cus {
        border: 1px solid rgb(210, 210, 210);
        padding: 10px;
    }

    #imgPreview img {
        height: 95px;
        width: 95px;
        border-radius: 10px;
    }
    #imgPreview2 img {
        height: 95px;
        width: 95px;
        border-radius: 10px;
    }
    #imgPreview3 img {
        height: 95px;
        width: 95px;
        border-radius: 10px;
    }
    .ecp_submit{
        background: #0070c0 !important;
        border-radius: 5px !important;
        font-size: 16px !important;
        color: white !important;
        font-weight: bold !important;
        padding: 7px 40px !important;
        margin-right: 20px;
    }
    .ecp_publish{
        background: #15e12d !important;
        border-radius: 5px !important;
        font-size: 16px !important;
        color: white !important;
        font-weight: bold !important;
        padding: 7px 40px !important;
    }
</style>
<?php
$subscription = [
    ''  => 'Select subscription',
    0.00 => '0.00',
    10.00 => '10.00',
    15.00 => '15.00',
    25.00 => '25.00',
    50.00 => '50.00',
    100.00 => '100.00',
    250.00 => '250.00',
    500.00 => '500.00',
    750.00 => '750.00',
    1000.00 => '1000.00',
    1500.00 => '1500.00',
    2500.00 => '2500.00',
    5000.00 => '5000.00',
];
$ref_fee = [
    ''  => 'Select percentage',
    0.00 => '0.00',
    5.00 => '5.00',
    7.50 => '7.50',
    10.00 => '10.00',
    12.50 => '12.50',
    15.00 => '15.00',
    17.50 => '17.50',
    20.00 => '20.00',
    25.00 => '25.00',
];
$users = [
    ''  => 'Select Users',
    1 => 'Bilal',
];
$role = [
    ''  => 'Select Role',
    'business_owner' => ' DSTV - Business Owner',
    'business_partner' => ' Business Partner (For Businesses)',
    'dealer' => ' Dealer',
    'agent' => ' Agent',
];
?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
        <div class="header">
            <h2>Add State News</h2>
        </div>
        <form action="{{ route('admin.seller.newssubscription-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="border_cus">
                <div class="row">
                    <div class="col-md-2"><label for="">HEADLINE</label></div>
                    <div class="col-md-10">
                        <textarea rows="2"  class="form-control" name="headline" placeholder="enter headline here ......." ></textarea>
                        {!! $errors->first('headline', '<span class="error">:message</span>') !!}
                    </div>
                </div>
                <div class="row"style="margin-top:10px;">
                    <div class="col-md-2"><label for="">Editor name</label></div>
                    <div class="col-md-10">
                        {{--  <textarea  class="form-control" name="story_board" placeholder="enter news story here .......">  --}}
                        <textarea rows="1"  class="form-control" name="editor_name" placeholder="enter editor name here ......."></textarea>
                        {!! $errors->first('editor_name', '<span class="error">:message</span>') !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="">HEADLINE PHOTO</label>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <div class="img_pre" id="imgPreview">

                        </div>
                        <input type="file" name="headline_image" id="fileInput" style="display: none;">
                        <label for="fileInput" id="label" style="margin-top: 20px;color:#2196F3;cursor: pointer;">Upload
                            Cover Photo</label>
                            {!! $errors->first('headline_image', '<span class="error">:message</span>') !!}
                    </div>

                </div>
                <div class="row"style="margin-top:10px;">
                    <div class="col-md-2"><label for="">Headline Description</label></div>
                    <div class="col-md-10">
                        {{--  <textarea  class="form-control" name="story_board" placeholder="enter news story here .......">  --}}
                        <textarea rows="2"  class="form-control" name="headline_description" placeholder="enter headline description here ......."></textarea>
                        {!! $errors->first('headline_description', '<span class="error">:message</span>') !!}
                    </div>
                </div>
            </div>
            <div class="border_cus" style="margin-top: 20px;">
                <div class="row">
                    <div class="col-md-2"><label for="">STORYBOARD</label></div>
                    <div class="col-md-10">
                        {{--  <input type="text" class="form-control" name="story_board" placeholder="enter news story here .......">  --}}
                        <textarea name="story_board" id="editor"></textarea>
                        {!! $errors->first('story_board', '<span class="error">:message</span>') !!}
                    </div>
                </div>
               
            </div>
            <div class="row" style="margin-top: 30px;text-align:end;margin-right:30px;">
                <button type="submit" class="btn btn-primary ecp_submit">SUBMIT</button>
                <button type="button" class="btn btn-success ecp_publish">Publish</button>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="header">
            <h2>
                News List
            </h2>
          
        </div>
        <div class="body">
            <div class="row">
            <table class="table">
                <tbody>
                    <tr>
                        <th>ID</th>
                        <th>Headline</th>
                        <th>Editor Name</th>
                        <th>Headline Description</th>
                        <th>Action</th>
                    </tr>
                </tbody>
                <tbody>
                  @foreach ($dstv as $headline)
                  <tr>
                    <td>{{ $headline->id }}</td>
                    <td>{{ $headline->headline }}</td>
                    <td>{{ $headline->editor_name }}</td>
                    <td>{{ $headline->headline_description }}</td>
                    <td>
                        <a href="{{ route('admin.seller.newssubscription-edit', ['id' => $headline->id]) }}" class="btn btn-primary">View</a>
                        <a href="{{ route('admin.seller.newssubscription-edit-news', ['id' => $headline->id]) }}" class="btn btn-warning">Edit</a>
                        <a href="{{ route('admin.seller.newssubscription-delete', ['id' => $headline->id]) }}" class="btn btn-danger">Delete</a>
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
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize CKEditor after the DOM is fully loaded
        CKEDITOR.replace('editor', {
            filebrowserUploadUrl: "{{ route('admin.ckeditor.upload', ['_token' => csrf_token()]) }}",
            filebrowserUploadMethod: 'form'
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {


        document.getElementById('fileInput').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    const imgPreview = document.getElementById('imgPreview');
                    imgPreview.innerHTML = ''; // Clear any existing content
                    imgPreview.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        });


    });
    document.addEventListener('DOMContentLoaded', function() {


        document.getElementById('fileInput2').addEventListener('change', function() {

            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    const imgPreview = document.getElementById('imgPreview2');
                    imgPreview.innerHTML = ''; // Clear any existing content
                    imgPreview.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        });


    });
    document.addEventListener('DOMContentLoaded', function() {


        document.getElementById('fileInput3').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    const imgPreview = document.getElementById('imgPreview3');
                    imgPreview.innerHTML = ''; // Clear any existing content
                    imgPreview.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        });


    });
</script>