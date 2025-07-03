<?php


namespace App\Http\Controllers\Api;

use App\Logic\SystemConfig;
use App\Models\Auto;
use App\Models\AdDetail;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\BreakingNews;
use App\Models\PublicNotice;
use App\Models\NoticeImage;
use Eav\Attribute;
use Eav\AttributeGroup;
use Eav\AttributeSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ProductController extends ApiController
{
    /**
     * Display a listing of the Category.
     *
     */



    function getProductCategory($id = null)
{
    // Fetch product categories
    $categories = ProductCategory::active()
        ->where('parent_id', $id)
        ->withCount('children')
        ->orderBy(\DB::raw('sequence IS NULL, sequence'), 'asc')
        ->paginate(50);

    // Fetch sponsor text
    $sponsorAll = SystemConfig::getOptionGroup(SystemConfig::SPONSOR_GROUP);
    $custom = collect(['sponsor' => $sponsorAll->{SystemConfig::PRODUCT_SPONSOR}]);
    if ($id) {
        $productCategory = ProductCategory::active()->where('id', $id)->first();
        $custom = $productCategory->sponsor_text ?
            collect(['sponsor' => $productCategory->sponsor_text]) :
            collect(['sponsor' => $sponsorAll->{SystemConfig::PRODUCT_SPONSOR}]);
    }

    // Merge sponsor with category data
    $data = $custom->merge($categories);

    // Get a random ad
    $values = [1, 3, 4];
    $randomIndex = random_int(0, count($values) - 1); // Get a random index
    $randomValue = $values[$randomIndex];

    $adDetail = AdDetail::where('ad_category', 'Shop')
        ->where('ad_type', 1)
        ->where('ad_content_type', 'Image')
        ->first();

    // Check if ad exists and append it to the data array
    if ($adDetail) {

        $img = url('storage/' . $adDetail->ad_image);

        $adData = [
            "id" => $adDetail->id,
            "name" => $adDetail->ad_name,
            "fullImage" => $img,
            "ad_link" => $adDetail->ad_link,
            "status" => $adDetail->status,
            "ad_type" => $adDetail->ad_type,
            "ad_category" => $adDetail->ad_category,
            "ad_content_type" => $adDetail->ad_content_type,
            "ad_video" => $adDetail->ad_video,
            "sequence" => $adDetail->sequence,
            "ad_description" => $adDetail->ad_description,
            "adImageWidth" => 375,
            "adImageHeight" => 160
        ];

        // Convert the pagination collection to an array and append the ad data
        $dataArray = $data->toArray();
        $dataArray['data'][] = $adData;

        return $this->genericSuccess($dataArray);
    }

    // Return the data if no ad exists
    return $this->genericSuccess($data);
}



//     function getProductCategory($id = null)
//     {

//         $categories = ProductCategory::active()->where('parent_id', $id)->withCount('children')->orderBy(\DB::raw('sequence IS NULL, sequence'), 'asc')->paginate(50);
//         $sponsorAll = SystemConfig::getOptionGroup(SystemConfig::SPONSOR_GROUP);
//         $custom = collect(['sponsor' => $sponsorAll->{SystemConfig::PRODUCT_SPONSOR}]);
//         if ($id) {
//             $productCategory = ProductCategory::active()->where('id', $id)->first();
//             $custom = $productCategory->sponsor_text ?
//                 collect(['sponsor' => $productCategory->sponsor_text]) :
//                 collect(['sponsor' => $sponsorAll->{SystemConfig::PRODUCT_SPONSOR}]);
//         }
//         $data = $custom->merge($categories);

//         $values = [1, 3, 4];
// $randomIndex = random_int(0, count($values) - 1); // Get a random index
// $randomValue = $values[$randomIndex];
// // echo $randomValue;
//       return $adType1 = AdDetail::where('ad_category', 'Shop')->where('ad_type', $randomValue)->where('ad_content_type', 'Image')->first();
// // die;

// // $data = array_push($data,$adType1);


//         return $this->genericSuccess($data);
//     }


    // function getProductCategory($id = null)   //Bilal
    // {

    //     $categories = ProductCategory::active()
    //     ->where('parent_id', $id)
    //     ->withCount('children')
    //     ->orderBy(\DB::raw('sequence IS NULL, sequence'), 'asc')
    //     ->paginate(50);
    
    // $sponsorAll = SystemConfig::getOptionGroup(SystemConfig::SPONSOR_GROUP);
    // $custom = collect(['sponsor' => $sponsorAll->{SystemConfig::PRODUCT_SPONSOR}]);
    
    // if ($id) {
    //     $productCategory = ProductCategory::active()->where('id', $id)->first();
    //     $custom = $productCategory->sponsor_text
    //         ? collect(['sponsor' => $productCategory->sponsor_text])
    //         : collect(['sponsor' => $sponsorAll->{SystemConfig::PRODUCT_SPONSOR}]);
    // }
    
    // // Fetch ads based on ad_type
    // $adType1 = AdDetail::where('ad_category', 'Shop')->where('ad_type', 1)->where('ad_content_type', 'Image')->first();
    // $adType2 = AdDetail::where('ad_category', 'Shop')->where('ad_type', 3)->where('ad_content_type', 'Image')->first();
    // $adType3 = AdDetail::where('ad_category', 'Shop')->where('ad_type', 4)->where('ad_content_type', 'Image')->first();
    
    // // Insert ads after specific intervals
    // $categoriesWithAds = $categories->items(); // Convert paginated data to an array
    // $adsInserted = [];
    // $offset = 0; // To keep track of the shifting index
    
    // foreach ($categoriesWithAds as $index => $category) {
    //     $adsInserted[] = $category;
    
    //     // Insert ads at dynamic positions accounting for shifting
    //     if ($index + 1 + $offset == 9 && $adType1) {
    //         $adsInserted[] = $adType1;
    //         $offset++; // Increment offset because we added an ad
    //     } elseif ($index + 1 + $offset == 19 && $adType2) {
    //         $adsInserted[] = $adType2;
    //         $offset++;
    //     } elseif ($index + 1 + $offset == 28 && $adType3) {
    //         $adsInserted[] = $adType3;
    //         $offset++;
    //     }
    // }
    
    // if (count($categoriesWithAds) < 9 && $adType1) {
    //     array_unshift($adsInserted, $adType1); // Add at the start
    //     $adsInserted[] = $adType1;            // Add at the end
    // }
    
    // // Convert to a Laravel Collection and include pagination metadata
    // $categoriesWithAdsCollection = collect($adsInserted);
    // $data = $categoriesWithAdsCollection;
    
    // // Return data
    // return $this->genericSuccess($data);
    
    // }

//     public function getProductCategory($id = null)
// {
//     // Fetch the parent categories and paginate
//     $categories = ProductCategory::active()
//         ->where('parent_id', $id)
//         ->withCount('children')
//         ->orderBy(\DB::raw('sequence IS NULL, sequence'), 'asc')
//         ->paginate(50);

//     // Fetch sponsor data
//     $sponsorAll = SystemConfig::getOptionGroup(SystemConfig::SPONSOR_GROUP);
//     $custom = collect(['sponsor' => $sponsorAll->{SystemConfig::PRODUCT_SPONSOR}]);

//     // If category id is provided, fetch specific category and its sponsor text if exists
//     if ($id) {
//         $productCategory = ProductCategory::active()->where('id', $id)->first();
//         $custom = $productCategory->sponsor_text ?
//             collect(['sponsor' => $productCategory->sponsor_text]) :
//             collect(['sponsor' => $sponsorAll->{SystemConfig::PRODUCT_SPONSOR}]);
//     }

//     // Merge sponsor data with categories
//     $data = $custom->merge($categories->items());

//     // Add ads after the 9th, 18th, and 27th categories
//     $categoriesWithAds = [];
//     foreach ($data as $index => $category) {
//         // Add the category to the list
//         $categoriesWithAds[] = $category;

//         // Insert ads after the 9th, 18th, and 27th categories
//         if (($index + 1) % 9 == 0) {
//             $adIndex = (($index + 1) / 9); // Determine which ad to fetch (1st, 2nd, or 3rd)
//             $ad = AdDetail::where('ad_category', 'Shop')
//                 ->where('ad_type', $adIndex) // Fetch ad based on type 1, 2, or 3
//                 ->first();

//             if ($ad) {
//                 $categoriesWithAds[] = ['ad_banner' => $ad, 'ad_position' => 'middle'];
//             }
//         }
//     }

//     // Handling subcategories for specific ad types
//     if ($id) {
//         $subcategories = ProductCategory::active()
//             ->where('parent_id', $id)
//             ->withCount('children')
//             ->orderBy(\DB::raw('sequence IS NULL, sequence'), 'asc')
//             ->get();

//         // Insert ad_type = 3 at the top of the subcategory list
//         $subcategoriesWithAds = [];
//         $ad = AdDetail::where('ad_category', 'Shop')
//             ->where('ad_type', 3)
//             ->first();
//         if ($ad) {
//             $subcategoriesWithAds[] = ['ad_banner' => $ad, 'ad_position' => 'top'];
//         }

//         // Add the subcategories
//         foreach ($subcategories as $subcategory) {
//             $subcategoriesWithAds[] = $subcategory;
//         }

//         // Insert ad_type = 4 at the bottom of the subcategory list
//         $ad = AdDetail::where('ad_category', 'Shop')
//             ->where('ad_type', 4)
//             ->first();
//         if ($ad) {
//             $subcategoriesWithAds[] = ['ad_banner' => $ad, 'ad_position' => 'bottom'];
//         }

//         // Merge the subcategories with the parent categories' ad logic
//         $categoriesWithAds = array_merge($categoriesWithAds, $subcategoriesWithAds);
//     }

//     // Return the final response with ads included
//     return $this->genericSuccess($categoriesWithAds);
// }

    

    


    function getProducts($catid)
    {
        $product = Product::with('images', 'categories')->whereHas('categories', function ($q) use ($catid) {
            $q->where('product_category_id', '=', $catid);
        })->orderBy(\DB::raw('sequence IS NULL, sequence'), 'asc')->paginate(30);
        
        $adDetail = AdDetail::where('ad_category', 'Shop')
        ->where('ad_type', 1)
        ->where('ad_content_type', 'Image')
        ->first();
    
    if ($adDetail) {
        $img = url('storage/' . $adDetail->ad_image);
        $adData = [
            "id" => $adDetail->id,
            "name" => $adDetail->ad_name,
            "fullImage" => $img,
            "ad_link" => $adDetail->ad_link,
            "status" => $adDetail->status,
            "ad_type" => $adDetail->ad_type,
            "ad_category" => $adDetail->ad_category,
            "ad_content_type" => $adDetail->ad_content_type,
            "ad_video" => $adDetail->ad_video,
            "sequence" => $adDetail->sequence,
            "ad_description" => $adDetail->ad_description,
            "adImageWidth" => 375,
            "adImageHeight" => 160,
        ];
    
        // Append ad data to the chat_ride collection
        $product->push($adData);
    }
        return $this->genericSuccess($product);
    }


    public function getProduct($id) {

        $auto = Product::select(['*', 'attr.*'])->with('images')->findOrFail($id);
        
        $attributeSet = AttributeSet::find($auto->attribute_set_id);

        $attributeGroups = $attributeSet ? $attributeSet->groups()->whereHas('attributes')->with(
            ['attributes' => function ($query) {
            // Ensure 'sequence' is selected from the related table (e.g., 'attribute_option')
            $query->select('attributes.attribute_id', 'attributes.entity_id', 'attributes.attribute_code', 
            'attributes.backend_class', 'attributes.backend_type', 'attributes.backend_table', 
            'attributes.frontend_class', 'attributes.frontend_type', 'attributes.frontend_label', 
            'attributes.source_class', 'attributes.default_value', 'attributes.is_filterable', 
            'attributes.is_searchable', 'attributes.is_required', 'attributes.required_validate_class',
            'attributes.sequence', 'attributes.user_id'); // Explicitly select sequence
        }])->get() : collect([]);
        $auto->backgroundImage = $auto['categories'][0]['fullImage'];
        return response()->json($auto->toArray() + [
                'attribute_groups' => $attributeGroups->map(function(AttributeGroup $attributeGroup) use ($auto){
                    return [
                        'name' => $attributeGroup->name(),
                        'attributes' => $attributeGroup->attributes->map(function(Attribute $attribute) use ($auto){
                            return [
                                'label' => $attribute->frontendLabel(),
                                'code' => Str::camel($attribute->code()),
                                'val' => old($attribute->attribute_code, $auto->{$attribute->attribute_code}),
                                'sequence'=>$attribute->getAttributeSequence()
                            ];
                        })
                    ];
                })
            ]);
    }

    public function sendEmail(Request $request)
    {
        $toEmail = $request->email;
        $subject = 'Test Email Subject';
        $bodyContent = '<h1>This is a test email body</h1><p>Here is some content</p>';
        
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);
    
        try {
            // Server settings (customize these as per your email provider)
            $mail->isSMTP();
            $mail->Host = 'smtpout.secureserver.net'; // Specify SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'info@sevenelevensl.com'; // SMTP username
            $mail->Password = 'Sigma@2024'; // SMTP password
            $mail->SMTPSecure = 'ssl';   // Enable TLS encryption
            $mail->Port = 465; // TCP port (e.g., 587 for TLS)
    
            // Recipients
            $mail->setFrom('info@sevenelevensl.com', 'Mailer');
            $mail->addAddress($toEmail); // Add recipient
    
            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $bodyContent;
    
            // Send email
            $mail->send();
    
            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully!'
            ], 200);
    
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email.',
                'error' => $mail->ErrorInfo
            ], 500);
        }
    }

        public function get_breaking_news(Request $request){
            $break_news = BreakingNews::where('seller_id',$request->seller_id)->get();
            if ($break_news) {
                return response()->json([
                    'success' => true,
                    'Data' => $break_news,
                ], 200);
            }else{
                return response()->json([
                    'success' => true,
                    'Data' => 'No news found',
                ], 200);
            }
         
        }
        
        public function get_notices(Request $request)
        {
            $notices = PublicNotice::with('images')->get();
        
            foreach ($notices as $notice) {
                // Process images
                foreach ($notice->images as $image) {
                    $image->image = url('storage/' . $image->image); // Adjust path if stored elsewhere
                }
        
                // Add onepager to the beginning of the images array
                if (!empty($notice->one_page)) {
                    $notice->images->prepend([
                        'image' => url('storage/' . $notice->one_page), // Adjust path if stored elsewhere
                        'type' => 'pdf',
                    ]);
                }
            }
        
            if ($notices->isNotEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => $notices,
                ], 200);
            } else {
                return response()->json([
                    'success' => true,
                    'data' => 'No news found',
                ], 200);
            }
        }
        
}
