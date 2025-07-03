@extends('admin.layout.main')

@section('content')
{!! Form::model($adDetail, ['route' => ['admin.ad-detail.updatead', $adDetail->id], 'method' => 'post', 'files' => true]) !!}
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Edit Ad Details</h2>
                </div>
                
                <div class="body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="ad_name" value="{{ old('ad_name', $adDetail->ad_name) }}" required>
                                    <label class="form-label">Ad Name</label>
                                </div>
                                @if ($errors->has('ad_name'))
                                    <label class="error">{{ $errors->first('ad_name') }}</label>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="ad_link" value="{{ old('ad_link', $adDetail->ad_link) }}">
                                    <label class="form-label">Ad Link</label>
                                </div>
                                @if ($errors->has('ad_link'))
                                    <label class="error">{{ $errors->first('ad_link') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sequence -->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <input type="text" class="form-control" name="sequence" value="{{ old('sequence', $adDetail->sequence) }}">
                                    <label class="form-label">Sequence</label>
                                </div>
                                @if ($errors->has('sequence'))
                                    <label class="error">{{ $errors->first('sequence') }}</label>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Ad Description -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="description">Ad Description</label>
                                <textarea class="form-control" name="ad_description">{{ old('ad_description', $adDetail->ad_description) }}</textarea>
                                @if ($errors->has('ad_description'))
                                    <label class="error">{{ $errors->first('ad_description') }}</label>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Ad Content Type -->
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="ad_content_type">Ad Content Type</label>
                                <select id="ad_content_type" name="ad_content_type" class="form-control">
                                    <option value="Image" {{ $adDetail->ad_content_type == 'Image' ? 'selected' : '' }}>Image</option>
                                    <option value="Video" {{ $adDetail->ad_content_type == 'Video' ? 'selected' : '' }}>Video</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Image or Video Input -->
                    <div class="row">
                        <div class="col-sm-6" id="image_div" style="{{ $adDetail->ad_content_type == 'Image' ? '' : 'display:none' }}">
                            <div class="form-group">
                                <label for="ad_image">Ad Image</label>
                                <input type="file" class="form-control" name="ad_image">
                                @if ($adDetail->ad_image)
                                    <img src="{{ asset('storage/' . $adDetail->ad_image) }}" alt="Ad Image" class="img-thumbnail mt-2" width="150">
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-6" id="video_div" style="{{ $adDetail->ad_content_type == 'Video' ? '' : 'display:none' }}">
                            <div class="form-group">
                                <label for="ad_video">Ad Video</label>
                                <input type="file" class="form-control" name="ad_video">
                                @if ($adDetail->ad_video)
                                    <video class="mt-2" width="150" controls>
                                        <source src="{{ asset('storage/' . $adDetail->ad_video) }}" type="application/x-mpegURL">
                                    </video>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{!! Form::close() !!}
@endsection
