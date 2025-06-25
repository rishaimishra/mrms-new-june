@php $filteredCategories = $categories->where('parent_id', $parent) @endphp
<ul>
    @foreach($filteredCategories as $category)
        @php $hasChild = $category->where('parent_id', $category->id)->first(); @endphp
        <li>

            @if(! $hasChild)
                <div class="checkbox">
                    {!! Form::checkbox('categories[]', $category->id, null, ['class' => '', 'id' => 'category-' . $category->id]) !!}
                    <label for="category-{{ $category->id }}">{{ $category->name }}</label>
                </div>
            @else
                <p>{{ $category->name }}</p>
            @endif


            @if($hasChild)
                @include('admin.place.categories', ['categories' => $categories, 'parent' => $category->id])
            @endif
        </li>
    @endforeach
</ul>