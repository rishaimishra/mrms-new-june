<div class="form-group form-float">
    <div class="form-line{{ $errors->has($name) ? ' error' : '' }}">

        <label>{{ $label }}</label>
        {!! Form::select($name, $options, $value ?? null, array_merge(['class' => 'form-control'], (array)$attributes)) !!}

    </div>
    {!! $errors->first($name, '<p class="error">:message</p>') !!}
</div>
