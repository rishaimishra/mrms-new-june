<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class FreightExport implements FromCollection
{
    protected $seaFreights;

    public function __construct($seaFreights)
    {
        $this->seaFreights = $seaFreights;
    }

    public function collection()
    {
        $exportData = collect();

        // Add headers
        $exportData->push([
            'ID', 'Originating Country', 'Freight Number', 'Consignor Name', 'Consignor Address', 'Consignor Mobile',
            'Consignee Name', 'Consignee Address', 'Consignee Mobile', 'Transport Type', 'Vessel Name',
            'Port of Loading', 'Port of Discharge', 'Container Batch No', 'Bill of Lading No', 
            'Description of Goods', 'Freight Charges', 'Created At', 'Updated At', 'Seller ID', 'Seller Name', 
            'Estimated Date Departure', 'Estimated Date Arrival', 'Booking Reference ID',
            'Barrel','5 CM box','10 CM box','250 CM box','Car','Weight','Other Items','Other Items Quantity','Account ID',
            'Delivery Entity Service Charge', 'Management', 'Delivery Trip Cost', 'Weight', 
            'Weight Multiplying Factor', 'Convenience Fee', 'GST', 'Gateway Provider Charge', 'Total', 'Payment', 'Status'
        ]);

        // Loop through seaFreights and format the data
        foreach ($this->seaFreights as $freight) {
            $exportData->push([
                $freight->id,
                $freight->region,
                $freight->freight_number,
                $freight->consignor_name,
                $freight->consignor_address,
                $freight->consignor_mobile_no,
                $freight->consignee_name,
                $freight->consignee_address,
                $freight->consignee_mobile_no,
                $freight->transport_type,
                $freight->vessel_name,
                $freight->port_of_loading,
                $freight->port_of_discharge,
                $freight->container_batch_no,
                $freight->bill_of_lading_no,
                $freight->description_of_goods,
                $freight->freight_charges,
                $freight->created_at,
                $freight->updated_at,
                $freight->seller_id,
                $freight->seller_name,
                $freight->estimated_date_departure,
                $freight->estimated_date_arrival,
                $freight->booking_reference_id,
                $freight->Barrel,
                $freight->five_cm_box,
                $freight->ten_cm_box,
                $freight->twenty_five_cm_box,
                $freight->car,
                $freight->weight,
                $freight->other_items,
                $freight->other_items_quantity,
                $freight->account_id,
                $freight->deliveryBook ? $freight->deliveryBook->delivery_entity_service_charge : null,
                $freight->deliveryBook ? $freight->deliveryBook->management : null,
                $freight->deliveryBook ? $freight->deliveryBook->delivery_trip_cost : null,
                $freight->deliveryBook ? $freight->deliveryBook->weight : null,
                $freight->deliveryBook ? $freight->deliveryBook->weight_multiplying_factor : null,
                $freight->deliveryBook ? $freight->deliveryBook->convenience_fee : null,
                $freight->deliveryBook ? $freight->deliveryBook->gst : null,
                $freight->deliveryBook ? $freight->deliveryBook->gateway_provider_charge : null,
                $freight->deliveryBook ? $freight->deliveryBook->total : null,
                $freight->deliveryBook ? $freight->deliveryBook->payment : null,
                $freight->deliveryBook ? $freight->deliveryBook->status : null
            ]);
        }

        return $exportData;
    }
}
