@extends('admin.layout.edit')

@section('content')
    @include('admin.layout.partial.alert')
    @isset($question->id)
        {!! Form::model($question, ['files' => true, 'route' => ['admin.question.update', $question->id],'method' => 'PATCH']) !!}
    @else
        {!!Form::open(['files' => true, 'route' => 'admin.question.store']) !!}
    @endisset
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
                        @isset($question->id)
                            <div class="pull-right">
                                {{ Form::button('Delete', ['type' => 'button', 'class' => 'btn btn-warning btn-sm delete'] )  }}
                            </div>
                        @endisset
                    </h2>
            </div>
            <div class="body">

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <label>Question: </label>

                                <input type="text" class="form-control"  name="question" required value="{{old('question',$question->question)}}">


                            </div>
                            @if ($errors->has('question'))
                                <label class="error">{{ $errors->first('question') }}</label>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        {!! Form::materialFile('Question Image:', 'question_image', $errors->first('question')) !!}

                        @if(isset($question->image))
                            <img width="150" height="150" src="{{ asset('storage/' . $question->image) }}" alt="" class="img-responsive" style=" margin: 0 auto;padding: 10px;"/>

                        @endif
                    </div>
                </div>

                @for($i=0;$i<=4;$i++)
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <label>Option {{$i+1}}:</label>
                                <input type="hidden" name="options[{{$i}}][id]" value="{{$question->options[$i]->id??''}}">
                                <input type="text" class="form-control"  name="options[{{$i}}][text]" value="{{$question->options[$i]->option_value??''}}" >

                            </div>
                            @error('options.$i.text')
                            {!! $errors->first('options.$i.text', '<p class="help-text">:message</p>') !!}
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-4">
                        {!! Form::materialFile('Option '. ($i+1) .' Image:', "options[$i][image]", $errors->first("option[0]['image']"),["class"=>"pull-left"]) !!}
                        @if(isset($question->options[$i]->option_image) && ($question->options[$i]->option_image))
                            <img width="150" height="150" src="{{ asset('storage/' . $question->options[$i]->option_image) }}" alt="" class="img-responsive" style="margin: 0 auto;width: 10%;float: right;padding: 10px;"/>

                        @endif
                    </div>
                    <div class="col-sm-4">
                        <div class="demo-radio-button">
                            <input name="is_answer" type="radio" id="radio_{{$i}}" class="radio-col-red" value="{{$i}}" {{(isset($question->options[$i]->is_answer) && ($question->options[$i]->is_answer)?'checked':'')}} />
                            <label for="radio_{{$i}}">Is Answer</label>
                        </div>

                    </div>
                </div>
                @endfor




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
                    @include('admin.knowledgebase.categories', ['categories' => $categories, 'parent' => null])
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    @isset($question->id)
        {!! Form::open(['route' => ['admin.question.destroy', $question->id], 'method' => 'DELETE','class'=>'delete','id'=>'deleteForm']) !!}

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
