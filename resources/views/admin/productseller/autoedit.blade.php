@extends('admin.layout.edit')

@section('content')
<style>
    .bootstrap-select.btn-group .dropdown-menu{
           width:100% !important;
       }
       .bootstrap-select .bs-searchbox .form-control, .bootstrap-select .bs-actionsbox .form-control, .bootstrap-select .bs-donebutton .form-control{
           margin-left:0px !important;
       }
       .bootstrap-select .bs-searchbox:after{
           display: none;
       }
       .bootstrap-select.btn-group .dropdown-toggle .caret{
           left: 0px;
       }
       #images{
           width:100%;
       }
</style>
<div class="container">
    <h2>{{ $auto ? 'Edit Auto' : 'Create New Auto' }}</h2>

    <form action="{{ route('admin.save-auto', ['id' => $auto->id ?? null]) }}" method="POST" enctype="multipart/form-data">
        @csrf
       

        <!-- Auto Name -->
        <div class="form-group">
            <label for="name">Auto Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $auto->name ?? '') }}" required>
        </div>

        <!-- Images -->
        <div class="form-group">
            <label for="images">Auto Images</label>
            <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
            <div id="image-preview" class="mt-2"></div> <!-- Preview Images -->
        </div>


        <!-- Auto Title -->
        <div class="form-group">
            <label for="title">Auto Title</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $auto->title ?? '') }}" required>
        </div>

        <!-- About Auto -->
        <div class="form-group">
            <label for="about">About</label>
            <textarea class="form-control" id="about" name="about">{{ old('about', $auto->about ?? '') }}</textarea>
        </div>

        <!-- Attribute Set -->
        <div class="form-group">
            <label for="attribute_set_id">Attribute Set</label>
            <select class="form-control" id="attribute_set_id" name="attribute_set_id" disabled>
                <option value="">Select Attribute Set</option>
                @foreach ($attributeSets as $attributeSet)
                    <option value="{{ $attributeSet->attribute_set_id }}" 
                        @if($auto && $auto->attribute_set_id == $attributeSet->attribute_set_id) selected @endif>
                        {{ $attributeSet->attribute_set_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Attribute Group -->
        <div class="form-group" id="attribute_group_section" style="{{ $auto ? 'display:block;' : 'display:none;' }}">
            <label for="attribute_group_id">Attribute Group</label>
            <select class="form-control" id="attribute_group_id" name="attribute_group_id" disabled>
                <option value="">Select Attribute Group</option>
                @foreach ($attributeGroups as $attributeGroup)
                    <option value="{{ $attributeGroup->attribute_group_id }}" 
                        @if($auto && $auto->attribute_group_id == $attributeGroup->attribute_group_id) selected @endif>
                        {{ $attributeGroup->attribute_group_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Attributes -->
        <div class="form-group" id="attributes_section" style="{{ $auto ? 'display:block;' : 'display:none;' }}">
            <label for="attribute_id">Attribute</label>
            <select class="form-control" id="attribute_id" name="attribute_id">
                <option value="">Select Attribute</option>
                @foreach ($attributes as $attribute)
                    <option value="{{ $attribute->attribute_id }}" 
                        @if($auto && $auto->attribute_id == $attribute->attribute_id) selected @endif>
                        {{ $attribute->attribute_code }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Categories -->
        <div class="form-group">
            <label for="categories">Select Categories</label>
            <select class="form-control" id="categories" name="categories[]" multiple disabled>
                @foreach ($autoCategories as $category)
                    <option value="{{ $category->id }}" 
                        @if(in_array($category->id, $autoCategoryIds)) selected @endif>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>


        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Save Auto</button>
    </form>
</div>

@push('scripts')
<script>
    // Handle Attribute Group and Attribute dropdowns based on the selected Attribute Set

    // Show/hide Attribute Group and Attributes sections based on Attribute Set selection
    document.getElementById('attribute_set_id').addEventListener('change', function() {
        var selectedAttributeSetId = this.value;
        var attributeGroupSection = document.getElementById('attribute_group_section');
        var attributesSection = document.getElementById('attributes_section');

        if (selectedAttributeSetId) {
            // Show the attribute group dropdown
            attributeGroupSection.style.display = 'block';

            // Optionally, use AJAX to load the attribute groups based on selected attribute set
            fetch(`/api/attribute-groups/${selectedAttributeSetId}`)
                .then(response => response.json())
                .then(data => {
                    var attributeGroupSelect = document.getElementById('attribute_group_id');
                    attributeGroupSelect.innerHTML = '<option value="">Select Attribute Group</option>';
                    data.forEach(attributeGroup => {
                        var option = document.createElement('option');
                        option.value = attributeGroup.attribute_group_id;
                        option.textContent = attributeGroup.attribute_group_name;
                        attributeGroupSelect.appendChild(option);
                    });

                    // Reset the attributes dropdown
                    attributesSection.style.display = 'none';
                });
        } else {
            // Hide both the attribute group and attributes sections
            attributeGroupSection.style.display = 'none';
            attributesSection.style.display = 'none';
        }
    });

    // Handle Attribute selection based on selected Attribute Group
    document.getElementById('attribute_group_id').addEventListener('change', function() {
        var selectedAttributeGroupId = this.value;
        var attributesSection = document.getElementById('attributes_section');

        if (selectedAttributeGroupId) {
            // Show the attributes dropdown
            attributesSection.style.display = 'block';

            // Optionally, use AJAX to load the attributes for the selected attribute group
            fetch(`/api/attributes/${selectedAttributeGroupId}`)
                .then(response => response.json())
                .then(data => {
                    var attributeSelect = document.getElementById('attribute_id');
                    attributeSelect.innerHTML = '<option value="">Select Attribute</option>';
                    data.forEach(attribute => {
                        var option = document.createElement('option');
                        option.value = attribute.attribute_id;
                        option.textContent = attribute.attribute_code;
                        attributeSelect.appendChild(option);
                    });
                });
        } else {
            // Hide the attributes section if no attribute group is selected
            attributesSection.style.display = 'none';
        }
    });


</script>
@endpush

@endsection