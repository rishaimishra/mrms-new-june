@extends('admin.layout.main')

@section('content')
{!!Form::open(['files' => true, 'route' => 'admin.ad-detail.store']) !!}
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Enter Ad Details (Create image of Height: 400px, Width: 720px) </h2>

                </div>
                
                <div class="body">
                    
                        <div class="row">
                        <div class="col-sm-6">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" value="{{ old('ad_name') }}" name="ad_name" required>
                                <label class="form-label">Ad Name</label>

                            </div>
                            @if ($errors->has('ad_name'))
                                <label class="error">{{ $errors->first('ad_name') }}</label>
                             @endif
                        </div>
                        </div>
                        
                      
                        
                        <div class="row">
                            <div class="col-sm-6">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" value="{{ old('ad_link') }}" name="ad_link" >
                                <label class="form-label">Ad Link</label>

                            </div>
                            @if ($errors->has('ad_link'))
                                    <label class="error">{{ $errors->first('ad_link') }}</label>
                            @endif
                        </div>
                            </div>
                            <div class="col-sm-6">

                            </div>
                            
                        </div>



                        <div class="row">
                        <div class="col-sm-6">
                       
                            <div class="form-line">
                                <label for="ad_type">Ad Type</label>
                                <select id="ad_type" name="ad_type" size="3">
                                    <option value="1">Small ad</option>
                                    <option value="2">Banner</option>
                                    
                                </select>
                                <!-- <input type="text" class="form-control" value="{{ old('ad_type') }}"  required>
                                <label class="form-label">Ad Type</label> -->

                            </div>
                            @if ($errors->has('ad_name'))
                                <label class="error">{{ $errors->first('ad_name') }}</label>
                             @endif
                        
                        </div>
                      
                        
                        <div class="row">
                            <div class="col-sm-6">
                        
                            <div class="form-line">
                                <!-- <input type="text" class="form-control" value="{{ old('ad_link') }}" name="ad_link" >
                                <label class="form-label">Ad Category</label> -->

                                <label for="ad_category">Ad Category</label>
                                <select id="ad_category" name="ad_category" size="3">
                                    <option value="All">All</option>
                                    <option value="Shop">Shop</option>
                                    <option value="Auto">Autos</option>
                                    <option value="RealEstate">RealEstate</option>
                                    <option value="Utilities">Utilities</option>
                                </select>

                            </div>
                            @if ($errors->has('ad_link'))
                                    <label class="error">{{ $errors->first('ad_link') }}</label>
                            @endif
                        
                            </div>
                            <div class="col-sm-6">

                            </div>
                            
                        </div>



                        <div class="col">
                        <div class="col-sm-6">
                       
                            <div class="form-line">
                                <label for="ad_content_type">Ad Content Type</label>
                                <select id="ad_content_type" name="ad_content_type" size="3">
                                    <option value="None">Select Option</option>
                                    <option value="Image">Image</option>
                                    <option value="Video">Video</option>
                                    
                                </select>
                                <!-- <input type="text" class="form-control" value="{{ old('ad_type') }}"  required>
                                <label class="form-label">Ad Type</label> -->

                            </div>
                            @if ($errors->has('ad_name'))
                                <label class="error">{{ $errors->first('ad_name') }}</label>
                             @endif
                        
                        </div>



                        

                        <div class="col" id="image_div" style="display:none">
                            <div class="col-sm-6">
                                <div class="form-group form-float">
                                            <div class="col-sm-6">
                                            {!! Form::materialFile('Ad Image:', 'ad_image', $errors->first('question')) !!}

                                        
                                            </div>
                                </div>
                            </div>
                        </div>


                        <div class="col" id="video_div" style="display:none">
                            <div class="col-sm-6">
                                <div class="form-group form-float">
                                            <div class="col-sm-6">
                                            <div class="mb-3">
  <label for="ad_video" class="form-label">Video Upload</label>
  <input class="form-control" type="file" id="ad_video" name="ad_video">
</div>
                                        
                                            </div>
                                </div>
                            </div>
                        </div>




                            <div class="col-sm-6" style=" margin-top: 20px; ">
                                 <button class="btn btn-primary waves-effect" type="submit">SUBMIT</button>
                            </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    @stop

@push('scripts')
    <script src="{{ url('admin/plugins/jquery-validation/jquery.validate.js') }}"></script>
    <script src="{{ url('admin/js/pages/forms/form-validation.js') }}"></script>

    <script>

        var selectedValue = $('#ad_content_type').val();
        
        if(selectedValue === null){
            $(":submit").attr("disabled", true);
        }

        document.getElementById('ad_content_type').addEventListener('change', function() {
        var selectedValue = this.value;

        // console.log(selectedValue);

        if (selectedValue === "Image") {
            // console.log("show image")
            $('#video_div').hide();
            $('#image_div').show();
            $(":submit").attr("disabled", false);
        }else if (selectedValue === "Video") {
            // console.log("show video")
            $('#video_div').show();
            $('#image_div').hide();
            $(":submit").attr("disabled", false);
        }else {
            $('#video_div').hide();
            $('#image_div').hide();
            $(":submit").attr("disabled", true);
        }
    });
    </script>

    @endpush
