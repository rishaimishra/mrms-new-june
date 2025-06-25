<!DOCTYPE html>
<html>
<head>
    <title>SEVEN ELEVEN - UTILITIES</title>
</head>
<style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Trebuchet MS', sans-serif;
        }



        hr {
            border: none;
            height: 1px;
            background-color: grey;
            margin: 0; /* Removes default margins */
        }

        .background_image {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50%; /* Adjust the width as needed */
            height: auto;
            opacity: 0.1; /* Adjust opacity for watermark effect */
            z-index: -1; /* Ensure it stays behind other elements */
        }


        .inline-text {
            display: inline-block;
            margin-right: 20px; /* Adjust spacing between texts */
        }

        .inline-text-block {
            padding:10px;
            background-color: blue;
            color:white;
            font-weight:bold;
            display: inline-block;
            margin-right: 20px; /* Adjust spacing between texts */
        }

        table { table-layout: fixed; }
        td { 
            text-align: center;
        }
        
        .footer {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            background-color: white;
            color: #0096FF;
            text-align: center;
        }
        p{
            margin: 10px !important;
        }

</style>
<body>

<table style="width:100%">
    <tr>
        <td style="width:20%">
            <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('img/splash_logo.png')))}}" class="header-image-left" alt="Image" height="100" widht="100"/>
        </td>
        <td style="width:60%;">
            <p style="font-family: Tahoma, Verdana, sans-serif; font-weight:bold; font-size:20px;">
                SEVEN ELEVEN - UTILITIES
            </p>
            <p style="font-family: Tahoma, Verdana, sans-serif; font-weight:bold; font-size:17px; color: #909090;">
                TRANSACTION RECEIPT
            </p>
        </td>
        <td style="width:20%">
            <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('img/oie_transparent.png')))}}" class="header-image-right" alt="Image" height="150" widht="150"/>
        </td>
    </tr>
</table>
<hr>

