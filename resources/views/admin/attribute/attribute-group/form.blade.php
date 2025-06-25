<div class="row">
    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>{{ __("General Details") }}</h2>
            </div>
            <div class="body">
                {!! Form::materialText("Name", 'attribute_group_name') !!}

                {!! Form::materialSelect("Attribute Set", 'attribute_set_id', $attributeSets) !!}

                {!! Form::materialText("Sequence", 'sequence') !!}
            </div>
        </div>
        {!! Form::submit("Save", ['class' => 'btn btn-primary btn-lg']) !!}

        @isset($attributeGroup)
            {!! Form::button("Delete", ['class' => 'btn btn-link btn-lg', 'id' => 'delete']) !!}
        @endisset
    </div>
</div>
