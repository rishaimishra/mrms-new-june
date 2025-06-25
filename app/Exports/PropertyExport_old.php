<?php

namespace App\Exports;

use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\PropertySanitationType;
use App\Models\PropertyWindowType;
use App\Models\PropertyRoofsMaterials;
use App\Models\PropertyWallMaterials;
use App\Models\PropertyUse;
use App\Models\PropertyZones;



class PropertyExport implements FromQuery, WithHeadings, ShouldAutoSize, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $properties;
    protected $year;

    public function __construct($properties)
    {
        $this->properties = $properties;
    }

//    public function collection()
//    {
//        return $this->properties->select(
//            'users.name',
//            'properties.street_number',
//            'properties.street_name',
//            'properties.ward',
//            'properties.constituency',
//            'properties.section',
//            'properties.chiefdom',
//            'properties.district',
//            'properties.province',
//            'properties.postcode',
//            'properties.organization_addresss',
//            'landlord_details.first_name',
//            'landlord_details.middle_name',
//            'landlord_details.surname',
//            'landlord_details.sex',
//            'landlord_details.street_name as landlord_steet',
//            'landlord_details.ward as landlord_ward',
//            'landlord_details.constituency  as landlord_constituency',
//            'landlord_details.section as landlord_section',
//            'landlord_details.chiefdom as landlord_chiefdom',
//            'landlord_details.district as landlord_district',
//            'landlord_details.province as landlord_province',
//            'landlord_details.postcode as landlord_postcode',
//            'landlord_details.mobile_1',
//            'landlord_details.mobile_2',
//            'property_assessment_details.property_rate_without_gst',
//            'property_assessment_details.property_use',
//            'property_assessment_details.zone',
//            'property_assessment_details.no_of_mast',
//            'property_assessment_details.no_of_shop',
//            'property_assessment_details.no_of_compound_house',
//            'property_assessment_details.compound_name',
//            'property_geo_registry.digital_address'
//        )
//            ->addSelect(\DB::raw('SUM(property_payments.amount) as paid_amount'))
//            ->leftJoin('property_assessment_details', 'property_assessment_details.property_id', '=', 'properties.id')
//            ->leftJoin('landlord_details', 'landlord_details.property_id', '=', 'properties.id')
//            ->leftJoin('property_geo_registry', 'property_geo_registry.property_id', '=', 'properties.id')
//            ->leftJoin('users', 'users.id', '=', 'properties.user_id')
//            ->leftJoin('property_payments', 'property_payments.property_id', '=', 'properties.id')
//            ->groupBy('properties.id')
//            ->orderBy('properties.id', 'desc')->get();
//
//    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        $transactions = [];
        $amount_paid = [];
        $sum=0;
        
        $assessment_year = $row->assessment->created_at->format('Y');
        foreach($row->payments  as $pay)
        {
            if ($assessment_year == $pay->created_at->format('Y')) {
                $sum += $pay->amount;
            }

            array_push($transactions, 'assessment: '.$pay->assessment, 'amount: '.$pay->amount, 'Payment Type: '.$pay->payment_type, 'Balance: '.$pay->balance, '----');
        }
        array_push($amount_paid, $sum);

        // dd($amount_paid, $assessment_year);
        return [
            $row->id,
            $row->assessment->created_at->format('Y'),
            $row->user->name,
            $row->street_number,
            $row->street_name,
            $row->ward,
            $row->constituency,
            $row->section,
            $row->chiefdom,
            $row->district,
            $row->province,
            $row->postcode,
            $row->organization_name,
            $row->organization_addresss,
            $row->landlord->first_name,
            $row->landlord->middle_name,
            $row->landlord->surname,
            $row->landlord->sex,
            $row->landlord->street_name,
            $row->landlord->ward,
            $row->landlord->constituency,
            $row->landlord->section,
            $row->landlord->chiefdom,
            $row->landlord->district,
            $row->landlord->province,
            $row->landlord->postcode,
            $row->landlord->mobile_1,
            $row->landlord->mobile_2,
            $row->assessment->property_rate_without_gst,
            optional(PropertyUse::find($row->assessment->property_use))->label,
            $row->assessment->zone,
            $row->assessment->no_of_mast,
            $row->assessment->no_of_shop,
            $row->assessment->no_of_compound_house,
            $row->assessment->compound_name,
            $row->assessment->types->pluck('label')->implode(', '),
            $row->assessment->typesTotal->pluck('label')->implode(', '),
            optional(PropertyZones::find($row->assessment->zone))->label,
            number_format($row->assessment->square_meter, 2,'.',''),
            optional(optional($row->assessment)->swimming)->label,
            optional(PropertyWallMaterials::find($row->assessment->property_wall_materials))->label,
            optional(PropertyRoofsMaterials::find($row->assessment->roofs_materials))->label,
            $row->assessment->valuesAdded->pluck('label')->implode(', '),
            optional(PropertyWindowType::find($row->assessment->property_window_type))->label,
            $row->geoRegistry->digital_address,
            //$row->landlord->postcode ." ". $row->geoRegistry->open_location_code,
            $row->newDigitalAddress(),
            $row->assessment->getImageOneUrl(),
            $row->assessment->getImageTwoUrl(),
            $row->assessment->water_percentage,
            $row->assessment->electricity_percentage,
            $row->assessment->waste_management_percentage,
            $row->assessment->market_percentage,
            $row->assessment->hazardous_precentage,
            $row->assessment->informal_settlement_percentage,
            $row->assessment->easy_street_access_percentage,
            $row->assessment->paved_tarred_street_percentage,
            $row->assessment->drainage_percentage,
            optional(PropertySanitationType::find($row->assessment->sanitation))->label,
            // $row->assessment->getTotalPayable(),
            $row->assessment->property_rate_without_gst + $row->assessment->getTotalPayable() - $sum,
            @$amount_paid[0],
            implode(',',$transactions),



        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Year',
            'User Name',
            'Property Street Number',
            'Property Street Name',
            'Property Ward',
            'Property Constituency',
            'Property Section',
            'Property Chiefdom',
            'Property District',
            'Property Province',
            'Property Postcode',
            'Property Organization Name',
            'Property Organization Address',
            'Landlord First Name',
            'Landlord Middle Name',
            'Landlord Surname',
            'Landlord Gender',
            'Landlord Street Name',
            'Landlord Ward',
            'Landlord Constituency',
            'Landlord Section',
            'Landlord Chiefdom',
            'Landlord District',
            'Landlord Province',
            'Landlord Postcode',
            'Landlord Mobile 1',
            'Landlord Mobile 2',
            'Assessment Property Rate Without Gst',
            'Assessment Property Use',
            'Assessment Zone',
            'Assessment No Of Mast',
            'Assessment No Of Shop',
            'Assessment No Of Compound House',
            'Assessment Compound Name',
            'Habitable Floors',
            'Total No. of Floors',
            'Zone',
            'Property Dimension(Sq. Meters)',
            'Swimming Pool',
            'Wall Material Type',
            'Roof Material Type',
            'Value Added',
            'Window Type',
            'Digital Address',
            'Open Location Code',
            'Image One',
            'Image Two',
            'Water Percentage',
            'Electricity Percentage',
            'Waste Management Percentage',
            'Market Percentage',
            'Hazardous Precentage',
            'Informal Settlement Percentage',
            'Easy Atreet Access Percentage',
            'Paved Tarred Street Percentage',
            'Drainage Percentage',
            'Sanitation',
            'Total Due',
            'Amount Paid',
            'Payments',

        ];
    }

    /**
     * @return Builder
     */
    public function query()
    {
        return $this->properties;
    }


}
