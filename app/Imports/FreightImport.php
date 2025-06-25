<?php
namespace App\Imports;

use App\Models\SeaFreightShipment; // Your model namespace
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class FreightImport implements ToModel
{

    private $rowCount = 0; // To keep track of row number
    private $sellerId;      // To store seller ID
    private $sellerName;    // To store seller name
    public function __construct($sellerId, $sellerName)
    {
        $this->sellerId = $sellerId;
        $this->sellerName = $sellerName;
    }
   public function model(array $row)
{
    
    $this->rowCount++;

    // Skip the first two rows
    if ($this->rowCount <= 2 || $row[0] == null) {
        return null; // Skip rows
    }

    $existingShipment = SeaFreightShipment::where('container_batch_no', $row[12])->first();
    if (!$existingShipment) {
        return new SeaFreightShipment([
            'region' => $row[0],
            'bill_of_lading_no' => $row[1],
            'freight_number' => $row[2],
            'container_batch_no' => $row[3],
            'vessel_name' => $row[4],
            'port_of_loading' => $row[5],
            'port_of_discharge' => $row[6],
            'account_id' => $row[7],
            'consignor_name' => $row[8],
            'consignor_address' => $row[9],
            'consignor_mobile_no' => $row[10],
            'consignee_name' => $row[11],
            'consignee_address' => $row[12],
            'consignee_mobile_no' => $row[13],
            'transport_type' => $row[14],
            'description_of_goods' => $row[15],
            'Barrel' => $row[16],
            'five_cm_box' => $row[17],
            'ten_cm_box' => $row[18],
            'twenty_five_cm_box' => $row[19],
            'car' => $row[20],
            'weight' => $row[21],
            'other_items' => $row[22],
            'other_items_quantity' => $row[23],
            'freight_charges' => $row[24],
            'seller_id' => $this->sellerId,
            'seller_name' => $this->sellerName, 
            'unique_generate_key' => Str::random(8), 
            // 'estimated_date_departure' => $row[16],
            // 'estimated_date_arrival' => $row[17],
            'booking_reference_id' =>Str::random(8)
        ]);
    }
}

}
