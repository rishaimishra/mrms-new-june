@extends('admin.layout.edit')

@section('content')
    @include('admin.layout.partial.alert')
    @if(Auth::user()->user_type == 'shop')
        @isset($product->id)
            <!-- {!! Form::model($product, ['files' => true, 'route' => ['admin.seller-product.update', $product->id],'method' => 'PATCH']) !!} -->
            {!! Form::model($product, ['files' => true, 'route' => ['admin.update-product-seller', $product->id],'method' => 'POST']) !!}
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="type" value="{{$type}}">

        @else
            {!!Form::open(['files' => true, 'route' => 'admin.seller-product.store']) !!}
        @endisset
    @else
    @isset($product->id)
        {!! Form::model($product, ['files' => true, 'route' => ['admin.product.update', $product->id],'method' => 'PATCH']) !!}
    @else
        {!!Form::open(['files' => true, 'route' => 'admin.product.store']) !!}
    @endisset
    @endif
  
    <div class="row">
        <div class="col-lg-8  col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        Add / Edit Product Base
                        
                        @if(Auth::user()->user_type == 'admin')
                        @isset($product->id)
                        <div class="pull-right">
                            {{ Form::button('Delete', ['type' => 'button', 'class' => 'btn btn-warning btn-sm delete'] )  }}
                        </div>
                         @endisset
                        @endif

                        
                    </h2>
                </div>
                <div class="body">

                    <div class="row">
                        <div class="col-lg-12 col-md-12 multi-img-upload">

                            <label for="">Photos</label>


                            <div class="padding-img">
                                <a href="javascript: return false;" class="add" id="hidden-files">
                                    <i class="material-icons">camera_enhance</i>
                                </a>
                            </div>


                            <input type="file" name="images[]" style="display: none;" id="hidden-images" multiple>
                            <span>[Select multiple images using 'Ctrl']</span>
                            {!! $errors->first('images.*', '<p class="error">:message</p>') !!}
                        </div>
                    </div>
                    @if($product->images)
                    <div class="row">

                        @foreach($product->images as $image)
                            <div class="col-lg-3">
                                <div class="padding-img" style="width: 50%" id="img{{$image->id}}">
                                    <img class="img-thumbnail" src="{{ asset('storage/' . $image->image) }}" alt="">
                                    <span class="imgs text-danger">
                                        @if (Auth::user()->user_type == 'shop')
                                        <a href="javascript:sellerimgdelete({{$image->id}})" class="text-danger">
                                            Delete
                                        </a>
                                        @else
                                        <a href="javascript:imgdelete({{$image->id}})" class="text-danger">
                                            Delete
                                        </a>
                                        @endif
                                 
                                </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                @endif

                    {{--  <div class="row">
                        <div class="col-lg-12 col-md-12 multi-img-upload">

                            <label for="">Background Photo</label>


                            <div class="padding-img">
                                <a href="javascript: return false;" class="add" id="hidden-bg-files">
                                    <i class="material-icons">camera_enhance</i>
                                </a>
                            </div>


                            <input type="file" name="background_image" style="display: none;" id="hidden-bg-images">
                            {!! $errors->first('images.*', '<p class="error">:message</p>') !!}
                        </div>
                    </div>  --}}


                    {{--  @if($product->background_image)
                    <div class="row">

                            <div class="col-lg-3">
                                <div class="padding-img" style="width: 50%" id="img{{$product->id}}">
                                    <img class="img-thumbnail" src="{{ asset('product_background_images/' . $product->background_image) }}" alt="">
                                    <span class="imgs text-danger">
                                    <a href="javascript:bgimgdelete({{$product->id}})" class="text-danger">
                                        Delete
                                    </a>
                                </span>
                                </div>
                            </div>
                    </div>

                @endif  --}}

                    <div class="form-group form-float">
                        <div class="form-line">
                            <label>Product Name: </label>
                            <input type="text" class="form-control" name="name" required
                                   value="{{ old('name',$product->name) }}">
                        </div>
                        @if ($errors->has('name'))
                            <label class="error">{{ $errors->first('name') }}</label>
                        @endif
                    </div>

                    @if(! isset($product->id) || $attributeGroups === false)
                        {!! Form::materialSelect('Attribute Set', 'attribute_set_id', $attributeSets) !!}
                    @endif
                       
                    <div class="row">
                        <div class="col-sm-4">
                            {!! Form::materialSelect('Stock Availability', 'stock_availability', array_combine(\App\Models\Product::STOCK_AVAILABILITY_OPTIONS, \App\Models\Product::STOCK_AVAILABILITY_OPTIONS)) !!}
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Order Quantity:</label>
                                    <input type="number" class="form-control" name="quantity"
                                           value="{{ old('quantity',$product->quantity) }}" required>
                                </div>
                                @if ($errors->has('quantity'))
                                    <label class="error">{{ $errors->first('quantity') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Weight (in KG):</label>

                                    <input type="text" class="form-control" name="weight"
                                           value="{{ old('weight',$product->weight) }}" required>

                                </div>
                                @if ($errors->has('weight'))
                                    <label class="error">{{ $errors->first('weight') }}</label>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Price (in Leones):</label>

                                    <input type="text" class="form-control" name="price"
                                           value="{{ old('price',$product->price) }}" required>


                                </div>
                                @if ($errors->has('price'))
                                    <label class="error">{{ $errors->first('price') }}</label>
                                @endif
                            </div>

                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <label>Order number of items</label>

                                    <input type="text" class="form-control" name="unit"
                                           value="{{ old('unit',$product->unit) }}"
                                           required>


                                </div>
                                @if ($errors->has('unit'))
                                    <label class="error">{{ $errors->first('unit') }}</label>
                                @endif
                            </div>

                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="">Sequence</label>
                                    <input type="number" min="0" class="form-control" name="sequence" id="sequence"
                                           value="{{ old('sequence',$product->sequence) }}">
                                </div>
                            </div>
                            {!! $errors->first('sequence', '<p class="error">:message</p>'); !!}
                        </div>

                    </div>


                    <button class="btn btn-primary waves-effect" type="submit">Save</button>


                </div>
            </div>

            @if(isset($product->id) && $attributeGroups !== false)

                <div class="card"  @if(Auth::user()->user_type == 'admin') style="opacity:1;" @else style="opacity:0;" @endif>
                    <div class="header">
                        <h2>{{ __("Additional Attributes") }}</h2>

                    </div>
                    <div class="body">
                        @foreach($attributeGroups as $group)
                            <h2 class="card-inside-title">{{ $group->attribute_group_name }}</h2>

                            @foreach ($group->attributes as $attribute)

                                @if($attribute->frontend_type == 'input')
                                    {!!Form::materialText($attribute->frontend_label, $attribute->attribute_code) !!}
                                @else
                                    <div class="form-group form-float">
                                        <div class="form-line{{ $errors->has($attribute->attribute_code) ? ' error' : '' }}">
                                            <label>{{ $attribute->frontend_label }}</label>

                                            <select class="form-control" name="{{ $attribute->attribute_code }}">
                                                <option value="">Choose a option</option>
                                                @foreach($attribute->optionValues as $option)
                                                    <option {{ old($attribute->attribute_code, $product->{$attribute->attribute_code}) == $option->label ? "selected" : "" }} {!! $option->is_featured == 1 ? 'style="font-weight: bold; color: #000"' : '' !!} value="{{ $option->label }}">{{ $option->label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {!! $errors->first($attribute->attribute_code, '<p class="error">:message</p>') !!}
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
        
        <div class="col-lg-4" @if(Auth::user()->user_type == 'admin') style="opacity:1;" @else style="opacity:0;" @endif>
            <div class="card">
                <div class="header">
                    <h2>Categories
                    </h2>
                </div>
                <div class="body list-tree">
                    @if ($errors->has('categories'))
                        <label class="error">{{ $errors->first('categories') }}</label>
                    @endif
                    @include('admin.product.categories', ['categories' => $categories, 'parent' => null])

                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    @if(Auth::user()->user_type == 'shop')
    @isset($product->id)
    {!! Form::open(['route' => ['admin.seller-product.destroy', $product->id], 'method' => 'DELETE','class'=>'delete','id'=>'deleteForm']) !!}
        <input type="hidden" name="product_id" value="{{ $product->id }}">
    {!! Form::close() !!}
@endisset
    @else
    @isset($product->id)
        {!! Form::open(['route' => ['admin.product.destroy', $product->id], 'method' => 'DELETE','class'=>'delete','id'=>'deleteForm']) !!}

        {!! Form::close() !!}
    @endisset
    @endif
    
@stop
@push("before_head_close")
    <style type="text/css" media="screen">
        .map_canvas {
            width: 100%;
            height: 215px;
            margin: 10px 20px 10px 0;
        }

        .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {
            width: 100%;
        }

        .show-tick {
            width: 100% !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"/>
     <link href="{{ url('admin/css/jquery.businessHours.css') }}" rel="stylesheet"/>

    <link href="{{ url('admin/css/jquery.timepicker.min.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ url('admin/css/jquery.fancybox.css') }}" type="text/css" media="screen"/>

@endpush
@push('before_body_close')

    <script type="text/javascript" src="{{ url('admin/js/jquery.fancybox.pack.js') }}"></script>

    <script type="text/javascript" src="{{ url('admin/js/jquery.fancybox-media.js') }}"></script>

    <script type="text/javascript" src="{{ url('admin/js/jquery.geocomplete.js') }}"></script>

    <script type="text/javascript" src="{{ url('admin/js/jquery.timepicker.min.js') }}"></script>

    <script type="text/javascript" src="{{ url('admin/js/businessHours.js') }}"></script>

    <script>
        $(document).ready(function () {

            $(".delete").click(function () {
                if (confirm("Are you sure?")) {
                    $("#deleteForm").submit(); // Submit the form
                }

            });
        });

        function imgdelete(id) {
            if (confirm("Are you sure?")) {
                $.ajax({
                    headers: {
                        'X-CSRF-Token': "{{ csrf_token() }}"
                    },
                    type: 'GET',
                    url: "{{url('/sl-admin/imageDelete/product/')}}/" + id,
                    success: function (data) {

                        $("#img" + id).css('display', 'none');
                    }
                });
            }
        }
        function sellerimgdelete(id) {
            if (confirm("Are you sure?")) {
                $.ajax({
                    headers: {
                        'X-CSRF-Token': "{{ csrf_token() }}"
                    },
                    type: 'GET',
                    url: "{{url('/sl-admin/imageDelete/seller-product/')}}/" + id,
                    success: function (data) {

                        $("#img" + id).css('display', 'none');
                    }
                });
            }
        }
        function bgimgdelete(id) {
            if (confirm("Are you sure?")) {
                $.ajax({
                    headers: {
                        'X-CSRF-Token': "{{ csrf_token() }}"
                    },
                    type: 'GET',
                    url: "{{url('/sl-admin/bgimageDelete/product/')}}/" + id,
                    success: function (data) {

                        $("#img" + id).css('display', 'none');
                    }
                });
            }
        }


        //hidden files
        $("#hidden-files").on('click', function () {
            $("#hidden-images").trigger('click');
        });
        $("#hidden-images").on('change', function () {
            $(".selected-images").remove();
            $(this).after('<div class="selected-images">' + $(this)[0].files.length + ' image selected.</div>');
        });

        $(document).ready(function () {
            $("#hidden-file").on('click', function () {
                $("#hidden-image").trigger('click');
            });
        });

         //hidden files
         $("#hidden-bg-files").on('click', function () {
            $("#hidden-bg-images").trigger('click');
        });
        $("#hidden-bg-images").on('change', function () {
            $(".selected-bg-images").remove();
            $(this).after('<div class="selected-bg-images">' + $(this)[0].files.length + ' image selected.</div>');
        });

        $(document).ready(function () {
            $("#hidden-bg-file").on('click', function () {
                $("#hidden-bg-image").trigger('click');
            });
        });

    </script>
@endpush
