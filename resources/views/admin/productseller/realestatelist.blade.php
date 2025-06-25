@extends('admin.layout.edit')

@section('content')

<style>
    /* Custom styling for child categories */
    [id^="child-categories-"] {
        display: flex;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .child-category {
        width: 150px !important;
    }

    .child-category img {
        width: auto !important;
        height: 72px !important;
    }

    .category-container {
        margin-bottom: 20px;
        text-align: center;
        width: 200px; /* Control the width of each category */
    }

    .parent-categories-row {
        display: flex;
        flex-wrap: wrap; /* Allow categories to wrap if needed */
        justify-content: space-around; /* Space out categories evenly */
        gap: 15px; /* Space between categories */
        margin-bottom: 20px;
    }

    .parent-category {
        width: 150px;
        height: 150px;
    }

    .child_cat {
        display: none; /* Default state is hidden */
        margin-top: 10px;
    }

    .parent-category img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Ensures image is cropped to fit */
    }

    .parentCat {
    margin-bottom: 20px;
    text-align: center;
    width: 200px;
}
</style>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
        @endif

        <div class="body">
            <div class="row">
                <div class="parentCat">
                <img src="http://3.23.33.189/storage/auto_category/nF3XQ7vdg7UvSsCgXDNoUTXh4IE15Aus0he4CakQ.png" alt="">
                <p style="">Real Estate</p>
                </div>
            </div>
            <div class="row showallcategory" style="display:none">
                <!-- Parent Categories Row -->
                <div class="parent-categories-row">
                    @foreach ($parentCategories as $category)
                        <div class="category-container">
                            <!-- Parent Category Image and Name -->
                            <a href="javascript:void(0);" onclick="toggleChildCategories({{ $category->id }})">
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="parent-category">
                            </a>
                            <div>{{ $category->name }}</div>

                            <!-- Render Child Categories and Products -->
                            <div id="child-categories-{{ $category->id }}" class="child_cat">
                                <!-- Display Child Categories -->
                                @if($category->children->isNotEmpty())
                                    <div>
                                        <h5>Subcategories:</h5>
                                        <div style="display: flex; gap: 10px; justify-content: flex-start;">
                                            @foreach ($category->children as $childCategory)
                                                <div class="child-category">
                                                    <a href="javascript:void(0);" onclick="toggleChildCategories({{ $childCategory->id }})">
                                                        <img src="{{ asset('storage/' . $childCategory->image) }}" alt="{{ $childCategory->name }}">
                                                    </a>
                                                    <div>{{ $childCategory->name }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Display Products in this Category -->
                                @if ($category->products->isNotEmpty())
                                    <div style="margin-top: 10px;">
                                        <h5>Products in this category:</h5>
                                        <div style="display: flex; gap: 10px; justify-content: flex-start;">
                                            @foreach ($category->products as $product)
                                            <div class="product text-center">
                                                <img src="{{ asset('storage/' . ($product->image ?? 'default-image.jpg')) }}" alt="{{ $product->name }}" style="width: 100px; height: 80px;">
                                                <div>{{ $product->name }}</div>
                                                <a href="{{ route('admin.edit-property-seller', ['id' => $product->id]) }}">                      
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6" style="height:30px">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg> 
                                                </a>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div style="margin-top: 10px;">
                                        <p>No products in this category.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    // Function to toggle the visibility of child categories and products
    function toggleChildCategories(categoryId) {
        const childCategoryDiv = document.getElementById(`child-categories-${categoryId}`);

        if (childCategoryDiv) {
            const currentDisplay = window.getComputedStyle(childCategoryDiv).display;

            // Toggle display between 'none' and 'block'
            if (currentDisplay === 'none') {
                childCategoryDiv.style.display = 'block';
            } else {
                childCategoryDiv.style.display = 'none';
            }
        }
    }

    $("body").on("click", ".parentCat", function () {
        $(".showallcategory").toggle();
        });
</script>

@stop
