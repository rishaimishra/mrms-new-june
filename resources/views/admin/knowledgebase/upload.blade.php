@extends('admin.layout.edit')

@section('content')
    @include('admin.layout.partial.alert')


    {!! Form::open(['files' => true, 'route' => 'admin.question.import']) !!}

    <div class="row">
        <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        @isset($knowledgebaseCategory->id)
                            Edit
                        @else
                            Add
                        @endisset
                        Fun & Games

                    </h2>
                </div>
                <div class="body">

                    <div class="row">

                        <div class="col-sm-6">

                            {!! Form::materialFile('Uplolad Quiz:', 'select_file', $errors->first('select_file')) !!}

                        </div>
                    </div>

                    <button class="btn btn-primary waves-effect" type="submit">Save</button>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="header">
                    <h2>Categories</h2>
                </div>
                <div class="body list-tree">
                    @if ($errors->has('categories'))
                        <label class="error">{{ $errors->first('categories') }}</label>
                    @endif
                    @include('admin.knowledgebase.categories', ['categories' => $categories, 'parent' => null])
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}

@stop
