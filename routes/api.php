<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::get('/get_wallet_balance', 'Api\UserController@get_wallet_balance');
    Route::get('/get_movie_doc', 'Api\UserController@get_movie_doc');
    Route::get('/get_chat_a_ride', 'Api\UserController@get_chat_a_ride');
    Route::get('/get_movie_doc_seller', 'Api\UserController@get_movie_doc_seller');
    Route::post('/get_chat_a_ride_seller', 'Api\UserController@get_chat_a_ride_seller');
    Route::get('/get_service_category', 'Api\UserController@get_service_category');
    Route::get('/get_online_booking', 'Api\UserController@get_online_booking');
    Route::get('/get_service_category_seller', 'Api\UserController@get_service_category_seller');
    Route::get('/get_autos_category_seller', 'Api\UserController@get_autos_category_seller');
    Route::get('/get_realestate_category_seller', 'Api\UserController@get_realestate_category_seller');
    Route::post('/get_online_booking_seller', 'Api\UserController@get_online_booking_seller');
    Route::post('email', 'Api\ProductController@sendEmail');
    Route::post('login', 'Api\AuthController@login');
    Route::post('register', 'Api\AuthController@register');
    Route::post('register-seller', 'Api\AuthController@registerSeller');
    Route::post('forgot-password', 'Api\AuthController@forgotPassword');

    Route::post('reset-password', 'Api\AuthController@resetPassword');

    Route::post('check-otp', 'Api\AuthController@checkOTP');
    Route::get('shipper-by-country', 'Api\AuthController@shipper_by_country');
    Route::post('get_notices', 'Api\ProductController@get_notices');
    Route::post('get_breaking_news', 'Api\ProductController@get_breaking_news');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('edsa_api', 'Api\SavedDstvRechargeCardController@edsa_api');
        Route::get('get_edsa_transaction', 'Api\SavedDstvRechargeCardController@get_edsa_transaction');
        Route::post('dstv_recharge', 'Api\SavedDstvRechargeCardController@dstv_recharge');
        Route::post('star_recharge', 'Api\SavedDstvRechargeCardController@star_recharge');
        Route::post('track_shipping', 'Api\SavedDstvRechargeCardController@track_shipping');
        Route::get('get_dstv_transaction', 'Api\SavedDstvRechargeCardController@get_dstv_transaction');
        Route::get('get_star_transaction', 'Api\SavedDstvRechargeCardController@get_star_transaction');
        Route::get('get_track_shipping_transaction', 'Api\SavedDstvRechargeCardController@get_track_shipping_transaction');
        Route::post('add_air_sea_freight', 'Api\SavedDstvRechargeCardController@add_air_sea_freight');

        Route::post('change-mobile-number', 'Api\AuthController@changeMobileNumber');

        Route::post('resend-otp', 'Api\AuthController@resendOTP');

        Route::post('mobile-verification', 'Api\AuthController@mobileVerification');
        Route::post('get_seller/{id}', 'Api\AuthController@get_seller');
        Route::get('get_payment_collection/{customer_id}', 'Api\AuthController@get_payment_collection');
        Route::get('get_money_transfer/{customer_id}', 'Api\AuthController@get_money_tranfer_collection');
        Route::get('get_money_transfer_seller', 'Api\AuthController@get_money_tranfer_collectionSeller');
        Route::get('get_collection_seller', 'Api\AuthController@getcollectionSeller');

        Route::get('/me', 'Api\UserController@getUser');


        //seller
        Route::post('/upload-document', 'Api\SellerDetailController@uploadDocuments');
        Route::get('/check-verify', 'Api\SellerDetailController@checkVerify');
        Route::post('/create-store', 'Api\SellerDetailController@createCategory');
        Route::post('/create-product', 'Api\SellerDetailController@createProduct');




        Route::post('logout', 'Api\AuthController@logout');

        Route::middleware('verified_mobile')->group(function () {
            Route::group(['prefix' => 'me'], function () {

                Route::post('/', 'Api\UserController@store');

            });

            Route::post('update-password', 'Api\AuthController@updatePassword');

            Route::group(['prefix' => 'digital-address'], function () {

                Route::get('/', 'Api\DigitalAddressController@index');
                Route::get('/{id}', 'Api\DigitalAddressController@show');
                Route::post('/', 'Api\DigitalAddressController@store');
                Route::put('/{id}', 'Api\DigitalAddressController@update');
                Route::delete('/{id}', 'Api\DigitalAddressController@destroy');
                Route::post('/search', 'Api\DigitalAddressController@search');
                Route::post('/area_search', 'Api\DigitalAddressController@areaSearch');
                Route::post('/area_by_id', 'Api\DigitalAddressController@areaById');
            });

            Route::group(['prefix' => 'place'], function () {

                Route::get('/category/{id?}', 'Api\PlaceController@getPlaceCategory');
                Route::get('/{id}', 'Api\PlaceController@getPlace');
            });

            Route::group(['prefix' => 'knowledgebase'], function () {

                Route::get('/category/{id?}', 'Api\KnowledgebaseController@getKnowledgebaseCategory');
                Route::get('/{id}', 'Api\KnowledgebaseController@getQuestion');
            });

            Route::group(['prefix' => 'product'], function () {

                Route::get('/category/{id?}', 'Api\ProductController@getProductCategory');
                Route::get('/{id}', 'Api\ProductController@getProducts');
                Route::get('/single/{id}', 'Api\ProductController@getProduct');
            });
          
            Route::group(['prefix' => 'auto'], function () {

                Route::get('/category/{id?}', 'Api\AutoController@getAutoCategory');
                Route::get('/{id}', 'Api\AutoController@getAutos');
                Route::get('/single/{id}', 'Api\AutoController@getAuto');
                Route::post('setInterested/{auto}', 'Api\AutoController@setInterested');
            });

            Route::group(['prefix' => 'realestate'], function () {

                Route::get('/category/{id?}', 'Api\RealEstateController@getAutoCategory');
                Route::get('/{id}', 'Api\RealEstateController@getProperties');
                Route::get('/single/{id}', 'Api\RealEstateController@getProperty');
                Route::post('setInterested/{realEstate}', 'Api\RealEstateController@setInterested');
            });

            Route::group(['prefix' => 'cart'], function () {

                Route::get('/getCart', 'Api\CartController@getCart');
                Route::post('/addcart', 'Api\CartController@addCart');
                Route::post('/addDigitalAddress', 'Api\CartController@addDigitalAddress');
                Route::post('/updateCart', 'Api\CartController@updateCart');
                Route::post('/deleteItemFromCart', 'Api\CartController@deleteItemFromCart');

            });

            Route::post('/place-order', 'Api\OrderController@placeOrder');
            Route::get('/place-order', 'Api\OrderController@index');
           
            
            //saved meter apis
            Route::get('/saved-meters', 'Api\SavedMeterController@index');
            Route::get('/edsa-transaction', 'Api\EdsaTransactionController@index');
            Route::post('/add/saved-meter','Api\SavedMeterController@create');
            Route::post('/delete/saved-meter','Api\SavedMeterController@delete');
            Route::post('/add/edsa-transaction','Api\EdsaTransactionController@create');
            
            
            
            //saved dstv recharge cards
            Route::get('/saved-dstv-recharge-cards', 'Api\SavedDstvRechargeCardController@index');
            Route::post('/add/saved-recharge-cards','Api\SavedDstvRechargeCardController@create');
            Route::post('/delete/saved-recharge-cards','Api\SavedDstvRechargeCardController@delete');
            
            
            //saved star recharge cards
            Route::get('/saved-star-recharge-cards', 'Api\SavedStarRechargeCardController@index');
            Route::post('/add/saved-star-recharge-cards','Api\SavedStarRechargeCardController@create');
            Route::post('/delete/saved-star-recharge-cards','Api\SavedStarRechargeCardController@delete');
            
            
            //edsa user verifications
            Route::get('/get-edsa-otp-verify', 'Api\UserController@setEdsaPasswordOTP');
            Route::post('/verify-edsa-otp','Api\UserController@checkUserOtp');
            Route::post('/set-edsa-password','Api\UserController@setEdsaPassword');
            Route::post('/verify-edsa-password','Api\UserController@verifyEdsaPassword');
            
        });
    });

    Route::get('/get-ads', 'Api\AdDetailController@index');
    Route::get('/get-shop-ads','Api\AdDetailController@shopAds');
    Route::get('/get-auto-ads','Api\AdDetailController@autoAds');
    Route::get('/get-realestate-ads','Api\AdDetailController@realestateAds');
    Route::get('/get-utilities-ads','Api\AdDetailController@utilitiesAds');
    Route::post('digital-address/area_search', 'Api\DigitalAddressController@areaSearch');
    Route::post('/send-mail', 'MailController@sendMail');
    Route::get('/aboutApp', 'Api\AboutAppController@index');
    Route::get('/legal', 'Api\LegalTermsAndPoliciesController@index');
    Route::get('/privacy', 'Api\PrivacyPolicyController@index');
    Route::get('/intellectual', 'Api\IntellectualPropertyController@index');
    Route::get('/cookies', 'Api\CookiesController@index');
    Route::get('/payments', 'Api\PaymentsDeliveriesController@index');
    Route::get('/returns', 'Api\ReturnController@index');
    Route::get('/testOtp', 'Api\UserController@testOTP');
    
    Route::get('seller-list-by-category/{category_id}', 'Api\SellerDetailController@sellerListByCategory');
    // Route::post('/about-app', 'AboutAppController@checkAndUpdate');
  
    
    Route::get('/state-news/seller/{seller_id}', 'Api\NewsController@getStateNewsByUser');
    Route::get('/state-news/{id}', 'Api\NewsController@getStateNewsDetail');
    Route::get('/national-news/seller/{seller_id}', 'Api\NewsController@getNationalNewsByUser');
    Route::get('/national-news/{id}', 'Api\NewsController@getNationalNewsDetail');
    Route::post('/news/sellers', 'Api\NewsController@getNewsSellers');
    
});
