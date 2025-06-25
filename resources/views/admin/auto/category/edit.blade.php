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
    @isset($autoCategory->id)
        {!! Form::model($autoCategory, ['files' => true, 'route' => ['admin.auto-category.update', $autoCategory->id],'method' => 'PATCH']) !!}
    @else
        {!!Form::open(['route' => 'admin.auto-category.store','files' => true]) !!}
    @endisset

    <div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">

                <h2>
                    Add / Edit Auto Category
                </h2>


            </div>
            <div class="body">



                <div class="row">
                    <div class="col-sm-12">
                        {!! Form::materialText('Name', 'name') !!}
                    </div>
                    <div class="col-sm-12">
                        <label class="form-label">Parent Category</label>
                        {!! Form::select('parent_id', $mainCategories, old('parent_id',optional($autoCategory)->parent_id), ['class' => 'form-control', 'id' => 'parent','data-live-search'=>'true']) !!}
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
                           id="customCheck1" {{old('is_active',optional($autoCategory)->is_active)==1?'checked':''}} >
                    <label class="custom-control-label" for="customCheck1">Active </label>

                </div>
                <button class="btn btn-primary waves-effect" type="submit">Save</button>
                @isset($autoCategory->id)
                    {{ Form::button('Delete', ['type' => 'button', 'class' => 'btn btn-warning btn-sm delete'] )  }}
                @endisset
            </div>
        </div>
        <div class="card">
            <div class="body">
                {!! Form::materialFile('Image', 'images', $errors->first('images')) !!}
                @if(isset($autoCategory->image) && ($autoCategory->image))
                    <img width="150" height="150" src="{{ asset('storage/' . $autoCategory->image) }}" alt=""
                         class="img-responsive" style=" margin: 0 auto; "/>

                @endif
            </div>
        </div>
        <div class="card">
            <div class="body">
                {!! Form::materialFile('Background Image', 'bg_image', $errors->first('bg_image')) !!}
                @if(isset($autoCategory->background_image) && ($autoCategory->background_image))
                    <img width="150" height="150" src="{{ asset('storage/' . $autoCategory->background_image) }}" alt=""
                         class="img-responsive" style=" margin: 0 auto; "/>
                @endif
                
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    @isset($autoCategory->id)
        {!! Form::open(['route' => ['admin.auto-category.destroy', $autoCategory->id], 'method' => 'DELETE','class'=>'delete','id'=>'deleteForm']) !!}

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
