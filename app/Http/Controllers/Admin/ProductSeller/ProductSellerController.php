<?php


namespace App\Http\Controllers\Admin\ProductSeller;


use App\Http\Controllers\Controller;
use App\Library\Grid\Grid;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SellerCategory;
use App\Models\ProductImage;
use App\Models\Auto;
use App\Models\AutoCategory;

use Eav\Attribute;
use Eav\AttributeGroup;
use Eav\AttributeSet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use DB;

class ProductSellerController extends Controller
{
    const ENTITY_ID = 3;

    public function index()
    {
        $userId = auth()->id();
    
        // Function to fetch categories with their children recursively
        function fetchCategoriesWithChildren($categories, $parentId = null)
        {
            $children = [];
    
            // Fetch categories with the specified parent_id
            foreach ($categories as $category) {
                if ($category->parent_id === $parentId) {
                    // Recursively get children for the current category
                    $category->children = fetchCategoriesWithChildren($categories, $category->id);
                    $children[] = $category;
                }
            }
    
            return collect($children);  // Ensure it's a collection
        }
    
        // Fetch all categories (including parent and child categories)
        $categories = DB::table('product_categories')
            ->select('id', 'name', 'image', 'parent_id')
            ->orderby('sequence')
            ->get();
    
        // Fetch seller categories for the authenticated seller
        $sellerCategories = DB::table('seller_categories')
            ->join('product_categories', 'seller_categories.category_id', '=', 'product_categories.id')
            ->where('seller_categories.seller_id', '=', $userId)
            ->select('product_categories.id as category_id', 'product_categories.name', 'product_categories.image')
            ->get();
    
        // Fetch the entire nested category structure for each seller category
        foreach ($sellerCategories as $category) {
            // Fetch children categories and wrap them as a collection
            $category->children = fetchCategoriesWithChildren($categories, $category->category_id);
    
            // Check if the category has no children (empty collection)
            if ($category->children->isEmpty()) {
                // No children, so fetch products for this category
                $category->products = DB::table('product_product_categories')
                    ->join('products', 'product_product_categories.product_id', '=', 'products.id')
                    ->where('product_product_categories.product_category_id', $category->category_id)
                    ->select('products.id', 'products.name', 'products.price')
                    ->get();
            } else {
                // If there are children, loop through them and check if they have products or more children
                $category->products = collect();  // Initialize empty collection for products
    
                foreach ($category->children as $childCategory) {
                    if ($childCategory->children->isEmpty()) {
                        // Child has no further children, so fetch products for this child category
                        $products = DB::table('product_product_categories')
                            ->join('products', 'product_product_categories.product_id', '=', 'products.id')
                            ->where('product_product_categories.product_category_id', $childCategory->id)  // Use child category ID
                            ->select('products.id', 'products.name', 'products.price')
                            ->get();
    
                        $category->products = $category->products->merge($products);  // Merge products for the parent category
                    } else {
                        // Recursively handle deeper children (this section can be extended if needed)
                        foreach ($childCategory->children as $childCategory2) {
                           
                            if ($childCategory2->children->isEmpty()) {
                                // Fetch products for childCategory2 (if it has no further children)
                                $products = DB::table('product_product_categories')
                                ->join('products', 'product_product_categories.product_id', '=', 'products.id')
                                ->join('product_images', 'products.id', '=', 'product_images.product_id') // Join with the product_images table
                                ->where('product_product_categories.product_category_id', $childCategory2->id) // Use childCategory2 ID
                                ->select('products.*','products.id', 'products.name', 'products.price', 'product_images.image') // Select the image column
                                ->get();
                                // return $products;
                                // $category->products = $category->products->merge($products);  // Merge products
                                $childCategory2->products = $products;  // Merge products
                                // return $category;
                            }
                        }
                    }
                }
            }
        }
    
        // Debugging: Check the result before returning it to the view
        // dd($sellerCategories); 
        // return $sellerCategories;
        // Return the seller categories with their children and products to the view
        return view('admin.productseller.grid', compact('sellerCategories'));
    }
    public function Autoindex()
    {
        // Fetch all categories (including parent and child categories)
        $categories = DB::table('auto_categories')
            ->select('id', 'name', 'image', 'parent_id')
            ->orderBy('sequence')
            ->get();
    
        // Function to fetch categories with their children recursively
        function fetchCategoriesWithChildren($categories, $parentId = null)
        {
            $children = [];
        
            // Fetch categories with the specified parent_id
            foreach ($categories as $category) {
                if ($category->parent_id === $parentId) {
                    // Recursively get children for the current category
                    $category->children = fetchCategoriesWithChildren($categories, $category->id);
                    
                    // Manually fetch products (autos) for this category using the pivot table
                    $category->products = DB::table('autos')
                        ->join('auto_auto_categories', 'autos.id', '=', 'auto_auto_categories.auto_id')
                        ->join('auto_categories', 'auto_auto_categories.auto_category_id', '=', 'auto_categories.id')
                        ->leftJoin('auto_images', 'autos.id', '=', 'auto_images.auto_id')  // Left join to get images
                        ->where('auto_auto_categories.auto_category_id', $category->id)
                        ->select('autos.id', 'autos.name', 'autos.title', 'auto_images.image') // Select auto details and first image
                        ->groupBy('autos.id')
                        ->get();
    
                    $children[] = $category;
                }
            }
        
            return collect($children);  // Ensure it's a collection
        }
    
        // Fetch seller categories for the authenticated seller
        $sellerCategories = DB::table('seller_categories')
            ->join('auto_categories', 'seller_categories.auto_category_id', '=', 'auto_categories.id')
            ->select('auto_categories.id as category_id', 'auto_categories.name', 'auto_categories.image')
            ->get();
    
        // Add children and products for each seller category
        foreach ($sellerCategories as $category) {
            $category->children = fetchCategoriesWithChildren($categories, $category->category_id);
        }
    
        // Fetch all parent categories without children to show on the first row
        $parentCategories = fetchCategoriesWithChildren($categories, null);
        return view('admin.productseller.autogrid', compact('sellerCategories', 'parentCategories'));
    }
    
    
    public function PropertyIndex()
    {
        // Fetch all categories (including parent and child categories)
        $categories = DB::table('real_estate_categories')
            ->select('id', 'name', 'image', 'parent_id')
            ->orderBy('sequence')
            ->get();
    
        // Function to fetch categories with their children recursively
        function fetchCategoriesWithChildren($categories, $parentId = null)
        {
            $children = [];
        
            // Fetch categories with the specified parent_id
            foreach ($categories as $category) {
                if ($category->parent_id === $parentId) {
                    // Recursively get children for the current category
                    $category->children = fetchCategoriesWithChildren($categories, $category->id);
                    
                    // Manually fetch products (autos) for this category using the pivot table
                    $category->products = DB::table('real_estates')
                        ->join('real_estate_real_estate_categories', 'real_estates.id', '=', 'real_estate_real_estate_categories.real_id')
                        ->join('real_estate_categories', 'real_estate_real_estate_categories.real_category_id', '=', 'real_estate_categories.id')
                        ->leftJoin('real_estate_images', 'real_estates.id', '=', 'real_estate_images.real_estate_id')  // Left join to get images
                        ->where('real_estate_real_estate_categories.real_category_id', $category->id)
                        ->select('real_estates.id', 'real_estates.name', 'real_estates.title', 'real_estate_images.image') // Select auto details and first image
                        ->groupBy('real_estates.id')
                        ->get();
    
                    $children[] = $category;
                }
            }
        
            return collect($children);  // Ensure it's a collection
        }
    
        // Fetch seller categories for the authenticated seller
        $sellerCategories = DB::table('seller_categories')
            ->join('real_estate_categories', 'seller_categories.realstate_category_id', '=', 'real_estate_categories.id')
            ->select('real_estate_categories.id as category_id', 'real_estate_categories.name', 'real_estate_categories.image')
            ->get();
    
        // Add children and products for each seller category
        foreach ($sellerCategories as $category) {
            $category->children = fetchCategoriesWithChildren($categories, $category->category_id);
        }
    
        // Fetch all parent categories without children to show on the first row
        $parentCategories = fetchCategoriesWithChildren($categories, null);
        return view('admin.productseller.realestatelist', compact('sellerCategories', 'parentCategories'));
    }
    
    
    
