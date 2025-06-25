<div class="form-group">
    <div class="form-line">
    <div class="add-more-field">
        <label for="">Sections</label>
        <div class="demo-google-material-icon " style="right: 0px; position: absolute;">
            @if(isset($key) && $key != 0)
                <i class="material-icons add-more cursor">add</i>
                <i class="material-icons remove-more cursor">clear</i>
            @else
                <i class="material-icons add-more cursor">add</i>
                <i class="material-icons remove-more cursor">clear</i>
            @endif
        </div>


    <input type="text" name="sections[]" value="{{ !isset($section) ? '' : $section }}"  class="form-control mobile_number" placeholder="" readonly="true">
    </div></div>
</div>
