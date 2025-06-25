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
            background-size: cover; /* Make sure the image covers the whole background */
            opacity: 0.2; /* Adjust opacity to make it look like a watermark */
            z-index: -1;
            height: auto; /* Maintain aspect ratio */
            margin-top:-450px;
            display: block;
            margin-left: auto;
            margin-right: auto;
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
            <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('img/trans_powered_by_startimes.png')))}}" class="header-image-right" alt="Image" height="150" widht="150"/>
        </td>
    </tr>
</table>
<hr>

<table style="width:100%; margin-top:20px;">
<tr>
        <td style="color:#fff; width:50%; font-family: 'Trebuchet MS', sans-serif;font-weight:bold; font-size:15px;">
            <p style="background-color: #0096FF;padding:10px;">RECEIPT NUMBER:</p>
        </td>

        <td style=" text-align: center; width:50%; font-family: Tahoma, Verdana, sans-serif; font-weight:bold; color: #909090; font-size:15px;">
            <p>1234567890</p>
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
        <td  style="width:100%; text-align: left;">
            <p><strong>Utility Category</strong></p>
        </td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td  style="width:100%; text-align: right;">
            <p><strong>{{ $transaction->product_name }}</strong></p>
        </td>
        
    </tr>
    <tr>
        <td  style="width:100%; text-align: left;">
            <p><strong>Transaction ID</strong></p>
        </td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td  style="width:100%; text-align: right;">
            <p><strong>{{ $transaction->transaction_id }}</strong></p>
        </td>
        
    </tr>

    <tr>
        <td  style="width:100%; text-align: left;">
            <p><strong>Transaction Status</strong></p>
        </td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td  style="width:100%; text-align: right;">
            @if ($transaction->transaction_status)
             <p><strong>Success</strong></p>
            @endif
        </td>
        
    </tr>


   

    <tr>
        <td  style="width:100%; text-align: left;">
            <p><strong>Smart Card Number</strong></p>
        </td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td  style="width:100%; text-align: right;">
            <p><strong>{{ $transaction->smartcard_number }}</strong></p>
        </td>
        
    </tr>


    <tr>
        <td  style="width:100%; text-align: left;">
            <p><strong>Bouquet Name</strong></p>
        </td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td  style="width:100%; text-align: right;">
            <p><strong>{{ $transaction->bouquet_name }}</strong></p>
        </td>
        
    </tr>



    <tr>
        <td  style="width:100%; text-align: left;">
            <p><strong>Service Action</strong></p>
        </td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td  style="width:100%; text-align: right;">
            <p><strong>{{ $transaction->subscription_type }}</strong></p>
        </td>
        
    </tr>

    <tr>
        <td  style="width:100%; text-align: left;">
            <p><strong>Amount</strong></p>
        </td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td  style="width:100%; text-align: right;">
            <p><strong>{{ $transaction->amount }}</strong></p>
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
            <p><strong>{{ $transaction->convinience_fee }}</strong></p>
        </td>
        
    </tr> 
    <tr>
        <td  style="width:100%; text-align: left;">
            <p><strong>Gatway Charge</strong></p>
        </td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td  style="width:100%; text-align: right;">
            <p><strong>{{ $transaction->gateway_charge }}</strong></p>
        </td>
        
    </tr>
    <tr>
        <td  style="width:100%; text-align: left;">
            <p><strong>Due Date</strong></p>
        </td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td style="width:20%;"></td>
        <td  style="width:100%; text-align: right;">
            <p><strong>{{ $transaction->due_date }}</strong></p>
        </td>
        
    </tr>
<table>
<img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('img/splash_logo.png')))}}" alt="Right Logo" class="background_image">

<div class="footer">
  <p>Sigma Ventures Limited</p>
</div>
</body>
</html>

