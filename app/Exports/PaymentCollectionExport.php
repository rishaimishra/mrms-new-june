<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class PaymentCollectionExport implements FromCollection
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
            'ID', 'Customer ID','Seller ID','Loan Officer', 'Customer Name', 'Customer Address', 'Telephone', 'Email',
            'NIN', 'Loan Type', 'Item Loaned', 'Cost of loaned item', 'Payback',
            'Loan Term', 'Monthly Spread', 'No of months', 'Payment', 
            'Balance','created_at','updated_at','Interest','Payee Name','Payment Mode','Collection Agent Name','Digital Payment Name',
            'Payment Date Timestamp'
        ]);

        // Loop through seaFreights and format the data
        foreach ($this->payments as $payment) {
            $exportData->push([
                $payment->id,
                $payment->customer_id,
                $payment->seller_id,
                $payment->loan_officer,
                $payment->customer_name,
                $payment->customer_address,
                $payment->telephone,
                $payment->email,
                $payment->nin,
                $payment->loan_type,
                $payment->item_loaned,
                $payment->cost_of_loaned_item,
                $payment->payback,
                $payment->loan_term,
                $payment->monthly_spread,
                $payment->no_of_months,
                $payment->payment,
                $payment->balance,
                $payment->created_at,
                $payment->updated_at,
                $payment->interest,
                $payment->payee_name,
                $payment->payment_mode,
                $payment->collection_agent_name,
                $payment->digital_payment_type,
                $payment->payment_date_timestamp,
            ]);
        }

        return $exportData;
    }
}
