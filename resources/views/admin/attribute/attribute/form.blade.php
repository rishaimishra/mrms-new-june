<div class="row">
    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>{{ __("General Details") }}</h2>
            </div>
            <div class="body">
                {!! Form::materialText("Label", 'frontend_label') !!}

                {!! Form::materialText("Code", 'attribute_code', null,  ['disabled' => isset($attribute)]) !!}

                {!! Form::materialSelect("Attribute Set", 'attribute_set', $attributeSets, null, ['id' => 'attribute-set-id']) !!}

                {!! Form::materialSelect("Attribute Group", 'attribute_group', $attributeGroups, null, ['id' => 'attribute-groups']) !!}

                {!! Form::materialSelect("Frontend Type", 'frontend_type', ['input' => 'input', 'select' => 'Select'], null, ['placeholder' => 'Choose a option', 'disabled' => isset($attribute), 'id' => 'frontend_type']) !!}

                {!! Form::materialText("Sequence", 'sequence') !!}
                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
            </div>
        </div>

        {!! Form::submit("Save", ['class' => 'btn btn-primary']) !!}
        @isset($attribute)
            {!! Form::button("Delete", ['class' => 'btn btn-link btn-lg', 'id' => 'delete']) !!}
        @endisset
    </div>

    <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 options-col"
         style="display: {{ old('frontend_type', isset($attribute) ? $attribute->frontend_type : 'input') == 'select' ? 'block' : 'none' }}">
        <div class="card">
            <div class="header">
                <h2>{{ __("Options") }}</h2>
            </div>

            <div class="body">

                <div id="options">

                    @foreach(old('options', isset($attribute) ? $attribute->options() : [[]]) as $option)
                        @include('admin.attribute.attribute.option')
                    @endforeach
                </div>

                <button type="button" class="btn btn-primary" id="add-option">Add Option</button>

                {!! $errors->first('options', '<p class="help-text">:message</p>') !!}
            </div>
        </div>
    </div>
</div>



@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {

            $("#attribute-set-id").on('change', function () {

                fetch('/sl-admin/attribute/attribute-groups-by-attribute-set/' + $(this).val())
                    .then(res => res.json())
                    .then(res => {
                        var options = res.map(option => `<option value="${option.attributeGroupName}">${option.attributeGroupName}</option>`);
                        $("#attribute-groups").html(options).selectpicker('refresh');
                    })
            });

            var attributeIndex = {{ isset($attribute) ? count($attribute->options()) -1 : '0' }}

            $("#add-option").on('click', function () {

                attributeIndex++;

                var optionTemplate = $(`@include('admin.attribute.attribute.option', ['index' => null])`)

                var removeButton = $('<div class="input-group-btn"><button class="btn btn-danger">Remove</button></div>').on('click', function () {
                    $(this).parent().remove();
                })

                optionTemplate.find('.input-group').append(removeButton);

                $("#options").append(optionTemplate);

                $.AdminBSB.input.activate();
            });

            $("button.remove-option").on('click', function () {
                $(this).closest('.form-group').remove();
            })

            $("#frontend_type").on('change', function () {
                if ($(this).val() === 'select') {
                    $('.options-col').show();
                } else {
                    $('.options-col').hide();
                }
            });
        });
    </script>
@endpush
