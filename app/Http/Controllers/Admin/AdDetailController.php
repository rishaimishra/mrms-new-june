<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Library\Grid\Grid;
use App\Models\AdDetail;
use Illuminate\Http\Request;
//use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel;
use FFMpeg;

class AdDetailController extends Controller
{
    public function index()
    {
        $results = AdDetail::latest()->paginate();
        $grid = (new Grid())
            ->setQuery(AdDetail::latest())
            ->setColumns([
                [
                    'field' => 'ad_name',
                    'label' => 'Ad Name',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'ad_image',
                    'label' => 'Ad Image',
                    //'sortable' => true,
                    /*'filterable' => [
                        'callback' => function($query, $value) {
                            $query->whereHas('addressSection', function($query) use ($value) {
                                $query->where('name', 'like', "%{$value}%");
                            });
                        },
                    ],*/
                    'formatter' => function ($field, AdDetail $Question) {

                        if($Question->ad_image != null) {
                            return '<img width="150" height="150" src="' . asset("storage/" . $Question->ad_image) . '" alt="" class="img-responsive" style=" margin: 0 auto; "/>'; //$Place->categories()->pluck('name')->implode(', ');
                        }else {
                            return '<p>Video Ad</p>';
                        }
                        
                    }
                ],
                [
                    'field' => 'ad_link',
                    'label' => 'Ad Link',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'ad_type',
                    'label' => 'Ad Type',
                    'sortable' => true,
                    'filterable' => true,
                    'formatter' => function ($field, AdDetail $Question) {

                        if($Question->ad_type == 1){
                            return 'Small Ad'.'-('.$Question->ad_content_type.')'; 
                        }else {
                            return 'Banner Ad' .'-('.$Question->ad_content_type.')';
                        }
                       
                    }
                ],
                [
                    'field' => 'ad_category',
                    'label' => 'Ad Category',
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
                // [
                //     'label' => 'Edit',
                //     'icon' => 'remove_red_eye',
                //     'url' => function ($item) {
                //         return route('admin.ad-detail.index', $item->id);
                //     }
                // ]
                [
                    'label' => 'Delete',
                    'icon' => 'delete',
                    'url' => function ($item) {
                        return route('admin.ad-detail.show', $item->id);
                    },
                    'class' => 'btn btn-danger',
                    'confirm' => 'Are you sure you want to delete this item?'
                ]

            ])->generate();
        return view('admin.ad-detail.grid', compact('grid'));
    }

    // public function destroy($id)
    // {
    //     $adDetail = AdDetail::findOrFail($id);
    //     $adDetail->delete();

    //     return redirect()->route('admin.ad-detail.index')->with('success', 'Ad deleted successfully');
    // }

    public function show($id)
    {
        $adDetail = AdDetail::findOrFail($id);
        $adDetail->delete();

        return redirect()->route('admin.ad-detail.index')->with('success', 'Ad deleted successfully');
        // Return a view to display the details of the ad detail item
    }

    /* public function import()
    {

        //dd(Excel::import(new QuestionsImport, public_path("/admin/QUESTIONS SL.xlsx")));

        return redirect('/')->with('success', 'All good!');
    }*/

    public function create()
    {

        return view('admin.ad-detail.create');
    }


 

    public function store(Request $request)
    {
        $values = $request->all();
    
        // Handle video conversion if content type is Video
        if($values['ad_content_type'] == "Video") {
            // Check if the request has a video file
            if ($request->hasFile('ad_video')) {
                $video = $request->file('ad_video');
    
                // Store the original video file
                $path = $this->storeVideo($video, 'ads', $video);
                $values['ad_video'] = $path;
    
                // Convert the video to HLS format
                $videoPath = $path; // Path of the original video file
                $outputDir = storage_path('app/public/ads/hls'); // Directory for HLS output
    
                // Ensure output directory exists
                if (!file_exists($outputDir)) {
                    mkdir($outputDir, 0777, true); // Create directory if it doesn't exist
                }
    
                // Convert the video to HLS
                FFMpeg::fromDisk('public')
                    ->open($videoPath)
                    ->exportForHLS()
                    ->toDisk('public')
                    ->onProgress(function ($percentage) {
                        echo "Conversion Progress: {$percentage}%\n"; // Optional: Show conversion progress
                    })
                    ->save("ads/hls/playlist.m3u8"); // Save the HLS files in the specified directory
    
                // Save the HLS directory path (or the .m3u8 file path) in the database
                $values['ad_video_hls'] = 'ads/hls/playlist.m3u8'; // Or store it in a column specific to HLS if you want
            }
        } else {
            // Handle image upload if content type is not Video
            if ($request->hasFile('ad_image')) {
                $path = $this->resizeImage($request->file('ad_image'), 'ads', 800);
                $values['ad_image'] = $path;
            }
        }
    
        // Store the ad data in the database
        $ads = AdDetail::create($values);
    
        // Redirect back with a success message
        return redirect()->route('admin.ad-detail.index')->with($this->setMessage('Ad Saved successfully', self::MESSAGE_SUCCESS));
    }
    

    // public function store(Request $request)
    // {

    //     $this->validator($request)->validate();

    //     $question = ['question' => $request->question];
    //     if ($request->hasFile('question_image')) {

    //         $path = $this->resizeImage($request->file('question_image'), 'question', 800);
    //         $question['image'] = $path;
    //     }


    //     $question = Question::create($question);
    //     $question->categories()->attach($request->categories);

    //     foreach ($request->options as $key => $option) {
    //         if ($request->hasFile("options.{$key}.image") || $request->filled("options.{$key}.text")) {

    //             $createOption = [
    //                 'option_value' => $option['text'],
    //                 'is_answer' => $request->is_answer == $key
    //             ];

    //             if (isset($option['image'])) {

    //                 $path = $this->resizeImage($option['image'], 'question', 800);
    //                 $createOption['option_image'] = $path;
    //             }

    //             $question->options()->create($createOption);
    //         }
    //     }
    //     return redirect()->route('admin.question.index')->with($this->setMessage('Question Saved successfully', self::MESSAGE_SUCCESS));
    // }

    // public function edit(Question $question, Request $request)
    // {
    //     $categories = KnowledgebaseCategory::get();
    //     $question->load('options');

    //     return view('admin.knowledgebase.edit', compact('categories', 'question'));
    // }

    // public function update(Question $question, Request $request)
    // {
    //     // $this->validator($request)->validate();

    //     $data = ['question' => $request->question];
    //     if ($request->hasFile('question_image')) {

    //         $path = $this->resizeImage($request->file('question_image'), 'question', 800);
    //         $question['image'] = $path;
    //     }

    //     $question->categories()->sync($request->categories);
    //     $question->update($data);

    //     foreach ($request->options as $key => $option) {

    //         if ($request->hasFile("options.{$key}.image") || $request->filled("options.{$key}.text")) {

    //             $opt = $option['id'] ?? false ? $question->options()->firstOrNew(['id' => $option['id']]) : new Answer();

    //             $opt->option_value = $option['text'];
    //             $opt->is_answer = $request->is_answer == $key;

    //             if (isset($option['image'])) {

    //                 $path = $this->resizeImage($option['image'], 'question', 800);

    //                 $opt->option_image = $path;
    //             }

    //             if ($option['id']) {
    //                 $opt->save();
    //             } else {
    //                 $question->options()->save($opt);
    //             }
    //         }
    //     }

    //     return redirect()->route('admin.question.index')->with($this->setMessage('Question update successfully', self::MESSAGE_SUCCESS));
    // }

    // public function destroy(Question $question, Request $request)
    // {
    //     $question->delete();

    //     return redirect()->route('admin.question.index')
    //         ->with($this->setMessage('Question Deleted successfully', self::MESSAGE_SUCCESS));
    // }

    // protected function validator($request, $isUpdate = false)
    // {
    //     return validator($request->all(), [
    //         'question' => 'required|string|max:191',
    //         'question_image' => 'nullable|file|mimes:jpg,jpeg,png',
    //         'options' => 'required|array|min:2|max:10',
    //         //'options.*.is_answer' => 'required_with:options|boolean',
    //         'options.*.text' => 'nullable|string|max:191',
    //         'options.*.image' => 'nullable|file|mimes:jpg,jpeg,png',
    //     ], [], [
    //         // 'options.*.is_answer' => 'Valid Answer',
    //         'options.*.text' => 'Text',
    //         'options.*.image' => 'Image'
    //     ]);
    // }

    // public function createUpload()
    // {
    //     $categories = KnowledgebaseCategory::get();

    //     return view('admin.knowledgebase.upload', compact('categories'));
    // }


    // function import(Request $request)
    // {

    //     $this->validate($request, [
    //         'select_file'  => 'required|mimes:xls,xlsx',
    //         'categories' => 'required|array',
    //         'categories.*' => 'required|numeric',
    //     ]);
    //     //dd($request->select_file);
    //     Excel::import(new QuestionsImport($request), request()->file('select_file'));
    //     //Excel::import(new QuestionsImport, request()->file('select_file'));

    //     return back()->with($this->setMessage('Excel Data Imported Successfully.', self::MESSAGE_SUCCESS));
    // }
}
