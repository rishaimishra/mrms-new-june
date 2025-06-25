@php $index = isset($loop) ? $loop->index : "\${attributeIndex}" @endphp
<div class="form-group{{ $errors->has('options.' . $index) ? ' has-error' : '' }}">
    <div class="input-group">
        <span class="input-group-addon">
            {!! Form::checkbox('options['.$index.'][is_featured]', null, null, ['class' => 'filled-in', 'id' => "ig_checkbox_{$index}"]) !!}
            <label for="ig_checkbox_{{ $index }}"></label>
        </span>
        <div class="form-line">
        {!! Form::text('options['.$index.'][value]', null, ['class' => 'form-control', 'placeholder' => 'Option']) !!}
        </div>
        <div class="input-group-btn">
            @if($index > 0)
                <button type="button" class="btn btn-danger remove-option">{{ __("Remove") }}</button>
            @endif
        </div>
    </div>
    {!! $errors->first("options.{$index}.value", "<div style='margin-top: -26px; padding-left: 35px' class='error'>:message</div>") !!}
</div>