<table style="width:100%; margin-top:20px;">
    <tr>
        <td style="color:#fff; width:100%; font-family: 'Trebuchet MS', sans-serif;font-weight:bold; font-size:15px;">
            <p style="background-color: #0096FF;padding:10px;">RECEIPT NUMBER:</p>
        </td>

        <td style=" text-align: center; width:20%; font-family: Tahoma, Verdana, sans-serif; font-weight:bold; color: #909090; font-size:15px;">
            <p>00000{{ $id }}</p>
        </td>
        <td style="width:30%">
        </td>
        <td style="width:30%">
        </td>
        <td style="text-align: right; width:100%; font-family: Tahoma, Verdana, sans-serif; font-weight:bold; color: #909090; font-size:15px;">
        <p>DATE: {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M, Y') }}</p>
        </td>
    </tr>

    <tr>
        <td  style="width:33%; text-align: left;">
            <p>CUSTOMER DETAILS</p>
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%; text-align: right;">
            <p></p>
        </td>
        
    </tr>
   

   <tr>
    <td  style="width:33%; text-align: left;">
        <p><strong>Meter Number:</strong></p>
    </td>
    <td  style="width:33%">
    </td>
    <td  style="width:33%">
    </td>
    <td  style="width:33%">
    </td>
    <td  style="width:33%; text-align: right;">
        <p><strong>{{ $transaction->meter_number }}</strong></p>
    </td>
    
</tr>
   <tr>
    <td  style="width:33%; text-align: left;">
        <p><strong>Customer Name :</strong></p>
    </td>
    <td  style="width:33%">
    </td>
    <td  style="width:33%">
    </td>
    <td  style="width:33%">
    </td>
    <td  style="width:33%; text-align: right;">
        <p><strong>{{ $transaction->name }}</strong></p>
    </td>
    
</tr>
   <tr>
    <td  style="width:33%; text-align: left;">
        <p><strong>Account Number :</strong></p>
    </td>
    <td  style="width:33%">
    </td>
    <td  style="width:33%">
    </td>
    <td  style="width:33%">
    </td>
    <td  style="width:33%; text-align: right;">
        <p><strong>{{ $transaction->cus_account_number }}</strong></p>
    </td>
    
</tr>
<tr>
    <td  style="width:33%; text-align: left;">
        <p><strong>Tariff:</strong></p>
    </td>
    <td  style="width:33%">
    </td>
    <td  style="width:33%">
    </td>
    <td  style="width:33%">
    </td>
    <td  style="width:33%; text-align: right;">
        @if($transaction->edsa_tariff_category)
        <p><strong>{{ $transaction->edsa_tariff_category }}</strong></p>
        @else
        <p><strong>{{ $transaction->Tariff }}</strong></p>
        @endif
    </td>
   
</tr>



<tr>
    <td  style="width:33%; text-align: left;">
        <p>TRANSACTION DETAILS :</p>
    </td>
    <td  style="width:33%">
    </td>
    <td  style="width:33%">
    </td>
    <td  style="width:33%">
    </td>
    <td  style="width:33%; text-align: right;">
        <p><strong></strong></p>
    </td>
    
</tr>


    <tr>
        <td  style="width:33%; text-align: left;">
            <p><strong>Transaction Id:</strong></p>
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%; text-align: right;">
            <p><strong>{{ $transaction->transactionId }}</strong></p>
        </td>
        
    </tr>

    <tr>
        <td  style="width:33%; text-align: left;">
            <p><strong>Transaction Status:</strong></p>
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%; text-align: right;">
            <p><strong>{{ $transaction->transaction_status }}</strong></p>
        </td>
        
    </tr>
    <tr>
        <td  style="width:33%; text-align: left;">
            <p><strong>Paying Amount :</strong></p>
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%; text-align: right;">
            <p><strong>{{  $transaction->amount }}</strong></p>
        </td>
        
    </tr>
     <tr>
        <td  style="width:33%; text-align: left;">
            <p><strong>GST:</strong></p>
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%; text-align: right;">
            <p><strong>{{ $transaction->TaxCharge }}</strong></p>
        </td>
        
    </tr>
    <tr>
        <td  style="width:33%; text-align: left;">
            <p><strong>Service Charge:</strong></p>
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%; text-align: right;">
            @if($transaction->service_charge)
            <p><strong>{{ $transaction->service_charge }}</strong></p>
            @else
            <p><strong>{{ $transaction->ServiceCharge }}</strong></p>
            @endif
        </td>
        
    </tr>
    
    <tr>
        <td  style="width:33%; text-align: left;">
            <p><strong>Debt Recovery</strong></p>
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%; text-align: right;">
            <p><strong>{{ $transaction->debitRecovery }}</strong></p>
        </td>
    </tr>
    <tr>
        <td  style="width:33%; text-align: left;">
            <p><strong>Cost of Units:</strong></p>
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%; text-align: right;">
            @if($transaction->units)
            <p><strong>{{ $transaction->CostOfUnits }}</strong></p>
            @else
            <p><strong>{{ $transaction->CostOfUnits }}</strong></p>
            @endif
        </td>
        
    </tr>
    
    <tr>
        <td  style="width:33%; text-align: left;">
            <p><strong>Units (Kwh):</strong></p>
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%; text-align: right;">
        <p><strong>{{ number_format($transaction->units, 2) }}</strong></p>
        </td>
        
    </tr>
     <tr>
        <td  style="width:33%; text-align: left;">
            <p><strong>Token:</strong></p>
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%">
        </td>
        <td  style="width:33%; text-align: right;">
        <p><strong>{{ rtrim(chunk_split($transaction->meterToken1, 4, '-'), '-') }}</strong></p>
            {{--  <p><strong>{{ implode('-', str_split($transaction->meterToken1, 4)) }}</strong></p>  --}}
        </td>
        
    </tr> 
     <tr>
        <td  style="width:100%; text-align: left;">
            <p><strong>Convinience fee:</strong></p>
        </td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td  style="width:100%; text-align: right;">
            {{--  <p><strong>{{ $transaction->convinience_fee }}</strong></p>  --}}
            <p><strong>0</strong></p>
        </td>
        
    </tr> 
    <tr>
        <td style="width:100%; text-align: left; white-space: nowrap;">
            <p><strong>Gateway Service Provider Fee</strong></p>
        </td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td style="width:100%; text-align: right;">
            <p><strong>0</strong></p>
        </td>
    </tr>
   
    
<table>
<img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('img/splash_logo.png')))}}" alt="Right Logo" class="background_image">

<div class="footer">
  <p>Sigma Ventures Limited</p>
</div>
</body>
</html>
