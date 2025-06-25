<?php


namespace App\Http\Controllers\Admin\Product;


use App\Http\Controllers\Controller;
use App\Library\Grid\Grid;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductCategoryController extends Controller
{

    public function index()
    {
        if(Auth::user()->user_type == 'shop'){
            $grid = (new Grid())
            ->setQuery(ProductCategory::where('user_id',Auth::user()->id)->latest())
            ->setColumns([
                [
                    'field' => 'name',
                    'label' => 'Name',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'parent',
                    'label' => 'Parent',
                    //'sortable' => true,
                    'filterable' => [
                        'callback' => function ($query, $value) {
                            $query->whereHas('parent', function ($query) use ($value) {
                                $query->where('name', 'like', "%{$value}%");
                            });
                        },
                    ],
                    'formatter' => function ($field, ProductCategory $productCategory) {
                        return $productCategory->parent->name;
                    }
                ],
                [
                    'field' => 'sequence',
                    'label' => 'Sequence',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'created_at',
                    'label' => 'Created At',
                    'sortable' => true
                ],
                [
                    'field' => 'updated_at',
                    'label' => 'Updated At',
                    'sortable' => true
                ]
            ])->setButtons([
                [
                    'label' => 'Edit',
                    'icon' => 'remove_red_eye',
                    'url' => function ($item) {
                        if(Auth::user()->user_type != 'shop'){
                            return route('admin.seller-product-category.edit', $item->id);
                        }
                    }
                ]

            ])->generate();
        return view('admin.product.category.grid', compact('grid'));
        }
        else{
            $grid = (new Grid())
            ->setQuery(ProductCategory::where('user_id',Auth::user()->id)->latest())
            ->setColumns([
                [
                    'field' => 'name',
                    'label' => 'Name',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'parent',
                    'label' => 'Parent',
                    //'sortable' => true,
                    'filterable' => [
                        'callback' => function ($query, $value) {
                            $query->whereHas('parent', function ($query) use ($value) {
                                $query->where('name', 'like', "%{$value}%");
                            });
                        },
                    ],
                    'formatter' => function ($field, ProductCategory $productCategory) {
                        return $productCategory->parent->name;
                    }
                ],
                [
                    'field' => 'sequence',
                    'label' => 'Sequence',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'created_at',
                    'label' => 'Created At',
                    'sortable' => true
                ],
                [
                    'field' => 'updated_at',
                    'label' => 'Updated At',
                    'sortable' => true
                ]
            ])->setButtons([
                [
                    'label' => 'Edit',
                    'icon' => 'remove_red_eye',
                    'url' => function ($item) {
                        return route('admin.product-category.edit', $item->id);
                    }
                ]

            ])->generate();
        return view('admin.product.category.grid', compact('grid'));
        }

        
    }

    public function create()
    {
        $productCategory = new ProductCategory();
        $mainCategories = $this->getCategoriesTree();

        return view('admin.product.category.edit', compact('mainCategories', 'productCategory'));
    }

    public function store(Request $request)
    {
        // return $request;
        $request->validate([
            'parent_id' => 'nullable|numeric',
            'name' => 'required|string|max:191',
            'is_active' => 'nullable|boolean',
            'images' => 'nullable|required|file|mimes:jpg,jpeg,png',
            'sequence' => 'nullable|numeric|min:1|max:9999',
            'sponsor_text' => 'nullable|string|max:191',
        ]);

        if ($request->hasFile('images')) {
            $path = $this->resizeImage($request->file('images'), 'product_category');
            $request->request->add(['image' => $path]);
        }
        if ($request->hasFile('background_images')) {
            $path = $this->resizeImage($request->file('background_images'), 'product_category');
            $request->request->add(['background_image' => $path]);
        }

        // ProductCategory::create($request->all());
        ProductCategory::create(array_merge($request->all(), ['user_id' => Auth::id()]));
        if(Auth::user()->user_type == 'shop'){
            return redirect()->route('admin.seller-product-category.index')
            ->with($this->setMessage('Category Saved successfully', self::MESSAGE_SUCCESS));
        }else{
            return redirect()->route('admin.product-category.index')
            ->with($this->setMessage('Category Saved successfully', self::MESSAGE_SUCCESS));
        }
       
    }

    public function edit(ProductCategory $productCategory, Request $request)
    {
    //     if($productCategory->parent_id == null){

    //     return "asdf";
    // }
        // return $productCategory;
        $mainCategories = $this->getCategoriesTree([['id', '!=', $productCategory->id]]);

        return view('admin.product.category.edit', compact('mainCategories', 'productCategory'));
    }

    public function update(ProductCategory $productCategory, Request $request)
    {
        // return $productCategory;
        // return $request;
        $request->validate([
            'parent_id' => 'nullable|numeric',
            'name' => 'required|string|max:191',
            'is_active' => 'nullable|boolean',
            'sequence' => 'nullable|numeric|min:1|max:9999',
            'sponsor_text' => 'nullable|string|max:191',
        ]);

        if ($request->hasFile('images')) {
            $path = $this->resizeImage($request->file('images'), 'product_category');
            $request->request->add(['image' => $path]);
        }
        if ($request->hasFile('background_images')) {
            $path = $this->resizeImage($request->file('background_images'), 'product_category');
            $request->request->add(['background_image' => $path]);
        }

        $productCategory->fill($request->all());
        $productCategory->update();
        if(Auth::user()->user_type=="shop"){
            return redirect()->route('admin.seller-product-category.index')
            ->with($this->setMessage('Category updated successfully', self::MESSAGE_SUCCESS));
        }
        else{
            return redirect()->route('admin.product-category.index')
            ->with($this->setMessage('Category updated successfully', self::MESSAGE_SUCCESS));
        }
       
    }

    public function destroy(ProductCategory $productCategory, Request $request)
    {
        $productCategory->children()->update([
            'parent_id' => null
        ]);

        $productCategory->delete();

        return redirect()->route('admin.product-category.index')
            ->with($this->setMessage('Category deleted successfully', self::MESSAGE_SUCCESS));
    }

    protected function getCategoriesTree($where = [])
    {
        $mainCategories = ProductCategory::orderBy('name')->where($where)->get();


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
