@foreach ($categories as $category)
    <div class="child-category">
        <a href="javascript:void(0);" onclick="toggleChildCategories({{ $category->category_id }})">
            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" style="width: 100px; height: auto;">
        </a>
        <div>{{ $category->name }}12</div>

        <!-- Render sub-child categories recursively -->
        @if ($category->children->isNotEmpty())
        <div id="child-categories-{{ $category->category_id }}" class="child_cat" style="display: none; margin-top: 10px;">
            @include('admin.productseller.child.child_autocategories', ['categories' => $category->children])
        </div>
        @endif
    </div>
@endforeach
