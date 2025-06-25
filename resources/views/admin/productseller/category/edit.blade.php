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
</style>
    @isset($productCategory->id)
        {!! Form::model($productCategory, ['files' => true, 'route' => ['admin.product-category.update', $productCategory->id],'method' => 'PATCH']) !!}
    @else
        {!!Form::open(['files' => true, 'route' => 'admin.product-category.store']) !!}
    @endisset

    <div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                    <h2>Add / Edit Product Category</h2>
            </div>
            <div class="body">
                    <div class="row">
                        <div class="col-sm-12">
                            {!! Form::materialText('Name', 'name') !!}
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label">Parent Category</label>
                            {!! Form::select('parent_id', $mainCategories, old('parent_id',optional($productCategory)->parent_id), ['class' => 'form-control', 'id' => 'parent','data-live-search'=>'true']) !!}
                            {!! $errors->first('parent', '<p class="error">:message</p>') !!}


                        </div>
                        <div class="col-sm-12">
                            {!! Form::materialText('Sponsor Text', 'sponsor_text') !!}
                        </div>
                        <div class="col-sm-12">
                            {!! Form::materialText('Sequence', 'sequence') !!}
                        </div>
                </div>


        </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="body">
                <div class="custom-control custom-checkbox">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="custom-control-input filled-in"
                           id="customCheck1" {{old('is_active',optional($productCategory)->is_active)==1?'checked':''}} >
                    <label class="custom-control-label" for="customCheck1">Active </label>

                </div>
                <button class="btn btn-primary waves-effect" type="submit">Save</button>
                @isset($productCategory->id)
                    {{ Form::button('Delete', ['type' => 'button', 'class' => 'btn btn-warning btn-sm delete'] )  }}
                @endisset
            </div>
        </div>
        <div class="card">
            <div class="body">
                {!! Form::materialFile('Image', 'images', $errors->first('images')) !!}
                @if(isset($productCategory->image) && ($productCategory->image))
                    <img  src="{{ asset('storage/' . $productCategory->image) }}" alt=""
                          class="img-responsive" style=" margin: 10px auto; "/>

                @endif
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    @isset($productCategory->id)
        {!! Form::open(['route' => ['admin.product-category.destroy', $productCategory->id], 'method' => 'DELETE','class'=>'delete','id'=>'deleteForm']) !!}

        {!! Form::close() !!}
    @endisset
    <script>
        $(document).ready(function(){

            $(".delete").click(function(){
                if(confirm("Are you sure?")){
                    $("#deleteForm").submit(); // Submit the form
                }

            });
        });

    </script>
@stop
