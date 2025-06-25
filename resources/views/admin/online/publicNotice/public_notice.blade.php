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
      height: 140px;
      border: 1px solid #aaaaaa;
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
            <h2>Add Notice</h2>
        </div>
        <form action="{{ route('admin.notice-store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="border_cus" style="margin-top: 20px;">
                <div class="row">
                    <div class="col-md-2"><label for="">Entity acronym</label></div>
                    <div class="col-md-10">
                        <textarea rows="1" name="acronym" class="form-control"></textarea>
                        {!! $errors->first('description', '<span class="error">:message</span>') !!}
                    </div>
                </div>
               
            </div>
            <div class="border_cus" style="border-bottom-width: 0;">
                <div class="row">
                 
                  <div class="col-md-12">
                    <div class="row">
                      
                      <div class="col-md-2">
                        <p style="font-weight: 700;">Notice Name</p>
                      </div>
                      <div class="col-md-10">
                        <textarea rows="1"  class="form-control" name="notice" placeholder="enter notice here ......." ></textarea>
                          {!! $errors->first('notice', '<span class="error">:message</span>') !!}
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <div class="border_cus" style="margin-top: 20px;">
                <div class="row">
                    <div class="col-md-2"><label for="">Description</label></div>
                    <div class="col-md-10">
                        <textarea name="description" class="form-control"></textarea>
                        {!! $errors->first('description', '<span class="error">:message</span>') !!}
                    </div>
                </div>
               
            </div>
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-2" style="margin-left: 10px;">
                    <p style="font-weight: 700;">Upload Notice One Pager</p>
                </div>
                <div class="col-md-8">
                    <input type="file" name="one_page" accept="image/*">
                   
                </div>
            </div>
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-2" style="margin-left: 10px;">
                    <p style="font-weight: 700;">Upload Notice Slides</p>
                </div>
                <div class="col-md-8">
                    <input type="file" id="images" name="images[]" multiple accept="image/*">
                    <div id="imagePreview"></div>
                </div>
            </div>
           
            <div class="row" style="margin-top: 30px;text-align:end;margin-right:30px; padding-bottom: 20px;">
                <button type="submit" class="btn btn-primary ecp_submit">SUBMIT</button>
                <button type="button" class="btn btn-success ecp_publish">Publish</button>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="header">
            <h2>
                Notice List
            </h2>
          
        </div>
        <div class="body">
            <div class="row">
            <table class="table">
                <tbody>
                    <tr>
                        <th>ID</th>
                        <th>Notice</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </tbody>
                <tbody>
                  @foreach ($dstv as $notice)
                  <tr>
                    <td>{{ $notice->id }}</td>
                    <td>{{ $notice->notice }}</td>
                    <td>{!! $notice->description !!}</td>
                    <td>
                        {{--  <a href="{{ route('admin.notice-edit', ['id' => $notice->id]) }}" class="btn btn-primary">View</a>  --}}
                        <a href="{{ route('admin.notice-delete', ['id' => $notice->id]) }}" class="btn btn-danger">Delete</a>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        const imageInput = document.getElementById('images');
        const imagePreview = document.getElementById('imagePreview');
        const existingFiles = new Set(); // Track files by name to prevent duplicates
    
        if (imageInput) {
            imageInput.addEventListener('change', function(event) {
                const files = event.target.files;
    
                Array.from(files).forEach(file => {
                    // Skip if the file is already previewed
                    if (!file.type.startsWith('image/') || existingFiles.has(file.name)) return;
                    
                    existingFiles.add(file.name); // Add new file to the set
                    
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        // Create a container for the image and delete button
                        const imgContainer = document.createElement('div');
                        imgContainer.style.position = 'relative';
                        imgContainer.style.display = 'inline-block';
                        imgContainer.style.margin = '10px';
    
                        // Create the image element
                        const imgElement = document.createElement('img');
                        imgElement.src = e.target.result;
                        imgElement.style.width = '100px';
                        imgElement.style.height = '100px';
                        imgElement.style.objectFit = 'cover';
                        imgContainer.appendChild(imgElement);
    
                        // Create the delete (cross) button
                        const deleteButton = document.createElement('span');
                        deleteButton.innerHTML = '&times;'; // Cross icon
                        deleteButton.style.position = 'absolute';
                        deleteButton.style.top = '5px';
                        deleteButton.style.right = '5px';
                        deleteButton.style.cursor = 'pointer';
                        deleteButton.style.fontSize = '20px';
                        deleteButton.style.color = '#ff0000';
                        deleteButton.style.fontWeight = 'bold';
    
                        // Add click event to remove the image preview
                        deleteButton.addEventListener('click', function() {
                            imgContainer.remove();
                            existingFiles.delete(file.name); // Remove file from set
                        });
    
                        imgContainer.appendChild(deleteButton);
                        imagePreview.appendChild(imgContainer);
                    };
    
                    reader.readAsDataURL(file);
                });
            });
        }
    });
    
</script>
<!-- #2196f3 -->