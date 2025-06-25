<?php

use App\Http\Controllers\Admin\Attribute\AttributeSetController;
use App\Http\Controllers\Admin\ProductSeller\ProductSellerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Models\AddressArea;
use App\Models\AddressSection;
use App\Models\AddressChiefdom;


use App\Http\Controllers\Admin\Auth\SellerLoginController;

// Define a route to send email
// Define a route to send email
Route::get('/send-email', function () {
    // Email recipient
    $toEmail = 'rishavbeas@gmail.com'; // Change this to the recipient's email address

    // Email data
    $subject = 'Test Email';
    $messageContent = 'This is a test email sent directly from the route!';

    // Send the email
    Mail::raw($messageContent, function ($message) use ($toEmail, $subject) {
        $message->to($toEmail)
                ->subject($subject)
                ->from('info@sevenelevensl.com', 'Your Name'); // Ensure you provide a valid sender name
    });

    return 'Email sent successfully!';
});

// Fetch address sections for a given area
Route::get('/api/address-sections/{area_id}', function($area_id) {
    return AddressSection::where('address_area_id', $area_id)->get();
});

// Fetch address chiefdoms for a given area
Route::get('/api/address-chiefdoms/{area_id}', function($area_id) {
    return AddressChiefdom::where('address_area_id', $area_id)->get();
});


