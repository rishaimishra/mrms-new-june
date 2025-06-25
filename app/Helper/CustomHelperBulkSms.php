<?php
namespace App\Helper;
use App\Models\Property;
use App\Models\LandlordDetail;
use App\Models\SmsToProperty;

class CustomHelperBulkSms{

    function send_sms($arr=''){
    	// dd("k",$arr);
    	$b=[];
    	$allLandLordNumber=LandlordDetail::whereIn('property_id',$arr)->select('mobile_1','property_id')->get();
    	// dd($allLandLordNumber);
        // dd(count($allLandLordNumber));
    	if(count($allLandLordNumber)>0){
    	    foreach($allLandLordNumber as $val){
    	    	// dd($val->property_id,$val->mobile_1);


                 //insert code to smsToProperty
                $ins=new SmsToProperty;
                $ins->property_id=@$val->property_id;
                $ins->sms_status="Y";
                $ins->save();






//-----------------get all details o sent in sms for each property

    	    	  $property = Property::find($val->property_id);

                //part 1
                    $CurrentYearAssessmentAmount = 0;
                    $PastPayableDue = 0;
                    $Penalty = 0;
                    $CurrentYearTotalPayment2021 = 0;
                    $CurrentYearTotalDue = 0;
                    $CurrentYearTotalDue2021 = 0;

                    $PastPayableDue2022 = 0;
                    $CurrentYearTotalPayment2022 = 0;
                    $Penalty2022 = 0;
                    $CurrentYearTotalDue2022 = 0;
                    $PastPayableDue = 0;

                 // part -2
                for($i=0; $i<count(@$property->assessmentHistory); $i++){
                  
                    $AssessmentYear = $property->assessmentHistory[$i]->created_at->year;
                    $CurrentYearAssessmentAmount = $property->assessmentHistory[$i]->current_year_assessment_amount;
                    if($PastPayableDue > 0){
                        $Penalty = $PastPayableDue*0.25;
                    }
                    else{
                        $Penalty =0;
                    }
                    
                    $CurrentYearTotalPayment = $property->assessmentHistory[$i]->getCurrentYearTotalPayment();
                    $CurrentYearTotalDue =$CurrentYearAssessmentAmount+$PastPayableDue+$Penalty - $CurrentYearTotalPayment;
                   
                   // if($i==2){
                   // dd($AssessmentYear,$CurrentYearAssessmentAmount,$PastPayableDue,$Penalty,$CurrentYearTotalPayment,$CurrentYearTotalDue,count(@$property->assessmentHistory));
                   // }
                    if($AssessmentYear!="2023"){
                       $PastPayableDue = $CurrentYearTotalDue;
                     }
                }


                // part-3
                 // dd($AssessmentYear,$CurrentYearAssessmentAmount,$PastPayableDue,$Penalty,$CurrentYearTotalPayment,$CurrentYearTotalDue);
                 $arr2=[];
                 $arr2['property_id']= $val->property_id;
                  $arr2['mobile']= $val->mobile_1;
                 $arr2['AssessmentYear']=$AssessmentYear ;
                 $arr2['CurrentYearAssessmentAmount']= number_format($CurrentYearAssessmentAmount,2) ;
                 $arr2['PastPayableDue']= number_format($PastPayableDue,2) ;
                 $arr2['Penalty']= number_format($Penalty,2) ;
                 $arr2['CurrentYearTotalPayment']= number_format($CurrentYearTotalPayment,2) ;
                 $arr2['CurrentYearTotalDue']= number_format($CurrentYearTotalDue,2);

                 // dd($arr2);
                 array_push($b,$arr2);









 //----------sms part

    	    	$msg=  "Pay Property rate. ID:".$arr2['property_id'] .".Amount due: NLe" . $arr2['CurrentYearTotalDue'] .".Download your demand notice from link:http://wardc.org/p-d/".$arr2['property_id'] . ".Contact toll line 8282 for detail" ; 
    	          



                 // "Your Property ID: ".$arr2['property_id'] .".Amount due: NLe" . $arr2['CurrentYearTotalDue'] .".For details, download your demand notice from link:http://wardc.org/p-d/".$arr2['property_id'] . ".Please contact our toll line 8282 for detail" ;


                 // "Your property rate account information are: The Property ID is ".$arr2['property_id'] .".   The billing year is ". $arr2['AssessmentYear'] . ".  Billing amount is NLe". $arr2['CurrentYearAssessmentAmount']. ".   Arrears is NLe". $arr2['PastPayableDue']. ".   Penalty is NLe". $arr2['Penalty']. ".   Amount Paid is NLe". $arr2['CurrentYearTotalPayment'] . ".  Amount due is NLe" . $arr2['CurrentYearTotalDue'] .".   Kindly pay your amount due to WARDC UBA revenue bank account No. 540910060000010,  or Orange Money by dialing #144*3*5# or Afrimoney by dialing *161#  within 2 weeks.  For enquiries, please contact our mobile toll line 8282. To download the demand notice please click below link    http://wardc.org/property-details/".$arr2['property_id'] . "" ; 
                  // dd($msg);
    	          // dd($arr2,$number,$msg); "text":'.$msg.'
                	$a="hi";
   
				   $data_json = '{
				       "from":"WARDC",
				       "to":"'.$val->mobile_1.'",
				      "text":"'.$msg.'"
				    }';



				    // dd( $data_json);
				    $authorization = base64_encode('tommyfarmar:Munjay@123');
				    // $authorization = base64_encode('jeetbasak54:Jjb_12345678*');
				    $ch = curl_init();
				    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json',"Authorization: Basic $authorization"));
				    //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
				    curl_setopt($ch, CURLOPT_URL, 'https://api.infobip.com/sms/1/text/single');

				    curl_setopt($ch, CURLOPT_POST, 1);
				    curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
				    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				    $response  = curl_exec($ch);
				    //var_dump(curl_getinfo($ch));
				   
				    // var_dump($response);
				    curl_close($ch);






    	   }//end foreach

    	} //end if
    	 // dd($b);
    	
    	
    	

    }
}
