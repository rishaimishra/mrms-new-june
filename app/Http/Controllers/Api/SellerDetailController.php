<?php

namespace App\Http\Controllers\Api;


use App\Models\SellerDetail;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\SellerThemeRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;



class SellerDetailController extends ApiController
{


    const ENTITY_ID = 3;

    // protected function getUserSavedRechargeCards()
    // {
    //     return request()->user()->savedstarrechargecards();
    // }

    // public function index(Request $request)
    // {
    //     $user = $request->user();
    //     $saveddstvrechargecards = SavedStarRechargeCard::where('user_id',$user->id)->get();
    //     return $this->success('', [
    //         'savedstarrechargecards' => $saveddstvrechargecards
    //     ]);
    // }

    // public function create(Request $request)
    // {
    //     $saveddstvrechargecard = new SavedStarRechargeCard();

    //     $user = $request->user();
    //     $saveddstvrechargecard->user_id = $user->id;
    //     $saveddstvrechargecard->recharge_card_number = $request->recharge_card_number;
    //     $saveddstvrechargecard->recharge_card_name = $request->recharge_card_name;
    //     $saveddstvrechargecard->save();

    //     return $this->success("Success", [
    //     ]);


    // }

    // public function delete(Request $request) {
    //     $id = $request->id;
    //     $saveddstvrechargecard = SavedStarRechargeCard::find($id);
    //     $saveddstvrechargecard->delete();
        
    //     return $this->success("Success", [
    //     ]);
    // }



    public function uploadDocuments(Request $request) {

        $request->validate([
            'store_document_1' => 'nullable|mimes:jpg,jpeg,png|',
            'store_document_2' => 'nullable|mimes:jpg,jpeg,png|',
            'store_document_3' => 'nullable|mimes:jpg,jpeg,png|',
            'store_document_4' => 'nullable|mimes:jpg,jpeg,png|',
        ]);

        $user = $request->user();
        $sellerdetail = SellerDetail::where('user_id',$user->id)->first();
        $value = $request->all();

        if ($request->hasFile('store_document_1')) {
            if (file_exists(storage_path('app/' . $sellerdetail->store_document_1))) {
                @unlink(storage_path('app/' . $sellerdetail->store_document_1));
            }
            $path = \Storage::disk('public')->putFile('store_docs', $request->file('store_document_1'));

            $value['store_document_1'] = $path;
        }
        //$sellerdetail->store_document_1 = asset("storage/{$sellerdetail->store_document_1}");

        if ($request->hasFile('store_document_2')) {
            if (file_exists(storage_path('app/' . $sellerdetail->store_document_2))) {
                @unlink(storage_path('app/' . $sellerdetail->store_document_2));
            }
            $path = \Storage::disk('public')->putFile('store_docs', $request->file('store_document_2'));

            $value['store_document_2'] = $path;
        }
       // $sellerdetail->store_document_2 = asset("storage/{$sellerdetail->store_document_2}");


        if ($request->hasFile('store_document_3')) {
            if (file_exists(storage_path('app/' . $sellerdetail->store_document_3))) {
                @unlink(storage_path('app/' . $sellerdetail->store_document_3));
            }
            $path = \Storage::disk('public')->putFile('store_docs', $request->file('store_document_3'));

            $value['store_document_3'] = $path;
        }
       // $sellerdetail->store_document_3 = asset("storage/{$sellerdetail->store_document_3}");


        if ($request->hasFile('store_document_4')) {
            if (file_exists(storage_path('app/' . $sellerdetail->store_document_4))) {
                @unlink(storage_path('app/' . $sellerdetail->store_document_4));
            }
            $path = \Storage::disk('public')->putFile('store_docs', $request->file('store_document_4'));

            $value['store_document_4'] = $path;
        }
       // $sellerdetail->store_document_4 = asset("storage/{$sellerdetail->store_document_4}");


        $sellerdetail->update($value);
        //$user->avatar =  \Storage::url($user->avatar);
        //$user->avatar = asset("storage/{$user->avatar}");
        return $this->success('You are successfully Updated.', ['Seller' => $sellerdetail]);
    }


    public function checkVerify(Request $request) {
        $user = $request->user();
        $sellerdetail = SellerDetail::where('user_id',$user->id)->first();
        return $this->success(['Seller' => $sellerdetail]);
    }

    public function sellerListByCategory($id){
        $sellerdetail = SellerDetail::with(['themeRelations.theme'])->where('store_category',$id)->get();

        if ($sellerdetail->isEmpty()) {
            // $sellerdetail is empty
            $sellerdetail = SellerDetail::with(['themeRelations.theme'])->where('store_category_name',$id)->get();

        } 
        
         // Loop through each seller to modify the businessLogo field
            foreach ($sellerdetail as $seller) {
                // Assuming 'businessLogo' is the name of the field in your SellerDetail model
                $seller->businessRegistrationImage = asset('busniess_images/' . $seller->business_registration_image);
                $seller->businessLogo = asset('busniess_images/' . $seller->business_logo);

                foreach ($seller->themeRelations as $relation) {
                    if ($relation->theme) {
                        // Assuming themeName is the field in your theme model, append the full path
                        $relation->theme->themeName = asset('storage/' .$relation->theme->theme_name);
                    }
                }
            }
        return $this->success(['Sellers' => $sellerdetail]);
    }


    public function createCategory(Request $request)
    {
        $request->validate([
            'parent_id' => 'nullable|numeric',
            'is_active' => 'nullable|boolean',
            'images' => 'nullable|required|file|mimes:jpg,jpeg,png',
        ]);


        $user = $request->user();
        $sellerdetail = SellerDetail::where('user_id',$user->id)->first();
        $input = $request->all();
        $input['name'] = $sellerdetail->store_name;
        $input['seller_detail_id'] = $sellerdetail->id;
        

        if ($request->hasFile('images')) {
            $path = $this->resizeImage($request->file('images'), 'product_category');
            $input['image'] = $path;
        }

        ProductCategory::create($input);

        return $this->success('You are successfully Created Category.');
    }

    public function createProduct(Request $request) 
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'quantity' => 'required|numeric|min:1|max:999999',
            'weight' => 'required|numeric|min:0|max:999999',
            'price' => 'required|regex:/^(?!,$)[\d,.]+$/|string|max:20',
            'unit' => 'required|string|max:50',
            'images' => 'required|file|mimes:jpg,jpeg,png',
        ]);
        $user = $request->user();
        $sellerdetail = SellerDetail::where('user_id',$user->id)->first();
        $productcategory = ProductCategory::where('seller_detail_id', $sellerdetail->id)->first();

        $product = new Product();
        $product->entity_id = self::ENTITY_ID;
        $product->name = $request->name;
        $product->quantity = $request->quantity;
        $product->weight = $request->weight;
        $product->price = str_replace(',', '', $request->price);
        $product->unit = $request->unit;
        $product->sequence = $request->input('sequence', '');
        $product->attribute_set_id = 279;
        $product->stock_availability = $request->input('stock_availability');

        $product->forceFill([
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $product->save();

        $product->categories()->sync($productcategory->id);

        if ($request->hasFile('images')) {
           
                //$path = \Storage::disk('public')->putFile('basket', $image);
                $path = $this->resizeImage($request->file('images'), 'basket', 800);
                $product->images()->create(['image' => $path]);
            
        }
        return $this->success('You are successfully Created Category.');





    }
}
