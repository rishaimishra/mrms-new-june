<div class="row">
    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>{{ __("General Details") }}</h2>
            </div>
            <div class="body">
                {!! Form::materialText("Name", 'attribute_set_name') !!}

                {!! Form::materialSelect("Entity", 'entity_id', $entities) !!}
            </div>
        </div>
        {!! Form::submit("Save", ['class' => 'btn btn-primary btn-lg']) !!}

        @isset($attributeSet)
            {!! Form::button("Delete", ['class' => 'btn btn-link btn-lg', 'id' => 'delete']) !!}
        @endisset
    </div>
</div>
