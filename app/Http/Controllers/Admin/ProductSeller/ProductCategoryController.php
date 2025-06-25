<?php


namespace App\Http\Controllers\Admin\Product;


use App\Http\Controllers\Controller;
use App\Library\Grid\Grid;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductSellerCategoryController extends Controller
{

    public function index()
    {

        $grid = (new Grid())
            ->setQuery(ProductCategory::latest())
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

    public function create()
    {
        $productCategory = new ProductCategory();
        $mainCategories = $this->getCategoriesTree();

        return view('admin.product.category.edit', compact('mainCategories', 'productCategory'));
    }

    public function store(Request $request)
    {
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

        ProductCategory::create($request->all());

        return redirect()->route('admin.product-category.index')
            ->with($this->setMessage('Category Saved successfully', self::MESSAGE_SUCCESS));
    }

    public function edit(ProductCategory $productCategory, Request $request)
    {

        $mainCategories = $this->getCategoriesTree([['id', '!=', $productCategory->id]]);

        return view('admin.product.category.edit', compact('mainCategories', 'productCategory'));
    }

    public function update(ProductCategory $productCategory, Request $request)
    {
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

        $productCategory->fill($request->all());
        $productCategory->update();

        return redirect()->route('admin.product-category.index')
            ->with($this->setMessage('Category updated successfully', self::MESSAGE_SUCCESS));
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
