<?php

namespace App\Http\Controllers\Admin\Knowledgebase;


use App\Http\Controllers\Controller;
use App\Library\Grid\Grid;
use App\Models\KnowledgebaseCategory;
use Illuminate\Http\Request;

class KnowledgebaseCategoryController extends Controller
{

    public function index()
    {

        $grid = (new Grid())
            ->setQuery(KnowledgebaseCategory::latest())
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
                    'formatter' => function ($field, KnowledgebaseCategory $KnowledgebaseCategory) {
                        return $KnowledgebaseCategory->parent->name;
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
                        return route('admin.knowledgebase-category.edit', $item->id);
                    }
                ]

            ])->generate();
        return view('admin.knowledgebase.category.grid', compact('grid'));
    }


    public function create()
    {
        $knowledgebaseCategory = new KnowledgebaseCategory();
        $mainCategories = $this->getCategoriesTree();

        return view('admin.knowledgebase.category.edit', compact('mainCategories', 'knowledgebaseCategory'));
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
            $path = $this->resizeImage($request->file('images'), 'question_category');
            $request->request->add(['image' => $path]);
        }

        $questionsCategory = KnowledgebaseCategory::create($request->all());

        return redirect()->route('admin.knowledgebase-category.index')
            ->with($this->setMessage('Category Saved successfully', self::MESSAGE_SUCCESS));
    }

    public function edit(KnowledgebaseCategory $knowledgebaseCategory, Request $request)
    {
        $mainCategories = $this->getCategoriesTree([['id', '!=', $knowledgebaseCategory->id]]);

        return view('admin.knowledgebase.category.edit', compact('mainCategories', 'knowledgebaseCategory'));
    }

    public function update(KnowledgebaseCategory $knowledgebaseCategory, Request $request)
    {

        $request->validate([
            'parent_id' => 'nullable|numeric',
            'name' => 'required|string|max:191',
            'is_active' => 'nullable|boolean',
            'sequence' => 'nullable|numeric|min:1|max:9999',
            'sponsor_text' => 'nullable|string|max:191',
        ]);

        if ($request->hasFile('images')) {
            $path = $this->resizeImage($request->file('images'), 'question_category');
            $request->request->add(['image' => $path]);
        }

        $knowledgebaseCategory->fill($request->all());
        $knowledgebaseCategory->update();

        return redirect()->route('admin.knowledgebase-category.index')
            ->with($this->setMessage('Category updated successfully', self::MESSAGE_SUCCESS));
    }

    public function destroy(KnowledgebaseCategory $knowledgebaseCategory, Request $request)
    {
        $knowledgebaseCategory->children()->update([
            'parent_id' => null
        ]);

        $knowledgebaseCategory->delete();

        return redirect()->route('admin.knowledgebase-category.index')
            ->with($this->setMessage('Category deleted successfully', self::MESSAGE_SUCCESS));
    }


    protected function getCategoriesTree($where = [])
    {
        $mainCategories = KnowledgebaseCategory::orderBy('name')->where($where)->get();


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
