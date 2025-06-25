<div class="form-group">
    <div class="form-line">
    <div class="add-more-field">
        <label for="">Chiefdom</label>
        <div class="demo-google-material-icon " style="right: 0px; position: absolute;">
        @if(isset($key) && $key != 0)
                <i class="material-icons add-more cursor">add</i>
             <i class="material-icons remove-more cursor">clear</i>
        @else
            <i class="material-icons add-more cursor">add</i>
             <i class="material-icons remove-more cursor">clear</i>
        @endif
        </div>

    <input type="text" name="chiefdoms[]" value="{{ !isset($chiefdom) ? '' : $chiefdom }}"  class="form-control" placeholder="" readonly="true">
    </div></div>
</div>
