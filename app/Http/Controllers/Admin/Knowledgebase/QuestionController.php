<?php

namespace App\Http\Controllers\Admin\Knowledgebase;


use App\Http\Controllers\Controller;
use App\Imports\QuestionsImport;
use App\Library\Grid\Grid;
use App\Models\Answer;
use App\Models\Knowledgebase;
use App\Models\KnowledgebaseCategory;
use App\Models\Question;
use Illuminate\Http\Request;
//use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel;

class QuestionController extends Controller
{
    public function index()
    {
        $results = Question::latest()->paginate();
        $grid = (new Grid())
            ->setQuery(Question::latest())
            ->setColumns([
                [
                    'field' => 'question',
                    'label' => 'Question',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'image',
                    'label' => 'Image',
                    //'sortable' => true,
                    /*'filterable' => [
                        'callback' => function($query, $value) {
                            $query->whereHas('addressSection', function($query) use ($value) {
                                $query->where('name', 'like', "%{$value}%");
                            });
                        },
                    ],*/
                    'formatter' => function ($field, Question $Question) {


                        return '<img width="150" height="150" src="' . asset("storage/" . $Question->image) . '" alt="" class="img-responsive" style=" margin: 0 auto; "/>'; //$Place->categories()->pluck('name')->implode(', ');
                    }
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
                        return route('admin.question.edit', $item->id);
                    }
                ]

            ])->generate();
        return view('admin.knowledgebase.grid', compact('grid'));
    }

    /* public function import()
    {

        //dd(Excel::import(new QuestionsImport, public_path("/admin/QUESTIONS SL.xlsx")));

        return redirect('/')->with('success', 'All good!');
    }*/

    public function create()
    {
        $categories = KnowledgebaseCategory::get();
        $question = new Question();
        return view('admin.knowledgebase.edit', compact('categories', 'question'));
    }

    public function store(Request $request)
    {

        $this->validator($request)->validate();

        $question = ['question' => $request->question];
        if ($request->hasFile('question_image')) {

            $path = $this->resizeImage($request->file('question_image'), 'question', 800);
            $question['image'] = $path;
        }


        $question = Question::create($question);
        $question->categories()->attach($request->categories);

        foreach ($request->options as $key => $option) {
            if ($request->hasFile("options.{$key}.image") || $request->filled("options.{$key}.text")) {

                $createOption = [
                    'option_value' => $option['text'],
                    'is_answer' => $request->is_answer == $key
                ];

                if (isset($option['image'])) {

                    $path = $this->resizeImage($option['image'], 'question', 800);
                    $createOption['option_image'] = $path;
                }

                $question->options()->create($createOption);
            }
        }
        return redirect()->route('admin.question.index')->with($this->setMessage('Question Saved successfully', self::MESSAGE_SUCCESS));
    }

    public function edit(Question $question, Request $request)
    {
        $categories = KnowledgebaseCategory::get();
        $question->load('options');

        return view('admin.knowledgebase.edit', compact('categories', 'question'));
    }

    public function update(Question $question, Request $request)
    {
        // $this->validator($request)->validate();

        $data = ['question' => $request->question];
        if ($request->hasFile('question_image')) {

            $path = $this->resizeImage($request->file('question_image'), 'question', 800);
            $question['image'] = $path;
        }

        $question->categories()->sync($request->categories);
        $question->update($data);

        foreach ($request->options as $key => $option) {

            if ($request->hasFile("options.{$key}.image") || $request->filled("options.{$key}.text")) {

                $opt = $option['id'] ?? false ? $question->options()->firstOrNew(['id' => $option['id']]) : new Answer();

                $opt->option_value = $option['text'];
                $opt->is_answer = $request->is_answer == $key;

                if (isset($option['image'])) {

                    $path = $this->resizeImage($option['image'], 'question', 800);

                    $opt->option_image = $path;
                }

                if ($option['id']) {
                    $opt->save();
                } else {
                    $question->options()->save($opt);
                }
            }
        }

        return redirect()->route('admin.question.index')->with($this->setMessage('Question update successfully', self::MESSAGE_SUCCESS));
    }

    public function destroy(Question $question, Request $request)
    {
        $question->delete();

        return redirect()->route('admin.question.index')
            ->with($this->setMessage('Question Deleted successfully', self::MESSAGE_SUCCESS));
    }

    protected function validator($request, $isUpdate = false)
    {
        return validator($request->all(), [
            'question' => 'required|string|max:191',
            'question_image' => 'nullable|file|mimes:jpg,jpeg,png',
            'options' => 'required|array|min:2|max:10',
            //'options.*.is_answer' => 'required_with:options|boolean',
            'options.*.text' => 'nullable|string|max:191',
            'options.*.image' => 'nullable|file|mimes:jpg,jpeg,png',
        ], [], [
            // 'options.*.is_answer' => 'Valid Answer',
            'options.*.text' => 'Text',
            'options.*.image' => 'Image'
        ]);
    }

    public function createUpload()
    {
        $categories = KnowledgebaseCategory::get();

        return view('admin.knowledgebase.upload', compact('categories'));
    }


    function import(Request $request)
    {

        $this->validate($request, [
            'select_file'  => 'required|mimes:xls,xlsx',
            'categories' => 'required|array',
            'categories.*' => 'required|numeric',
        ]);
        //dd($request->select_file);
        Excel::import(new QuestionsImport($request), request()->file('select_file'));
        //Excel::import(new QuestionsImport, request()->file('select_file'));

        return back()->with($this->setMessage('Excel Data Imported Successfully.', self::MESSAGE_SUCCESS));
    }
}
