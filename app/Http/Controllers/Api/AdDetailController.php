<?php

namespace App\Http\Controllers\Api;


use App\Models\AdDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;



class AdDetailController extends ApiController
{

    public function index(Request $request)
    {
        $ads = AdDetail::get();
        return $this->success('success', [
            'ads' => $ads
        ]);
    }

    public function shopAds()
    {
       $banner_ads = AdDetail::where('ad_category','Shop')->where('ad_type',1)->get();
       $small_ads = AdDetail::where('ad_category','Shop')->where('ad_type',2)->get();
       return $this->success('success', [
           'bannerads' => $banner_ads,
           'smallads' => $small_ads
       ]);
    }

    public function autoAds()
    {
       $banner_ads = AdDetail::where('ad_category','Auto')->where('ad_type',1)->get();
       $small_ads = AdDetail::where('ad_category','Auto')->where('ad_type',2)->get();
       return $this->success('success', [
           'bannerads' => $banner_ads,
           'smallads' => $small_ads
       ]);
    }


    public function realestateAds()
    {
       $banner_ads = AdDetail::where('ad_category','RealEstate')->where('ad_type',1)->get();
       $small_ads = AdDetail::where('ad_category','RealEstate')->where('ad_type',2)->get();
       return $this->success('success', [
           'bannerads' => $banner_ads,
           'smallads' => $small_ads
       ]);
    }


    public function utilitiesAds()
    {
       $banner_ads = AdDetail::where('ad_category','Utilities')->where('ad_type',1)->get();
       $small_ads = AdDetail::where('ad_category','Utilities')->where('ad_type',2)->get();
       return $this->success('success', [
           'bannerads' => $banner_ads,
           'smallads' => $small_ads
       ]);
    }
}
