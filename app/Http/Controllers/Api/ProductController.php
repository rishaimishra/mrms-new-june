<?php


namespace App\Http\Controllers\Api;

use App\Logic\SystemConfig;
use App\Models\Auto;
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

        $categories = ProductCategory::active()->where('parent_id', $id)->withCount('children')->orderBy(\DB::raw('sequence IS NULL, sequence'), 'asc')->paginate(50);
        $sponsorAll = SystemConfig::getOptionGroup(SystemConfig::SPONSOR_GROUP);
        $custom = collect(['sponsor' => $sponsorAll->{SystemConfig::PRODUCT_SPONSOR}]);
        if ($id) {
            $productCategory = ProductCategory::active()->where('id', $id)->first();
            $custom = $productCategory->sponsor_text ?
                collect(['sponsor' => $productCategory->sponsor_text]) :
                collect(['sponsor' => $sponsorAll->{SystemConfig::PRODUCT_SPONSOR}]);
        }
        $data = $custom->merge($categories);
        return $this->genericSuccess($data);
    }

    function getProducts($catid)
    {
        $product = Product::with('images', 'categories')->whereHas('categories', function ($q) use ($catid) {
            $q->where('product_category_id', '=', $catid);
        })->orderBy(\DB::raw('sequence IS NULL, sequence'), 'asc')->paginate(30);

        return $this->genericSuccess($product);
    }


    public function getProduct($id) {

        $auto = Product::select(['*', 'attr.*'])->with('images')->findOrFail($id);
        
        $attributeSet = AttributeSet::find($auto->attribute_set_id);

        $attributeGroups = $attributeSet ? $attributeSet->groups()->whereHas('attributes')->with('attributes')->get() : collect([]);
        $auto->backgroundImage = $auto['categories'][0]['fullImage'];
        return response()->json($auto->toArray() + [
                'attribute_groups' => $attributeGroups->map(function(AttributeGroup $attributeGroup) use ($auto){
                    return [
                        'name' => $attributeGroup->name(),
                        'attributes' => $attributeGroup->attributes->map(function(Attribute $attribute) use ($auto){
                            return [
                                'label' => $attribute->frontendLabel(),
                                'code' => Str::camel($attribute->code()),
                                'val' => old($attribute->attribute_code, $auto->{$attribute->attribute_code})
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
        
            // Append full URL to each image
            foreach ($notices as $notice) {
                foreach ($notice->images as $image) {
                    $image->image = url('storage/' . $image->image); // Adjust path if stored elsewhere
                }
            }
            foreach ($notices as $notice) {
                // dd($notice);
                    $notice->one_page = url('storage/' . $notice->one_page); // Adjust path if stored elsewhere
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
