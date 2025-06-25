<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class MoneyTransferExport implements FromCollection
{
    protected $payments;

    public function __construct($payments)
    {
        $this->payments = $payments;
    }

    public function collection()
    {
        $exportData = collect();

        // Add headers
        $exportData->push([
            'ID', 'Customer ID','Seller ID','Originating Country', 'Transaction Code', 'Sender Name', 'Sender Address', 'Sender Mobile','Receiver Name','Receiver Address','Receiver Mobile','Amount Sent','Amount sent dest Country'
            
        ]);

        // Loop through seaFreights and format the data
        foreach ($this->payments as $payment) {
            $exportData->push([
                $payment->id,
                $payment->customer_id,
                $payment->seller_id,
                $payment->originating_country,
                $payment->transaction_code_number,
                $payment->sender_name,
                $payment->sender_address,
                $payment->sender_mobile,
                $payment->receiver_name,
                $payment->receiver_address,
                $payment->receiver_mobile,
                $payment->amount_sent,
                $payment->amount_sent_destination_country,
            ]);
        }

        return $exportData;
    }
}
