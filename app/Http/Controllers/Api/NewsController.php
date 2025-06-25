<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewsSubscription;
use App\Models\NationalNews;
use App\Models\StateNewsSubscription;
use App\Models\NationalNewsSubscription;
use App\Models\SellerDetail;
use App\Models\User;

class NewsController extends ApiController
{
    
    public function getStateNewsByUser($userId) {
        $newsList = NewsSubscription::where('user_id', $userId)->get()->map(function ($news) {
            $news->headline_image = !empty($news->headline_image) ? asset('storage/' . $news->headline_image) : null;
            return $news;
        });
    
        return $this->success('', [
            'state_news' => $newsList
        ]);
    }
    
   
    public function getStateNewsDetail($id){
        $newsList = NewsSubscription::where('id', '=', $id)->get()->map(function ($news) {
            $news->headline_image = !empty($news->headline_image) ? asset('storage/' . $news->headline_image) : null;
            
            // Create a dynamic URL for each news item based on the ID
            $news->detail_url = url('/sl-admin/news_detail/' . $news->id);
            
            return $news;
        });
    
    
        return $this->success('', [
            'state_news' => $newsList,
        ]);
    }
   
    public function getNationalNewsDetail($id){
        $newsList = NationalNews::where('id','=',$id)->get()->map(function ($news) {
            $news->headline_image = !empty($news->headline_image) ? asset('storage/' . $news->headline_image) : null;
            $news->front_image = !empty($news->front_image) ? asset('storage/' . $news->front_image) : null;
            $news->detail_url = url('/sl-admin/national_news_detail/' . $news->id);
            return $news;
        });
    
        return $this->success('', [
            'national_news' => $newsList
        ]);
    }


    public function getNationalNewsByUser($userId) {
        $newsList = NationalNews::where('user_id', $userId)->get()->map(function ($news) {
            $news->headline_image = !empty($news->headline_image) ? asset('storage/' . $news->headline_image) : null;
            $news->front_image = !empty($news->front_image) ? asset('storage/' . $news->front_image) : null;
            return $news;
        });
    
        return $this->success('', [
            'national_news' => $newsList
        ]);
    }
    

    public function getNewsSellers(Request $request){
        // dd($request->user_type);
        if (isset($request->user_type)) {
           
            $sellerdetail = SellerDetail::with(['themeRelations.theme'])->where('user_id',105)->get();
        }else{
             $userIds = User::where('user_type', '=', 'national news')->pluck('id');
          

            $sellerdetail = SellerDetail::with(['themeRelations.theme', 'user'])
                ->whereIn('user_id', $userIds)
                ->get();

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

    
}
