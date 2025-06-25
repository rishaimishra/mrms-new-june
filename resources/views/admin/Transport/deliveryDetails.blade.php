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
                      
                    </h2>
                    
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
                        DRIVER DETAILS
                        <!-- {{$user}} -->
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
                                            <label>Transport type</label><br>
                                            <label for="">{{ $categories->transport_type }}</label>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Transport model</label><br>
                                            <label for="">{{ $categories->transport_model }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Transport Make</label><br>
                                            <label for="">{{ $categories->transport_make }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                              
                               
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Manufacturer Year</label><br>
                                            <label for="">{{ $categories->manufacture_year }}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Air Conditioning</label><br>
                                            <label for="">
                                            @if ($categories->air_conditioning == 1)
                                                Yes
                                            @else
                                                No
                                            @endif
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Audio/Radio</label><br>
                                            <label for=""> 
                                                @if ($categories->audio_radio == 1)
                                                Yes
                                            @else
                                                No
                                            @endif</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                               
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Vehicle License</label><br>
                                            <label for="">{{ $categories->vehicle_license }}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Vehicle Insurance</label><br>
                                            <label for="">{{ $categories->vehicle_insurance }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <label>Driver Address</label><br>
                                            <label for="">{{ $categories->driver_address }}</label>
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
                                            <img src="{{ url('admin/images/registration_img.jpeg') }}" width="150" height="150" alt="User"/>
                                           
                                        
                            </div>
                            <div class="form-group form-float">
                                            <label>Business Reg. Image</label><br>
                                            <img src="{{ url('admin/images/registration_img.jpeg') }}" width="150" height="150" alt="User"/>
                                            <button class="btn btn-primary upload-btn mt-1" style="margin-top:10px">Download document</button>
                                        
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
                                                <label for=""></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label>Afri Money</label><br>
                                                <label for=""></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label>Q Money</label><br>
                                                <label for=""></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                    </div>

                                

                
            </div>
           
           
            <div class="card">
                <div class="header clearfix">
                    <h2 class="pull-left">
                        RATES
                    </h2>
                </div>
                    <div class="body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label>Hourly Rates</label><br>
                                                <label for="">{{ $categories->hourly_rate }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label>Daily Rates</label><br>
                                                <label for="">{{ $categories->daily_rate }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label>Weekly Rates</label><br>
                                                <label for="">{{ $categories->weekly_rate }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <label>Montly Rates</label><br>
                                                <label for="">{{ $categories->monthly_rate }}</label>
                                            </div>
                                        </div>
                                    </div>
                                  
                                </div>



                    </div>

                                

                
            </div>
        </div>
    </div>

    @endsection