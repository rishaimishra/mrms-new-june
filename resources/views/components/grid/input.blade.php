<div class="form-group form-float" style="margin-bottom: 0">
    <div class="form-line">
        {!! Form::text("grid[filter][{$column['field']}]", request('grid.filter.' . $column['field']), ['class' => 'form-control form-control-sm', 'placeholder' => ('Search for ' . $column['orig_label'])]) !!}
    </div>
</div>