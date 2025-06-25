@extends('admin.layout.edit')

@section('content')

<style>
    [id^="child-categories-"] {
    }

    [id^="child-categories-"] > div {
        flex-wrap: wrap;
    }

    .child-category {
        width: 150px !important;
    }

    .child-category img {
        width: auto !important;
        height: 72px !important;
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
                <table class="table">
                    <tbody>
                        @foreach ($sellerCategories as $category)
                        <tr>
                            <td class="">

                                <!-- Parent Category Image and Name -->
                                <a href="javascript:void(0);" onclick="toggleChildCategories({{ $category->category_id }})">
                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" style="width: 100px; height: auto;">
                                </a>
                                <div>{{ $category->name }}</div>

                                <!-- Render Child Categories Recursively -->
                                <div id="child-categories-{{ $category->category_id }}" class="child_cat" style="display: none; margin-top: 10px;">
                                    @include('admin.productseller.child.child_categories', ['categories' => $category->children])
                                </div>

                                <!-- If no child categories, display products -->
                               
                                @if ($category->children->isEmpty())
                                <!-- isset($category->children->products) -->
                                <div style="margin-top: 10px;">
                                    <h5>Products in this category:</h5>
                                    <div style="display: flex; gap: 10px; justify-content: center;">
                                        @foreach ($category->children as $subchildren)
                                            @foreach ($subchildren->children as $subsubchildren)
                                                @foreach ($subsubchildren->products as $subsubsubchildren)
                                                
                                                <div class="product text-center">
                                                {{($subsubsubchildren)}}
                                                    <!-- <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="width: 100px; height: auto;">
                                                    <div>{{ $product->name }}</div>
                                                    <div>Price: ${{ $product->price }}</div> -->
                                                </div>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                                @elseif ($category->children->isEmpty())
                                <!-- If the category has no children and no products, show a message -->
                                <div style="margin-top: 10px;">
                                    <p>No products in this category.</p>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleChildCategories(categoryId) {
        const childCategoryDiv = document.getElementById(`child-categories-${categoryId}`);
        if (childCategoryDiv.style.display === 'none') {
            childCategoryDiv.style.display = 'block';
        } else {
            childCategoryDiv.style.display = 'none';
        }
        const productsDiv = document.getElementById(`products-${categoryId}`);
        if (productsDiv) {
            productsDiv.style.display = productsDiv.style.display === 'none' ? 'block' : 'none';
        }
    }
</script>
@stop
