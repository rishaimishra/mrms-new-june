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
            <form action="{{ route('admin.seller.store') }}" method="POST">

            <div class="card">
                <div class="header" style="display: flex;align-items:center;justify-content:space-between;">
                    <h2 class="pull-left">
                        BUSINESS DETAILS
                    </h2>
                    <div>
                        <button class="btn btn-success btn-lg waves-effect pull-right" style="margin-left:10px;">APPROVE</button>
                        <button class="btn btn-success btn-lg waves-effect pull-right" type="submit">PUBLISH</button>
                    </div>
                </div>
                <div class="body">
                    <div class="row">
                        
                            <div class="col-md-9">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <label for="business_name">Business name</label><br>
                                                    <input type="hidden" name="user_id" value="{{ ($user->seller_detail->user_id) }}" class="form-control">
                                                    <input type="text" name="business_name" value="{{ ($user->seller_detail->business_name) }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <label for="tin">TIN</label><br>
                                                    <input type="text" name="tin" value="{{ ($user->seller_detail->tin) }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <label for="street_number">Street Number</label><br>
                                                    <input type="text" name="street_number" value="{{ ($user->seller_detail->street_number) }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Repeat similar blocks for all other fields -->
                                    <div class="row">
                                  
                                   
                                        <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <label>Street Name</label><br>
                                                    {{-- <label for="">{{ $user->seller_detail->street_name }}</label> --}}
                                                    <input type="text" name="street_name" value="{{ ($user->seller_detail->street_name) }}" class="form-control">
                                                
                                                </div>
                                            </div>
                                        </div>
        
                                        <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <label>Area</label><br>
                                                    <input type="text" name="area" value="{{ ($user->seller_detail->area) }}" class="form-control">
                                                
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <label>Ward</label><br>
                                                    <input type="text" name="ward" value="{{ ($user->seller_detail->ward) }}" class="form-control">
                                                
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                       
                                        <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <label>Section</label><br>
                                                    <input type="text" name="section" value="{{ ($user->seller_detail->section) }}" class="form-control">
                                                
                                                </div>
                                            </div>
                                        </div>
        
                                        <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <label>Chiefdon</label><br>
                                                    <input type="text" name="chiefdon" value="{{ ($user->seller_detail->chiefdon) }}" class="form-control">
                                                
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <label>Province</label><br>
                                                   <input type="text" name="province" value="{{ ($user->seller_detail->province) }}" class="form-control">
                                                
                                                </div>
                                            </div>
                                        </div>
                                    </div>
        
        
                                    <div class="row">
                                       
                                        <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <label>Business Co-ordinates</label><br>
                                                    <input type="text" name="business_coordinates" value="{{ ($user->seller_detail->business_coordinates) }}" class="form-control">
                                                
                                                </div>
                                            </div>
                                        </div>
        
                                        <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <label>Mobile 1</label><br>
                                                    <input type="text" name="mobile1" value="{{ ($user->seller_detail->mobile1) }}" class="form-control">
                                                
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <label>Mobile 2</label><br>
                                                    <input type="text" name="mobile2" value="{{ ($user->seller_detail->mobile2) }}" class="form-control">
                                                
                                                </div>
                                            </div>
                                        </div>
                                       
                                    </div>
        
                                    <div class="row">
        
                                    <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <label>Mobile 3</label><br>
                                                    <input type="text" name="mobile3" value="{{ ($user->seller_detail->mobile3) }}" class="form-control">
                                                
                                                </div>
                                            </div>
                                        </div>
                                      
                                        <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <label>Business Email</label><br>
                                                    <input type="text" name="business_email" value="{{ ($user->seller_detail->business_email) }}" class="form-control">
                                                
                                                </div>
                                            </div>
                                        </div>
        
                                    </div>
        
                                    <div class="row">
                                     
                                        <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <label>Opening Time</label><br>
                                                    <input type="text" name="opening_time" value="{{ ($user->seller_detail->opening_time) }}" class="form-control">
                                                
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <label>Closing Time</label><br>
                                                    <input type="text" name="closing_time" value="{{ ($user->seller_detail->closing_time) }}" class="form-control">
                                                
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                
                              
                                <form action="{{ route('admin.seller.additional_desc') }}" method="post">
                                    @csrf
    
                                    <div class="input-group" style="display: flex; align-items: center; justify-content: start;">
                                        <!-- Green Submit Button -->
                                        <button type="submit" class="btn btn-success" style="white-space: nowrap;">STORE ADDITIONAL MESSAGE</button>
    
                                        <!-- Hidden Seller ID Field -->
                                        <input type="hidden" name="seller_id" value="{{ $user->id ?? '' }}">
    
                                        <!-- Text Input Field for Additional Description -->
                                        <input type="text" name="addition_desc" class="form-control input-field" 
                                            value="{{$user->seller_detail->additional_information}}"
                                            placeholder="Maximum Eighty Characters..." 
                                            style="border: 1px solid #d1d1d1; margin-left: 10px; padding: 8px; height: auto; border-radius: 0;"
                                            maxlength="80" oninput="updateCharacterCount(this.value.length);">
    
                                            &nbsp;&nbsp;<p id="charCount">0/80</p>
    
                                        <script>
                                        function updateCharacterCount(count) {
                                            document.getElementById('charCount').innerText = count + '/80';
                                        }
                                        </script>
    
                                    </div>
                                </form>
    
                              
    
    
                            </div>
                        
                       
                       
                       
                       
                       <div class="col-md-3" style="
                                padding-left: 100px;
                            ">
                            <div class="form-group form-float">
                            <form action="{{ route('admin.business.logo.seller') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                                            <label>Business Logo</label><br>
                                            <img src="{{ url('busniess_images/' . $user->seller_detail->business_logo) }}" width="150" height="150" alt="User"/>
                                    
                                    <input type="hidden" name="seller_id" value="{{ $user->id }}">
                                   
                                    <input type="file" name="business_logo" id="uploadLogo" class="form-control mt-2" accept="image/*" style="margin-top:10px;">
                                    <button type="submit" class="btn btn-success mt-1" style="margin-top:10px;">Upload Image</button>
                                    </form>
                                        
                            </div>
                            <form action="{{ route('admin.upload.business.reg.image') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group form-float">
                                    <label>Business Reg. Image</label><br>
                                    <!-- <img src="{{ url('admin/images/registration_img.jpeg') }}" width="150" height="150" alt="User" id="previewImage"/> -->
                                    <img src="{{ url('storage/' . $user->seller_detail->business_registration_image) }}" width="150" height="150" alt="User"/>
                                    
                                    <input type="hidden" name="seller_id" value="{{ $user->id }}">
                                   
                                    <input type="file" name="business_reg_image" id="uploadImage" class="form-control mt-2" accept="image/*" style="margin-top:10px;">
                                    <button type="submit" class="btn btn-success mt-1" style="margin-top:10px;">Upload Image</button>
                                    <button class="btn btn-primary upload-btn mt-1" style="margin-top:10px">Download Document</button>
                                </div>
                            </form>

                            <script>
                                document.getElementById('uploadImage').addEventListener('change', function(event) {
                                    const file = event.target.files[0];
                                    if (file) {
                                        const reader = new FileReader();
                                        reader.onload = function(e) {
                                            document.getElementById('previewImage').src = e.target.result;
                                        };
                                        reader.readAsDataURL(file);
                                    }
                                });
                                document.getElementById('uploadLogo').addEventListener('change', function(event) {
                                    const file = event.target.files[0];
                                    if (file) {
                                        const reader = new FileReader();
                                        reader.onload = function(e) {
                                            document.getElementById('previewImage').src = e.target.result;
                                        };
                                        reader.readAsDataURL(file);
                                    }
                                });
                            </script>




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
                               <button class="btn btn-warning upload-btn">Edit</button>
                                
                                <form action="{{ route('admin.assign.delete') }}" method="POST" id="deleteThemeForm">
                                    @csrf
                                    
                                    <!-- Hidden inputs to send the seller_id and theme_id -->
                                    <input type="hidden" name="seller_id" value="{{ $user->id }}">
                                    <input type="hidden" name="theme_id" value="{{ $specific_theme->id ?? '' }}">
                                    
                                    <button type="submit" class="btn btn-danger upload-btn" 
                                        onclick="return confirm('Are you sure you want to delete this theme?');">Delete</button>
                                </form>
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
                            <form id="themeForm" action="{{ route('admin.assign.themes') }}" method="POST" class="p-3 border rounded shadow-sm bg-light">
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


                                <div class="header clearfix">
                                    <h2 class="pull-left" style="margin-left: -17px">
                                        MOBILE MONEY
                                    </h2>
                                </div>
                                <div class="row" style="margin-top:18px">
                                    <div class="col-sm-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label>Orange Money</label><br>
                                                <label for=""> @if ($user->mobile_money_orange == 1)
                                                Yes
                                            @else
                                                No
                                            @endif</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label>Afri Money</label><br>
                                                <label for="">@if ($user->mobile_money_afri == 1)
                                                Yes
                                            @else
                                                No
                                            @endif</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label>Q Money</label><br>
                                                <label for="">@if ($user->mobile_money_Q == 1)
                                                Yes
                                            @else
                                                No
                                            @endif</label>
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
            <div class="card">
                <div class="header clearfix">
                    <h2 class="pull-left">
                        Digital Addresses
                    </h2>
                </div>
                {{--<div class="body">
                @grid([
                    'dataProvider' => $dataProvider,
                    'rowsPerPage' => 15,
                    'columns' => [
                        'addressArea.name',
                        'addressChiefdom.name',
                        'addressSection.name',
                        'address.ward_number',
                        'address.constituency',
                        'address.district',
                        'address.province',
                        [
                            'class' => 'actions',
                            'value' => [
                                'view:/sl-admin/digitl-address/{id}',
                            ]
                        ]
                    ]
                ])
                </div>--}}
                <div class="body">
                    {!! Form::open(['method' => 'get']) !!}
                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Area</label>
                                    <input type="text" class="form-control" value="{{ request('area') }}" name="area">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Section</label>
                                    <input type="text" class="form-control" value="{{ request('section') }}" name="section">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Chiefdom</label>
                                    <input type="text" class="form-control" value="{{ request('chiefdom') }}" name="chiefdom">
                                </div>
                            </div>
                        </div>


                        <div class="col-sm-3" style=" margin: 20px 0 10px 0; ">
                            <button class="btn btn-primary waves-effect btn-lg" type="submit">Filter</button>
                            <a href="{{ route('admin.user.show', $user->id) }}" class="btn-lg btn btn-default">Clear Filter</a>
                        </div>

                    </div>



                    {!! Form::close() !!}
                    @if ($digitalAddresses->count())
                        <table class="table">
                            <tbody>
                            <th>Area</th>
                            <th>Ward Number</th>
                            <th>Constituency</th>
                            <th>Section</th>
                            <th>Chiefdom</th>
                            <th>District</th>
                            <th>Province</th>
                            <th>Digital address</th>
                            <th>Tag</th>
                            <th>Created At</th>
                            </tbody>
                            <tbody>

                            @foreach($digitalAddresses as $address)

                                <tr>
                                    <td><a href="{{ route('admin.digitl-address.show', $address->id) }}" >{{ $address->addressArea->name }}</a></td>
                                    <td>{{ $address->address->ward_number }}</td>
                                    <td>{{ $address->address->constituency }}</td>
                                    <td>{{ $address->addressSection->name }}</td>
                                    <td>{{ $address->addressChiefdom->name}}</td>
                                    <td>{{ $address->address->district}}</td>
                                    <td>{{ $address->address->province}}</td>
                                    <td>{{ $address->digital_addresses}}</td>
                                    <td>{{ $address->type}}</td>

                                    <td>{{ \Carbon\Carbon::parse($address->created_at)->format('Y M, d') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        {!! $digitalAddresses->links() !!}
                    @else
                        <div class="alert alert-info">No result found.</div>
                    @endif
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
