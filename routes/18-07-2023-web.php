<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Notification;
use App\Notifications\BroadcastNotification;
use App\Notifications\PaymentSMSNotification;
use OpenLocationCode\OpenLocationCode;
use App\Http\Controllers\StreetNamingAppController;

Route::get('/', function () {
	 //\DB::statement('');
	// exit('done');
  return redirect('back-admin/dashboard');



// \Mail::raw('Text to e-mail', function ($message) {
//     $message->to('kingshuk.mat@gmail.com')->subject('Your Test!');
// });

});
Route::get('non-profit/business-registration', 'BusinessRegController@nonProfitBusinessReg');
Route::post('non-profit/business-registration/store', 'BusinessRegController@nonProfitBusinessRegStore')->name("business_reg.store_non_profit");
Route::get('profit/business-registration', 'BusinessRegController@profitBusinessReg');
Route::post('business-registration/send-email', 'BusinessRegController@sendBusinessEditFormEmail')->name("business_reg.sendBusinessEditFormEmail");
Route::get('profit/business-registration/{token}', 'BusinessRegController@updateProfitBusiness')->name("business_reg.updateProfitBusiness");
Route::get('non-profit/business-registration/{token}', 'BusinessRegController@updateNonProfitBusiness')->name("business_reg.updateNonProfitBusiness");
Route::post('profit/business-registration/store', 'BusinessRegController@profitBusinessRegStore')->name("business_reg.store_profit");
Route::post('profit/business-registration/update', 'BusinessRegController@profitBusinessRegUpdate')->name("business_reg.update_profit");
Route::post('non-profit/business-registration/update', 'BusinessRegController@nonProfitBusinessRegUpdate')->name("business_reg.update_non_profit");
Route::resource('business-registration', 'BusinessRegController');
Route::get('business-registration/get-bussiness-type/{type}','BusinessRegController@getBusinessType');
Route::get('business-registration/all-license/{type}','BusinessRegController@getBusinessAllLicenseCategory');
Route::get('business-registration/get-bussiness-license-category/{type}','BusinessRegController@getBusinessLicenseCategory');
Route::get('business-registration/get-license-fee-id-category/{id}/','BusinessRegController@getLicenseIDFeeCategory');
Route::get('business-registration/get-license-fee-category/{id}/{size}','BusinessRegController@getLicenseFeeCategory');
  Route::get('business-list', 'BusinessRegController@BusinessList');
Route::get('business-list/{id}', 'BusinessRegController@BusinessListById');
Route::post('business-update/{id}', 'BusinessRegController@updateBus');

Route::get('/image-list', function () {

$propertiesDtls = DB::table('properties')
            ->join('property_assessment_details', 'properties.id', '=', 'property_assessment_details.property_id')
            ->select('property_assessment_details.property_id', 'property_assessment_details.assessment_images_2','property_assessment_details.assessment_images_1','property_assessment_details.demand_note_recipient_photo')
           // ->limit(10)
            ->get();
//dd($propertiesDtls);

   echo'<table>';
   $i = 1;
   foreach ($propertiesDtls as $dtls) {
    if((($dtls->assessment_images_2) && !(file_exists(storage_path().'/app/'.$dtls->assessment_images_2))) || (($dtls->assessment_images_1) && !(file_exists(storage_path().'/app/'.$dtls->assessment_images_1))) || (($dtls->demand_note_recipient_photo) && !(file_exists(storage_path().'/app/'.$dtls->demand_note_recipient_photo))) ){

        echo '<tr>';
        echo '<td>'.$i++.'</td>';
        echo '<td>'.$dtls->property_id.'</td>';
        echo '<td>'.((($dtls->assessment_images_2) && (file_exists(storage_path().'/app/'.$dtls->assessment_images_2)))? '': $dtls->assessment_images_2) .'</td>';

        echo '<td>'.((($dtls->assessment_images_1) && (file_exists(storage_path().'/app/'.$dtls->assessment_images_1)))? '': $dtls->assessment_images_1) .'</td>';

        echo '<td>'.((($dtls->demand_note_recipient_photo) && (file_exists(storage_path().'/app/'.$dtls->demand_note_recipient_photo)))? '': $dtls->demand_note_recipient_photo) .'</td>';
        //echo '<td>'. $dtls->assessment_images_2 .'</td>';
        //echo '<td>'.if (file_exists( ) { $dtls->property_id }.'</td>';
        echo '</tr>';
    }
   }
   echo'</table>';
});


