<div class="form-group form-float">
    <div class="form-line{{ $errors->has($name) ? ' error' : '' }}">

        <label>{{ $label }}</label>
        {!! Form::text($name, $value ?? null, ['class' => 'form-control']) !!}

    </div>
    {!! $errors->first($name, '<p class="error">:message</p>') !!}
</div>
