<?php

namespace App\Http\Controllers\Admin\RealEstate;

use App\Http\Controllers\Admin\AdminController;
use App\Library\Grid\Grid;
use App\Models\RealEstateCategory;
use Illuminate\Http\Request;

class RealEstateCategoryController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $grid = (new Grid())
            ->setQuery(RealEstateCategory::latest())
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
                    'formatter' => function ($field, RealEstateCategory $realEstateCategory) {
                        return $realEstateCategory->parent->name;
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
                        return route('admin.real-estate-category.edit', $item->id);
                    }
                ]

            ])->generate();

        return view('admin.realestate.category.grid', compact('grid'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $realEstateCategory = new RealEstateCategory();
        $mainCategories = $this->getCategoriesTree();

        return view('admin.realestate.category.edit', compact('mainCategories', 'realEstateCategory'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
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
            $path = $this->resizeImage($request->file('images'), 'real_estate_category');
            $request->request->add(['image' => $path]);
        }
        if ($request->hasFile('bg_image')) {
            $path = $this->resizeImage($request->file('bg_image'), 'real_estate_category');
            $request->request->add(['background_image' => $path]);
        }

        $realEstateCategory = RealEstateCategory::create($request->all());

        return redirect()->route('admin.real-estate-category.index')
            ->with($this->setMessage('Category Saved successfully', self::MESSAGE_SUCCESS));
    }

    public function edit(RealEstateCategory $realEstateCategory)
    {

        $mainCategories = $this->getCategoriesTree([['id', '!=', $realEstateCategory->id]]);

        return view('admin.realestate.category.edit', compact('mainCategories', 'realEstateCategory'));
    }

    public function update(Request $request, RealEstateCategory $realEstateCategory)
    {
        $request->validate([
            'parent_id' => 'nullable|numeric',
            'name' => 'required|string|max:191',
            'is_active' => 'nullable|boolean',
            'sequence' => 'nullable|numeric|min:1|max:9999',
            'sponsor_text' => 'nullable|string|max:191',
        ]);

        if ($request->hasFile('images')) {
            $path = $this->resizeImage($request->file('images'), 'real_estate_category');

            $request->request->add(['image' => $path]);
        }
        if ($request->hasFile('bg_image')) {
            $path = $this->resizeImage($request->file('bg_image'), 'real_estate_category');

            $request->request->add(['background_image' => $path]);
        }

        $realEstateCategory->fill($request->all());
        $realEstateCategory->update();

        return redirect()->route('admin.real-estate-category.index')
            ->with($this->setMessage('Category updated successfully', self::MESSAGE_SUCCESS));
    }

    public function destroy(RealEstateCategory $realEstateCategory)
    {
        $realEstateCategory->children()->update([
            'parent_id' => null
        ]);

        $realEstateCategory->delete();

        return redirect()->route('admin.real-estate-category.index')
            ->with($this->setMessage('Category deleted successfully', self::MESSAGE_SUCCESS));
    }

    protected function getCategoriesTree($where = [])
    {
        $mainCategories = RealEstateCategory::orderBy('name')->where($where)->get();

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