    public function Myindex()
    {
        $userId = auth()->id();
        $product = Product::where('user_id',$userId)->get();
        // dd($product);
      
        return view('admin.productseller.myproductlist', compact('product'));
    }
    public function create()
    {
        $categories = ProductCategory::get();
        $product = new Product();

        $attributeSets = AttributeSet::where('entity_id', self::ENTITY_ID)->pluck('attribute_set_name', 'attribute_set_id');

        return view('admin.productseller.edit', compact('categories', 'product', 'attributeSets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'attribute_set_id' => ['required'],
            'quantity' => 'required|numeric|min:1|max:999999',
            'weight' => 'required|numeric|min:0|max:999999',
            'price' => 'required|regex:/^(?!,$)[\d,.]+$/|string|max:20',
            'unit' => 'required|string|max:50',
            'images' => 'required|array',
            'stock_availability' => ['required', Rule::in(Product::STOCK_AVAILABILITY_OPTIONS)],
            'images.*' => 'required|file|mimes:jpg,jpeg,png',
            'categories' => 'required|array',
            'categories.*' => 'required||exists:product_categories,id',
            'sequence' => 'nullable|numeric|min:1|max:999999',
        ]);

        $product = new Product();
        $product->entity_id = self::ENTITY_ID;
        $product->name = $request->name;
        $product->quantity = $request->quantity;
        $product->weight = $request->weight;
        $product->price = str_replace(',', '', $request->price);
        $product->unit = $request->unit;
        $product->sequence = $request->input('sequence', '');
        $product->attribute_set_id = $request->attribute_set_id;
        $product->stock_availability = $request->input('stock_availability');

        $product->forceFill([
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $product->save();

        $product->categories()->sync($request->categories);

        if ($request->hasFile('images')) {

            if ((count($request->images)) > 20) {
                return redirect()->back()->with($this->setMessage('You can not upload product images more then 20', self::MESSAGE_ERROR))->withInput();
            }

            foreach ($request->images as $image) {
                //$path = \Storage::disk('public')->putFile('basket', $image);
                $path = $this->resizeImage($image, 'basket', 800);
                $product->images()->create(['image' => $path]);
            }
        }
        return redirect()->route('admin.productseller.index')->with($this->setMessage('Product Saved Successfully', self::MESSAGE_SUCCESS));
    }

    protected function getAttributes(Product $product)
    {
        $attributeSet = AttributeSet::find($product->attribute_set_id);

        if (! $attributeSet) {
            return false;
        }

        return $attributeSet->groups()->whereHas('attributes')->with('attributes.optionValues')->get();
    }

    public function product_edit($id, Request $request,$type)
    {
        $product = Product::select(['*', 'attr.*'])->findOrFail($id);

        $categories = ProductCategory::get();

        $attributeGroups = $this->getAttributes($product);

        $attributeSets = AttributeSet::where('entity_id', self::ENTITY_ID)->pluck('attribute_set_name', 'attribute_set_id');
         
        return view('admin.product.edit', compact('categories', 'product', 'attributeGroups', 'attributeSets','type'));
    }
  

    public function MyProductedit($id)
    {
        $product = Product::select(['*', 'attr.*'])->findOrFail($id);
        if (Auth::user()->user_type == 'shop') {
            //     $categories = SellerCategory::where('seller_id', Auth::user()->id)
            //    ->with('categories')
            //    ->get()
            //    ->pluck('categories')
            //    ->flatten();
            $category_id = SellerCategory::where('seller_id', Auth::user()->id)->pluck('category_id');
            $categories = ProductCategory::where(function ($query) use ($category_id) {
                $query->where('id', $category_id)
                      ->orWhere('parent_id', $category_id);
            })->get();
            }else{
                $categories = ProductCategory::get();
            }
        // $categories = ProductCategory::get();

        $attributeGroups = $this->getAttributes($product);
        $attributeSets = AttributeSet::where('entity_id', self::ENTITY_ID)->pluck('attribute_set_name', 'attribute_set_id');

        // Fetch product images
    $productImages = ProductImage::where('product_id', $id)->get();
      
            return view('admin.productseller.myproductlistEdit', compact('categories', 'product', 'attributeGroups', 'attributeSets','productImages'));
        
        
    }

    public function update_product(Product $product, Request $request)
    {  
        // return $request;
        $product = Product::where('id',$request->product_id)->first();

        // return $request;
        if ($request->type == 'edit') {
            $request->validate([
                'name' => 'required|string|max:191',
                'quantity' => 'required|numeric|min:1|max:999999',
                'weight' => 'required|numeric|min:0|max:999999',
                'price' => 'required|regex:/^(?!,$)[\d,.]+$/|string|max:20',
                'unit' => 'required|string|max:50',
                'images' => 'nullable|array',
                'stock_availability' => ['required', Rule::in(Product::STOCK_AVAILABILITY_OPTIONS)],
                'images.*' => 'required|file|mimes:jpg,jpeg,png',
                'categories' => 'required|array',
                'categories.*' => 'required||exists:product_categories,id',
                'sequence' => 'nullable|numeric|min:1|max:9999',
            ]);


            if ($request->input('attribute_set_id')) {
                $product->attribute_set_id = $request->input('attribute_set_id');
                $product->save();
            }
    
            $attributeGroups = $this->getAttributes($product);
    
            $additionalRules = $attributeGroups->flatMap(function (AttributeGroup $attributeGroup) {
                return $attributeGroup->attributes->map(function (Attribute $attribute) {
                    return [
                        $attribute->attribute_code => [$attribute->is_required ? 'required' : 'nullable', 'string']
                    ];
                });
            })->toArray();
    
         
    
            $attributeValues = $attributeGroups->flatMap(function (AttributeGroup $attributeGroup) {
                return $attributeGroup->attributes->pluck('attribute_code')->map(function ($code) {
                    return [$code => \request($code)];
                });
            })->toArray();
    
            $product->name = $request->name;
            $product->quantity = $request->quantity;
            $product->weight = $request->weight;
            $product->price = str_replace(',', '', $request->price); //$request->price;
            $product->unit = $request->unit;
            $product->sequence = $request->input('sequence', '');
            $product->stock_availability = $request->input('stock_availability');
    
            foreach ($attributeValues as $key => $attr) {
    
                $field = array_keys($attr)[0];
                $val = $attr[$field];
    
                $product->{$field} = $val;
            }
    
            $product->save();
            $product->categories()->sync($request->categories);
    
          
        }
        elseif ($request->type == 'similar'){
            // return $request;

            $request->validate([
                'name' => 'required|string|max:191',
                'attribute_set_id' => ['required'],
                'quantity' => 'required|numeric|min:1|max:999999',
                'weight' => 'required|numeric|min:0|max:999999',
                'price' => 'required|regex:/^(?!,$)[\d,.]+$/|string|max:20',
                'unit' => 'required|string|max:50',
                // 'images' => 'required|array',
                'stock_availability' => ['required', Rule::in(Product::STOCK_AVAILABILITY_OPTIONS)],
                // 'images.*' => 'required|file|mimes:jpg,jpeg,png',
                'categories' => 'required|array',
                'categories.*' => 'required||exists:product_categories,id',
                'sequence' => 'nullable|numeric|min:1|max:999999',
            ]);
            $product = new Product();
            $product->entity_id = self::ENTITY_ID;
            $product->name = $request->name;
            $product->quantity = $request->quantity;
            $product->weight = $request->weight;
            $product->price = str_replace(',', '', $request->price);
            $product->unit = $request->unit;
            $product->sequence = $request->input('sequence', '');
            $product->attribute_set_id = $request->attribute_set_id;
            $product->user_id = Auth::user()->id;
            $product->stock_availability = $request->input('stock_availability');

            $product->forceFill([
                'created_at' => now(),
                'updated_at' => now()
            ]);
                // return $product;
            $product->save();

            $product->categories()->sync($request->categories);

            // return $product;

        }else{
            
        return redirect()->route('admin.show-seller-product')
            ->with($this->setMessage('Product Successfully', self::MESSAGE_SUCCESS));
            // return redirect()->route('admin.show-seller-product')->with($this->setMessage('Product Not Found', self::MESSAGE_ERROR));
        }

        if ($request->hasFile('images')) {
    
            if ((count($request->images)) > 20) {
                return redirect()->back()->with($this->setMessage('You can not upload product images more then 20', self::MESSAGE_ERROR))->withInput();
            }

            foreach ($request->images as $image) {
                //$path = \Storage::disk('public')->putFile('basket', $image);
                $path = $this->resizeImage($image, 'basket', 800);
                $product->images()->create(['image' => $path]);
            }
        }
        
        return redirect()->route('admin.show-seller-product')
            ->with($this->setMessage('Product Successfully', self::MESSAGE_SUCCESS));
        // return redirect()->route('admin.show-seller-product')
        // ->with('success', $this->setMessage('Product Update Successfully', self::MESSAGE_SUCCESS));
    }

        public function destroy($id)
    {
        $image = ProductImage::findOrFail($id);

        // Delete the image file from storage
        if ($image->image && \Storage::exists($image->image)) {
            \Storage::delete($image->image);
        }

        // Delete the database record
        $image->delete();

        return response()->json(['success' => true]);
    }


    public function updateMyProduct(Product $product, Request $request)
    {
       
        if ($request->input('attribute_set_id')) {
            $product->attribute_set_id = $request->input('attribute_set_id');
            $product->save();
        }

        if (Auth::user()->user_type == 'shop') {
            $product = Product::where('id',$request->product_id)->first();
        }
      
        $attributeGroups = $this->getAttributes($product);

        $additionalRules = $attributeGroups->flatMap(function (AttributeGroup $attributeGroup) {
            return $attributeGroup->attributes->map(function (Attribute $attribute) {
                return [
                    $attribute->attribute_code => [$attribute->is_required ? 'required' : 'nullable', 'string']
                ];
            });
        })->toArray();
        
        try {
            $request->validate([
                'name' => 'required|string|max:191',
                'quantity' => 'required|numeric|min:1|max:999999',
                'weight' => 'required|numeric|min:0|max:999999',
                'price' => 'required|regex:/^(?!,$)[\d,.]+$/|string|max:20',
                'unit' => 'required|string|max:50',
                'images' => 'nullable|array',
                'stock_availability' => 'required',
                'images.*' => 'required|file|mimes:jpg,jpeg,png',
                'categories' => 'required|array',
                'categories.*' => 'required|exists:product_categories,id',
                'sequence' => 'nullable|numeric|min:1|max:9999',
            ] + $additionalRules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors()); // Dump and inspect validation errors
        }
       
        $attributeValues = $attributeGroups->flatMap(function (AttributeGroup $attributeGroup) {
            return $attributeGroup->attributes->pluck('attribute_code')->map(function ($code) {
                return [$code => \request($code)];
            });
        })->toArray();

        $product->name = $request->name;
        $product->quantity = $request->quantity;
        $product->weight = $request->weight;
        $product->price = str_replace(',', '', $request->price); //$request->price;
        $product->unit = $request->unit;
        $product->sequence = $request->input('sequence', '');
        $product->stock_availability = $request->input('stock_availability');
        
        foreach ($attributeValues as $key => $attr) {

            $field = array_keys($attr)[0];
            $val = $attr[$field];

            $product->{$field} = $val;
        }
        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            // Get the original filename
            $filename = time() . '.' . $file->getClientOriginalExtension();
            // Save the image in the 'public/images' folder
            $file->move(public_path('product_background_images'), $filename);

            // You can also store the path to the database if needed
            // Image::create(['path' => 'images/' . $filename]);
            $product->background_image = $filename;
            $product->save();
        }
        $product->save();
        $product->categories()->sync($request->categories);

        if ($request->hasFile('images')) {

            if ((count($request->images)) > 20) {
                return redirect()->back()->with($this->setMessage('You can not upload product images more then 20', self::MESSAGE_ERROR))->withInput();
            }

            foreach ($request->images as $image) {
                //$path = \Storage::disk('public')->putFile('basket', $image);
                $path = $this->resizeImage($image, 'basket', 800);
                $product->images()->create(['image' => $path]);
            }
        }
      
            return redirect()->route('admin.my-seller-product')->with($this->setMessage('Product Update Successfully', self::MESSAGE_SUCCESS));
      
        
    }

    public function productDestroy($id)
    {
        // echo $id;
        // die;
        $product = Product::findOrFail($id);
        $product->delete();
      

        return redirect()->route('admin.show-seller-product')
            ->with($this->setMessage('Product Deleted Successfully', self::MESSAGE_SUCCESS));
    }

    public function imageDelete($id)
    {

        $productImage = ProductImage::findOrFail($id);

        if ($productImage->image) {
            \Storage::disk('public')->delete($productImage->image);
        }

        $productImage->delete();

        return response()->json(['status' => true]);
    }

    protected function getCategoriesTree($where = [])
    {
        $mainCategories = AutoCategory::orderBy('name')->where($where)->get();


        function generateTree($categories, $parent, $lvl)
        {

            $cats = $categories->where('parent_id', $parent)->all();
            $output = [];

            foreach ($cats as $category) {

                $output[$category->id] = (str_repeat('----', $lvl) . $category->name);

                if ($categories->where('parent_id', $category->id)->first()) {
                    $output = $output + generateTree($categories, $category->id, ++$lvl);
                }
            }
            return $output;
        }

        return collect(generateTree($mainCategories, null, 0))->prepend('Select a Category', '');
    }

}
