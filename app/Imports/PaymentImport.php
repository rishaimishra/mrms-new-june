<?php
namespace App\Imports;

use App\Models\CollectionPayment;
use Maatwebsite\Excel\Concerns\ToModel;



class PaymentImport implements ToModel
{

    private $rowCount = 0; // To keep track of row number
    private $sellerId;  
    private $expectedColumns = 15;
    public function __construct($sellerId)
    {
        $this->sellerId = $sellerId;
    }
    public function model(array $row)
    {
        // dd($row[20]);
        // die;
        $this->rowCount++;

        // Skip the first two rows
        if ($this->rowCount === 1) {
            return null; // Skip rows
        }
        if (count($row) < $this->expectedColumns) {
            return null; // Skip rows that don't have enough data
        }
            return new CollectionPayment([
                'customer_id' => $row[0],
                'seller_id' => $this->sellerId,
                'loan_officer' => $row[1],
                'customer_name' => $row[2],
                'customer_address' => $row[3],
                'telephone' => $row[4],
                'email' => $row[5],
                'nin' => $row[6],
                'loan_type' => $row[7],
                'item_loaned' => $row[8],
                'cost_of_loaned_item' => $row[9],
                'interest' => $row[10],
                'payback' => $row[11],
                'loan_term' => $row[12],
                'monthly_spread' => $row[13],
                'payment' => $row[14],
                'payee_name' => $row[15],
                'payment_mode' => $row[16],
                'collection_agent_name' => $row[17],
                'digital_payment_type' => $row[18],
                'payment_date_timestamp' => $row[19],
                // 'no_of_months' => $row[12],
                'balance' => $row[20],           
            ]);       
    }
}
