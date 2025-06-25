@extends('admin.layout.edit')

@section('content')
<style>
    .bootstrap-select.btn-group .dropdown-menu {
        width: 100% !important;
    }
    .bootstrap-select .bs-searchbox .form-control,
    .bootstrap-select .bs-actionsbox .form-control,
    .bootstrap-select .bs-donebutton .form-control {
        margin-left: 0px !important;
    }
    .bootstrap-select .bs-searchbox:after {
        display: none;
    }
    .bootstrap-select.btn-group .dropdown-toggle .caret {
        left: 0px;
    }
    #images {
        width: 100%;
    }
</style>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
        <div class="header" style="display: flex; justify-content: space-between;">
            <h2>Edit Product</h2>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                {{ $message }}
            </div>
        @endif

        <div class="body">
            <form action="{{ route('admin.update-product.seller', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="row">
                    <div class="col-lg-12 col-md-12 multi-img-upload">
                        <label for="images">Photos</label>
                        <div class="padding-img">
                            <a href="javascript:void(0);" class="add" id="trigger-file-upload">
                                <i class="material-icons">camera_enhance</i>
                            </a>
                        </div>
                        <input type="file" name="images[]" style="display: none;" id="file-input" multiple>
                        <span>[Select multiple images using 'Ctrl']</span>
                        <div class="image-preview" style="display: none;"></div>
                    </div>
                    
                </div>
                <div class="form-group">
                    <label for="product-images">Uploaded Images</label>
                    <div id="uploaded-images" class="d-flex gap-3">
                        @foreach ($productImages as $image)
                            <div class="image-preview">
                                <img src="{{ $image->full_image }}" alt="Product Image" class="img-thumbnail" style="width: 100px; height: 100px;">
                                <button type="button" class="btn btn-danger btn-sm remove-image" data-image-id="{{ $image->id }}">Remove</button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <script>
                    document.getElementById('trigger-file-upload').addEventListener('click', function () {
                            console.log('File upload button clicked!');
                            document.getElementById('file-input').click();
                        });

                        $('body').on('change', '#file-input',function( ) {
                            $('.image-preview').hide();
                            renderImagePreview()
                            
                        })


                        function renderImagePreview() {
                            var files = $("#file-input")[0].files;
                            let el = "";

                            if (files.length > 0) {
                                el = "";

                                [...files].forEach((e) => {
                                let src = URL.createObjectURL(e);
                                el += `<img src="${src}" alt="your image" style="width: auto;height: 90px;">`;
                                });

                                $(".image-preview").show().html(el);
                            }
                            }

                            document.querySelectorAll('.remove-image').forEach(button => {
                                button.addEventListener('click', function () {
                                    const imageId = this.getAttribute('data-image-id');

                                    // Send an AJAX request to delete the image
                                    fetch(`/sl-admin/delete/product-images/${imageId}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    })
                                    .then(response => {
                                        if (response.ok) {
                                            this.closest('.image-preview').remove();
                                        } else {
                                            alert('Failed to remove the image. Please try again.');
                                        }
                                    });
                                });
                            });

                </script>


                <div class="form-group form-float">
                    <div class="form-line">
                        <label>Product Name:</label>
                        <input type="text" class="form-control" name="name" value="{{ $product->name }}" required>
                        <input type="hidden" class="form-control" name="product_id" value="{{ $product->id }}" required>
                    </div>
                </div>

                <div class="form-group form-float">
                    <div class="form-line">
                        <label>Attribute Set:</label>
                        <select class="form-control" name="attribute_set_id">
                            @foreach ($attributeSets as $id => $setName)
                                <option value="{{ $id }}" {{ $product->attribute_set_id == $id ? 'selected' : '' }}>
                                    {{ $setName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <label>Stock Availability</label>
                                <select class="form-control" name="stock_availability">
                                    @foreach ($product::STOCK_AVAILABILITY_OPTIONS as $option)
                                        <option value="{{ $option }}" {{ $product->stock_availability == $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <label>Order Quantity:</label>
                                <input type="number" class="form-control" name="quantity" value="{{ $product->quantity }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <label>Weight (in KG):</label>
                                <input type="text" class="form-control" name="weight" value="{{ $product->weight }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <label>Price (in Leones):</label>
                                <input type="text" class="form-control" name="price" value="{{ $product->price }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <label>Unit:</label>
                                <input type="text" class="form-control" name="unit" value="{{ $product->unit }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <div class="form-line">
                                <label>Sequence:</label>
                                <input type="number" min="0" class="form-control" name="sequence" value="{{ $product->sequence }}">
                            </div>
                        </div>
                    </div>

                  

                </div>
                <div class="form-group form-float">
                        <div class="form-line">
                            <label>Categories:</label>
                            <select class="form-control" name="categories[]" multiple required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" 
                                        {{ in_array($category->id, $product->categories->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <small class="text-muted">Hold down the Ctrl (Windows) / Command (Mac) key to select multiple options.</small>
                    </div>
                <button class="btn btn-primary waves-effect" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
@stop
