<style>
    svg{
        width: 20px;
    }
</style>
<div style="display: flex; gap: 10px; justify-content: center;">
    @foreach ($categories as $child)
        <div class="child-category text-center">
            <a href="javascript:void(0);" onclick="toggleChildCategories({{ $child->id }})">
                <img src="{{ asset('storage/' . $child->image) }}" alt="{{ $child->name }}" style="width: 80px; height: auto;">
            </a>
            <div>{{ $child->name }}</div>

            <!-- Products, initially hidden -->
            <div id="products-{{ $child->id }}" style="display: none; margin-top: 10px;">
                @foreach ($child->products??[] as $product)
              
                    <div>
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="width: 100px; height: auto;">
                        {{--  <div>{{ $product->id }}</div>  --}}
                        <div>{{ $product->name }}</div>
                        {{--  <div>{{ $product->user_id }}</div>  --}}
                        <div>Price: (NLe){{ $product->price }}</div>
                        <svg data-toggle="modal" data-target="#exampleModal{{ $product->id }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>   
                        <a href="{{ route('admin.edit-product-seller', ['id' => $product->id,'type' => 'similar']) }}">                      
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                          </svg> 
                        </a>
                          @if ($product->user_id == Auth::user()->id)
                          <a href="{{ route('admin.edit-product-seller', ['id' => $product->id,'type' => 'edit']) }}">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                          </svg>
                        </a>

                          <svg data-toggle="modal" data-target="#delete{{ $product->id }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                          </svg>
                           
                          @endif                          
                                                                          
                    </div>
                    <div class="modal fade" id="exampleModal{{ $product->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">{{ $product->name }}</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="width: 100px; height: auto;">
                                <div>{{ $product->name }}</div>
                                <div>Price: (NLe){{ $product->price }}</div>
                                <div>Quantity: {{ $product->quantity }}</div>
                                <div>Weight: {{ $product->weight }}</div>
                                <div>Availblity: {{ $product->stock_availability }}</div>
                                <div>Unit: {{ $product->unit }}</div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                          </div>
                        </div>
                    </div>
                    <div class="modal fade" id="delete{{ $product->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">{{ $product->name }}</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                               <p>Are you sure you want to delete this?</p>
                            </div>
                            <div class="modal-footer">
                                <a href="{{ route('admin.delete-product-seller', ['id' => $product->id]) }}" class="btn btn-primary">Yes</a>
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                            </div>
                          </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Further child categories, initially hidden -->
            @if ($child->children)
                <div id="child-categories-{{ $child->id }}" style="display: none; margin-top: 10px;">
                    @include('admin.productseller.child.child_categories', ['categories' => $child->children])
                </div>
            @endif
        </div>
    @endforeach
</div>

<script>
    function toggleChildCategories(categoryId) {
        const productsElement = document.getElementById(`products-${categoryId}`);
        const childCategoryElement = document.getElementById(`child-categories-${categoryId}`);

        // Toggle visibility of products and child categories
        if (productsElement) {
            productsElement.style.display = productsElement.style.display === 'none' ? 'block' : 'none';
        }

        if (childCategoryElement) {
            childCategoryElement.style.display = childCategoryElement.style.display === 'none' ? 'block' : 'none';
        }
    }
</script>