Route::get('news_detail/{id}', 'UtilityExtendController@news_detail')->name('news_detail');
Route::get('national_news_detail/{id}', 'UtilityExtendController@national_news_detail')->name('national_news_detail');
Route::get('sellerLogin', [SellerLoginController::class,'showForm'])->name('seller.login.form'); 
Route::post('sellerLogin', [SellerLoginController::class,'login'])->name('seller.login');
Route::get('sellerLogout', [SellerLoginController::class,'logout'])->name('seller.logout');
Route::group(['as' => 'auth.', 'namespace' => 'Auth'], function () {

    Route::get('login', 'LoginController@showForm')->name('login');
    Route::post('login', 'LoginController@login');
    Route::get('logout', 'LoginController@logout')->name('logout');
    Route::get('forgot-password', 'ForgotPasswordController@showLinkRequestForm')->name('forgot-password');
    Route::post('forgot-password', 'ForgotPasswordController@sendResetLinkEmail');
    Route::get('reset-password', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::POST('reset-password', 'ResetPasswordController@reset')->name('password.update');
   

});



Route::get('/seller/product', function () {
    echo phpinfo();
    // return view('admin.seller_system.seller_dashboard');
    echo "testBilal";
    die;
})->name('testBilal');


// Seller CRUD Routes

Route::get('/show-seller-product', 'ProductSeller\ProductSellerController@index')->name('show-seller-product');
Route::get('/show-auto-product', 'ProductSeller\ProductSellerController@Autoindex')->name('show-auto-product');
Route::get('/show-realestate-product', 'ProductSeller\ProductSellerController@PropertyIndex')->name('show-realestate-product');
Route::get('delete_product/{id}', 'ProductSeller\ProductSellerController@productDestroy')->name('delete-product-seller');
Route::get('edit_product/{id}/{type}', 'ProductSeller\ProductSellerController@product_edit')->name('edit-product-seller');
Route::get('edit_auto/{id}', 'Auto\AutoSellerController@auto_edit')->name('edit-auto-seller');
Route::post('save-auto/{id}', 'Auto\AutoSellerController@auto_edit_save_similar')->name('save-auto');

Route::get('edit_property/{id}', 'Auto\AutoSellerController@edit_property')->name('edit-property-seller');

Route::get('/my-auto-product', 'Auto\AutoSellerController@AutoMyindex')->name('edit-seller-product');
Route::get('/edit-myauto/{id}', 'Auto\AutoSellerController@MyAutoedit')->name('edit-auto.seller');
Route::post('/edit-myauto/{id}', 'Auto\AutoSellerController@MyAutoeditsave')->name('edit-auto.seller.save');

Route::post('update_product', 'ProductSeller\ProductSellerController@update_product')->name('update-product-seller');

Route::get('/edit-product/{id}', 'ProductSeller\ProductSellerController@MyProductedit')->name('edit-product.seller');
Route::patch('/update-product/{id}', 'ProductSeller\ProductSellerController@updateMyProduct')
    ->name('update-product.seller');
    Route::delete('/delete/product-images/{id}', 'ProductSeller\ProductSellerController@destroy');



Route::get('/my-seller-product', 'ProductSeller\ProductSellerController@Myindex')->name('my-seller-product');


Route::resource('seller-product', 'Product\ProductController')->except('show');
Route::resource('seller-product-category', 'Product\ProductCategoryController')->except('show');
Route::get('imageDelete/seller-product/{id?}', 'Product\ProductController@imageDelete');
Route::get('bgimageDelete/product/{id?}', 'Product\ProductController@bgimageDelete');

Route::group(['prefix' => 'attribute'], function() {

    Route::get('/attribute-groups-by-attribute-set/{id}', 'Attribute\AttributeController@attributeGroupsByAttributeSet');
    Route::resource('seller-attribute', "Attribute\AttributeController")->except('show');
    Route::resource('seller-attribute-set', "Attribute\AttributeSetController")->except('show');
    Route::resource('seller-attribute-group', "Attribute\AttributeGroupController")->except('show');

});



// Route::group(['as' => 'auth.', 'namespace' => 'Auth'], function () {
    
//     Route::post('sellerLogin', 'SellerLoginController@login');
//     Route::get('sellerLogin', 'SellerLoginController@showForm')->name('login');

// });


Route::get('/seller/sellerDashboard', function () {
    return view('admin.seller_system.seller_dashboard');
})->name('sellerDashboard');

    Route::get('/seller/sea-air-frights', 'SellerDetailController@sea_air_frieghts')->name('seafrieghts.users');
    Route::get('/seller/users', 'SellerDetailController@users')->name('seller.users');
    Route::get('/seller/notifications', 'SellerDetailController@notification')->name('sellernotification.users');
    Route::get('/seller/account', 'SellerDetailController@sellerAccount')->name('selleraccount');
    Route::get('/seller/state-news', 'SellerDetailController@stateNews')->name('seller.state.news');
    Route::get('/seller/national-news', 'SellerDetailController@nationalNews')->name('seller.national.news');
    Route::post('/seller/national_news_store','SellerDetailController@national_news_store')->name('seller.national-news-store');
    Route::get('/seller/national_news_delete/{id}','SellerDetailController@national_news_delete')->name('seller.national-news-delete');
    Route::get('/seller/national_news_edit/{id}','SellerDetailController@national_news_edit')->name('seller.national-news-edit');
    Route::get('/seller/national_news_edit/save/{id}','SellerDetailController@national_news_edit_save')->name('seller.national-news-edit-save');
    Route::post('/seller/national_news_edit/save/{id}','SellerDetailController@national_news_edit_update')->name('seller.national-news-edit-update');
    

    Route::post('/seller/news_subscription_store','SellerDetailController@news_subscription_store')->name('seller.newssubscription-store');
    Route::post('/seller/news_subscription_update','SellerDetailController@news_subscription_update')->name('seller.newssubscription-store.update');
    Route::get('/seller/news_subscription_edit/{id}','SellerDetailController@news_subscription_edit')->name('seller.newssubscription-edit');
    Route::get('/seller/news_subscription_edit-news/{id}','SellerDetailController@news_subscription_edit_news')->name('seller.newssubscription-edit-news');
    Route::get('/seller/news_subscription_delete/{id}','SellerDetailController@news_subscription_delete')->name('seller.newssubscription-delete');
   
    
    Route::post('assign/seller-themes', 'SellerDetailController@assignThemeSeller')->name('assign.sellerthemes');
    Route::post('update/seller-logo', 'SellerDetailController@UpdateLogo')->name('business.logo');
   

    Route::get('/assign-seller-role', 'SellerDetailController@showAssignRoleForm')->name('assign.sellerrole.form');
    Route::post('/assign-seller-role', 'SellerDetailController@assignRole')->name('assign.sellerrole');

    Route::get('/seller/collection-payments', 'SellerDetailController@collectionPayments')->name('sellercollection.users');
    Route::get('/seller/money-transfer', 'SellerDetailController@MoneyTransfer')->name('sellermoneytransfer.users');
    Route::POST('seller/upload_money_transfer', 'SellerDetailController@upload_money_transfer')->name('import_money_transfer.seller');
    Route::post('seller/export_money_transfer_excel', 'SellerDetailController@export_payment_excel_moneytransfer')->name('payment_money_excel.seller');
    
    Route::post('/seller/freightExport', 'SellerDetailController@FreightExport')->name('seller.frightexport');

    Route::get('/admin/sea-air-frights', 'SellerDetailAdminController@sea_air_frieghts')->name('seller.seafrieghts');
    Route::get('/admin/seller/collection', 'SellerDetailAdminController@collectionPayments')->name('seller.collection');
    Route::get('/admin/seller/notification', 'SellerDetailAdminController@notification')->name('seller.notification');
    Route::post('/export_frieght_excel', 'SellerDetailAdminController@export_frieght_excel')->name('seller.export_excel');
    Route::POST('seller/upload-sea-air-frights', 'SellerDetailController@upload_sea_air_frieghts')->name('admin.UFSF');
    Route::POST('seller/upload_payment_collection', 'SellerDetailAdminController@upload_payment_collection')->name('seller.import_payment');
    Route::post('/seller_export_payment_excel', 'SellerDetailAdminController@export_payment_excel')->name('seller.payment_export_excel');
    Route::post('/single_export_frieght_excel_seller', 'SellerDetailAdminController@single_export_payment_excel')->name('seller.payment_export_excel');




    












//Route::resource('productseller', 'ProductSeller\ProductSellerController')->except('show');

Route::group(['middleware' => 'auth:admin'], function () {


    
   

    Route::get('/admin/sea-air-frights', 'SellerDetailAdminController@sea_air_frieghts')->name('seafrieghts');
    Route::get('/admin/collection', 'SellerDetailAdminController@collectionPayments')->name('collection');
    Route::get('/admin/notification', 'SellerDetailAdminController@notification')->name('notification');
    Route::get('/admin/notification/{id}', 'SellerDetailAdminController@notificationDetail')->name('notification.detail');
    Route::post('/export_frieght_excel', 'SellerDetailAdminController@export_frieght_excel')->name('export_excel');
    Route::POST('upload-sea-air-frights', 'SellerDetailController@upload_sea_air_frieghts')->name('admin.UFSF');
    Route::POST('upload_payment_collection', 'SellerDetailAdminController@upload_payment_collection')->name('import_payment');
    Route::post('/export_payment_excel', 'SellerDetailAdminController@export_payment_excel')->name('payment_export_excel');
    Route::post('/single_export_frieght_excel', 'SellerDetailAdminController@single_export_payment_excel')->name('payment_export_excel');
    Route::post('/single_export_collection_excel', 'SellerDetailAdminController@single_export_collection_excel')->name('single_export_payment_excel');


    // Route::get('cep/newsletter', 'SellerDetailAdminController@newsletter')->name('newsletter');
    // Route::get('cep/newsletter_show', 'SellerDetailAdminController@newsletter_show')->name('newsletter_show');
    // Route::post('cep/headline_store', 'SellerDetailAdminController@headline_store')->name('head-line-store');
    // Route::get('cep/newsletter_delete', 'SellerDetailAdminController@newsletter_delete')->name('newsletter-delete');

    Route::get('/admin/money-transfer', 'SellerDetailAdminController@moneyTransfer')->name('money.transfer');
    Route::POST('upload_money_transfer', 'SellerDetailAdminController@upload_money_transfer')->name('import_money_transfer');
    
    Route::post('/export_money_transfer_excel', 'SellerDetailAdminController@export_payment_excel_moneytransfer')->name('payment_money_excel');
    Route::post('/single_export_moneytransfer_excel', 'SellerDetailAdminController@single_export_moneytransfer_excel')->name('single_export_moneytrans_excel');






    


    Route::get('dashboard', 'DashboardController')->name('dashboard');
    

    Route::group(['prefix' => 'account', 'as' => 'account.', 'namespace' => 'Account'], function () {
        Route::get('reset-password', 'ResetPasswordController')->name('reset-password');
        Route::post('reset-password', 'ResetPasswordController@update')->name('update-password');
        Route::post('updateprofile', 'ResetPasswordController@update')->name('update-profile');
    });


    Route::get('digitl-address/{id}', 'DigitalAddressController@show')->name('digitl-address.show');
    Route::get('digitl-address/', 'DigitalAddressController@index')->name('digitl-address.index');

    Route::group(['prefix' => 'attribute'], function() {

        Route::get('/attribute-groups-by-attribute-set/{id}', 'Attribute\AttributeController@attributeGroupsByAttributeSet');
        Route::resource('attribute', "Attribute\AttributeController")->except('show');

        Route::resource('attribute-set', "Attribute\AttributeSetController")->except('show');
        Route::resource('attribute-group', "Attribute\AttributeGroupController")->except('show');
    });

    
    Route::resource('user', 'UserController');
    Route::get('user-show/{id}', 'UserController@showUser')->name('usershow.show');
    Route::resource('address', 'AddressController');
    Route::resource('address-area', 'AddressAreasController');
    Route::resource('address-section', 'AddressSectionsController');
    
    
    Route::resource('edsauser', 'EdsaUserController');
    Route::post('edsauser', 'EdsaUserController@UpdateMinimumBalance')->name('edsatransaction.updateminbal');
    Route::resource('savedmeter', 'SavedMeterController');
    Route::resource('saveddstvrechargecard', 'SavedDstvRechargeCardController');
    Route::resource('savedstartimerechargecard', 'SavedStarTimeRechargeCardController');
    Route::resource('edsatransaction','EdsaTransactionController');
    Route::get('edsatransaction/{transaction}/download-pdf', 'EdsaTransactionController@downloadPdf')->name('edsatransaction.download-pdf');
    Route::resource('dstvtransaction','DstvTransactionController');
    Route::get('dstvtransaction/{transaction}/download-pdf', 'DstvTransactionController@downloadPdf')->name('dstvtransaction.download-pdf');
    Route::resource('startransaction','StarTransactionController');
    Route::get('startransaction/{transaction}/download-pdf', 'StarTransactionController@downloadPdf')->name('startransaction.download-pdf');
    Route::resource('seller', 'SellerDetailController');
    Route::get('seller-verify/{id}', 'SellerDetailController@verify')->name('seller.verify');

    

    Route::get('system-user/create', 'AdminUserController@showUserForm')->name('system-user.create');
    Route::get('system-user/list', 'AdminUserController@list')->name('system-user.list');
    Route::get('system-user/show/{id}', 'AdminUserController@show')->name('system-user.show');
    Route::post('system-user/update', 'AdminUserController@update')->name('system-user.update');
    Route::get('system-user/delete/{id}', 'AdminUserController@destroy')->name('system-user.delete');
    Route::post('system-user/create', 'AdminUserController@store');

    Route::resource('place-category', 'Place\PlaceCategoryController')->except('show');

    Route::resource('place', 'Place\PlaceController')->except('show');

    Route::resource('auto-category', 'Auto\AutoCategoryController')->except('show');

    Route::resource('auto', 'Auto\AutoController')->except('show');
    Route::group(['prefix' => 'auto','namespace' => 'Auto', 'as' => 'auto.'], function () {
        Route::get('interested-users/{auto}', 'AutoController@interestedUsers')->name('interested.users');
    });

    //Route::resource('realestate-category', 'RealEstate\RealEstateCategoryController')->except('show');
    Route::resource('real-estate-category', 'RealEstate\RealEstateCategoryController')->except('show');

    Route::resource('real-estate', 'RealEstate\RealEstateController')->except('show');

    Route::group(['prefix' => 'real-estate','namespace' => 'RealEstate', 'as' => 'real-estate.'], function () {
        Route::get('interested-users/{realEstate}', 'RealEstateController@interestedUsers')->name('interested.users');
    });

    Route::resource('knowledgebase-category', 'Knowledgebase\KnowledgebaseCategoryController')->except('show');

    Route::resource('question', 'Knowledgebase\QuestionController')->except('show');
    Route::get('question-create-upload','Knowledgebase\QuestionController@createUpload')->name('question.upload');
    Route::post('question-create-upload','Knowledgebase\QuestionController@import')->name('question.import');



    Route::resource('ad-detail', 'AdDetailController');


    Route::resource('product-category', 'Product\ProductCategoryController')->except('show');
    Route::resource('product', 'Product\ProductController')->except('show');


    Route::get('imageDelete/product/{id?}', 'Product\ProductController@imageDelete');
    Route::get('bgimageDelete/product/{id?}', 'Product\ProductController@bgimageDelete');
    Route::get('imageDelete/place/{id?}', 'Place\PlaceController@imageDelete');
    Route::get('imageDelete/real-estate/{id?}', 'RealEstate\RealEstateController@imageDelete');
    Route::get('imageDelete/auto/{id?}', 'Auto\AutoController@imageDelete');
    Route::get('imagebgDelete/auto/{id?}', 'Auto\AutoController@imagebgDelete');

    Route::resource('order', 'OrderController');
    Route::resource('order-report', 'OrderReportController');

    Route::resource('auto-report', 'AutoReportController');

    Route::resource('real-estate-report', 'RealEstateReportController');

    Route::resource('setting', 'SettingController');

    Route::get('import', 'Knowledgebase\QuestionController@import')->name('import');;

    Route::get('/section/{id}', 'AddressAreasController@getSection');
    Route::group(['prefix' => 'system/config', 'namespace' => 'Config', 'as' => 'config.'], function () {


        Route::get('sponsor', 'SponsorController')->name("sponsor");
        Route::post('sponsor', 'SponsorController@save');

        Route::get('tax', 'TaxController')->name("tax");
        Route::post('tax', 'TaxController@save');
    });
    Route::group(['middleware' => ['role:admin']], function () {

    });
    // Route::resource('aboutapp', 'AboutAppController')->only(['index', 'update']);
    // Route::resource('aboutapp', 'AboutAppController')->only(['index', 'update'])->names([
    //     'index' => 'admin.aboutapp.index',
    //     'update' => 'admin.aboutapp.update'
    // ]);
    Route::resource('aboutapp', 'AboutAppController')->only(['index', 'update']);
    Route::resource('legal', 'LegalTermsAndPoliciesController')->only(['index', 'update']);
    Route::get('legal/seller', 'LegalTermsAndPoliciesController@SellerTerms')->name('seller.terms');
    Route::put('legal/seller/update', 'LegalTermsAndPoliciesController@SellerTermsUpdate')->name('seller.terms.update');
    Route::resource('privacy', 'PrivacyPolicyController')->only(['index', 'update']);
    Route::resource('intellectual', 'IntellectualPropertyController')->only(['index', 'update']);
    Route::resource('cookies', 'CookiesController')->only(['index', 'update']);
    Route::resource('payment', 'PaymentsDeliveriesController')->only(['index', 'update']);
    Route::resource('returns', 'ReturnController')->only(['index', 'update']);
    Route::resource('edsautilities', 'EdsaUtilitiesController')->only(['index']);
    Route::post('/admin/save-meter', 'EdsaUtilitiesController@saveMeter')->name('save.meter');
    Route::resource('dstvutilities', 'DstvUtilitiesController')->only(['index']);
    Route::post('/admin/recharge-card', 'DstvUtilitiesController@rechargeCard')->name('recharge.card');
    Route::resource('starutilities', 'StarUtilitiesController')->only(['index']);
    Route::post('/admin/star-card', 'StarUtilitiesController@starRecharge')->name('star.recharge');
    Route::get('edsa_subscription','EdsaUtilitiesController@edsa_subscription')->name('edsasubscription');
    Route::post('edsa_subscription_store','EdsaUtilitiesController@edsa_subscription_store')->name('edsasubscription-store');
    Route::get('edsa_subscription_edit/{id}','EdsaUtilitiesController@edsa_subscription_edit')->name('edsasubscription-edit');
    Route::post('edsa_subscription_update','EdsaUtilitiesController@edsa_subscription_update')->name('edsasubscription-update');
    Route::get('edsa_subscription_delete/{id}','EdsaUtilitiesController@edsa_subscription_delete')->name('edsasubscription-delete');
    Route::get('dstv_subscription','DstvUtilitiesController@dstv_subscription')->name('dstvsubscription');
    Route::post('dstv_subscription_store','DstvUtilitiesController@dstv_subscription_store')->name('dstvsubscription-store');
    Route::get('dstv_subscription_edit/{id}','DstvUtilitiesController@dstv_subscription_edit')->name('dstvsubscription-edit');
    Route::post('dstv_subscription_update','DstvUtilitiesController@dstv_subscription_update')->name('dstvsubscription-update');
    Route::get('dstv_subscription_delete/{id}','DstvUtilitiesController@dstv_subscription_delete')->name('dstvsubscription-delete');
    Route::get('startime_subscription','StarUtilitiesController@startime_subscription')->name('startimesubscription');
    Route::post('star_subscription_store','StarUtilitiesController@star_subscription_store')->name('starsubscription-store');
    Route::get('star_subscription_edit/{id}','StarUtilitiesController@star_subscription_edit')->name('starsubscription-edit');
    Route::post('star_subscription_update','StarUtilitiesController@star_subscription_update')->name('starsubscription-update');
    Route::get('star_subscription_delete/{id}','StarUtilitiesController@star_subscription_delete')->name('starsubscription-delete');



    Route::get('news_subscription','UtilityExtendController@news_subscription')->name('newssubscription');
    Route::post('news_subscription_store','UtilityExtendController@news_subscription_store')->name('newssubscription-store');
    Route::get('news_subscription_edit/{id}','UtilityExtendController@news_subscription_edit')->name('newssubscription-edit');
    Route::post('news_subscription_update','UtilityExtendController@news_subscription_update')->name('newssubscription-update');
    Route::get('news_subscription_delete/{id}','UtilityExtendController@news_subscription_delete')->name('newssubscription-delete');
    Route::get('national_news','UtilityExtendController@national_news')->name('national-news');
    Route::post('national_news_store','UtilityExtendController@national_news_store')->name('national-news-store');
    Route::get('national_news_edit/{id}','UtilityExtendController@national_news_edit')->name('national-news-edit');
    Route::post('national_news_update','UtilityExtendController@national_news_update')->name('national-news-update');
    Route::get('national_news_delete/{id}','UtilityExtendController@national_news_delete')->name('national-news-delete');
    Route::post('cep/ckeditor/upload','UtilityExtendController@upload')->name('ckeditor.upload');
    Route::post('add_breaking_national_news','UtilityExtendController@breaking_national_news')->name('breaking-national-news');



    // notice routes
    Route::get('notice','UtilityExtendController@notice')->name('notice');
    Route::post('notice_store','UtilityExtendController@notice_store')->name('notice-store');
    Route::get('notice_edit/{id}','UtilityExtendController@notice_edit')->name('notice-edit');
    Route::post('notice_update','UtilityExtendController@notice_update')->name('notice-update');
    Route::get('notice_delete/{id}','UtilityExtendController@notice_delete')->name('notice-delete');
    Route::post('cep/ckeditor/upload','UtilityExtendController@upload')->name('ckeditor.upload');




    Route::get('state_subscription','UtilityExtendController@state_subscription')->name('statesubscription');
    Route::post('state_subscription_store','UtilityExtendController@state_subscription_store')->name('statesubscription-store');
    Route::get('state_subscription_edit/{id}','UtilityExtendController@state_subscription_edit')->name('statesubscription-edit');
    Route::post('state_subscription_update','UtilityExtendController@state_subscription_update')->name('statesubscription-update');
    Route::get('state_subscription_delete/{id}','UtilityExtendController@state_subscription_delete')->name('statesubscription-delete');


    Route::get('national_subscription','UtilityExtendController@national_subscription')->name('nationalsubscription');
    Route::post('national_subscription_store','UtilityExtendController@national_subscription_store')->name('nationalsubscription-store');
    Route::get('national_subscription_edit/{id}','UtilityExtendController@national_subscription_edit')->name('nationalsubscription-edit');
    Route::post('national_subscription_update','UtilityExtendController@national_subscription_update')->name('nationalsubscription-update');
    Route::get('national_subscription_delete/{id}','UtilityExtendController@national_subscription_delete')->name('nationalsubscription-delete');


    Route::get('auto_subscription','UtilityExtendController@auto_subscription')->name('autosubscription');
    Route::post('auto_subscription_store','UtilityExtendController@auto_subscription_store')->name('autosubscription-store');
    Route::get('auto_subscription_edit/{id}','UtilityExtendController@auto_subscription_edit')->name('autosubscription-edit');
    Route::post('auto_subscription_update','UtilityExtendController@auto_subscription_update')->name('autosubscription-update');
    Route::get('auto_subscription_delete/{id}','UtilityExtendController@auto_subscription_delete')->name('autosubscription-delete');


    Route::get('real_subscription','UtilityExtendController@real_subscription')->name('realsubscription');
    Route::post('real_subscription_store','UtilityExtendController@real_subscription_store')->name('realsubscription-store');
    Route::get('real_subscription_edit/{id}','UtilityExtendController@real_subscription_edit')->name('realsubscription-edit');
    Route::post('real_subscription_update','UtilityExtendController@real_subscription_update')->name('realsubscription-update');
    Route::get('real_subscription_delete/{id}','UtilityExtendController@real_subscription_delete')->name('realsubscription-delete');


    Route::get('notice_subscription','UtilityExtendController@notice_subscription')->name('noticesubscription');
    Route::post('notice_subscription_store','UtilityExtendController@notice_subscription_store')->name('noticesubscription-store');
    Route::get('notice_subscription_edit/{id}','UtilityExtendController@notice_subscription_edit')->name('noticesubscription-edit');
    Route::post('notice_subscription_update','UtilityExtendController@notice_subscription_update')->name('noticesubscription-update');
    Route::get('notice_subscription_delete/{id}','UtilityExtendController@notice_subscription_delete')->name('noticesubscription-delete');

    Route::get('sacton_subscription','UtilityExtendController@sacton_subscription')->name('sactonsubscription');
    Route::post('sacton_subscription_store','UtilityExtendController@sacton_subscription_store')->name('sactonsubscription-store');
    Route::get('sacton_subscription_edit/{id}','UtilityExtendController@sacton_subscription_edit')->name('sactonsubscription-edit');
    Route::post('sacton_subscription_update','UtilityExtendController@sacton_subscription_update')->name('sactonsubscription-update');
    Route::get('sacton_subscription_delete/{id}','UtilityExtendController@sacton_subscription_delete')->name('sactonsubscription-delete');

    Route::resource('seller-themes', SellerThemeController::class);
    Route::post('seller-themes/assign', 'UserController@assignTheme')->name('assign.themes');
    Route::post('seller-themes/delete', 'UserController@DeletassignTheme')->name('assign.delete');
    Route::post('seller/desc-add', 'UserController@addSellerAdditionalDesc')->name('seller.additional_desc');
    Route::post('seller/assign-registration-img', 'UserController@uploadBusinessRegImage')->name('upload.business.reg.image');
    Route::post('seller/update-details', 'UserController@updateSellerBusinessdetails')->name('seller.store');
    Route::post('seller-logo-update', 'UserController@UpdateLogo')->name('business.logo.seller');

    Route::get('services','ServiceCategoryController@index')->name('services.list');
    Route::get('services/create','ServiceCategoryController@create')->name('services.create');
    Route::post('services/create','ServiceCategoryController@store')->name('services.add');
    Route::get('services/edit/{id}','ServiceCategoryController@edit')->name('services.edit');
    Route::post('services/update','ServiceCategoryController@update')->name('services.update');
    Route::delete('/services/{serviceCategory}', 'ServiceCategoryController@destroy')->name('services.destroy');



    Route::get('chat_a_ride','ChatRideCategoryController@index')->name('chat_a_ride.list');
    Route::get('chat_a_ride/create','ChatRideCategoryController@create')->name('chat_a_ride.create');
    Route::post('chat_a_ride/create','ChatRideCategoryController@store')->name('chat_a_ride.add');
    Route::get('chat_a_ride/edit/{id}','ChatRideCategoryController@edit')->name('chat_a_ride.edit');
    Route::post('chat_a_ride/update','ChatRideCategoryController@update')->name('chat_a_ride.update');
    Route::delete('/chat_a_ride/{serviceCategory}', 'ChatRideCategoryController@destroy')->name('chat_a_ride.destroy');


    Route::get('mobi-doc','MobidoCategoryController@index')->name('movie_doc');
    Route::get('mobi-doc/create','MobidoCategoryController@create')->name('movie_doc.create');
    Route::post('mobi-doc/create','MobidoCategoryController@store')->name('movie_doc.add');
    Route::get('mobi-doc/edit/{id}','MobidoCategoryController@edit')->name('movie_doc.edit');
    Route::post('mobi-doc/update','MobidoCategoryController@update')->name('movie_doc.update');
    Route::delete('/mobi-doc/{serviceCategory}', 'MobidoCategoryController@destroy')->name('movie_doc.destroy');

    
    
    // Route::get('online-booking','OnlineBookCategoryController@index')->name('online');
    // Route::get('online-booking/create','OnlineBookCategoryController@create')->name('online.create');
    // Route::post('online-booking/create','OnlineBookCategoryController@store')->name('online.add');
    // Route::get('online-booking/edit/{id}','OnlineBookCategoryController@edit')->name('online.edit');
    // Route::post('online-booking/update','OnlineBookCategoryController@update')->name('online.update');
    
    
    Route::get('transport','TransportCategoryController@VehicleIndex')->name('transport.vehicles');
    Route::get('transport-vehicle/{id}','TransportCategoryController@VehicleDetail')->name('vehicle.show');
    Route::get('transport-delivery','TransportCategoryController@DeliveryIndex')->name('transport.delivery');
    Route::get('transport-delivery/{id}','TransportCategoryController@DeliveryDetail')->name('delivery.show');
    Route::get('transport/create','TransportCategoryController@create')->name('transport.create');
    Route::post('transport/create','TransportCategoryController@store')->name('transport.add');
    Route::get('transport/edit/{id}','TransportCategoryController@edit')->name('transport.edit');
    Route::post('transport/update','TransportCategoryController@update')->name('transport.update');

});
