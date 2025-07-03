<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Library\Grid\Grid;
use App\Models\AdDetail;
use Illuminate\Http\Request;
//use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel;
use FFMpeg;
use Carbon\Carbon;


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
                        if ($Question->ad_image != null) {
                            // Render image if `ad_image` is set
                            return '<img width="150" height="150" src="' . asset("storage/" . $Question->ad_image) . '" alt="" class="img-responsive" style=" margin: 0 auto; "/>';
                        } elseif ($Question->ad_video != null) {
                            // Render Video.js player if `ad_video` is set
                            return ' 
                                    <video id="video-' . $Question->id . '" class="video-js vjs-default-skin" controls preload="auto" width="300" height="300" data-setup=\'{}\' >
                                        <source src="' . asset('storage/'.$Question->ad_video) . '" type="application/x-mpegURL">
                                        Your browser does not support the video tag.
                                    </video>

                                     
                                     ';
                        } else {
                            return '<p>No Ad Content</p>';
                        }
                        // if($Question->ad_image != null) {
                        //     return '<img width="150" height="150" src="' . asset("storage/" . $Question->ad_image) . '" alt="" class="img-responsive" style=" margin: 0 auto; "/>'; //$Place->categories()->pluck('name')->implode(', ');
                        // }else {
                        //     return '<p>Video Ad</p>';
                        // }
                        
                    }
                ],
                [
                    'field' => 'ad_link',
                    'label' => 'Ad Link',
                    'sortable' => true,
                    'filterable' => true
                ],
                [
                    'field' => 'sequence',
                    'label' => 'Ad Sequence',
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
                            return 'Small Ad(Large)'.'-('.$Question->ad_content_type.')'; 
                        }elseif($Question->ad_type == 3){
                            return 'Small Ad(medium)'.'-('.$Question->ad_content_type.')'; 
                        }elseif($Question->ad_type == 4){
                            return 'Small Ad(small)'.'-('.$Question->ad_content_type.')'; 
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
                [
                    'label' => 'Edit',
                    'icon' => 'remove_red_eye',
                    'url' => function ($item) {
                        return route('admin.ad-detail.editad', $item->id);
                    }
                ],
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

    public function editAd($id)
{
    $adDetail = AdDetail::findOrFail($id); // Fetch the record by ID
    return view('admin.ad-detail.edit', compact('adDetail')); // Pass the record to the edit view
}

public function updateAd(Request $request, $id)
{
    $adDetail = AdDetail::findOrFail($id);

    $values = $request->only(['ad_name', 'ad_link', 'ad_content_type', 'sequence', 'ad_description', 'ad_type', 'ad_category']);

    // Handle file uploads for ad_image or ad_video
    if ($request->ad_content_type == 'Image' && $request->hasFile('ad_image')) {
        $path = $this->resizeImage($request->file('ad_image'), 'ads', 800);
        $values['ad_image'] = $path;
        $values['ad_video'] = null; // Remove video if changing to image
    } elseif ($request->ad_content_type == 'Video' && $request->hasFile('ad_video')) {
        $video = $request->file('ad_video');
        $path = $this->storeVideo($video, 'ads', $video);
        $values['ad_video'] = $path;
        $values['ad_image'] = null; // Remove image if changing to video
    }

    // Update the ad detail record
    $adDetail->update($values);

    return redirect()->route('admin.ad-detail.index')
        ->with($this->setMessage('Ad Updated Successfully', self::MESSAGE_SUCCESS));
}



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


    
//     public function store(Request $request)
// {
//     $values = $request->all();

//     // Handle video conversion if content type is Video
//     if ($values['ad_content_type'] == "Video") {
//         if ($request->hasFile('ad_video')) {
//             $video = $request->file('ad_video');
            
//             // Store the uploaded video and get the path
//             $path = $this->storeVideo($video, 'ads', $video);
//             // dd($path);
            
//             // Convert relative path to full absolute path
//             $videoPath = storage_path('app/public/' . $path);
            
//             // Log the full video path for debugging
//             \Log::info("Full video path: " . $videoPath);
            
//             // Check if the video file exists
//             if (!file_exists($videoPath)) {
//                 \Log::error("Video file does not exist: " . $videoPath);
//                 return "Video file is missing.";
//             }

//             $outputDir = '/var/www/html/';
            
//             // Ensure the directory exists
//             if (!file_exists($outputDir)) {
//                 mkdir($outputDir, 0777, true);
//             }

//             $currentTime = Carbon::now()->format('YmdHis');

//             try {
//                 // Ensure paths are properly formatted
//                 $videoPath = rtrim($videoPath, '/');
//                 $outputDir = rtrim($outputDir, '/');

//                 // Custom FFmpeg command in PHP to diagnose potential issues
//                 $command = "ffmpeg -i {$videoPath} -hls_time 10 -hls_list_size 0 -f hls {$outputDir}/file-{$currentTime}.m3u8";
                
//                 // Log the command to see what gets executed
//                 \Log::info("Running command: " . $command);
                
//                 // Run the command using exec() and capture the output
//                 exec($command . " 2>&1", $output, $status);

//                 // Log the output to see if there are any errors
//                 \Log::info("FFMpeg output: " . implode("\n", $output));

//                 if ($status !== 0) {
//                     \Log::error("FFMpeg command failed with status {$status}");
//                     return "Error during video conversion. See logs for details.";
//                 }

//                 // Check if the .ts files were created
//                 $tsFiles = glob("{$outputDir}/file-{$currentTime}-*.ts");
//                 if (count($tsFiles) > 0) {
//                     \Log::info("TS files generated successfully.");
//                 } else {
//                     \Log::error("No TS files found.");
//                 }

//                 // Save the .m3u8 path in the database
//                 $values['ad_video'] = 'var/www/html/file-' . $currentTime . '.m3u8';

//                 // Cleanup the temporary files
//                 foreach ($tsFiles as $file) {
//                     unlink($file);
//                 }
//                 unlink("{$outputDir}/file-{$currentTime}.m3u8");

//             } catch (\Exception $e) {
//                 \Log::error("Error during video conversion: " . $e->getMessage());
//                 return "Error during video conversion.";
//             }
//         }
//     } else {
//         if ($request->hasFile('ad_image')) {
//             $path = $this->resizeImage($request->file('ad_image'), 'ads', 800);
//             $values['ad_image'] = $path;
//         }
//     }

//     // Save the ad data to the database
//     $ads = AdDetail::create($values);
    
//     return redirect()->route('admin.ad-detail.index')->with($this->setMessage('Ad Saved successfully', self::MESSAGE_SUCCESS));
// }

public function store(Request $request)
{
    $values = $request->all();

    // Validate request
    $validated = $request->validate([
        'ad_name' => 'required|string|max:255',
        'ad_type' => 'required|in:1,2,3,4', // Ensure `ad_type` is valid
        'ad_link' => 'nullable|string|max:255',
        'ad_content_type' => 'required|in:Image,Video',
        'ad_image' => 'required_if:ad_content_type,Image|image|mimes:jpg,jpeg,png|max:2048',
        'ad_video' => 'required_if:ad_content_type,Video|mimes:mp4,avi,mkv|max:102400',
        'description' => 'nullable|string|max:500',
        'sequence' => 'nullable|integer|min:1',
    ]);
    // dd($values);
    $values = $request->only(['ad_name','ad_type', 'ad_link', 'ad_content_type', 'description', 'sequence']);
    $values['ad_type'] = $request->input('ad_type', null); // Default to NULL if not provided
    $values['sequence'] = $request->input('sequence', null); // Default to NULL if not provided
    $values['ad_description'] = $request->input('description', null); // Default to NULL if not provided
    $values['ad_category'] = $request->input('ad_category', null);
    
    // Handle video conversion if content type is Video
    if ($values['ad_content_type'] == "Video") {
        if ($request->hasFile('ad_video')) {
            $video = $request->file('ad_video');
            
            // Store the uploaded video and get the path
            $path = $this->storeVideo($video, 'ads', $video);
            
            // Convert relative path to full absolute path
            $videoPath = storage_path('app/public/' . $path);
            
            // Log the full video path for debugging
            \Log::info("Full video path: " . $videoPath);
            
            // Check if the video file exists
            if (!file_exists($videoPath)) {
                \Log::error("Video file does not exist: " . $videoPath);
                return "Video file is missing.";
            }

            // Define the output directory
            $outputDir = '/var/www/html/SEVEN_ELEVEN_BACKEND/storage/app/public/ads/hls';
            
            // Ensure the directory exists
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 0777, true);
            }

            $currentTime = Carbon::now()->format('YmdHis');

            try {
                // Ensure paths are properly formatted
                $videoPath = rtrim($videoPath, '/');
                $outputDir = rtrim($outputDir, '/');

                // FFmpeg command to convert the video to HLS
                $command = "ffmpeg -i {$videoPath} -hls_time 10 -hls_list_size 0 -f hls {$outputDir}/file-{$currentTime}.m3u8";
                
                // Log the command for debugging
                \Log::info("Running command: " . $command);
                
                // Run the command using exec() and capture the output
                exec($command . " 2>&1", $output, $status);

                // Log the output to see if there are any errors
                \Log::info("FFMpeg output: " . implode("\n", $output));

                if ($status !== 0) {
                    \Log::error("FFMpeg command failed with status {$status}");
                    return "Error during video conversion. See logs for details.";
                }

                // Save the .m3u8 path in the database
                $values['ad_video'] = "ads/hls/file-{$currentTime}.m3u8";

            } catch (\Exception $e) {
                \Log::error("Error during video conversion: " . $e->getMessage());
                return "Error during video conversion.";
            }
        }
    } else {
        if ($request->hasFile('ad_image')) {
            $path = $this->resizeImage($request->file('ad_image'), 'ads', 800);
            $values['ad_image'] = $path;
        }
    }

    // Save the ad data to the database
    $ads = AdDetail::create($values);
    
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
