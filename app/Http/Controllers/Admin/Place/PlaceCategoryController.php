<?php

namespace App\Http\Controllers\Admin\Place;


use App\Http\Controllers\Controller;
use App\Library\Grid\Grid;
use App\Models\PlaceCategory;
use Illuminate\Http\Request;


class PlaceCategoryController extends Controller
{

    public function index()
    {


        $grid = (new Grid())
            ->setQuery(PlaceCategory::latest())
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
                    'formatter' => function ($field, PlaceCategory $PlaceCategory) {
                        return $PlaceCategory->parent->name;
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
                        return route('admin.place-category.edit', $item->id);
                    }
                ]

            ])->generate();
        return view('admin.place.category.grid', compact('grid'));
    }


    public function create()
    {
        $placeCategory = new PlaceCategory();
        $mainCategories = $this->getCategoriesTree();

        return view('admin.place.category.edit', compact('mainCategories', 'placeCategory'));
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
            $path = $this->resizeImage($request->file('images'), 'place_category');
            $request->request->add(['image' => $path]);
        }

        PlaceCategory::create($request->all());

        return redirect()->route('admin.place-category.index')
            ->with($this->setMessage('Category Saved successfully', self::MESSAGE_SUCCESS));
    }

    public function edit(PlaceCategory $placeCategory, Request $request)
    {
        $mainCategories = $this->getCategoriesTree([['id', '!=', $placeCategory->id]]);

        return view('admin.place.category.edit', compact('mainCategories', 'placeCategory'));
    }

    public function update(PlaceCategory $placeCategory, Request $request)
    {
        $request->validate([
            'parent_id' => 'nullable|numeric',
            'name' => 'required|string|max:191',
            'is_active' => 'nullable|boolean',
            'sequence' => 'nullable|numeric|min:1|max:9999',
            'sponsor_text' => 'nullable|string|max:191',
        ]);

        if ($request->hasFile('images')) {
            $path = $this->resizeImage($request->file('images'), 'place_category');

            $request->request->add(['image' => $path]);
        }

        $placeCategory->fill($request->all());
        $placeCategory->update();

        return redirect()->route('admin.place-category.index')
            ->with($this->setMessage('Category updated successfully', self::MESSAGE_SUCCESS));
    }

    public function destroy(PlaceCategory $placeCategory, Request $request)
    {
        $placeCategory->children()->update([
            'parent_id' => null
        ]);

        $placeCategory->delete();

        return redirect()->route('admin.place-category.index')
            ->with($this->setMessage('Category deleted successfully', self::MESSAGE_SUCCESS));
    }


    protected function getCategoriesTree($where = [])
    {
        $mainCategories = PlaceCategory::orderBy('name')->where($where)->get();


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
