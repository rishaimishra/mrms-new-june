@extends('admin.layout.main')

@section('content')
<style>
    .upload-box {
  width: 150px;
  height: 150px;
  border: 1px solid #ccc;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 10px;
}

.upload-btn {
  padding: 10px 20px;
  background-color: #f1f1f1;
  border: none;
  cursor: pointer;
}

.upload-btn:hover {
  background-color: #ddd;
}

/* Modal styling */
.modal {
  display: none; /* Hidden by default */
  position: fixed; 
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

.modal-content {
  background-color: #fefefe;
  margin: 15% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
  max-width: 400px;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}

.upload {
  margin-top: 10px;
  padding: 10px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  cursor: pointer;
}

.upload:hover {
  background-color: #45a049;
}
</style>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="header clearfix">
                    <h2 class="pull-left">
                        {{ $user->name }}
                          REGISTRANT DETAILS
                         <!-- @if($user->is_edsa_agent)
                            <a href="" class="badge badge-pill badge-primary">EDSA AGENT</a>
                        @endif -->
                    </h2>
                    
                     <h2 style="margin-left:250px;">
                        @if($user->is_dstv_agent)
                            <a href="" class="badge badge-pill badge-primary">DSTV AGENT</a>
                        @endif
                    </h2>
                    
                    
                    @role('admin')
                    <a id="edit" class="btn btn-success btn-lg waves-effect pull-right"
                       href="{{ route('admin.user.edit', $user->id) }}">Edit</a>
                    @endrole
                </div>
                @include('admin.layout.partial.alert')
                <div class="body">
                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            @if($user->avatar)
                            <img width="150" height="150" src="{{ asset('storage/' . $user->avatar) }}" alt="" class="img-responsive" style=" margin: 0 auto; "/>
                            @else
                                <img width="150" height="150" src="{{ asset('storage/avatar.png') }}" alt="" class="img-responsive" style=" margin: 0 auto; "/>
                            @endif
                        </div>
                        <div class="col-sm-6 col-md-8">
                            <table class="table">
                                <tr>
                                    <td>Name</td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td>Username</td>
                                    <td>{{ $user->username }}</td>
                                </tr>
                                <tr>
                                    <td>Mobile Number</td>
                                    <td>{{ $user->mobile_number }}</td>
                                </tr>
                                <tr>
                                    <td>Active</td>
                                    <td>{{ ($user->is_active)?"Yes":"No" }}</td>
                                </tr>


                            </table>
                        </div>

                        <div class="col-sm-6 col-md-12">
                            <table class="table">


                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="header" style="display: flex;align-items:center;justify-content:space-between;">
                    <h2 class="pull-left">
                        BUSINESS DETAILS
                    </h2>
                    <div>
                        <button class="btn btn-success btn-lg waves-effect pull-right" style="margin-left:10px;">APPROVE</button>
                        <button class="btn btn-success btn-lg waves-effect pull-right">PUBLISH</button>
                    </div>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Business name</label><br>
                                            <label for="">{{ $user->seller_detail->business_name }}</label>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>TIN</label><br>
                                            <label for="">{{ $user->seller_detail->tin }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Street Number</label><br>
                                            <label for="">{{ $user->seller_detail->street_number }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                              
                               
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Street Name</label><br>
                                            <label for="">{{ $user->seller_detail->street_name }}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Area</label><br>
                                            <label for="">{{ $user->seller_detail->area }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Ward</label><br>
                                            <label for="">{{ $user->seller_detail->ward }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                               
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Section</label><br>
                                            <label for="">{{ $user->seller_detail->section }}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Chiefdon</label><br>
                                            <label for="">{{ $user->seller_detail->chiefdon }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Province</label><br>
                                            <label for="">{{ $user->seller_detail->province }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                               
                                <div class="col-sm-8">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Business Co-ordinates</label><br>
                                            <label for="">{{ $user->seller_detail->business_coordinates }}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Mobile 1</label><br>
                                            <label for="">{{ $user->seller_detail->mobile1 }}</label>
                                        </div>
                                    </div>
                                </div>
                               
                            </div>

                            <div class="row">

                            <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Mobile 2</label><br>
                                            <label for="">{{ $user->seller_detail->mobile2 }}</label>
                                        </div>
                                    </div>
                                </div>
                              
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Mobile 3</label><br>
                                            <label for="">{{ $user->seller_detail->mobile3 }}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Business Email</label><br>
                                            <label for="">{{ $user->seller_detail->business_email }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                             
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Opening Time</label><br>
                                            <label for="">{{ $user->seller_detail->opening_time }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Closing Time</label><br>
                                            <label for="">{{ $user->seller_detail->closing_time }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>


                          


                        </div>
                       <div class="col-md-3" style="
                                padding-left: 100px;
                            ">
                          <div class="form-group form-float">
                            <label>Business Logo</label><br>
                            <!-- Make the image clickable to open the file input -->
                            <img class="upload-box" id="business-logo-preview" src="{{ url('busniess_images/' . $user->seller_detail->business_logo) }}" width="150" height="150" alt="Business Logo" onclick="document.getElementById('business-logo-input').click();" style="cursor: pointer;"/>
                            
                            <form action="{{ route('admin.business.logo') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <!-- Hidden file input triggered by the image click -->
                                <input type="file" name="business_logo" class="form-control" style="display: none;" id="business-logo-input" onchange="previewLogo(event)">
                                
                                <!-- Hidden button to click when image is clicked -->
                                <button type="submit" class="btn btn-primary" style="margin-left: 10px;">Upload Logo</button>
                            </form>
                        </div>

                        <script>
                            // Preview the selected logo before upload
                            function previewLogo(event) {
                                var output = document.getElementById('business-logo-preview');
                                output.src = URL.createObjectURL(event.target.files[0]);
                                output.onload = function() {
                                    URL.revokeObjectURL(output.src) // Free memory
                                }
                            }
                        </script>



                            <div class="form-group form-float">
                                            <label>Business Reg. Image</label><br>
                                            <img src="{{ url('admin/images/registration_img.jpeg') }}" width="150" height="150" alt="User"/>
                                            <button class="btn btn-primary upload-btn mt-1" style="margin-top:10px">Download document</button>
                                        
                            </div>

                            <div class="form-group form-float">
                                    <label>Store Theme</label><br>
                                <div class="upload-box uploadBox" width="150" height="150" id="">
                                    
                                    @if(isset($specific_theme->theme_name))
                                    <img src="{{ url('storage/' .$specific_theme->theme_name) }}" width="150" height="150" alt="User"/>
                                    @else
                                    <p1>Upload Background</p1>
                                    @endif
                                </div>
                               <div class="row uploadBox" style="display: flex;justify-content: space-evenly;margin-right: auto;">
                               <button class="btn btn-warning upload-btn">Edit Theme</button>
                                
                               
                               </div>
                            </div>
                       </div>
                    </div>
                </div>
            </div>
            <div id="myModal" class="modal">
            <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="display: flex;justify-content:space-between;padding:0px;">
                <h5 class="modal-title" id="exampleModalLabel">Select a Background Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                <form id="themeForm" action="{{ route('admin.assign.sellerthemes') }}" method="POST" class="p-3 border rounded shadow-sm bg-light">
                    @csrf
                    <div class="row">
                        @foreach ($sellerThemes as $theme)
                            <div class="col-md-4 mb-3">
                                <input class="form-check-input" type="radio" name="theme_id" value="{{ $theme->id }}" id="theme_{{ $theme->id }}" required>
                                <label class="form-check-label" for="theme_{{ $theme->id }}" style="margin-top:10px;">
                                    <p>Theme {{ $theme->id }}</p>
                                    
                                </label>
                                <img src="{{ asset('storage/' . $theme->theme_name) }}" alt="Theme Image" class="img-thumbnail" width="100">
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="seller_id" value="{{ $user->id }}">
                    <button type="submit" class="btn btn-primary mt-3" style="margin-top:15px">Upload</button>
                </form>
            </div>
        </div>
    </div>
              </div>

              
            <div class="card">
                <div class="header clearfix">
                    <h2 class="pull-left">
                        ADDITIONAL INFORMATION
                    </h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Information</label><br>
                                    <p>{{ $user->seller_detail->additional_information }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="header clearfix">
                    <h2 class="pull-left">
                        BANK ACCOUNT DETAILS
                    </h2>
                </div>
                <div class="body">
                <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Bank Name</label><br>
                                            <label for="">{{ $user->seller_detail->bank_account }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Account Name</label><br>
                                            <label for="">{{ $user->seller_detail->account_name }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Account Number</label><br>
                                            <label for="">{{ $user->seller_detail->account_number }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Swift Code</label><br>
                                            <label for="">{{ $user->seller_detail->swift_code }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>EFSC</label><br>
                                            <label for="">{{ $user->seller_detail->esfc }}</label>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                </div>
            </div>


            <div class="card">
                <div class="header clearfix">
                    <h2 class="pull-left">
                        SUBSCRIPTION DETAIL &nbsp;&nbsp;&nbsp; <h2 class="pull-left">
                           
                             @if($user->is_edsa_agent)
                                <a href="" class="badge badge-pill badge-primary">EDSA AGENT</a>
                            @endif
                        </h2>
                        
                         <h2 style="margin-left:250px;">
                            @if($user->is_dstv_agent)
                                <a href="" class="badge badge-pill badge-primary">DSTV AGENT</a>
                            @endif
                        </h2>
                    </h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Subscription Type</label><br>
                                    <label for="">{{ $user->user_type }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Subscription category</label><br>
                                    <label for="">{{ isset($plan->name) ? $plan->name : $user->user_type }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Subscription plan</label><br>
                                    <label for="">{{ $plan_name->plan_title ?? 'No plan choose' }}</label>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>
        
        </div>

    </div>
    <script>
        // Get modal element
var modal = document.getElementById("myModal");

// Get the box that opens the modal
// Get the elements with the class name "uploadBox" (returns a collection)
var uploadBoxes = document.getElementsByClassName("uploadBox");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// Loop through each uploadBox and add the onclick event listener
for (var i = 0; i < uploadBoxes.length; i++) {
    uploadBoxes[i].onclick = function() {
        console.log(1111);
        // Assuming 'modal' is defined somewhere in your code
        modal.style.display = "block";
    }
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}

    </script>
@endsection
