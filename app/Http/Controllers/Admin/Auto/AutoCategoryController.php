<?php

namespace App\Http\Controllers\Admin\Auto;

use App\Http\Controllers\Admin\AdminController;
use App\Library\Grid\Grid;
use App\Models\AutoCategory;
use Illuminate\Http\Request;

class AutoCategoryController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$results = AutoCategory::paginate();
        $grid = (new Grid())
            ->setQuery(AutoCategory::latest())
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
                    'formatter' => function ($field, AutoCategory $AutoCategory) {
                        return $AutoCategory->parent->name;
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
                        return route('admin.auto-category.edit', $item->id);
                    }
                ]

            ])->generate();

        return view('admin.auto.category.grid', compact('grid'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $autoCategory = new AutoCategory();
        $mainCategories = $this->getCategoriesTree();

        return view('admin.auto.category.edit', compact('mainCategories', 'autoCategory'));
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
            $path = $this->resizeImage($request->file('images'), 'auto_category');
            $request->request->add(['image' => $path]);
        }
        if ($request->hasFile('bg_image')) {
            $path = $this->resizeImage($request->file('bg_image'), 'auto_category');
            $request->request->add(['background_image' => $path]);
        }

        $placeCategory = AutoCategory::create($request->all());

        return redirect()->route('admin.auto-category.index')
            ->with($this->setMessage('Category Saved successfully', self::MESSAGE_SUCCESS));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\AutoCategory $autoCategory
     * @return \Illuminate\Http\Response
     */
    public function show(AutoCategory $autoCategory)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\AutoCategory $autoCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(AutoCategory $autoCategory)
    {
        $mainCategories = $this->getCategoriesTree([['id', '!=', $autoCategory->id]]);

        return view('admin.auto.category.edit', compact('mainCategories', 'autoCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\AutoCategory $autoCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AutoCategory $autoCategory)
    {
        $request->validate([
            'parent_id' => 'nullable|numeric',
            'name' => 'required|string|max:191',
            'is_active' => 'nullable|boolean',
            'sequence' => 'nullable|numeric|min:1|max:9999',
            'sponsor_text' => 'nullable|string|max:191',
        ]);

        if ($request->hasFile('images')) {
            $path = $this->resizeImage($request->file('images'), 'auto_category');

            $request->request->add(['image' => $path]);
        }
        if ($request->hasFile('bg_image')) {
            $path = $this->resizeImage($request->file('bg_image'), 'auto_category');

            $request->request->add(['background_image' => $path]);
        }

        $autoCategory->fill($request->all());
        $autoCategory->update();

        return redirect()->route('admin.auto-category.index')
            ->with($this->setMessage('Category updated successfully', self::MESSAGE_SUCCESS));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\AutoCategory $autoCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(AutoCategory $autoCategory)
    {
        $autoCategory->children()->update([
            'parent_id' => null
        ]);

        $autoCategory->delete();

        return redirect()->route('admin.auto-category.index')
            ->with($this->setMessage('Category deleted successfully', self::MESSAGE_SUCCESS));
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