Route::get('/test', function () {
    return \App\Models\Property::withAssessmentCalculation(2019)->having('total_payable_due', 0)->orderBy('total_payable_due')->get();
});

Route::get('storage/{filename}', function ($filename) {

    return Image::make(storage_path('app/' . $filename))->response();
});

Route::get('paypal', 'PaymentController@index');
Route::post('paypal', 'PaymentController@payWithpaypal');


// route for check status of the payment
Route::get('status', 'PaymentController@getPaymentStatus')->name('status');
Route::get('cancel', 'PaymentController@cancel')->name('cancel');

Route::get('/sms-test', function() {

   /* @var $property \App\Models\Property */

   $property = \App\Models\Property::find(16615);

   //$property->landlord->notify(new PaymentSMSNotification($property, $property->landlord->mobile_1, $property->payments()->first()));
  //$property->landlord->notify(new PaymentSMSNotification($property, $property->landlord->mobile_1, \App\Models\Payment::first()));
   //$property->landlord->notify(new PaymentSMSNotification($property, '7003520826', $property->payments()->first()));

//dd(config('services.mrms.connections.twilio'));
  // Twilio::from(config('services.mrms.connections.twilio'))->message('+917003520826', 'Hello world!!');
   //Twilio::message('+917003520826', 'Hello world!!');

// Twilio::message(
//                 '+91...',
//                 [
//                     "body" => 'test sms 4',
//                     "from" => config('services.twilio.alphanumeric_sender')
//                     //   On US phone numbers, you could send an image as well!
//                     //  'mediaUrl' => $imageUrl
//                 ]
//             );

   //Notification::send($property->landlord, new BroadcastNotification($property, 'test sms'));
   //$property->landlord->notify(new BroadcastNotification($property, 'hiiii'));
   echo 'sms-test done';
});

//
//Route::get('property/list', 'admin\PropertyController@list');
//Route::get('property/details', 'admin\PropertyController@view');
Route::get('property/geo', 'Admin\PropertyController@PropertyAssesmentBckupGeo');
Route::get('property/bckup', 'admin\PropertyController@PropertyAssesmentBckup');
Route::get('property/bckupTWO', 'admin\PropertyController@PropertyAssesmentBckupTWO');
Route::post('import', 'admin\PropertyController@import')->name('import');
Route::get('importExportView', 'admin\PropertyController@importExportView');


Route::get('p-d/{id}', 'Admin\PropertyController@demand_note_page');
Route::get('property-details-pdf/{id}', 'Admin\PropertyController@demand_note_page_pdf')->name('pdf.demand.draft');






//business reg
Route::get('business-registrations/step-1', 'BusinessRegController@step1_get');
Route::post('business-registrations/step-1/post', 'BusinessRegController@step1_post')->name('business.stepone.post');

Route::get('street-name-application-payment', 'StreetNamingAppController@payment')->name('street_name_application');
Route::post('street-name-application-payment/store', 'StreetNamingAppController@paymentStore')->name('street_name_application.store');
Route::get('street-name-application-payment/{key}/preview', 'StreetNamingAppController@previewKey')->name('street_name_application.previewKey');
Route::get('street-name-application-payment/{key}/download', 'StreetNamingAppController@downloadPDF')->name('street_name_application.download');

Route::get('street-name-application', 'StreetNamingAppController@index')->name('street_name_application.index');
Route::post('street-name-application/store', 'StreetNamingAppController@store')->name('street_name_application.form.store');
Route::post('street-name-application/send-email', 'StreetNamingAppController@sendStreetNameAppEditFormEmail')->name("street_name_application.form.sendStreetNameAppEditFormEmail");
Route::get('street-name-application/e/{token}', 'StreetNamingAppController@updateStreetNameApp')->name("street_name_application.form.updateStreetNameApp");
Route::post('street-name-application/update', 'StreetNamingAppController@update')->name('street_name_application.form.update');
Route::post('street-name-application/track-application', 'StreetNamingAppController@trackApplication')->name('street_name_application.form.track-application');
Route::get('street-name-application/reapplied/{token}', 'StreetNamingAppController@reapplied')->name("street_name_application.form.reapplied");
Route::post('street-name-application/reapplied/store', 'StreetNamingAppController@reappliedStore')->name("street_name_application.form.reapplied.store");
