<?php

namespace App\Imports;

use App\Models\MoneyTransfer;
use Maatwebsite\Excel\Concerns\ToModel;

class MoneyTransImport implements ToModel
{
    private $rowCount = 0;
    private $sellerId;

    public function __construct($sellerId)
    {
        $this->sellerId = $sellerId;
    }

    public function model(array $row)
    {
        $this->rowCount++;

        // Skip the first row (header)
        if ($this->rowCount === 1) {
            return null;
        }

        // Generate an 8-character alphanumeric customer ID
        $customerId = $this->generateAlphanumericId();

        return new MoneyTransfer([
            'customer_id' => $customerId,
            'seller_id' => $this->sellerId,
            'originating_country' => $row[1],
            'transaction_code_number' => $row[2],
            'sender_name' => $row[3],
            'sender_address' => $row[4],
            'sender_mobile' => $row[5],
            'receiver_name' => $row[6],
            'receiver_address' => $row[7],
            'receiver_mobile' => $row[8],
            'amount_sent' => $this->formatAmount($row[9]),
            'amount_sent_destination_country' => $row[10],
        ]);
    }

    /**
     * Format amount to handle "US$" and spaces.
     */
    private function formatAmount($rawAmount)
    {
        $cleanAmount = str_replace(['US$', ' '], '', $rawAmount);
        return (float) $cleanAmount;
    }

    private function generateAlphanumericId()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $id = '';
        for ($i = 0; $i < 8; $i++) {
            $id .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $id;
    }
}
