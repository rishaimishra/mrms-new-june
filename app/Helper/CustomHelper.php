<?php
namespace App\Helper;
use App\Models\SmsToProperty;




class CustomHelper{

    function send_sms($arr='',$number=''){

        //insert code to smsToProperty
        $ins=new SmsToProperty;
        $ins->property_id=@$arr['property_id'];
        $ins->sms_status="Y";
        $ins->save();




    	
    	$msg=  "Pay Property rate. ID:".$arr['property_id'] .".Amount due: NLe" . $arr['CurrentYearTotalDue'] .".Download your demand notice from link:http://wardc.org/p-d/".$arr['property_id'] . ".Contact toll line 8282 for detail" ; 


        //  "Your Property ID: ".$arr['property_id'] .".Amount due: NLe" . $arr['CurrentYearTotalDue'] .".For details, download your demand notice from link:http://wardc.org/p-d/".$arr['property_id'] . ".Please contact our toll line 8282 for detail" ; 
        

        // "Your property rate account information are: The Property ID is ".$arr['property_id'] .".   The billing year is ". $arr['AssessmentYear'] . ".  Billing amount is NLe". $arr['CurrentYearAssessmentAmount']. ".   Arrears is NLe". $arr['PastPayableDue']. ".   Penalty is NLe". $arr['Penalty']. ".   Amount Paid is NLe". $arr['CurrentYearTotalPayment'] . ".  Amount due is NLe" . $arr['CurrentYearTotalDue'] .".   Kindly pay your amount due to WARDC UBA revenue bank account No. 540910060000010 or Orange Money by dialing #144*3*5# or Afrimoney by dialing *161#  within 2 weeks.  For enquiries, please contact our mobile toll line 8282. To download the demand notice please click below link    http://wardc.org/property-details/".$arr['property_id'] . "" ; 

        // dd($msg);
    	// dd($arr,$number,$msg); "text":'.$msg.'
    	$a="hi";

   $data_json = '{
       "from":"WARDC",
       "to":"'.$number.'",
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

    }
}
