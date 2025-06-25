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
            <h2>Add Natioanl News</h2>
        </div>
        <form action="{{ route('admin.national-news-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="border_cus" style="border-bottom-width: 0;">
                <div class="row">
                  <div class="col-md-2">
                    <label for="">FRONTPAGE</label>
                    <div class="img_pre" id="imgPreviewCover"></div>
                    <input type="file" name="front_image" id="fileInputCover" style="display: none" />
                    <label for="fileInputCover" id="label" style="margin-top: 20px; color: #111; cursor: pointer">Upload Front Photo</label>
                    <script>
                      document.getElementById("fileInputCover").addEventListener("change", function () {
                        const file = this.files[0];
                        if (file) {
                          const reader = new FileReader();
                          reader.onload = function (e) {
                            const img = document.createElement("img");
                            img.src = e.target.result;
                            img.style.maxWidth = "100%"; // Optional: to fit the image within the div
                            img.style.height = "auto";
                  
                            const imgPreview = document.getElementById("imgPreviewCover");
                            imgPreview.innerHTML = ""; // Clear any existing content
                            imgPreview.appendChild(img);
                          };
                          reader.readAsDataURL(file);
                        }
                      });
                    </script>
                  </div>
                  <div class="col-md-10">
                    <div class="row">
                      <div class="col-md-12" style="opacity: 0;height: 30px;"><label for="">FRONTPAGE</label></div>
                      
                      <div class="col-md-2">
                        <p style="font-weight: 700;">EDITOR NAME</p>
                      </div>
                      <div class="col-md-10">
                        <textarea rows="1"  class="form-control" name="editor_name" placeholder="enter editor name here ......." ></textarea>
                          {!! $errors->first('editor_name', '<span class="error">:message</span>') !!}
                      </div>


                     
    
                      <div class="col-md-12" style="height: 15px;"></div>

                      <div class="col-md-2">
                        <p style="font-weight: 700;">HEADLINE</p>
                      </div>
                      <div class="col-md-10">
                        <textarea rows="1"  class="form-control" name="headline" placeholder="enter headline here ......." ></textarea>
                          {!! $errors->first('headline', '<span class="error">:message</span>') !!}
                      </div>
                     
                      <div class="col-md-12" style="height: 15px;"></div>
                      
                      <div class="col-md-2">
                        <p style="font-weight: 700;">HEADLINE DESCRIPTION</p>
                      </div>
                      <div class="col-md-10">
                      {{--  <textarea  class="form-control" name="story_board" placeholder="enter news story here .......">  --}}
                          <textarea rows="1"  class="form-control" name="headline_description" placeholder="enter headline description here ......."></textarea>
                          {!! $errors->first('headline_description', '<span class="error">:message</span>') !!}
                      </div>
    
                     
                    </div>
                  </div>
                </div>
                <!-- <div class="row"style="margin-top:10px;">
                    <div class="col-md-2"><label for=""></label></div>
                    <div class="col-md-10">
                        <p>Editor name</p>
                        {{--  <textarea  class="form-control" name="story_board" placeholder="enter news story here .......">  --}}
                        <textarea rows="1"  class="form-control" name="editor_name" placeholder="enter editor name here ......."></textarea>
                        {!! $errors->first('editor_name', '<span class="error">:message</span>') !!}
                    </div>
                </div> -->
                <div class="row" style="margin-top: 20px;">
                  <div class="col-md-2">
                    <div class="col-md-12" style="padding-left: 0;padding-right: 0;">
                      <label for="">COVER PAGE</label>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12" style="padding-left: 0;padding-right: 0;">
                      <div class="img_pre" id="imgPreview"></div>
                      <input type="file" name="headline_image" id="fileInput" style="display: none" />
                      <label for="fileInput" id="label" style="margin-top: 20px; color: #111; cursor: pointer">Upload Cover Photo</label>
                      {!! $errors->first('headline_image', '<span class="error">:message</span>') !!}
                    </div>                    
                  </div>
                  <div class="col-md-10" style="margin-top: 90px;">
                    <!-- <div class="row">
                      <div class="col-md-12"><label for="">Headline Description</label></div>
                      <div class="col-md-12">
                          {{--  <textarea  class="form-control" name="story_board" placeholder="enter news story here .......">  --}}
                          <textarea rows="1"  class="form-control" name="headline_description" placeholder="enter headline description here ......."></textarea>
                          {!! $errors->first('headline_description', '<span class="error">:message</span>') !!}
                      </div>
                    </div> -->
                  </div>
                </div>

                <!-- <div class="row" style="margin-top: 20px;">
                  <div class="col-md-2">
                    <div class="col-md-12" style="
                    padding-left: 0;
                    padding-right: 0;
                ">
                      <label for="">BREAKING NEWS</label>
                    </div>
                  </div>
                  <div class="col-md-10">
                    <textarea rows="1"  class="form-control" name="breaking_news" placeholder="enter headline description here ......."></textarea>
                  </div>
                </div> -->

                <!-- <form action="{{ route('admin.seller.additional_desc') }}" method="post"> -->
                                <!-- @csrf -->

                                <div class="input-group" style="display: flex; align-items: center; justify-content: start;">
                                    <!-- Green Submit Button -->
                                    <button type="button" class="btn btn-success" id="submitBreakingNews" style="white-space: nowrap;">BREAKING NEWS</button>

                                    <!-- Hidden Seller ID Field -->
                                    <input type="hidden" name="seller_id" id="seller_id" value="{{ $user_id ?? '' }}">

                                    <!-- Text Input Field for Additional Description -->
                                    <textarea name="addition_desc" id="addition_desc" class="form-control input-field" 
                                        placeholder="Maximum Eighty Characters..." 
                                        style="border: 1px solid #d1d1d1; margin-left: 10px; padding: 8px; height: auto; border-radius: 0; resize: none; overflow-y: auto;"
                                        maxlength="300" rows="2" oninput="updateCharacterCount(this.value.length);">{{$user->seller_detail->additional_information ?? ''}}</textarea>

                                        &nbsp;&nbsp;<p id="charCount">0/300</p>

                                    <script>
                                    function updateCharacterCount(count) {
                                        document.getElementById('charCount').innerText = count + '/300';
                                    }
                                    </script>

                                </div>
                            <!-- </form> -->

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
            <div class="row" style="margin-top: 30px;text-align:end;margin-right:30px; padding-bottom: 20px;">
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
                        <a href="{{ route('admin.national-news-edit', ['id' => $headline->id]) }}" class="btn btn-primary">View</a>
                        <a href="{{ route('admin.national-news-delete', ['id' => $headline->id]) }}" class="btn btn-danger">Delete</a>
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
<script>
    $(document).ready(function () {
        $('#submitBreakingNews').on('click', function () {
            // Get the values
            var sellerId = $('#seller_id').val();
            var additionDesc = $('#addition_desc').val();
    
            // AJAX request
            $.ajax({
                url: '{{ route('admin.breaking-national-news') }}', 
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ 
                    seller_id: sellerId, // Ensure this is correct
                    addition_desc: additionDesc 
                }),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                },
                success: function (data) {
                    if (data.success) {
                        alert('Breaking news submitted successfully!');
                        // Optionally, clear the textarea or perform other actions
                        $('#addition_desc').val('');
                        $('#charCount').text('0/300');
                    } else {
                        alert('Error submitting breaking news: ' + data.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('Error:', errorThrown);
                    alert('An error occurred while submitting the breaking news.');
                }
            });
        });
    });
    
</script>
<!-- #2196f3 -->