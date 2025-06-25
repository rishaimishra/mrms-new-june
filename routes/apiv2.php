<?php

use Illuminate\Http\Request;
use App\Http\Controllers\APIV2\General\PropertyController;

Route::post('get/all-property', 'APIV2\General\PropertyController@getAllProperty');
Route::post('update_new_property', 'APIV2\General\PropertyController@update_new_property');

Route::get('update/assessments', 'APIV2\General\PropertyController@updatePropertyAssessmentDetail');
Route::get('get/options', 'APIV2\General\PopulateAssessmentController@populateField');
Route::get('get/district', 'APIV2\General\DistrictController@getDistrict');
Route::post('create/inaccessibleproperty', 'APIV2\General\PropertyController@createInAccessibleProperties');
// Route::post('save/property', 'APIV2\General\PropertyController@save');

Route::group(
    [
        'middleware' => 'auth:api',
        'namespace' => 'APIV2'
    ],
    function () {
         Route::get('get/landlord-assesment/{property_id}', 'General\PropertyController@getLandLordAssesMent');


        Route::post('assessment-calculate', 'General\CalculatePropertyRateController@Calculate');
        Route::post('get/address-options-by-ward', 'General\PopulateOnWardController@Populate');
        Route::get('get/meta', 'General\PopulateAssessmentController@getMeta');
        Route::post('save/property', 'General\PropertyController@save');
        Route::get('get/incomplete-property', 'General\PropertyController@getIncompleteProperty');
        Route::post('update/property', 'General\PropertyController@update');
        Route::post('update/user-profile', 'General\AppUserController@editProfile');
        Route::get('get/my/district', 'General\PropertyController@getMyDistrict');

        Route::get('xyz', 'General\PropertyController@xyz');
        Route::get('get-all-counsil', 'General\PropertyController@get_all_counsil');

        Route::get('business-list/{id}', 'General\PropertyController@BusinessListById');
        Route::post('business-update/{id}', 'General\PropertyController@updateBusiness');

        Route::get('business-dropdown', 'General\PropertyController@dropDownData');


        Route::post('search-business', 'Business\BusinessController@searchBusiness');
        Route::post('payment-save', 'Business\BusinessController@paymentStore');
        Route::post('payment-history', 'Business\BusinessController@paymentHistory');
        
    }
);

Route::post('save/image', 'APIV2\General\PropertyController@saveImage');

Route::post('login', 'APIV2\User\AuthController@login');
//Route::post('signup', 'API\User\AuthController@signup');
Route::post('reset/password', 'APIV2\User\AuthController@resetPasswordRequest');
//Route::get('logout', 'API\User\AuthController@logout');


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('admin/login', 'APIV2\Admin\AuthController@login');
//Route::get('admin/logout', 'APIV2\Admin\AuthController@login');

Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin-api', 'namespace' => 'APIV2'], function () {
    Route::post('payment/{id}', 'Admin\PaymentController@store');
    Route::post('search-property', 'Admin\PaymentController@show');
});


Route::post('landlord/login', 'APIV2\Landlord\AuthController@login');
Route::post('landlord/otp', 'APIV2\Landlord\AuthController@mobileVerification');

Route::group(['prefix' => 'landlord', 'middleware' => 'auth:landlord-api', 'namespace' => 'APIV2'], function () {
    Route::post('payment/{id}', 'Landlord\PaymentController@payWithpaypal');
    Route::post('search-property', 'Landlord\PaymentController@show');
    Route::post('search-property-detail', 'Landlord\PaymentController@searchPropertyDetail');
    Route::post('payment-store', 'Landlord\PaymentController@savePropertyPayment');
});

Route::group(
    [
        'namespace' => 'APIV2'
    ],
    function () {
        Route::post('get-business-detail', 'Business\BusinessController@getBusinessDetail');
        Route::post('non-profit/insert-business', 'Business\BusinessController@nonProfitInsertBusiness');
        Route::post('profit/insert-business', 'Business\BusinessController@profitInsertBusiness');
        Route::post('non-profit/update-business', 'Business\BusinessController@nonProfitUpdateBusiness');
        Route::post('profit/update-business', 'Business\BusinessController@profitUpdateBusiness');
        Route::post('update-street-app-coordinates', 'StreetNamingAppController@update_coordinates');
    }
);
